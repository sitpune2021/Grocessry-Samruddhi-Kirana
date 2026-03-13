<?php

namespace App\Exports;

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Brand;
use App\Models\Tax;
use App\Models\Unit;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\NamedRange;

class ProductSampleExport implements WithHeadings, WithEvents
{
    public function headings(): array
    {
        return [
            'Category Name',      // A
            'Sub Category Name',  // B
            'Brand Name',         // C
            'Product Name',       // D
            'Barcode',            // E
            'Description',        // F
            'Unit',               // G
            'Unit Value',         // H
            'Base Price',         // I
            'Selling Price',      // J
            'MRP',                // K
            'GST',                // L
            'Image URL',          // M
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function ($event) {

                $spreadsheet = $event->sheet->getDelegate()->getParent();

                // ── Hidden Helper Sheets ──────────────────────────────
                $categorySheet    = new Worksheet($spreadsheet, "CategoryList");
                $subCategorySheet = new Worksheet($spreadsheet, "SubCategoryList");
                $brandSheet       = new Worksheet($spreadsheet, "BrandList");
                $unitSheet        = new Worksheet($spreadsheet, "UnitList");
                $gstSheet         = new Worksheet($spreadsheet, "GSTList");

                $spreadsheet->addSheet($categorySheet);
                $spreadsheet->addSheet($subCategorySheet);
                $spreadsheet->addSheet($brandSheet);
                $spreadsheet->addSheet($unitSheet);
                $spreadsheet->addSheet($gstSheet);

                // ── Fill Categories ───────────────────────────────────
                $categories = Category::with('subCategories')->orderBy('name')->get();
                $catRow = 1;
                foreach ($categories as $category) {
                    $categorySheet->setCellValue('A' . $catRow, $category->name);
                    $catRow++;
                }

                // ── Fill SubCategories per Category (Named Ranges) ────
                $col = 'A';
                foreach ($categories as $category) {
                    $subs = $category->subCategories->pluck('name')->toArray();
                    if (empty($subs)) { $col++; continue; }

                    $subRow = 1;
                    foreach ($subs as $sub) {
                        $subCategorySheet->setCellValue($col . $subRow, $sub);
                        $subRow++;
                    }
                    $rangeName = preg_replace('/[^A-Za-z0-9_]/', '_', $category->name);
                    $spreadsheet->addNamedRange(new NamedRange(
                        $rangeName, $subCategorySheet,
                        '$' . $col . '$1:$' . $col . '$' . count($subs)
                    ));
                    $col++;
                }

                // ── Fill Brands per SubCategory (Named Ranges) ────────
                $subCategories = SubCategory::with('brands')->orderBy('name')->get();
                $bCol = 'A';
                foreach ($subCategories as $subCat) {
                    $brands = $subCat->brands->where('status', 1)->pluck('name')->toArray();
                    if (empty($brands)) { $bCol++; continue; }

                    $bRow = 1;
                    foreach ($brands as $brand) {
                        $brandSheet->setCellValue($bCol . $bRow, $brand);
                        $bRow++;
                    }
                    $bRangeName = 'brand_' . preg_replace('/[^A-Za-z0-9_]/', '_', $subCat->name);
                    $spreadsheet->addNamedRange(new NamedRange(
                        $bRangeName, $brandSheet,
                        '$' . $bCol . '$1:$' . $bCol . '$' . count($brands)
                    ));
                    $bCol++;
                }

                // ── Fill Units ────────────────────────────────────────
                $units = Unit::orderBy('name')->get();
                $uRow = 1;
                foreach ($units as $unit) {
                    $unitSheet->setCellValue('A' . $uRow, $unit->name . ' (' . strtoupper($unit->short_name) . ')');
                    $uRow++;
                }
                $unitCount = $units->count();

                // ── Fill GST ──────────────────────────────────────────
                $taxes = Tax::where('is_active', 1)->get();
                $gRow = 1;
                foreach ($taxes as $tax) {
                    $gstSheet->setCellValue('A' . $gRow, $tax->name . ' (' . $tax->gst . '%)');
                    $gRow++;
                }
                $gstCount = $taxes->count();

                // ── Hide Helper Sheets ────────────────────────────────
                $categorySheet->setSheetState(Worksheet::SHEETSTATE_HIDDEN);
                $subCategorySheet->setSheetState(Worksheet::SHEETSTATE_HIDDEN);
                $brandSheet->setSheetState(Worksheet::SHEETSTATE_HIDDEN);
                $unitSheet->setSheetState(Worksheet::SHEETSTATE_HIDDEN);
                $gstSheet->setSheetState(Worksheet::SHEETSTATE_HIDDEN);

                $catCount = $categories->count();

                // ── Dropdowns for rows 2–500 ──────────────────────────
                for ($row = 2; $row <= 500; $row++) {

                    // A — Category
                    $v1 = $event->sheet->getCell('A' . $row)->getDataValidation();
                    $v1->setType(DataValidation::TYPE_LIST)->setErrorStyle(DataValidation::STYLE_STOP)
                        ->setAllowBlank(true)->setShowDropDown(true)
                        ->setFormula1("=CategoryList!\$A\$1:\$A\$$catCount");

                    // B — SubCategory (INDIRECT from A)
                    $v2 = $event->sheet->getCell('B' . $row)->getDataValidation();
                    $v2->setType(DataValidation::TYPE_LIST)->setErrorStyle(DataValidation::STYLE_STOP)
                        ->setAllowBlank(true)->setShowDropDown(true)
                        ->setFormula1('INDIRECT(SUBSTITUTE(A' . $row . '," ","_"))');

                    // C — Brand (INDIRECT from B with prefix)
                    $v3 = $event->sheet->getCell('C' . $row)->getDataValidation();
                    $v3->setType(DataValidation::TYPE_LIST)->setErrorStyle(DataValidation::STYLE_STOP)
                        ->setAllowBlank(true)->setShowDropDown(true)
                        ->setFormula1('INDIRECT("brand_"&SUBSTITUTE(B' . $row . '," ","_"))');

                    // G — Unit
                    $v7 = $event->sheet->getCell('G' . $row)->getDataValidation();
                    $v7->setType(DataValidation::TYPE_LIST)->setErrorStyle(DataValidation::STYLE_STOP)
                        ->setAllowBlank(true)->setShowDropDown(true)
                        ->setFormula1("=UnitList!\$A\$1:\$A\$$unitCount");

                    // L — GST
                    $v12 = $event->sheet->getCell('L' . $row)->getDataValidation();
                    $v12->setType(DataValidation::TYPE_LIST)->setErrorStyle(DataValidation::STYLE_STOP)
                        ->setAllowBlank(true)->setShowDropDown(true)
                        ->setFormula1("=GSTList!\$A\$1:\$A\$$gstCount");
                }

                // ── Column Widths ─────────────────────────────────────
                $widths = [
                    'A' => 20, 'B' => 20, 'C' => 20, 'D' => 25,
                    'E' => 15, 'F' => 30, 'G' => 15, 'H' => 12,
                    'I' => 12, 'J' => 12, 'K' => 12, 'L' => 15,
                    'M' => 40,
                ];
                foreach ($widths as $col => $width) {
                    $event->sheet->getColumnDimension($col)->setWidth($width);
                }

                // ── Header Style ──────────────────────────────────────
                $event->sheet->getStyle('A1:M1')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '4472C4'],
                    ],
                ]);
            }
        ];
    }
}