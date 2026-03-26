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

        $categories = Category::with('subCategories')
            ->orderBy('name')
            ->get();
        return view('menus.brands.index', compact('brands', 'categories'));
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

    public function openCsvModal()
    {
        $categories = Category::with('subCategories')->orderBy('name')->get();
        return view('brand.csv_modal', compact('categories'));
    }

    public function downloadSampleExcel(Request $request)
    {
   
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'required|array',
            'subcategory_id.*' => 'exists:sub_categories,id',
        ]);

        $category = Category::findOrFail($request->category_id);

        // ✅ FIX: get multiple subcategories
        $subcategories = SubCategory::whereIn('id', $request->subcategory_id)->get();

        $fileName = 'brand_sample_' . time() . '.csv';

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
        ];

        $callback = function () use ($category, $subcategories) {

            $file = fopen('php://output', 'w');

            // ✅ Header
            fputcsv($file, ['Category Name', 'Sub Category Name', 'Brand Name']);

            // ✅ Loop each subcategory
            foreach ($subcategories as $subcategory) {

                // Add 5 rows per subcategory (you can change count)
                for ($i = 0; $i < 5; $i++) {
                    fputcsv($file, [
                        $category->name,
                        $subcategory->name,
                        '' // user fills brand
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
