<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;


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
            'status'           => 'required|boolean',
        ]);

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

            'name'            => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'slug'            => 'nullable|string|max:255|unique:brands,slug,' . $brand->id,
            'description'     => 'nullable|string',
            'logo'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'          => 'required|boolean',
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

    
}
