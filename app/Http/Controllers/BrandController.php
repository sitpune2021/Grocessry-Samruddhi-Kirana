<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class BrandController extends Controller
{

    public function index()
    {
        $brands = Brand::orderBy('created_at', 'desc')->paginate(20);
        return view('menus.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('menus.brands.add-brands', [
            'categories'    => Category::select('id', 'name')->orderBy('name')->get(),
            'subCategories' => collect(),
            'mode'          => 'add',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id'      => 'required|exists:categories,id',
            'sub_category_id'  => 'required|exists:sub_categories,id',

            'name'             => 'required|string|max:255',
            'slug'             => 'nullable|string|max:255',
            'description'      => 'nullable|string',
            'logo'             => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            // 'status'           => 'required|boolean',
        ]);

        $validated['status'] = $request->has('status') ? 1 : 0;

        if (empty($validated['slug'])) {
            $slug = Str::slug($validated['name']);
        } else {
            $slug = Str::slug($validated['slug']);
        }

        $originalSlug = $slug;
        $count = 1;

        while (Brand::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        $validated['slug'] = $slug;

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $fileName = time() . '_' . $file->getClientOriginalName();

            $file->storeAs('brands', $fileName, 'public');
            $validated['logo'] = $fileName;
        }

        Brand::create($validated);

        return redirect()
            ->route('brands.index')
            ->with('success', 'Brand created successfully');
    }

    public function show($id)
    {
        $brand = Brand::findOrFail($id);
        $categories = Category::select('id', 'name')
            ->orderBy('name')
            ->get();
        $subCategories = SubCategory::where('category_id', $brand->category_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $mode = 'view';

        return view('menus.brands.add-brands', compact('brand', 'categories', 'subCategories', 'mode'));
    }

    public function edit(Brand $brand)
    {
        return view('menus.brands.add-brands', [
            'categories'    => Category::select('id', 'name')
                ->orderBy('name')
                ->get(),
            'subCategories' => SubCategory::where('category_id', $brand->category_id)
                ->select('id', 'name')
                ->orderBy('name')
                ->get(),
            'brand' => $brand,
            'mode' => 'edit'
        ]);
    }

    public function update(Request $request, Brand $brand)
    {


        $validated = $request->validate([
            'category_id'     => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id',

            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('brands', 'name')
                    ->where('category_id', $request->category_id)
                    ->whereNull('deleted_at')
                    ->ignore($brand->id),
            ],


            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('brands', 'slug')
                    ->ignore($brand->id)
                    ->whereNull('deleted_at'),
            ],

            'description' => 'nullable|string',
            'logo'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'      => 'required|boolean',
        ]);



        if (empty($validated['slug'])) {
            $slug = Str::slug($validated['name']);
        } else {
            $slug = Str::slug($validated['slug']);
        }

        $originalSlug = $slug;
        $count = 1;

        while (
            Brand::where('slug', $slug)
            ->where('id', '!=', $brand->id)
            ->exists()
        ) {
            $slug = $originalSlug . '-' . $count++;
        }

        $validated['slug'] = $slug;

        if ($request->hasFile('logo')) {

            if ($brand->logo && Storage::disk('public')->exists('brands/' . $brand->logo)) {
                Storage::disk('public')->delete('brands/' . $brand->logo);
            }

            $file = $request->file('logo');
            $fileName = time() . '_' . $file->getClientOriginalName();

            $file->storeAs('brands', $fileName, 'public');
            $validated['logo'] = $fileName;
        }
        $brand->update($validated);

        return redirect()
            ->route('brands.index')
            ->with('success', 'Brand updated successfully');
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();

        return redirect()->route('brands.index')
            ->with('success', 'Brand deleted successfully');
    }

    public function updateStatus(Request $request)
    {
        // Find brand by ID or fail
        $brand = Brand::findOrFail($request->id);

        // Toggle status: if 1 -> 0, if 0 -> 1
        $brand->status = $brand->status ? 0 : 1;
        $brand->save();

        // Return JSON if called via AJAX, or back() if standard
        return back();
    }
    public function bulkUpload(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,csv,txt|max:5120',
        ]);

        try {
            Log::info('Brand Bulk Upload Started', [
                'ip'         => $request->ip(),
                'excel_file' => $request->file('excel_file')->getClientOriginalName(),
            ]);

            $file      = $request->file('excel_file');
            $extension = strtolower($file->getClientOriginalExtension());
            $data      = [];

            if ($extension === 'xlsx') {

                $zip = new \ZipArchive();
                $opened = $zip->open($file->getRealPath());
                Log::info("Brand Bulk Upload: ZIP Open", ['status' => $opened, 'num_files' => $zip->numFiles]);

                // ZIP contents list
                $fileList = [];
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $fileList[] = $zip->getNameIndex($i);
                }
                Log::info("Brand Bulk Upload: ZIP Contents", ['files' => $fileList]);

                // SharedStrings
                $sharedStrings = [];
                $ssXml = $zip->getFromName('xl/sharedStrings.xml');
                Log::info("Brand Bulk Upload: SharedStrings", [
                    'found'  => $ssXml !== false,
                    'length' => strlen($ssXml ?: ''),
                ]);

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
                Log::info("Brand Bulk Upload: SharedStrings Parsed", [
                    'count'   => count($sharedStrings),
                    'strings' => array_slice($sharedStrings, 0, 10), // फक्त पहिले 10
                ]);

                // Sheet1
                $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
                Log::info("Brand Bulk Upload: Sheet1", [
                    'found'  => $sheetXml !== false,
                    'length' => strlen($sheetXml ?: ''),
                ]);
                $zip->close();

                if ($sheetXml) {
                    $sheet    = simplexml_load_string($sheetXml);
                    $rowCount = count($sheet->sheetData->row ?? []);
                    Log::info("Brand Bulk Upload: Sheet1 Row Count", ['count' => $rowCount]);

                    foreach ($sheet->sheetData->row as $row) {
                        $rowData = [];
                        foreach ($row->c as $cell) {
                            $type      = (string)$cell['t'];
                            $value     = (string)$cell->v;
                            $rowData[] = ($type === 's') ? ($sharedStrings[(int)$value] ?? '') : $value;
                        }
                        $data[] = $rowData;
                    }

                    Log::info("Brand Bulk Upload: Data Parsed", [
                        'total_rows' => count($data),
                        'first_row'  => $data[0] ?? [],
                        'second_row' => $data[1] ?? [],
                    ]);
                }

                array_shift($data); // header skip
                Log::info("Brand Bulk Upload: After Header Skip", ['count' => count($data)]);
            } else {
                $handle = fopen($file->getRealPath(), 'r');
                fgetcsv($handle);
                while (($row = fgetcsv($handle)) !== false) {
                    $data[] = $row;
                }
                fclose($handle);
                Log::info("Brand Bulk Upload: CSV Parsed", ['count' => count($data)]);
            }

            $successCount = 0;
            $skippedCount = 0;

            foreach ($data as $rowIndex => $row) {

                $categoryName    = trim($row[0] ?? '');
                $subCategoryName = trim($row[1] ?? '');
                $name            = trim($row[2] ?? '');
                $logoUrl         = trim($row[3] ?? '');

                Log::info("Brand Bulk Upload: Processing Row", [
                    'row'               => $rowIndex + 2,
                    'name'              => $name,
                    'category_name'     => $categoryName,
                    'sub_category_name' => $subCategoryName,
                ]);

                if (empty($name)) {
                    Log::warning("Brand Bulk Upload: Skipped — Name Empty", ['row' => $rowIndex + 2]);
                    $skippedCount++;
                    continue;
                }

                $category = Category::whereRaw('LOWER(name) = ?', [strtolower($categoryName)])->first();
                if (!$category) {
                    Log::warning("Brand Bulk Upload: Skipped — Invalid Category", [
                        'row' => $rowIndex + 2,
                        'name' => $name,
                        'category' => $categoryName,
                    ]);
                    $skippedCount++;
                    continue;
                }

                $subCategory = SubCategory::whereRaw('LOWER(name) = ?', [strtolower($subCategoryName)])
                    ->where('category_id', $category->id)->first();
                if (!$subCategory) {
                    Log::warning("Brand Bulk Upload: Skipped — Invalid SubCategory", [
                        'row' => $rowIndex + 2,
                        'name' => $name,
                        'sub_category' => $subCategoryName,
                    ]);
                    $skippedCount++;
                    continue;
                }

                if (Brand::where('name', $name)->where('category_id', $category->id)->exists()) {
                    Log::warning("Brand Bulk Upload: Skipped — Duplicate", ['row' => $rowIndex + 2, 'name' => $name]);
                    $skippedCount++;
                    continue;
                }

                $slug     = Str::slug($name);
                $original = $slug;
                $i = 1;
                while (Brand::where('slug', $slug)->exists()) {
                    $slug = $original . '-' . $i++;
                }

                // Logo save
                $logoName = null;
                if (!empty($logoUrl)) {
                    if (str_starts_with($logoUrl, 'data:image')) {
                        preg_match('/data:image\/(\w+);base64,(.+)/', $logoUrl, $matches);
                        if (count($matches) === 3) {
                            $imageData = base64_decode($matches[2]);
                            if ($imageData !== false) {
                                $logoName = time() . '_' . uniqid() . '.' . $matches[1];
                                Storage::disk('public')->put('brands/' . $logoName, $imageData);
                                Log::info("Logo Saved from Base64", ['file' => $logoName]);
                            }
                        }
                    } elseif (filter_var($logoUrl, FILTER_VALIDATE_URL)) {
                        try {
                            $response = Http::timeout(10)->withHeaders(['User-Agent' => 'Mozilla/5.0'])->get($logoUrl);
                            if ($response->successful()) {
                                $ext      = pathinfo(parse_url($logoUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                                $logoName = time() . '_' . uniqid() . '.' . $ext;
                                Storage::disk('public')->put('brands/' . $logoName, $response->body());
                                Log::info("Logo Saved from URL", ['file' => $logoName]);
                            }
                        } catch (\Exception $e) {
                            Log::warning("Logo Error", ['url' => $logoUrl, 'error' => $e->getMessage()]);
                        }
                    }
                }

                Brand::create([
                    'name'            => $name,
                    'slug'            => $slug,
                    'category_id'     => $category->id,
                    'sub_category_id' => $subCategory->id,
                    'status'          => 1,
                    'logo'            => $logoName,
                ]);

                Log::info("Brand Bulk Upload: Created", ['row' => $rowIndex + 2, 'name' => $name]);
                $successCount++;
            }

            Log::info('Brand Bulk Upload Completed', [
                'success' => $successCount,
                'skipped' => $skippedCount,
            ]);

            return redirect()->route('brands.index')
                ->with('success', "{$successCount} brands imported. {$skippedCount} skipped.");
        } catch (\Exception $e) {
            Log::error('Brand Bulk Upload Error', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return redirect()->back()->with('error', 'Upload failed: ' . $e->getMessage());
        }
    }



public function downloadSampleExcel()
{
    Log::info('Brand Sample Excel Download');

    $categories = Category::with('subCategories')->orderBy('name')->get();
    $catNames   = $categories->pluck('name')->toArray();
    $catCount   = count($catNames);

    // ── Shared Strings ───────────────────────────────────────────────
    $allStrings = ['Category Name', 'Sub Category Name', 'Brand Name', 'Logo URL'];
    foreach ($catNames as $c) { $allStrings[] = $c; }
    foreach ($categories as $cat) {
        foreach ($cat->subCategories->pluck('name') as $sub) {
            $allStrings[] = $sub;
        }
    }

    $strIndex = [];
    foreach ($allStrings as $s) {
        if (!isset($strIndex[$s])) $strIndex[$s] = count($strIndex);
    }

    $siXml = '';
    foreach (array_keys($strIndex) as $s) {
        $escaped = htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        $siXml  .= '<si><t xml:space="preserve">' . $escaped . '</t></si>';
    }
    $totalStrings = count($strIndex);

    // ── Sheet 2: CategoryList ────────────────────────────────────────
    $catRows = '';
    foreach ($catNames as $i => $cat) {
        $r       = $i + 1;
        $idx     = $strIndex[$cat];
        $catRows .= '<row r="' . $r . '"><c r="A' . $r . '" t="s"><v>' . $idx . '</v></c></row>';
    }

    // ── Sheet 3: SubCategoryList (each category = one column) ────────
    $subRows     = '';
    $namedRanges = '';
    $col         = 1;

    foreach ($categories as $category) {
        $subs = $category->subCategories->pluck('name')->toArray();
        if (empty($subs)) continue;

        $colLetter = $col <= 26
            ? chr(64 + $col)
            : chr(64 + intdiv($col - 1, 26)) . chr(64 + (($col - 1) % 26) + 1);

        foreach ($subs as $si => $sub) {
            $r        = $si + 1;
            $idx      = $strIndex[$sub];
            $subRows .= '<row r="' . $r . '"><c r="' . $colLetter . $r . '" t="s"><v>' . $idx . '</v></c></row>';
        }

        $subCount  = count($subs);
        // Named range name = category name with spaces/special chars → underscore
        $rangeName = preg_replace('/[^A-Za-z0-9_]/', '_', $category->name);
        $namedRanges .= '<definedName name="' . htmlspecialchars($rangeName, ENT_XML1) . '">'
            . 'SubCategoryList!$' . $colLetter . '$1:$' . $colLetter . '$' . $subCount
            . '</definedName>';
        $col++;
    }

    // ── Data Validations ─────────────────────────────────────────────
    // KEY FIX 1: Use sqref="A2:A500" (ONE block) instead of 500 individual entries
    // KEY FIX 2: INDIRECT with plain " quotes (valid in XML tag body)
    // KEY FIX 3: count must exactly match number of <dataValidation> elements

    // Category: 1 block covers A2:A500
    $dvCat = '<dataValidation type="list" allowBlank="1" showDropDown="0" sqref="A2:A500">'
        . '<formula1>CategoryList!$A$1:$A$' . $catCount . '</formula1>'
        . '</dataValidation>';

    // SubCategory: 1 block covers B2:B500 with INDIRECT
    // INDIRECT(SUBSTITUTE(A2," ","_")) — Excel evaluates per-row automatically
    $dvSub = '<dataValidation type="list" allowBlank="1" showDropDown="0" sqref="B2:B500">'
        . '<formula1>INDIRECT(SUBSTITUTE(A2," ","_"))</formula1>'
        . '</dataValidation>';

    $dvCount = 2; // exactly 2 dataValidation elements
    $dvXml   = $dvCat . $dvSub;

    // ── Header indexes ───────────────────────────────────────────────
    $h0 = $strIndex['Category Name'];
    $h1 = $strIndex['Sub Category Name'];
    $h2 = $strIndex['Brand Name'];
    $h3 = $strIndex['Logo URL'];

    // ── sheet1.xml ───────────────────────────────────────────────────
    $sheet1Xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"
           xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheetViews>
    <sheetView workbookViewId="0">
      <pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/>
    </sheetView>
  </sheetViews>
  <cols>
    <col min="1" max="1" width="22" customWidth="1"/>
    <col min="2" max="2" width="22" customWidth="1"/>
    <col min="3" max="3" width="22" customWidth="1"/>
    <col min="4" max="4" width="40" customWidth="1"/>
  </cols>
  <sheetData>
    <row r="1">
      <c r="A1" t="s" s="1"><v>' . $h0 . '</v></c>
      <c r="B1" t="s" s="1"><v>' . $h1 . '</v></c>
      <c r="C1" t="s" s="1"><v>' . $h2 . '</v></c>
      <c r="D1" t="s" s="1"><v>' . $h3 . '</v></c>
    </row>
  </sheetData>
  <dataValidations count="' . $dvCount . '">' . $dvXml . '</dataValidations>
</worksheet>';

    // ── sheet2.xml ───────────────────────────────────────────────────
    $sheet2Xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <sheetData>' . $catRows . '</sheetData>
</worksheet>';

    // ── sheet3.xml ───────────────────────────────────────────────────
    $sheet3Xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <sheetData>' . $subRows . '</sheetData>
</worksheet>';

    // ── sharedStrings.xml ────────────────────────────────────────────
    $sharedStringsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"
     count="' . $totalStrings . '" uniqueCount="' . $totalStrings . '">'
        . $siXml . '</sst>';

    // ── workbook.xml ─────────────────────────────────────────────────
    $workbookXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"
          xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <bookViews><workbookView activeTab="0"/></bookViews>
  <sheets>
    <sheet name="Worksheet"       sheetId="1" r:id="rId1"/>
    <sheet name="CategoryList"    sheetId="2" r:id="rId2" state="hidden"/>
    <sheet name="SubCategoryList" sheetId="3" r:id="rId3" state="hidden"/>
  </sheets>
  <definedNames>' . $namedRanges . '</definedNames>
</workbook>';

    // ── [Content_Types].xml ──────────────────────────────────────────
    $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml"  ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml"
    ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml"
    ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/worksheets/sheet2.xml"
    ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/worksheets/sheet3.xml"
    ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/sharedStrings.xml"
    ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>
  <Override PartName="/xl/styles.xml"
    ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
</Types>';

    // ── _rels/.rels ──────────────────────────────────────────────────
    $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1"
    Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument"
    Target="xl/workbook.xml"/>
</Relationships>';

    // ── xl/_rels/workbook.xml.rels ───────────────────────────────────
    $workbookRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1"
    Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet"
    Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2"
    Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet"
    Target="worksheets/sheet2.xml"/>
  <Relationship Id="rId3"
    Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet"
    Target="worksheets/sheet3.xml"/>
  <Relationship Id="rId4"
    Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings"
    Target="sharedStrings.xml"/>
  <Relationship Id="rId5"
    Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles"
    Target="styles.xml"/>
</Relationships>';

    // ── styles.xml ───────────────────────────────────────────────────
    $stylesXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <fonts count="2">
    <font><sz val="11"/><name val="Calibri"/></font>
    <font><b/><sz val="11"/><name val="Calibri"/><color rgb="FFFFFFFF"/></font>
  </fonts>
  <fills count="3">
    <fill><patternFill patternType="none"/></fill>
    <fill><patternFill patternType="gray125"/></fill>
    <fill><patternFill patternType="solid"><fgColor rgb="FF4472C4"/></patternFill></fill>
  </fills>
  <borders count="1">
    <border><left/><right/><top/><bottom/><diagonal/></border>
  </borders>
  <cellStyleXfs count="1">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>
  </cellStyleXfs>
  <cellXfs count="2">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
    <xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1"/>
  </cellXfs>
</styleSheet>';

    // ── Build ZIP ────────────────────────────────────────────────────
    $zipPath = sys_get_temp_dir() . '/brand_sample_' . uniqid() . '.xlsx';

    $zip = new \ZipArchive();
    if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
        return redirect()->back()->with('error', 'Could not create Excel file.');
    }

    $zip->addFromString('[Content_Types].xml',        $contentTypes);
    $zip->addFromString('_rels/.rels',                $rels);
    $zip->addFromString('xl/workbook.xml',            $workbookXml);
    $zip->addFromString('xl/_rels/workbook.xml.rels', $workbookRels);
    $zip->addFromString('xl/worksheets/sheet1.xml',   $sheet1Xml);
    $zip->addFromString('xl/worksheets/sheet2.xml',   $sheet2Xml);
    $zip->addFromString('xl/worksheets/sheet3.xml',   $sheet3Xml);
    $zip->addFromString('xl/sharedStrings.xml',       $sharedStringsXml);
    $zip->addFromString('xl/styles.xml',              $stylesXml);
    $zip->close();

    Log::info('Brand Sample Excel Download Success');

    return response()->download($zipPath, 'brand_sample.xlsx', [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ])->deleteFileAfterSend(true);
}
}
