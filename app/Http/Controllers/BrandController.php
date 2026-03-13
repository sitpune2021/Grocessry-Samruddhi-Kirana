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
use App\Exports\BrandSampleExport;
use Maatwebsite\Excel\Facades\Excel;

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
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            Log::info('Brand Bulk Upload Started', [
                'ip'         => $request->ip(),
                'excel_file' => $request->file('excel_file')->getClientOriginalName(),
            ]);

            $file = $request->file('excel_file');
            $data = array_map('str_getcsv', file($file->getRealPath()));
            array_shift($data); // header skip

            $successCount = 0;
            $skippedCount = 0;

            foreach ($data as $rowIndex => $row) {
                $name          = trim($row[0] ?? '');
                $slug          = !empty(trim($row[1] ?? '')) ? Str::slug(trim($row[1])) : Str::slug($name);
                $categoryId    = trim($row[2] ?? '');
                $subCategoryId = trim($row[3] ?? '');
                $status        = isset($row[4]) ? (int) trim($row[4]) : 1;
                $logoUrl       = trim($row[5] ?? '');

                Log::info("Brand Bulk Upload: Processing Row", [
                    'row'          => $rowIndex + 2,
                    'name'         => $name,
                    'category_id'  => $categoryId,
                    'sub_cat_id'   => $subCategoryId,
                    'logo_url'     => $logoUrl,
                ]);

                // Name empty check
                if (empty($name)) {
                    Log::warning("Brand Bulk Upload: Skipped — Name Empty", ['row' => $rowIndex + 2]);
                    $skippedCount++;
                    continue;
                }

                // Category check
                if (empty($categoryId) || !Category::where('id', $categoryId)->exists()) {
                    Log::warning("Brand Bulk Upload: Skipped — Invalid Category", ['name' => $name, 'category_id' => $categoryId]);
                    $skippedCount++;
                    continue;
                }

                // SubCategory check
                if (empty($subCategoryId) || !SubCategory::where('id', $subCategoryId)->where('category_id', $categoryId)->exists()) {
                    Log::warning("Brand Bulk Upload: Skipped — Invalid SubCategory", ['name' => $name, 'sub_category_id' => $subCategoryId]);
                    $skippedCount++;
                    continue;
                }

                // Duplicate name check (same category)
                if (Brand::where('name', $name)->where('category_id', $categoryId)->exists()) {
                    Log::warning("Brand Bulk Upload: Skipped — Duplicate", ['name' => $name]);
                    $skippedCount++;
                    continue;
                }

                // Slug unique बनवा
                $original = $slug;
                $i = 1;
                while (Brand::where('slug', $slug)->exists()) {
                    $slug = $original . '-' . $i++;
                }

                // ✅ Logo URL वरून download करा
                $logoName = null;
                if (!empty($logoUrl) && filter_var($logoUrl, FILTER_VALIDATE_URL)) {
                    try {
                        $context = stream_context_create([
                            'http' => [
                                'timeout'    => 10,
                                'user_agent' => 'Mozilla/5.0',
                            ],
                            'ssl' => [
                                'verify_peer'      => false,
                                'verify_peer_name' => false,
                            ],
                        ]);

                        $imageContents = file_get_contents($logoUrl, false, $context);

                        if ($imageContents !== false) {
                            $logoName = time() . '_' . basename(parse_url($logoUrl, PHP_URL_PATH));
                            Storage::disk('public')->put('brands/' . $logoName, $imageContents);
                            Log::info("Brand Bulk Upload: Logo Saved", ['name' => $logoName, 'brand' => $name]);
                        } else {
                            Log::warning("Brand Bulk Upload: Logo Download Failed", ['url' => $logoUrl]);
                        }
                    } catch (\Exception $e) {
                        Log::warning("Brand Bulk Upload: Logo Error", [
                            'url'   => $logoUrl,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                Brand::create([
                    'name'            => $name,
                    'slug'            => $slug,
                    'category_id'     => $categoryId,
                    'sub_category_id' => $subCategoryId,
                    'status'          => $status,
                    'logo'            => $logoName,
                ]);

                Log::info("Brand Bulk Upload: Brand Created", [
                    'name' => $name,
                    'slug' => $slug,
                    'logo' => $logoName,
                ]);

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
        return Excel::download(new BrandSampleExport, 'brand_sample.xlsx');
    }
}
