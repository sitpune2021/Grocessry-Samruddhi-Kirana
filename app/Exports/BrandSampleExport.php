<?php

namespace App\Exports;

use App\Models\Category;
use App\Models\SubCategory;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\NamedRange;

class BrandSampleExport implements WithHeadings, WithEvents
{

    public function headings(): array
    {
        return [
            'Category Name',
            'Sub Category Name',
            'Brand Name',
            'Logo URL',
        ];
    }

    public function registerEvents(): array
    {
        return [

            AfterSheet::class => function ($event) {

                $spreadsheet = $event->sheet->getDelegate()->getParent();

                // ── Hidden sheets ──────────────────────────────────────
                $categorySheet    = new Worksheet($spreadsheet, "CategoryList");
                $subCategorySheet = new Worksheet($spreadsheet, "SubCategoryList");

                $spreadsheet->addSheet($categorySheet);
                $spreadsheet->addSheet($subCategorySheet);

                // ── Categories ────────────────────────────────────────
                $categories = Category::with('subCategories')->orderBy('name')->get();

                $catRow = 1;
                foreach ($categories as $category) {
                    $categorySheet->setCellValue('A' . $catRow, $category->name);
                    $catRow++;
                }

                // ── SubCategories per Category (named ranges) ─────────
                $col = 'A';
                foreach ($categories as $category) {
                    $subs = $category->subCategories->pluck('name')->toArray();

                    if (empty($subs)) {
                        $col++;
                        continue;
                    }

                    $subRow = 1;
                    foreach ($subs as $sub) {
                        $subCategorySheet->setCellValue($col . $subRow, $sub);
                        $subRow++;
                    }

                    // Named range: category name spaces → underscore
                    $rangeName = preg_replace('/[^A-Za-z0-9_]/', '_', $category->name);
                    $subCount  = count($subs);

                    $spreadsheet->addNamedRange(
                        new NamedRange(
                            $rangeName,
                            $subCategorySheet,
                            '$' . $col . '$1:$' . $col . '$' . $subCount
                        )
                    );

                    $col++;
                }

                // ── Hide helper sheets ────────────────────────────────
                $categorySheet->setSheetState(Worksheet::SHEETSTATE_HIDDEN);
                $subCategorySheet->setSheetState(Worksheet::SHEETSTATE_HIDDEN);

                $catCount = $categories->count();

                // ── Dropdowns for rows 2–500 ──────────────────────────
                for ($row = 2; $row <= 500; $row++) {

                    // Column A — Category dropdown
                    $val1 = $event->sheet->getCell('A' . $row)->getDataValidation();
                    $val1->setType(DataValidation::TYPE_LIST);
                    $val1->setErrorStyle(DataValidation::STYLE_STOP);
                    $val1->setAllowBlank(true);
                    $val1->setShowDropDown(true);
                    $val1->setFormula1("=CategoryList!\$A\$1:\$A\$$catCount");

                    // Column B — SubCategory dropdown (INDIRECT based on A column)
                    $val2 = $event->sheet->getCell('B' . $row)->getDataValidation();
                    $val2->setType(DataValidation::TYPE_LIST);
                    $val2->setErrorStyle(DataValidation::STYLE_STOP);
                    $val2->setAllowBlank(true);
                    $val2->setShowDropDown(true);
                    $val2->setFormula1('INDIRECT(SUBSTITUTE(A' . $row . '," ","_"))');
                }

                // ── Column widths ─────────────────────────────────────
                $event->sheet->getColumnDimension('A')->setWidth(20);
                $event->sheet->getColumnDimension('B')->setWidth(20);
                $event->sheet->getColumnDimension('C')->setWidth(25);
                $event->sheet->getColumnDimension('D')->setWidth(40);

            }

        ];
    }
}