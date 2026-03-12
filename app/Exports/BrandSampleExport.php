<?php

namespace App\Exports;

use App\Models\Category;
use App\Models\SubCategory;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BrandSampleExport implements WithHeadings, WithEvents
{

    public function headings(): array
    {
        return [
            'Category Name',
            'Sub Category Name',
            'Brand Name'
        ];
    }

    public function registerEvents(): array
    {
        return [

            AfterSheet::class => function ($event) {

                $spreadsheet = $event->sheet->getDelegate()->getParent();

                $categorySheet = new Worksheet($spreadsheet, "CategoryList");
                $subCategorySheet = new Worksheet($spreadsheet, "SubCategoryList");

                $spreadsheet->addSheet($categorySheet);
                $spreadsheet->addSheet($subCategorySheet);

                $categories = Category::pluck('name')->toArray();
                $subCategories = SubCategory::pluck('name')->toArray();

                foreach ($categories as $index => $cat) {
                    $categorySheet->setCellValue('A'.($index+1), $cat);
                }

                foreach ($subCategories as $index => $sub) {
                    $subCategorySheet->setCellValue('A'.($index+1), $sub);
                }

                $categorySheet->setSheetState(Worksheet::SHEETSTATE_HIDDEN);
                $subCategorySheet->setSheetState(Worksheet::SHEETSTATE_HIDDEN);

                $catCount = count($categories);
                $subCount = count($subCategories);

                for ($row = 2; $row <= 500; $row++) {

                    // Category dropdown
                    $validation = $event->sheet->getCell('A'.$row)->getDataValidation();

                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(DataValidation::STYLE_STOP);
                    $validation->setAllowBlank(true);
                    $validation->setShowDropDown(true);
                    $validation->setFormula1("=CategoryList!A1:A$catCount");

                    // SubCategory dropdown
                    $validation2 = $event->sheet->getCell('B'.$row)->getDataValidation();

                    $validation2->setType(DataValidation::TYPE_LIST);
                    $validation2->setErrorStyle(DataValidation::STYLE_STOP);
                    $validation2->setAllowBlank(true);
                    $validation2->setShowDropDown(true);
                    $validation2->setFormula1("=SubCategoryList!A1:A$subCount");
                }

            }

        ];
    }
}