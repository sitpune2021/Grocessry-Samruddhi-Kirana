<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SubCategorySampleExport implements WithHeadings, WithEvents
{
    public function headings(): array
    {
        return [
            'Category Name',
            'Sub Category Name'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function ($event) {

                $spreadsheet = $event->sheet->getDelegate()->getParent();

                // hidden sheet create
                $categorySheet = new Worksheet($spreadsheet, "CategoryList");
                $spreadsheet->addSheet($categorySheet);

                $categories = Category::pluck('name')->toArray();

                // fill categories
                foreach ($categories as $index => $cat) {
                    $categorySheet->setCellValue('A' . ($index + 1), $cat);
                }

                // hide sheet
                $categorySheet->setSheetState(Worksheet::SHEETSTATE_HIDDEN);

                $lastRow = count($categories);

                // dropdown validation
                for ($row = 2; $row <= 500; $row++) {

                    $validation = $event->sheet->getDelegate()
                        ->getCell('A' . $row)
                        ->getDataValidation();

                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(DataValidation::STYLE_STOP);
                    $validation->setAllowBlank(true);
                    $validation->setShowDropDown(true);
                    $validation->setFormula1("=CategoryList!A1:A$lastRow");
                }
            }
        ];
    }
}
