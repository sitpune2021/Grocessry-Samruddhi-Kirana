<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('id', 'desc')->paginate(20);
        return view('menus.category.index', compact('categories'));
    }

    public function create()
    {
        $mode = 'add';
        return view('menus.category.add-category', compact('mode'));
    }

    public function store(Request $request)
    {
        Log::info('Category store request received', [
            'request_data' => $request->all(),
            'ip'           => $request->ip(),
        ]);

        try {
            $validated = $request->validate([
                'name'              => 'required|string|max:255|unique:categories,name',
                'slug'              => 'required|string|max:255',
                'category_images'   => 'required|array',
                'category_images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            if ($request->hasFile('category_images')) {
                $imageNames = [];
                foreach ($request->file('category_images') as $image) {
                    $name = time() . '_' . $image->getClientOriginalName();
                    $image->storeAs('categories', $name, 'public');
                    $imageNames[] = $name;
                }
                $validated['category_images'] = $imageNames;
            }

            $category = Category::create([
                'name'            => $validated['name'],
                'slug'            => $validated['slug'],
                'category_images' => $imageNames,
            ]);

            Log::info('Category created successfully', [
                'category_id'     => $category->id,
                'name'            => $category->name,
                'slug'            => $category->slug,
                'category_images' => $imageNames,
            ]);

            return redirect()
                ->route('menus.category.index')
                ->with('success', 'Category added successfully');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Category validation failed', ['errors' => $e->errors()]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error while creating category', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return redirect()
                ->route('category.index')
                ->with('success', 'Category added successfully');
        }
    }

    public function show(string $id)
    {
        try {
            $mode = 'view';
            Log::info('Category Show Request Received', ['id' => $id]);

            $category = Category::find($id);

            if (!$category) {
                Log::warning("Category Not Found", ['id' => $id]);
                return redirect()->route('category.index')->with('error', 'Category not found');
            }

            Log::info('Category Found', ['category' => $category]);
            return view('menus.category.add-category', compact('category', 'mode'));

        } catch (\Throwable $e) {
            Log::error('Category Show Error', ['error' => $e->getMessage()]);
            return redirect()->route('menus.category.index')->with('error', 'Category not found');
        }
    }

    public function edit($id)
    {
        abort_unless(hasPermission('category.edit'), 403);

        Log::info('Category Edit Request Received', ['id' => $id]);

        $category = Category::findOrFail($id);
        $mode     = 'edit';

        return view('menus.category.add-category', compact('category', 'mode'));
    }

    public function update(Request $request, $id)
    {
        Log::info('Category Update Request Received', [
            'id'      => $id,
            'request' => $request->all(),
        ]);

        try {
            $category = Category::find($id);

            if (!$category) {
                Log::warning('Category Not Found', ['id' => $id]);
                return redirect()->route('category.index')->with('error', 'Category not found');
            }

            $validated = $request->validate([
                'name'              => 'required|string|max:255|unique:categories,name,' . $id,
                'slug'              => 'required|string|max:255',
                'category_images'   => 'nullable|array',
                'category_images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            $imageNames = $category->category_images ?? [];

            if ($request->hasFile('category_images')) {
                $imageNames = [];
                foreach ($request->file('category_images') as $image) {
                    $name = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->storeAs('categories', $name, 'public');
                    $imageNames[] = $name;
                }
            }

            $category->update([
                'name'            => $validated['name'],
                'slug'            => $validated['slug'],
                'category_images' => $imageNames,
            ]);

            Log::info('Category Updated Successfully', [
                'category_id' => $category->id,
                'images'      => $imageNames,
            ]);

            return redirect()->route('category.index')->with('success', 'Category updated successfully');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Category Update Validation Failed', ['errors' => $e->errors()]);
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Category Update Error', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function destroy(string $id)
    {
        Log::info('Delete Category Request', ['id' => $id]);

        $category = Category::where('id', $id)->first();

        if (!$category) {
            Log::warning("Category not found for delete", ['id' => $id]);
            return response()->json(['status' => false, 'message' => 'Category not found'], 404);
        }

        Log::info('Category Found for Delete', ['category' => $category]);
        $category->delete();
        Log::info('Category Soft Deleted Successfully', ['id' => $id, 'deleted_at' => now()->toDateTimeString()]);

        return redirect()->route('category.index')->with('success', 'Category deleted successfully');
    }

    // =============================================
    // BULK UPLOAD
    // =============================================
    public function bulkUpload(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            Log::info('Bulk Upload Started', [
                'ip'         => $request->ip(),
                'excel_file' => $request->file('excel_file')->getClientOriginalName(),
            ]);

            $file      = $request->file('excel_file');
            $extension = strtolower($file->getClientOriginalExtension());
            $rows      = [];

            if ($extension === 'csv') {
                // ── CSV Parse ─────────────────────────────────────────
                $data = array_map('str_getcsv', file($file->getRealPath()));
                array_shift($data); // header skip
                $rows = $data;

            } elseif ($extension === 'xlsx') {
                // ── XLSX Parse (ZipArchive + XML) ─────────────────────
                $zip = new \ZipArchive();
                if ($zip->open($file->getRealPath()) !== true) {
                    return redirect()->back()->with('error', 'Could not open xlsx file.');
                }

                // sharedStrings
                $sharedStrings = [];
                $ssIndex = $zip->locateName('xl/sharedStrings.xml');
                if ($ssIndex !== false) {
                    $ssXml = simplexml_load_string($zip->getFromIndex($ssIndex));
                    foreach ($ssXml->si as $si) {
                        if (isset($si->t)) {
                            $sharedStrings[] = (string) $si->t;
                        } else {
                            $text = '';
                            foreach ($si->r as $r) {
                                $text .= (string) $r->t;
                            }
                            $sharedStrings[] = $text;
                        }
                    }
                }

                // sheet1
                $sheetXml = simplexml_load_string($zip->getFromName('xl/worksheets/sheet1.xml'));
                $zip->close();

                $sheetRows = [];
                foreach ($sheetXml->sheetData->row as $row) {
                    $rowData = [];
                    $maxCol  = 0;

                    foreach ($row->c as $cell) {
                        preg_match('/([A-Z]+)(\d+)/', (string) $cell['r'], $m);
                        $colIndex = 0;
                        foreach (str_split($m[1]) as $char) {
                            $colIndex = $colIndex * 26 + (ord($char) - 64);
                        }
                        $colIndex--;

                        $type  = (string) $cell['t'];
                        $value = (string) $cell->v;

                        if ($type === 's') {
                            $value = $sharedStrings[(int) $value] ?? '';
                        }

                        $rowData[$colIndex] = $value;
                        $maxCol = max($maxCol, $colIndex);
                    }

                    for ($i = 0; $i <= $maxCol; $i++) {
                        if (!isset($rowData[$i])) $rowData[$i] = '';
                    }
                    ksort($rowData);
                    $sheetRows[] = array_values($rowData);
                }

                array_shift($sheetRows); // header skip
                $rows = $sheetRows;

            } else {
                return redirect()->back()->with('error', 'Only CSV and XLSX files are supported.');
            }

            $successCount = 0;
            $skippedCount = 0;

            foreach ($rows as $rowIndex => $row) {
                $name     = trim($row[0] ?? '');
                $slug     = Str::slug($name);
                $imageUrl = trim($row[1] ?? '');

                Log::info("Bulk Upload: Processing Row", [
                    'row'   => $rowIndex + 2,
                    'name'  => $name,
                    'slug'  => $slug,
                    'image' => $imageUrl,
                ]);

                if (empty($name)) {
                    Log::warning("Bulk Upload: Skipped — Name Empty", ['row' => $rowIndex + 2]);
                    $skippedCount++;
                    continue;
                }

                if (Category::where('name', $name)->exists()) {
                    Log::warning("Bulk Upload: Skipped — Duplicate", ['name' => $name]);
                    $skippedCount++;
                    continue;
                }

                // Slug unique बनवा
                $original = $slug;
                $i = 1;
                while (Category::where('slug', $slug)->exists()) {
                    $slug = $original . '-' . $i++;
                }

                // Image URL वरून download करा
                $imageNames = [];
                if (!empty($imageUrl) && filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                    try {
                        $context = stream_context_create([
                            'http' => ['timeout' => 10, 'user_agent' => 'Mozilla/5.0'],
                            'ssl'  => ['verify_peer' => false, 'verify_peer_name' => false],
                        ]);

                        $imageContents = file_get_contents($imageUrl, false, $context);

                        if ($imageContents !== false) {
                            $imageName = time() . '_' . basename(parse_url($imageUrl, PHP_URL_PATH));
                            Storage::disk('public')->put('categories/' . $imageName, $imageContents);
                            $imageNames[] = $imageName;
                            Log::info("Bulk Upload: Image Saved", ['name' => $imageName]);
                        } else {
                            Log::warning("Bulk Upload: Image Download Failed", ['url' => $imageUrl]);
                        }
                    } catch (\Exception $e) {
                        Log::warning("Bulk Upload: Image Error", [
                            'url'   => $imageUrl,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                Category::create([
                    'name'            => $name,
                    'slug'            => $slug,
                    'category_images' => $imageNames,
                ]);

                Log::info("Bulk Upload: Category Created", ['name' => $name, 'slug' => $slug]);
                $successCount++;
            }

            Log::info('Bulk Upload Completed', ['success' => $successCount, 'skipped' => $skippedCount]);

            return redirect()->route('category.index')
                ->with('success', "{$successCount} categories imported. {$skippedCount} skipped.");

        } catch (\Exception $e) {
            Log::error('Bulk Upload Error', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return redirect()->back()->with('error', 'Upload failed: ' . $e->getMessage());
        }
    }

    // =============================================
    // SAMPLE EXCEL DOWNLOAD
    // =============================================
    public function downloadSampleExcel()
    {
        Log::info('Category Sample Excel Download');

        $allStrings = ['name', 'image_url'];
        $strIndex   = [];
        foreach ($allStrings as $s) {
            $strIndex[$s] = count($strIndex);
        }

        $h0 = $strIndex['name'];
        $h1 = $strIndex['image_url'];

        $siXml = '';
        foreach (array_keys($strIndex) as $s) {
            $siXml .= '<si><t>' . htmlspecialchars($s, ENT_XML1) . '</t></si>';
        }

        $sheet1Xml = '<?xml version="1.0" encoding="UTF-8"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <cols>
    <col min="1" max="1" width="25" customWidth="1"/>
    <col min="2" max="2" width="40" customWidth="1"/>
  </cols>
  <sheetData>
    <row r="1">
      <c r="A1" t="s" s="1"><v>' . $h0 . '</v></c>
      <c r="B1" t="s" s="1"><v>' . $h1 . '</v></c>
    </row>
  </sheetData>
</worksheet>';

        $sharedStringsXml = '<?xml version="1.0" encoding="UTF-8"?>
<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"
     count="' . count($strIndex) . '" uniqueCount="' . count($strIndex) . '">'
            . $siXml . '</sst>';

        $workbook = '<?xml version="1.0" encoding="UTF-8"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"
          xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>
    <sheet name="Worksheet" sheetId="1" r:id="rId1"/>
  </sheets>
</workbook>';

        $contentTypes = '<?xml version="1.0" encoding="UTF-8"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>
  <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
</Types>';

        $rels = '<?xml version="1.0" encoding="UTF-8"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1"
    Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument"
    Target="xl/workbook.xml"/>
</Relationships>';

        $workbookRels = '<?xml version="1.0" encoding="UTF-8"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1"
    Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet"
    Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2"
    Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings"
    Target="sharedStrings.xml"/>
  <Relationship Id="rId3"
    Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles"
    Target="styles.xml"/>
</Relationships>';

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

        $zipPath = sys_get_temp_dir() . '/category_sample.xlsx';
        if (file_exists($zipPath)) unlink($zipPath);

        $zip = new \ZipArchive();
        $zip->open($zipPath, \ZipArchive::CREATE);
        $zip->addFromString('[Content_Types].xml',        $contentTypes);
        $zip->addFromString('_rels/.rels',                $rels);
        $zip->addFromString('xl/workbook.xml',            $workbook);
        $zip->addFromString('xl/_rels/workbook.xml.rels', $workbookRels);
        $zip->addFromString('xl/worksheets/sheet1.xml',   $sheet1Xml);
        $zip->addFromString('xl/sharedStrings.xml',       $sharedStringsXml);
        $zip->addFromString('xl/styles.xml',              $styles);
        $zip->close();

        return response()->download($zipPath, 'category_sample.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}