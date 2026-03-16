<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\StockMovement;
use App\Models\SubCategory;
use App\Models\Tax;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class ProductController extends Controller
{
    public function index()
    {
        Log::info('Product Index Page Loaded');
        try {
            $products = Product::with(['category', 'tax'])->latest()->paginate(20);
            return view('menus.product.index', compact('products'));
        } catch (\Throwable $e) {
            Log::error('Product Index Error', ['message' => $e->getMessage(), 'line' => $e->getLine()]);
            return redirect()->back()->with('error', 'Unable to load products');
        }
    }

    public function create()
    {
        try {
            Log::info('Product Create Page Loaded');
            $mode          = 'add';
            $categories    = Category::select('id', 'name')->orderBy('name')->get();
            $subCategories = collect();
            $brands        = collect();
            $taxes         = Tax::where('is_active', 1)->get();
            $units         = Unit::orderBy('name')->get();
            return view('menus.product.add-product', compact('mode', 'categories', 'brands', 'subCategories', 'taxes', 'units'));
        } catch (\Throwable $e) {
            Log::error('Product Create Page Error', ['message' => $e->getMessage()]);
            return redirect()->route('product.index')->with('error', 'Unable to load product form');
        }
    }

    public function store(Request $request)
    {
        Log::info('Product Store Request', ['request' => $request->all()]);
        try {
            $validated = $request->validate([
                'category_id'      => 'required|exists:categories,id',
                'brand_id'         => 'required|exists:brands,id',
                'name'             => 'required|string|max:255',
                'sku'              => 'nullable|string|max:255',
                'barcode'          => 'nullable|string|max:20',
                'sub_category_id'  => 'required|exists:sub_categories,id',
                'description'      => 'nullable|string',
                'unit_id'          => 'required|exists:units,id',
                'unit_value'       => 'required|numeric|min:0.01',
                'base_price'       => 'required|numeric|min:1',
                'retailer_price'   => 'required|numeric|gte:base_price',
                'mrp'              => 'required|numeric|min:1',
                'tax_id'           => 'required|exists:taxes,id',
                'product_images'   => 'required|array',
                'product_images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            ], [
                'retailer_price.gte' => 'Selling price cannot be less than Base Price',
            ]);

            if ($request->retailer_price > $request->mrp) {
                return back()->withInput()->with('error', 'Selling price cannot be greater than MRP');
            }

            $tax                         = Tax::findOrFail($request->tax_id);
            $gstPercent                  = $tax->gst ?? 0;
            $gstAmount                   = ($request->retailer_price * $gstPercent) / 100;
            $finalPrice                  = $request->retailer_price + $gstAmount;
            $validated['gst_percentage'] = $gstPercent;
            $validated['gst_amount']     = round($gstAmount, 2);
            $validated['final_price']    = round($finalPrice, 2);

            if ($request->hasFile('product_images')) {
                $imageNames = [];
                foreach ($request->file('product_images') as $image) {
                    $name = time() . '_' . $image->getClientOriginalName();
                    $image->storeAs('products', $name, 'public');
                    $imageNames[] = $name;
                }
                $validated['product_images'] = $imageNames;
            }

            $product = Product::create($validated);
            Log::info('Product Created Successfully', ['product_id' => $product->id]);
            return redirect()->route('product.index')->with('success', 'Product created successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Product Store Validation Failed', ['errors' => $e->errors()]);
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Product Store Error', ['message' => $e->getMessage(), 'line' => $e->getLine()]);
            return back()->withInput()->with('error', 'Something went wrong while saving product');
        }
    }

    public function show($id)
    {
        try {
            Log::info('Product View Request', ['id' => $id]);
            $product       = Product::with('tax')->findOrFail($id);
            $mode          = 'view';
            $categories    = Category::select('id', 'name')->get();
            $brands        = Brand::where('status', 1)->orderBy('name')->get();
            $subCategories = SubCategory::where('category_id', $product->category_id)->get();
            $units         = Unit::orderBy('name')->get();
            return view('menus.product.add-product', compact('product', 'mode', 'categories', 'brands', 'subCategories', 'units'));
        } catch (\Throwable $e) {
            Log::error('Product View Error', ['message' => $e->getMessage()]);
            return redirect()->route('product.index')->with('error', 'Unable to view product');
        }
    }

    public function edit($id)
    {
        try {
            Log::info('Product Edit Request', ['id' => $id]);
            $product = Product::find($id);
            if (!$product) {
                return redirect()->route('product.index')->with('error', 'Product not found');
            }
            $mode          = 'edit';
            $categories    = Category::select('id', 'name')->get();
            $brands        = Brand::where('sub_category_id', $product->sub_category_id)->select('id', 'name')->orderBy('name')->get();
            $taxes         = Tax::where('is_active', 1)->get();
            $subCategories = SubCategory::where('category_id', $product->category_id)->get();
            $units         = Unit::orderBy('name')->get();
            return view('menus.product.add-product', compact('product', 'mode', 'categories', 'brands', 'subCategories', 'taxes', 'units'));
        } catch (\Throwable $e) {
            Log::error('Product Edit Error', ['message' => $e->getMessage()]);
            return redirect()->route('product.index')->with('error', 'Unable to edit product');
        }
    }

    public function update(Request $request, $id)
    {
        Log::info('Product Update Request', ['id' => $id]);
        try {
            $product = Product::find($id);
            if (!$product) {
                return redirect()->route('product.index')->with('error', 'Product not found');
            }

            $validated = $request->validate([
                'category_id'      => 'required|exists:categories,id',
                'brand_id'         => 'required|exists:brands,id',
                'sub_category_id'  => 'required|exists:sub_categories,id',
                'name'             => ['required', 'string', 'max:255', Rule::unique('products', 'name')->ignore($product->id)],
                'sku'              => 'nullable|string|max:255',
                'barcode'          => 'nullable|string|max:12',
                'description'      => 'nullable|string',
                'unit_id'          => 'required|exists:units,id',
                'unit_value'       => 'required|numeric|min:0.01',
                'base_price'       => 'required|numeric|min:1',
                'retailer_price'   => 'required|numeric|min:1',
                'mrp'              => 'required|numeric|min:1',
                'tax_id'           => 'required|exists:taxes,id',
                'product_images'   => 'nullable|array',
                'product_images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            ], ['name.unique' => 'This product name already exists!']);

            if ($request->retailer_price < $request->base_price) {
                return back()->withInput()->with('error', 'Selling price cannot be less than Base Price');
            }
            if ($request->retailer_price > $request->mrp) {
                return back()->withInput()->with('error', 'Selling price cannot be greater than MRP');
            }

            $tax                         = Tax::findOrFail($request->tax_id);
            $gstPercent                  = $tax->gst ?? 0;
            $gstAmount                   = ($request->retailer_price * $gstPercent) / 100;
            $finalPrice                  = $request->retailer_price + $gstAmount;
            $validated['gst_percentage'] = $gstPercent;
            $validated['gst_amount']     = round($gstAmount, 2);
            $validated['final_price']    = round($finalPrice, 2);

            if ($request->hasFile('product_images')) {
                $imageNames = [];
                foreach ($request->file('product_images') as $image) {
                    $fileName = time() . '_' . uniqid() . '_' . $image->getClientOriginalName();
                    $image->storeAs('products', $fileName, 'public');
                    $imageNames[] = $fileName;
                }
                $validated['product_images'] = $imageNames;
            }

            $product->update($validated);
            Log::info('Product Updated Successfully', ['product_id' => $product->id]);
            return redirect()->route('product.index')->with('success', 'Product updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Product Update Validation Failed', ['errors' => $e->errors()]);
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Product Update Error', ['message' => $e->getMessage(), 'line' => $e->getLine()]);
            return redirect()->back()->withInput()->with('error', 'Something went wrong while updating product');
        }
    }

    public function destroy($id)
    {
        Log::info('Product Delete Request', ['product_id' => $id, 'user_id' => Auth::id()]);
        try {
            $product = Product::find($id);
            if (!$product) {
                return redirect()->route('product.index')->with('error', 'Product not found');
            }
            $product->delete();
            Log::info('Product Deleted Successfully', ['product_id' => $id]);
            return redirect()->route('product.index')->with('success', 'Product deleted successfully');
        } catch (\Throwable $e) {
            Log::error('Product Delete Error', ['product_id' => $id, 'message' => $e->getMessage()]);
            return redirect()->route('product.index')->with('error', 'Something went wrong while deleting product');
        }
    }

    public function getCategories()
    {
        return response()->json(Category::select('id', 'name')->orderBy('name')->get());
    }

    public function getBrands($subCategoryId)
    {
        Log::info('Loading brands for sub-category', ['sub_category_id' => $subCategoryId]);
        return Brand::where('sub_category_id', $subCategoryId)->select('id', 'name')->orderBy('name')->get();
    }

    public function downloadSampleExcel()
    {
        Log::info('Product Sample Excel Download');

        $categories = Category::with(['subCategories.brands' => function ($q) {
            $q->where('status', 1);
        }])->orderBy('name')->get();

        $units = Unit::orderBy('name')->get();
        $taxes = Tax::where('is_active', 1)->get();

        // ── Build all shared strings ───────────────────────────────────
        $allStrings = [
            'Category Name',
            'Sub Category Name',
            'Brand Name',
            'Product Name',
            'Barcode',
            'Description',
            'Unit',
            'Unit Value',
            'Base Price',
            'Selling Price',
            'MRP',
            'GST',
            'Image URL'
        ];

        foreach ($categories as $cat) {
            $allStrings[] = $cat->name;
            foreach ($cat->subCategories as $sub) {
                $allStrings[] = $sub->name;
                foreach ($sub->brands as $brand) {
                    $allStrings[] = $brand->name;
                }
            }
        }
        foreach ($units as $unit) {
            $allStrings[] = $unit->name . ' (' . strtoupper($unit->short_name) . ')';
        }
        foreach ($taxes as $tax) {
            $allStrings[] = $tax->name . ' (' . $tax->gst . '%)';
        }

        // Build unique string index
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

        // ── Sheet 2: CategoryList (Column A) ──────────────────────────
        $catNames = $categories->pluck('name')->toArray();
        $catCount = count($catNames);
        $catRows  = '';
        foreach ($catNames as $i => $cat) {
            $r       = $i + 1;
            $idx     = $strIndex[$cat];
            $catRows .= "<row r=\"$r\"><c r=\"A$r\" t=\"s\"><v>$idx</v></c></row>";
        }

        // ── Sheet 3: SubCategoryList — each category in its own column ─
        // Named range per category: e.g. "Electronics" → SubCategoryList!$A$1:$A$3
        $subRows       = '';
        $subNamedRanges = '';
        $colNum        = 1;
        $subColMap     = []; // category_name => [colLetter, count]

        foreach ($categories as $cat) {
            $subs = $cat->subCategories->pluck('name')->toArray();
            if (empty($subs)) continue;

            $colLetter = $colNum <= 26
                ? chr(64 + $colNum)
                : chr(64 + intdiv($colNum - 1, 26)) . chr(64 + (($colNum - 1) % 26) + 1);

            foreach ($subs as $si => $sub) {
                $r       = $si + 1;
                $idx     = $strIndex[$sub];
                $subRows .= "<row r=\"$r\"><c r=\"{$colLetter}{$r}\" t=\"s\"><v>$idx</v></c></row>";
            }

            // Named range must match SUBSTITUTE(A2," ","_")
            // e.g. "Baby Care" → named range "Baby_Care"
            $rangeName = preg_replace('/[^A-Za-z0-9]/', '_', $cat->name);
            $subNamedRanges .= '<definedName name="' . htmlspecialchars($rangeName, ENT_XML1) . '">'
                . '\'SubCategoryList\'!$' . $colLetter . '$1:$' . $colLetter . '$' . count($subs)
                . '</definedName>';

            $subColMap[$cat->name] = [$colLetter, count($subs)];
            $colNum++;
        }

        // ── Sheet 4: BrandList — each sub-category in its own column ──
        // Named range per sub-category: "brand_SubName" → BrandList!$A$1:$A$3
        $brandRows        = '';
        $brandNamedRanges = '';
        $bColNum          = 1;

        foreach ($categories as $cat) {
            foreach ($cat->subCategories as $sub) {
                $brands = $sub->brands->pluck('name')->toArray();
                if (empty($brands)) continue;

                $bColLetter = $bColNum <= 26
                    ? chr(64 + $bColNum)
                    : chr(64 + intdiv($bColNum - 1, 26)) . chr(64 + (($bColNum - 1) % 26) + 1);

                foreach ($brands as $bi => $brand) {
                    $r          = $bi + 1;
                    $idx        = $strIndex[$brand] ?? null;
                    if ($idx === null) continue;
                    $brandRows .= "<row r=\"$r\"><c r=\"{$bColLetter}{$r}\" t=\"s\"><v>$idx</v></c></row>";
                }

                // Named range: "brand_SubName" (spaces → _)
                $bRangeName = 'brand_' . preg_replace('/[^A-Za-z0-9]/', '_', $sub->name);
                $brandNamedRanges .= '<definedName name="' . htmlspecialchars($bRangeName, ENT_XML1) . '">'
                    . '\'BrandList\'!$' . $bColLetter . '$1:$' . $bColLetter . '$' . count($brands)
                    . '</definedName>';

                $bColNum++;
            }
        }

        // ── Sheet 5: UnitList ─────────────────────────────────────────
        $unitRows  = '';
        $unitCount = $units->count();
        foreach ($units as $ui => $unit) {
            $r         = $ui + 1;
            $label     = $unit->name . ' (' . strtoupper($unit->short_name) . ')';
            $idx       = $strIndex[$label];
            $unitRows .= "<row r=\"$r\"><c r=\"A$r\" t=\"s\"><v>$idx</v></c></row>";
        }

        // ── Sheet 6: GSTList ─────────────────────────────────────────
        $gstRows  = '';
        $gstCount = $taxes->count();
        foreach ($taxes as $gi => $tax) {
            $r        = $gi + 1;
            $label    = $tax->name . ' (' . $tax->gst . '%)';
            $idx      = $strIndex[$label];
            $gstRows .= "<row r=\"$r\"><c r=\"A$r\" t=\"s\"><v>$idx</v></c></row>";
        }

        // ── Header Row (Sheet 1) ──────────────────────────────────────
        $headers = [
            'Category Name',
            'Sub Category Name',
            'Brand Name',
            'Product Name',
            'Barcode',
            'Description',
            'Unit',
            'Unit Value',
            'Base Price',
            'Selling Price',
            'MRP',
            'GST',
            'Image URL'
        ];
        $cols      = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M'];
        $headerRow = '';
        foreach ($headers as $hi => $h) {
            $idx        = $strIndex[$h];
            $c          = $cols[$hi];
            $headerRow .= "<c r=\"{$c}1\" t=\"s\" s=\"1\"><v>$idx</v></c>";
        }

        // ── Data Validations ─────────────────────────────────────────
        // A: Category list (simple range)
        // B: SubCategory via INDIRECT(SUBSTITUTE(A2," ","_")) per row
        // C: Brand via INDIRECT("brand_"&SUBSTITUTE(B2," ","_")) per row
        // G: Unit list, L: GST list
        $dvXml =
            '<dataValidation type="list" allowBlank="1" showDropDown="0" sqref="A2:A500">'
            . '<formula1>\'CategoryList\'!$A$1:$A$' . $catCount . '</formula1></dataValidation>'
            . '<dataValidation type="list" allowBlank="1" showDropDown="0" sqref="G2:G500">'
            . '<formula1>\'UnitList\'!$A$1:$A$' . $unitCount . '</formula1></dataValidation>'
            . '<dataValidation type="list" allowBlank="1" showDropDown="0" sqref="L2:L500">'
            . '<formula1>\'GSTList\'!$A$1:$A$' . $gstCount . '</formula1></dataValidation>';

        // Per-row INDIRECT for B and C (rows 2–100)
        for ($row = 2; $row <= 100; $row++) {
            $dvXml .= '<dataValidation type="list" allowBlank="1" showDropDown="0" sqref="B' . $row . '">'
                . '<formula1>INDIRECT(SUBSTITUTE(A' . $row . '," ","_"))</formula1></dataValidation>';
            $dvXml .= '<dataValidation type="list" allowBlank="1" showDropDown="0" sqref="C' . $row . '">'
                . '<formula1>INDIRECT("brand_"&amp;SUBSTITUTE(B' . $row . '," ","_"))</formula1></dataValidation>';
        }
        $dvCount = 3 + (99 * 2);

        // ── Build XMLs ────────────────────────────────────────────────
        $mkSheet = function ($rows) {
            return '<?xml version="1.0" encoding="UTF-8"?>'
                . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
                . '<sheetData>' . $rows . '</sheetData>'
                . '</worksheet>';
        };

        $sheet1Xml = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<cols>'
            . '<col min="1" max="1" width="20" customWidth="1"/>'
            . '<col min="2" max="2" width="20" customWidth="1"/>'
            . '<col min="3" max="3" width="20" customWidth="1"/>'
            . '<col min="4" max="4" width="25" customWidth="1"/>'
            . '<col min="5" max="5" width="15" customWidth="1"/>'
            . '<col min="6" max="6" width="30" customWidth="1"/>'
            . '<col min="7" max="7" width="15" customWidth="1"/>'
            . '<col min="8" max="8" width="12" customWidth="1"/>'
            . '<col min="9" max="9" width="12" customWidth="1"/>'
            . '<col min="10" max="10" width="12" customWidth="1"/>'
            . '<col min="11" max="11" width="12" customWidth="1"/>'
            . '<col min="12" max="12" width="15" customWidth="1"/>'
            . '<col min="13" max="13" width="40" customWidth="1"/>'
            . '</cols>'
            . '<sheetData><row r="1">' . $headerRow . '</row></sheetData>'
            . '<dataValidations count="' . $dvCount . '">' . $dvXml . '</dataValidations>'
            . '</worksheet>';

        $sharedStringsXml = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"'
            . ' count="' . count($strIndex) . '" uniqueCount="' . count($strIndex) . '">'
            . $siXml . '</sst>';

        // Named ranges from BOTH sub-category and brand sheets
        $allNamedRanges = $subNamedRanges . $brandNamedRanges;

        $workbook = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"'
            . ' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets>'
            . '<sheet name="Worksheet" sheetId="1" r:id="rId1"/>'
            . '<sheet name="CategoryList" sheetId="2" r:id="rId2" state="hidden"/>'
            . '<sheet name="SubCategoryList" sheetId="3" r:id="rId3" state="hidden"/>'
            . '<sheet name="BrandList" sheetId="4" r:id="rId4" state="hidden"/>'
            . '<sheet name="UnitList" sheetId="5" r:id="rId5" state="hidden"/>'
            . '<sheet name="GSTList" sheetId="6" r:id="rId6" state="hidden"/>'
            . '</sheets>'
            . '<definedNames>' . $allNamedRanges . '</definedNames>'
            . '</workbook>';

        $contentTypes = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet2.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet3.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet4.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet5.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet6.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '</Types>';

        $rels = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';

        $workbookRels = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet2.xml"/>'
            . '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet3.xml"/>'
            . '<Relationship Id="rId4" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet4.xml"/>'
            . '<Relationship Id="rId5" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet5.xml"/>'
            . '<Relationship Id="rId6" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet6.xml"/>'
            . '<Relationship Id="rId7" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>'
            . '<Relationship Id="rId8" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>';

        $styles = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts><font><sz val="11"/><name val="Calibri"/></font>'
            . '<font><b/><sz val="11"/><name val="Calibri"/><color rgb="FFFFFFFF"/></font></fonts>'
            . '<fills><fill><patternFill patternType="none"/></fill>'
            . '<fill><patternFill patternType="gray125"/></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FF4472C4"/></patternFill></fill></fills>'
            . '<borders><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs>'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            . '<xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0"/>'
            . '</cellXfs>'
            . '</styleSheet>';

        // ── Build ZIP ─────────────────────────────────────────────────
        $zipPath = sys_get_temp_dir() . '/product_sample.xlsx';
        if (file_exists($zipPath)) unlink($zipPath);

        $zip = new \ZipArchive();
        $zip->open($zipPath, \ZipArchive::CREATE);
        $zip->addFromString('[Content_Types].xml',        $contentTypes);
        $zip->addFromString('_rels/.rels',                $rels);
        $zip->addFromString('xl/workbook.xml',            $workbook);
        $zip->addFromString('xl/_rels/workbook.xml.rels', $workbookRels);
        $zip->addFromString('xl/worksheets/sheet1.xml',   $sheet1Xml);
        $zip->addFromString('xl/worksheets/sheet2.xml',   $mkSheet($catRows));
        $zip->addFromString('xl/worksheets/sheet3.xml',   $mkSheet($subRows));
        $zip->addFromString('xl/worksheets/sheet4.xml',   $mkSheet($brandRows));
        $zip->addFromString('xl/worksheets/sheet5.xml',   $mkSheet($unitRows));
        $zip->addFromString('xl/worksheets/sheet6.xml',   $mkSheet($gstRows));
        $zip->addFromString('xl/sharedStrings.xml',       $sharedStringsXml);
        $zip->addFromString('xl/styles.xml',              $styles);
        $zip->close();

        return response()->download($zipPath, 'product_sample.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
    public function bulkUpload(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,csv,txt|max:5120',
        ]);

        try {
            Log::info('Product Bulk Upload Started', [
                'ip'      => $request->ip(),
                'file'    => $request->file('excel_file')->getClientOriginalName(),
                'user_id' => auth()->id(),
            ]);

            $file      = $request->file('excel_file');
            $extension = strtolower($file->getClientOriginalExtension());
            $data      = [];

            if ($extension === 'xlsx') {
                $zip = new \ZipArchive();
                $zip->open($file->getRealPath());

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

                $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
                $zip->close();

                if ($sheetXml) {
                    $sheet = simplexml_load_string($sheetXml);
                    foreach ($sheet->sheetData->row as $row) {
                        $rowData = [];
                        foreach ($row->c as $cell) {
                            $type      = (string)$cell['t'];
                            $value     = (string)$cell->v;
                            $rowData[] = ($type === 's') ? ($sharedStrings[(int)$value] ?? '') : $value;
                        }
                        $data[] = $rowData;
                    }
                }
                array_shift($data);
            } else {
                $handle = fopen($file->getRealPath(), 'r');
                fgetcsv($handle);
                while (($row = fgetcsv($handle)) !== false) {
                    $data[] = $row;
                }
                fclose($handle);
            }
            Log::info('Product Bulk Upload: Total Rows', ['count' => count($data)]);

            $successCount = 0;
            $skippedCount = 0;

            foreach ($data as $rowIndex => $row) {
                $categoryName    = trim($row[0] ?? '');
                $subCategoryName = trim($row[1] ?? '');
                $brandName       = trim($row[2] ?? '');
                $productName     = trim($row[3] ?? '');
                $barcode         = trim($row[4] ?? '');
                $description     = trim($row[5] ?? '');
                $unitLabel       = trim($row[6] ?? '');
                $unitValue       = trim($row[7] ?? '');
                $basePrice       = trim($row[8] ?? '');
                $sellingPrice    = trim($row[9] ?? '');
                $mrp             = trim($row[10] ?? '');
                $gstLabel        = trim($row[11] ?? '');
                $imageUrl        = trim($row[12] ?? '');
                $rowNum          = $rowIndex + 2;

                Log::info("Product Bulk Upload: Processing Row $rowNum", [
                    'product_name' => $productName,
                    'category' => $categoryName,
                    'sub_category' => $subCategoryName,
                    'brand' => $brandName,
                ]);

                if (empty($productName) || empty($categoryName) || empty($subCategoryName) || empty($brandName)) {
                    Log::warning("Skipped — Required Fields Empty", ['row' => $rowNum]);
                    $skippedCount++;
                    continue;
                }

                $category = Category::whereRaw('LOWER(name) = ?', [strtolower($categoryName)])->first();
                if (!$category) {
                    Log::warning("Skipped — Category Not Found", ['row' => $rowNum, 'category' => $categoryName]);
                    $skippedCount++;
                    continue;
                }

                $subCategory = SubCategory::whereRaw('LOWER(name) = ?', [strtolower($subCategoryName)])->where('category_id', $category->id)->first();
                if (!$subCategory) {
                    Log::warning("Skipped — SubCategory Not Found", ['row' => $rowNum]);
                    $skippedCount++;
                    continue;
                }

                $brand = Brand::whereRaw('LOWER(name) = ?', [strtolower($brandName)])->where('sub_category_id', $subCategory->id)->first();
                if (!$brand) {
                    Log::warning("Skipped — Brand Not Found", ['row' => $rowNum, 'brand' => $brandName]);
                    $skippedCount++;
                    continue;
                }

                preg_match('/^(.+?)\s*\(([^)]+)\)$/', $unitLabel, $unitMatch);
                $unitName = trim($unitMatch[1] ?? $unitLabel);
                $unit = Unit::whereRaw('LOWER(name) = ?', [strtolower($unitName)])->first();
                if (!$unit) {
                    Log::warning("Skipped — Unit Not Found", ['row' => $rowNum, 'unit' => $unitLabel]);
                    $skippedCount++;
                    continue;
                }

                preg_match('/^(.+?)\s*\(([0-9.]+)%\)$/', $gstLabel, $gstMatch);
                $gstName = trim($gstMatch[1] ?? $gstLabel);
                $tax = Tax::whereRaw('LOWER(name) = ?', [strtolower($gstName)])->first();
                if (!$tax) {
                    Log::warning("Skipped — GST Not Found", ['row' => $rowNum, 'gst' => $gstLabel]);
                    $skippedCount++;
                    continue;
                }

                if (!is_numeric($basePrice) || !is_numeric($sellingPrice) || !is_numeric($mrp)) {
                    Log::warning("Skipped — Invalid Price", ['row' => $rowNum]);
                    $skippedCount++;
                    continue;
                }
                if ($sellingPrice < $basePrice || $sellingPrice > $mrp) {
                    Log::warning("Skipped — Price Range Invalid", ['row' => $rowNum]);
                    $skippedCount++;
                    continue;
                }

                if (Product::whereRaw('LOWER(name) = ?', [strtolower($productName)])->exists()) {
                    Log::warning("Skipped — Duplicate", ['row' => $rowNum, 'name' => $productName]);
                    $skippedCount++;
                    continue;
                }

                $gstPercent = $tax->gst ?? 0;
                $gstAmount  = ($sellingPrice * $gstPercent) / 100;
                $finalPrice = $sellingPrice + $gstAmount;
                $sku        = strtoupper(Str::slug($productName, '')) . rand(1000, 9999);
                $imageNames = [];
                if (!empty($imageUrl)) {
                    $savedImage = $this->downloadImage($imageUrl, 'products');
                    if ($savedImage) $imageNames[] = $savedImage;
                }

                Product::create([
                    'category_id'     => $category->id,
                    'sub_category_id' => $subCategory->id,
                    'brand_id'        => $brand->id,
                    'name'            => $productName,
                    'sku'             => $sku,
                    'barcode'         => $barcode ?: null,
                    'description'     => $description ?: null,
                    'unit_id'         => $unit->id,
                    'unit_value'      => $unitValue,
                    'base_price'      => $basePrice,
                    'retailer_price'  => $sellingPrice,
                    'mrp'             => $mrp,
                    'tax_id'          => $tax->id,
                    'gst_percentage'  => $gstPercent,
                    'gst_amount'      => round($gstAmount, 2),
                    'final_price'     => round($finalPrice, 2),
                    'product_images'  => !empty($imageNames) ? $imageNames : null,
                ]);

                Log::info("Product Created", ['row' => $rowNum, 'name' => $productName]);
                $successCount++;
            }

            Log::info('Product Bulk Upload Completed', ['success' => $successCount, 'skipped' => $skippedCount]);

            return redirect()->route('product.index')
                ->with('success', "{$successCount} products imported. {$skippedCount} skipped.");
        } catch (\Exception $e) {
            Log::error('Product Bulk Upload Error', ['message' => $e->getMessage(), 'line' => $e->getLine()]);
            return redirect()->back()->with('error', 'Upload failed: ' . $e->getMessage());
        }
    }

    private function downloadImage(string $url, string $folder): ?string
    {
        if (str_starts_with($url, 'data:image')) {
            preg_match('/data:image\/(\w+);base64,(.+)/', $url, $matches);
            if (count($matches) === 3) {
                $imageData = base64_decode($matches[2]);
                if ($imageData !== false) {
                    $fileName = time() . '_' . uniqid() . '.' . $matches[1];
                    Storage::disk('public')->put($folder . '/' . $fileName, $imageData);
                    return $fileName;
                }
            }
        }

        if (filter_var($url, FILTER_VALIDATE_URL)) {
            try {
                $response = Http::timeout(10)->withHeaders(['User-Agent' => 'Mozilla/5.0'])->get($url);
                if ($response->successful()) {
                    $ext      = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                    $fileName = time() . '_' . uniqid() . '.' . $ext;
                    Storage::disk('public')->put($folder . '/' . $fileName, $response->body());
                    return $fileName;
                }
            } catch (\Exception $e) {
                Log::warning("Image download failed", ['url' => $url, 'error' => $e->getMessage()]);
            }
        }
        $siXml = '';
        foreach (array_keys($strIndex) as $s) {
            $siXml .= '<si><t>' . htmlspecialchars($s, ENT_XML1) . '</t></si>';
        }

        // ── Category Sheet ────────────────────────────────────────────
        $catRows = '';
        foreach ($catNames as $i => $cat) {
            $r        = $i + 1;
            $idx      = $strIndex[$cat];
            $catRows .= "<row r=\"$r\"><c r=\"A$r\" t=\"s\"><v>$idx</v></c></row>";
        }

        // ── SubCategory Sheet + Named Ranges ──────────────────────────
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
                $subRows .= "<row r=\"$r\"><c r=\"{$colLetter}{$r}\" t=\"s\"><v>$idx</v></c></row>";
            }
            $rangeName   = preg_replace('/[^A-Za-z0-9_]/', '_', $category->name);
            $namedRanges .= '<definedName name="' . htmlspecialchars($rangeName, ENT_XML1) . '">'
                . 'SubCategoryList!$' . $colLetter . '$1:$' . $colLetter . '$' . count($subs)
                . '</definedName>';
            $col++;
        }

        // ── Brand Sheet + Named Ranges ────────────────────────────────
        $brandRows       = '';
        $brandNamedRanges = '';
        $bCol            = 1;

        $subCategories = SubCategory::with(['brands' => function ($q) {
            $q->where('status', 1);
        }])->orderBy('name')->get();

        foreach ($subCategories as $subCat) {
            $brands = $subCat->brands->pluck('name')->toArray();
            if (empty($brands)) continue;

            $bColLetter = $bCol <= 26
                ? chr(64 + $bCol)
                : chr(64 + intdiv($bCol - 1, 26)) . chr(64 + (($bCol - 1) % 26) + 1);

            foreach ($brands as $bi => $brand) {
                $r          = $bi + 1;
                $idx        = $strIndex[$brand] ?? null;
                if ($idx === null) continue;
                $brandRows .= "<row r=\"$r\"><c r=\"{$bColLetter}{$r}\" t=\"s\"><v>$idx</v></c></row>";
            }
            $bRangeName       = 'brand_' . preg_replace('/[^A-Za-z0-9_]/', '_', $subCat->name);
            $brandNamedRanges .= '<definedName name="' . htmlspecialchars($bRangeName, ENT_XML1) . '">'
                . 'BrandList!$' . $bColLetter . '$1:$' . $bColLetter . '$' . count($brands)
                . '</definedName>';
            $bCol++;
        }

        // ── Unit Sheet ────────────────────────────────────────────────
        $unitRows  = '';
        $unitCount = $units->count();
        foreach ($units as $ui => $unit) {
            $r         = $ui + 1;
            $label     = $unit->name . ' (' . strtoupper($unit->short_name) . ')';
            $idx       = $strIndex[$label];
            $unitRows .= "<row r=\"$r\"><c r=\"A$r\" t=\"s\"><v>$idx</v></c></row>";
        }

        // ── GST Sheet ─────────────────────────────────────────────────
        $gstRows  = '';
        $gstCount = $taxes->count();
        foreach ($taxes as $gi => $tax) {
            $r        = $gi + 1;
            $label    = $tax->name . ' (' . $tax->gst . '%)';
            $idx      = $strIndex[$label];
            $gstRows .= "<row r=\"$r\"><c r=\"A$r\" t=\"s\"><v>$idx</v></c></row>";
        }

        // ── Dropdown Validations ──────────────────────────────────────
        // ── Dropdown Validations — Range based + INDIRECT per row ────
        // A, G, L — range based (एकच validation पुरे)
        $dvXml =
            '<dataValidation type="list" allowBlank="1" showDropDown="0" sqref="A2:A500">'
            . '<formula1>\'CategoryList\'!$A$1:$A$' . $catCount . '</formula1></dataValidation>'

            . '<dataValidation type="list" allowBlank="1" showDropDown="0" sqref="G2:G500">'
            . '<formula1>\'UnitList\'!$A$1:$A$' . $unitCount . '</formula1></dataValidation>'

            . '<dataValidation type="list" allowBlank="1" showDropDown="0" sqref="L2:L500">'
            . '<formula1>\'GSTList\'!$A$1:$A$' . $gstCount . '</formula1></dataValidation>';

        // B, C — INDIRECT साठी per-row लागतं — फक्त 100 rows
        $dvIndirect = '';
        for ($row = 2; $row <= 100; $row++) {
            $dvIndirect .= '<dataValidation type="list" allowBlank="1" showDropDown="0" sqref="B' . $row . '">'
                . '<formula1>INDIRECT(SUBSTITUTE(A' . $row . '," ","_"))</formula1></dataValidation>';
            $dvIndirect .= '<dataValidation type="list" allowBlank="1" showDropDown="0" sqref="C' . $row . '">'
                . '<formula1>INDIRECT("brand_"&SUBSTITUTE(B' . $row . '," ","_"))</formula1></dataValidation>';
        }
        $dvXml  .= $dvIndirect;
        $dvCount = 3 + (99 * 2); // 3 range + 198 per-row = 201
        // Headers index
        $headers = [
            'Category Name',
            'Sub Category Name',
            'Brand Name',
            'Product Name',
            'Barcode',
            'Description',
            'Unit',
            'Unit Value',
            'Base Price',
            'Selling Price',
            'MRP',
            'GST',
            'Image URL'
        ];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M'];
        $headerRow = '';
        foreach ($headers as $hi => $h) {
            $idx        = $strIndex[$h];
            $c          = $cols[$hi];
            $headerRow .= "<c r=\"{$c}1\" t=\"s\" s=\"1\"><v>$idx</v></c>";
        }

        // ── sheet1.xml ────────────────────────────────────────────────
        $sheet1Xml = '<?xml version="1.0" encoding="UTF-8"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
 
  <dimension ref="A1:M1"/>
 
  <sheetViews>
    <sheetView workbookViewId="0">
      <selection activeCell="A2" sqref="A2"/>
    </sheetView>
  </sheetViews>
 
  <cols>
    <col min="1" max="1" width="20" customWidth="1"/>
    <col min="2" max="2" width="20" customWidth="1"/>
    <col min="3" max="3" width="20" customWidth="1"/>
    <col min="4" max="4" width="25" customWidth="1"/>
    <col min="5" max="5" width="15" customWidth="1"/>
    <col min="6" max="6" width="30" customWidth="1"/>
    <col min="7" max="7" width="15" customWidth="1"/>
    <col min="8" max="8" width="12" customWidth="1"/>
    <col min="9" max="9" width="12" customWidth="1"/>
    <col min="10" max="10" width="12" customWidth="1"/>
    <col min="11" max="11" width="12" customWidth="1"/>
    <col min="12" max="12" width="15" customWidth="1"/>
    <col min="13" max="13" width="40" customWidth="1"/>
  </cols>
 
  <sheetData>
    <row r="1">' . $headerRow . '</row>
  </sheetData>
 
  <dataValidations>' . $dvXml . '</dataValidations>
 
</worksheet>';

        // ── Helper Sheets ─────────────────────────────────────────────
        $sheet2Xml = '<?xml version="1.0" encoding="UTF-8"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <sheetData>' . $catRows . '</sheetData></worksheet>';

        $sheet3Xml = '<?xml version="1.0" encoding="UTF-8"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <sheetData>' . $subRows . '</sheetData></worksheet>';

        $sheet4Xml = '<?xml version="1.0" encoding="UTF-8"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <sheetData>' . $brandRows . '</sheetData></worksheet>';

        $sheet5Xml = '<?xml version="1.0" encoding="UTF-8"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <sheetData>' . $unitRows . '</sheetData></worksheet>';

        $sheet6Xml = '<?xml version="1.0" encoding="UTF-8"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <sheetData>' . $gstRows . '</sheetData></worksheet>';

        // ── sharedStrings.xml ─────────────────────────────────────────
        $sharedStringsXml = '<?xml version="1.0" encoding="UTF-8"?>
<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"
     count="' . count($strIndex) . '" uniqueCount="' . count($strIndex) . '">'
            . $siXml . '</sst>';

        // ── workbook.xml ──────────────────────────────────────────────
        $allNamedRanges = $namedRanges . $brandNamedRanges;
        $workbook = '<?xml version="1.0" encoding="UTF-8"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"
          xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>
    <sheet name="Worksheet" sheetId="1" r:id="rId1"/>
    <sheet name="CategoryList" sheetId="2" r:id="rId2" state="hidden"/>
    <sheet name="SubCategoryList" sheetId="3" r:id="rId3" state="hidden"/>
    <sheet name="BrandList" sheetId="4" r:id="rId4" state="hidden"/>
    <sheet name="UnitList" sheetId="5" r:id="rId5" state="hidden"/>
    <sheet name="GSTList" sheetId="6" r:id="rId6" state="hidden"/>
  </sheets>
  <definedNames>' . $allNamedRanges . '</definedNames>
</workbook>';

        $contentTypes = '<?xml version="1.0" encoding="UTF-8"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/worksheets/sheet2.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/worksheets/sheet3.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/worksheets/sheet4.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/worksheets/sheet5.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/worksheets/sheet6.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
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
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet2.xml"/>
  <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet3.xml"/>
  <Relationship Id="rId4" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet4.xml"/>
  <Relationship Id="rId5" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet5.xml"/>
  <Relationship Id="rId6" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet6.xml"/>
  <Relationship Id="rId7" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>
  <Relationship Id="rId8" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
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
  <borders><border><left/><right/><top/><bottom/><diagonal/></border></borders>
  <cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>
  <cellXfs>
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
    <xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0"/>
  </cellXfs>
</styleSheet>';

        // ── ZIP बनवा ──────────────────────────────────────────────────
        $zipPath = sys_get_temp_dir() . '/product_sample.xlsx';
        if (file_exists($zipPath)) unlink($zipPath);

        $zip = new \ZipArchive();
        $zip->open($zipPath, \ZipArchive::CREATE);
        $zip->addFromString('[Content_Types].xml',        $contentTypes);
        $zip->addFromString('_rels/.rels',                $rels);
        $zip->addFromString('xl/workbook.xml',            $workbook);
        $zip->addFromString('xl/_rels/workbook.xml.rels', $workbookRels);
        $zip->addFromString('xl/worksheets/sheet1.xml',   $sheet1Xml);
        $zip->addFromString('xl/worksheets/sheet2.xml',   $sheet2Xml);
        $zip->addFromString('xl/worksheets/sheet3.xml',   $sheet3Xml);
        $zip->addFromString('xl/worksheets/sheet4.xml',   $sheet4Xml);
        $zip->addFromString('xl/worksheets/sheet5.xml',   $sheet5Xml);
        $zip->addFromString('xl/worksheets/sheet6.xml',   $sheet6Xml);
        $zip->addFromString('xl/sharedStrings.xml',       $sharedStringsXml);
        $zip->addFromString('xl/styles.xml',              $styles);
        $zip->close();

        return response()->download($zipPath, 'product_sample.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
