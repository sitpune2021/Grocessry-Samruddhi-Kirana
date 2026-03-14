<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Exports\SubCategorySampleExport;
use Maatwebsite\Excel\Facades\Excel;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subCategories = SubCategory::with('category')->latest()->paginate(20);
        return view('menus.sub-category.index', compact('subCategories'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $mode = 'add';
        $categories = Category::all();
        return view('menus.sub-category.add-subcategory', compact('categories', 'mode'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:255 |unique:sub_categories,name',
            'slug'        => 'required|string|max:255|unique:sub_categories,slug',
        ]);

        try {
            // Create Sub Category
            SubCategory::create([
                'category_id' => $validated['category_id'],
                'name'        => $validated['name'],
                'slug'        => Str::slug($validated['slug']),
                'created_by'  => auth()->id(),
            ]);

            // Success log
            Log::info('Sub Category created successfully', [
                'category_id' => $validated['category_id'],
                'name'        => $validated['name'],
                'created_by'  => auth()->id(),
            ]);

            return redirect()->route('sub-category.index')
                ->with('success', 'Sub Category created successfully');
        } catch (\Throwable $e) {

            // Error log
            Log::error('Failed to create Sub Category', [
                'error'      => $e->getMessage(),
                'trace'      => $e->getTraceAsString(),
                'request'    => $request->all(),
                'user_id'    => auth()->id(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while creating Sub Category');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SubCategory $subCategory)
    {
        $mode = 'view';
        $categories = Category::all();
        return view('menus.sub-category.add-subcategory', compact('subCategory', 'categories', 'mode'));
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubCategory $subCategory)
    {
        $mode = 'edit';
        $categories = Category::all();
        return view('menus.sub-category.add-subcategory', compact('subCategory', 'categories', 'mode'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SubCategory $subCategory)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:255',
            'slug'        => 'required|string|max:255|unique:sub_categories,slug,' . $subCategory->id,
        ]);

        $subCategory->update([
            'category_id' => $request->category_id,
            'name'        => $request->name,
            'slug'        => Str::slug($request->slug),
            'updated_by'  => auth()->id(),
        ]);

        return redirect()->route('sub-category.index')
            ->with('success', 'Sub Category updated successfully');
    }

    /** DELETE */
    public function destroy(SubCategory $subCategory)
    {
        $subCategory->delete();

        return redirect()->route('sub-category.index')
            ->with('success', 'Sub Category deleted successfully');
    }

    //get sub categories
    public function getSubCategories($categoryId)
    {
        $subCategories = SubCategory::where('category_id', $categoryId)
            ->get(['id', 'name']);

        return response()->json($subCategories);
    }
     public function bulkUpload(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,csv,txt|max:5120',
        ]);
 
        try {
            Log::info('SubCategory Bulk Upload Started', [
                'ip'      => $request->ip(),
                'file'    => $request->file('excel_file')->getClientOriginalName(),
                'user_id' => auth()->id(),
            ]);
 
            $file      = $request->file('excel_file');
            $extension = strtolower($file->getClientOriginalExtension());
            $rows      = [];
 
            if ($extension === 'xlsx') {
                // ── ZipArchive ने xlsx read करा ──────────────────────
                $zip = new \ZipArchive();
                $zip->open($file->getRealPath());
 
                // Shared Strings
                $sharedStrings = [];
                $ssXml = $zip->getFromName('xl/sharedStrings.xml');
                if ($ssXml) {
                    $ss = simplexml_load_string($ssXml);
                    foreach ($ss->si as $si) {
                        if (isset($si->r)) {
                            $text = '';
                            foreach ($si->r as $r) {
                                $text .= (string)$r->t;
                            }
                            $sharedStrings[] = $text;
                        } else {
                            $sharedStrings[] = (string)$si->t;
                        }
                    }
                }
 
                // Sheet1
                $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
                $zip->close();
 
                $sheet = simplexml_load_string($sheetXml);
                foreach ($sheet->sheetData->row as $row) {
                    $rowData = [];
                    foreach ($row->c as $cell) {
                        $type      = (string)$cell['t'];
                        $value     = (string)$cell->v;
                        $rowData[] = ($type === 's') ? ($sharedStrings[(int)$value] ?? '') : $value;
                    }
                    $rows[] = $rowData;
                }
                array_shift($rows); // header skip
 
            } else {
                // ── CSV ───────────────────────────────────────────────
                $handle = fopen($file->getRealPath(), 'r');
                fgetcsv($handle); // header skip
                while (($row = fgetcsv($handle)) !== false) {
                    $rows[] = $row;
                }
                fclose($handle);
            }
 
            $success = 0;
            $skipped = 0;
            $rowNum  = 1;
 
            foreach ($rows as $row) {
                $rowNum++;
                $categoryName = trim($row[0] ?? '');
                $name         = trim($row[1] ?? '');
 
                Log::info("SubCategory Bulk Upload: Processing Row $rowNum", [
                    'category' => $categoryName,
                    'name' => $name,
                ]);
 
                if (!$categoryName || !$name) {
                    Log::warning("Skipped — Empty", ['row' => $rowNum]);
                    $skipped++;
                    continue;
                }
 
                $category = Category::whereRaw('LOWER(name) = ?', [strtolower($categoryName)])->first();
                if (!$category) {
                    Log::warning("Skipped — Category Not Found", ['row' => $rowNum, 'category' => $categoryName]);
                    $skipped++;
                    continue;
                }
 
                if (SubCategory::where('name', $name)->where('category_id', $category->id)->exists()) {
                    Log::warning("Skipped — Duplicate", ['row' => $rowNum, 'name' => $name]);
                    $skipped++;
                    continue;
                }
 
                $slug     = Str::slug($name);
                $original = $slug;
                $i        = 1;
                while (SubCategory::where('slug', $slug)->exists()) {
                    $slug = $original . '-' . $i++;
                }
 
                SubCategory::create([
                    'category_id' => $category->id,
                    'name'        => $name,
                    'slug'        => $slug,
                    'created_by'  => auth()->id(),
                ]);
 
                Log::info("SubCategory Created", ['row' => $rowNum, 'name' => $name]);
                $success++;
            }
 
            Log::info('SubCategory Bulk Upload Completed', ['success' => $success, 'skipped' => $skipped]);
 
            return redirect()->route('sub-category.index')
                ->with('success', "$success sub categories imported, $skipped skipped");
        } catch (\Exception $e) {
            Log::error('SubCategory Bulk Upload Error', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

     public function downloadSampleExcel()
    {
        Log::info('SubCategory Sample Excel Download');
 
        $categories = Category::orderBy('name')->pluck('name')->toArray();
        $catCount   = count($categories);
 
        // Shared Strings
        $allStrings = array_merge(['Category Name', 'Sub Category Name'], $categories);
 
        $strIndex = [];
        foreach ($allStrings as $s) {
            if (!isset($strIndex[$s])) {
                $strIndex[$s] = count($strIndex);
            }
        }
 
        $siXml = '';
        foreach (array_keys($strIndex) as $s) {
            $siXml .= '<si><t>' . htmlspecialchars($s, ENT_XML1) . '</t></si>';
        }
 
        // Category Sheet Rows
        $catRows = '';
        foreach ($categories as $i => $cat) {
            $r   = $i + 1;
            $idx = $strIndex[$cat];
            $catRows .= "<row r=\"$r\"><c r=\"A$r\" t=\"s\"><v>$idx</v></c></row>";
        }
 
        $h0 = $strIndex['Category Name'];
        $h1 = $strIndex['Sub Category Name'];
 
        // Main Worksheet
        $sheet1Xml = '<?xml version="1.0" encoding="UTF-8"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"
           xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
 
  <dimension ref="A1:B1"/>
 
  <sheetViews>
    <sheetView workbookViewId="0">
      <selection activeCell="A2" sqref="A2"/>
    </sheetView>
  </sheetViews>
 
  <cols>
    <col min="1" max="1" width="25" customWidth="1"/>
    <col min="2" max="2" width="25" customWidth="1"/>
  </cols>
 
  <sheetData>
    <row r="1">
      <c r="A1" t="s" s="1"><v>' . $h0 . '</v></c>
      <c r="B1" t="s" s="1"><v>' . $h1 . '</v></c>
    </row>
  </sheetData>
 
  <dataValidations count="1">
    <dataValidation type="list"
                    allowBlank="1"
                    showDropDown="0"
                    sqref="A2:A500">
      <formula1>\'CategoryList\'!$A$1:$A$' . $catCount . '</formula1>
    </dataValidation>
  </dataValidations>
 
</worksheet>';
 
        // Category List Sheet
        $sheet2Xml = '<?xml version="1.0" encoding="UTF-8"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <sheetData>' . $catRows . '</sheetData>
</worksheet>';
 
        // sharedStrings
        $sharedStringsXml = '<?xml version="1.0" encoding="UTF-8"?>
<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"
     count="' . count($strIndex) . '" uniqueCount="' . count($strIndex) . '">'
            . $siXml . '</sst>';
 
        // Content Types
        $contentTypes = '<?xml version="1.0" encoding="UTF-8"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/worksheets/sheet2.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>
  <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
</Types>';
 
        // Root relationships
        $rels = '<?xml version="1.0" encoding="UTF-8"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1"
    Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument"
    Target="xl/workbook.xml"/>
</Relationships>';
 
        // Workbook relationships
        $workbookRels = '<?xml version="1.0" encoding="UTF-8"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1"
    Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet"
    Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2"
    Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet"
    Target="worksheets/sheet2.xml"/>
  <Relationship Id="rId3"
    Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings"
    Target="sharedStrings.xml"/>
  <Relationship Id="rId4"
    Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles"
    Target="styles.xml"/>
</Relationships>';
 
        // Workbook
        $workbook = '<?xml version="1.0" encoding="UTF-8"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"
          xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>
    <sheet name="Worksheet" sheetId="1" r:id="rId1"/>
    <sheet name="CategoryList" sheetId="2" r:id="rId2" state="hidden"/>
  </sheets>
</workbook>';
 
        // Styles
        $styles = '<?xml version="1.0" encoding="UTF-8"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <fonts>
    <font><sz val="11"/><name val="Calibri"/></font>
    <font><b/><sz val="11"/><name val="Calibri"/><color rgb="FFFFFFFF"/></font>
  </fonts>
  <fills>
    <fill><patternFill patternType="none"/></fill>
    <fill><patternFill patternType="gray125"/></fill>
    <fill><patternFill patternType="solid"><fgColor rgb="FF4472C4"/></patternFill></fill>
  </fills>
  <borders>
    <border><left/><right/><top/><bottom/><diagonal/></border>
  </borders>
  <cellStyleXfs count="1">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>
  </cellStyleXfs>
  <cellXfs>
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
    <xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0"/>
  </cellXfs>
</styleSheet>';
 
        // Create ZIP
        $zipPath = sys_get_temp_dir() . '/subcategory_sample.xlsx';
        if (file_exists($zipPath)) unlink($zipPath);
 
        $zip = new \ZipArchive();
        $zip->open($zipPath, \ZipArchive::CREATE);
        $zip->addFromString('[Content_Types].xml',        $contentTypes);
        $zip->addFromString('_rels/.rels',                $rels);
        $zip->addFromString('xl/workbook.xml',            $workbook);
        $zip->addFromString('xl/_rels/workbook.xml.rels', $workbookRels);
        $zip->addFromString('xl/worksheets/sheet1.xml',   $sheet1Xml);
        $zip->addFromString('xl/worksheets/sheet2.xml',   $sheet2Xml);
        $zip->addFromString('xl/sharedStrings.xml',       $sharedStringsXml);
        $zip->addFromString('xl/styles.xml',              $styles);
        $zip->close();
 
        return response()->download($zipPath, 'subcategory_sample.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }


 
 
}
