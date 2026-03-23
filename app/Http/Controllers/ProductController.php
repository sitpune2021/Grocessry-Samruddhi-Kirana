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
        Log::info('Product Sample Excel Download Started');

        $categories = Category::with(['subCategories.brands' => function ($q) {
            $q->where('status', 1);
        }])->orderBy('name')->get();

        $units = Unit::orderBy('name')->get();
        $taxes = Tax::where('is_active', 1)->get();

        // ── 1. Shared Strings Management ──────────────────────────────────
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
            $catUnderscore = preg_replace('/[^A-Za-z0-9_]/', '_', $cat->name);
            $allStrings[] = $catUnderscore;

            foreach ($cat->subCategories as $sub) {
                $subUnderscore = preg_replace('/[^A-Za-z0-9_]/', '_', $sub->name);
                $allStrings[] = $subUnderscore;
                foreach ($sub->brands as $brand) {
                    $allStrings[] = $brand->name;
                }
            }
        }
        foreach ($units as $u) {
            $allStrings[] = $u->name . ' (' . strtoupper($u->short_name) . ')';
        }
        foreach ($taxes as $t) {
            $allStrings[] = $t->name . ' (' . $t->gst . '%)';
        }

        $strIndex = [];
        foreach (array_unique($allStrings) as $s) {
            if (!isset($strIndex[$s])) $strIndex[$s] = count($strIndex);
        }

        $siXml = '';
        foreach (array_keys($strIndex) as $s) {
            $siXml .= '<si><t xml:space="preserve">' . htmlspecialchars($s, ENT_XML1) . '</t></si>';
        }

        // ── 2. Sheets Data Generation (Removing Blanks) ───────────────────

        // Sheet 2: CategoryList
        $catRows = '';
        foreach ($categories as $i => $cat) {
            $name = preg_replace('/[^A-Za-z0-9_]/', '_', $cat->name);
            $catRows .= '<row r="' . ($i + 1) . '"><c r="A' . ($i + 1) . '" t="s"><v>' . $strIndex[$name] . '</v></c></row>';
        }

        // Sheet 3: SubCategoryList (Each Category gets a Column)
        $subRows = [];
        $subNamedRanges = '';
        $colNum = 1;

        foreach ($categories as $cat) {
            $subs = $cat->subCategories->map(fn($s) => preg_replace('/[^A-Za-z0-9_]/', '_', $s->name))
                ->filter()->values()->toArray(); // Crucial: Remove blanks & Re-index

            if (empty($subs)) continue;

            $colLetter = $this->getColLetter($colNum);
            foreach ($subs as $si => $subName) {
                $r = $si + 1;
                $subRows[$r][] = '<c r="' . $colLetter . $r . '" t="s"><v>' . $strIndex[$subName] . '</v></c>';
            }

            $rangeName = preg_replace('/[^A-Za-z0-9_]/', '_', $cat->name);
            $subNamedRanges .= '<definedName name="' . htmlspecialchars($rangeName) . '">SubCategoryList!$' . $colLetter . '$1:$' . $colLetter . '$' . count($subs) . '</definedName>';
            $colNum++;
        }
        $subSheetData = '';
        foreach ($subRows as $rNum => $cells) {
            $subSheetData .= '<row r="' . $rNum . '">' . implode('', $cells) . '</row>';
        }

        // Sheet 4: BrandList (Each SubCategory gets a Column)
        $brandRowsArr = [];
        $brandNamedRanges = '';
        $bColNum = 1;

        foreach ($categories as $cat) {
            foreach ($cat->subCategories as $sub) {
                $brands = $sub->brands->pluck('name')->filter()->values()->toArray();
                if (empty($brands)) continue;

                $bColLetter = $this->getColLetter($bColNum);
                foreach ($brands as $bi => $bName) {
                    $r = $bi + 1;
                    $brandRowsArr[$r][] = '<c r="' . $bColLetter . $r . '" t="s"><v>' . $strIndex[$bName] . '</v></c>';
                }

                $bRangeName = 'brand_' . preg_replace('/[^A-Za-z0-9_]/', '_', $sub->name);
                $brandNamedRanges .= '<definedName name="' . htmlspecialchars($bRangeName) . '">BrandList!$' . $bColLetter . '$1:$' . $bColLetter . '$' . count($brands) . '</definedName>';
                $bColNum++;
            }
        }
        $brandSheetData = '';
        foreach ($brandRowsArr as $rNum => $cells) {
            $brandSheetData .= '<row r="' . $rNum . '">' . implode('', $cells) . '</row>';
        }

        // ── 3. Headers & Data Validations ────────────────────────────────
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M'];
        $headerRow = '';
        foreach (array_slice($allStrings, 0, 13) as $hi => $h) {
            $headerRow .= '<c r="' . $cols[$hi] . '1" t="s" s="1"><v>' . $strIndex[$h] . '</v></c>';
        }

        $dvXml = '<dataValidation type="list" allowBlank="1" sqref="A2:A500"><formula1>CategoryList!$A$1:$A$' . count($categories) . '</formula1></dataValidation>';
        $dvXml .= '<dataValidation type="list" allowBlank="1" sqref="B2:B500"><formula1>INDIRECT(A2)</formula1></dataValidation>';
        $dvXml .= '<dataValidation type="list" allowBlank="1" sqref="C2:C500"><formula1>INDIRECT("brand_"&amp;B2)</formula1></dataValidation>';
        $dvXml .= '<dataValidation type="list" allowBlank="1" sqref="G2:G500"><formula1>UnitList!$A$1:$A$' . $units->count() . '</formula1></dataValidation>';
        $dvXml .= '<dataValidation type="list" allowBlank="1" sqref="L2:L500"><formula1>GSTList!$A$1:$A$' . $taxes->count() . '</formula1></dataValidation>';

        // ── 4. Final Assembly ───────────────────────────────────────────
        $zipPath = sys_get_temp_dir() . '/sample_' . time() . '.xlsx';
        $zip = new \ZipArchive();
        $zip->open($zipPath, \ZipArchive::CREATE);

        // Standard XLSX Files
        $zip->addFromString('xl/sharedStrings.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . count($strIndex) . '" uniqueCount="' . count($strIndex) . '">' . $siXml . '</sst>');
        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Worksheet" sheetId="1" r:id="rId1"/><sheet name="CategoryList" sheetId="2" r:id="rId2" state="hidden"/><sheet name="SubCategoryList" sheetId="3" r:id="rId3" state="hidden"/><sheet name="BrandList" sheetId="4" r:id="rId4" state="hidden"/><sheet name="UnitList" sheetId="5" r:id="rId5" state="hidden"/><sheet name="GSTList" sheetId="6" r:id="rId6" state="hidden"/></sheets><definedNames>' . $subNamedRanges . $brandNamedRanges . '</definedNames></workbook>');

        // Worksheet 1 with Data Validations
        $zip->addFromString('xl/worksheets/sheet1.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetViews><sheetView workbookViewId="0"><pane ySplit="1" state="frozen"/></sheetView></sheetViews><sheetData><row r="1">' . $headerRow . '</row></sheetData><dataValidations count="5">' . $dvXml . '</dataValidations></worksheet>');

        // Data Sheets
        $zip->addFromString('xl/worksheets/sheet2.xml', $this->wrapSheet($catRows));
        $zip->addFromString('xl/worksheets/sheet3.xml', $this->wrapSheet($subSheetData));
        $zip->addFromString('xl/worksheets/sheet4.xml', $this->wrapSheet($brandSheetData));

        // Unit & GST Sheets
        $uRows = '';
        foreach ($units as $i => $u) {
            $label = $u->name . ' (' . strtoupper($u->short_name) . ')';
            $uRows .= '<row r="' . ($i + 1) . '"><c r="A' . ($i + 1) . '" t="s"><v>' . $strIndex[$label] . '</v></c></row>';
        }
        $zip->addFromString('xl/worksheets/sheet5.xml', $this->wrapSheet($uRows));

        $gRows = '';
        foreach ($taxes as $i => $t) {
            $label = $t->name . ' (' . $t->gst . '%)';
            $gRows .= '<row r="' . ($i + 1) . '"><c r="A' . ($i + 1) . '" t="s"><v>' . $strIndex[$label] . '</v></c></row>';
        }
        $zip->addFromString('xl/worksheets/sheet6.xml', $this->wrapSheet($gRows));

        // Common XMLs (styles, rels, [Content_Types]) - assuming you have standard ones
        $this->addStandardXlsxFiles($zip);

        $zip->close();

        return response()->download($zipPath, 'product_sample.xlsx')->deleteFileAfterSend(true);
    }

    // Helpers sathi khali dilyapramane methods add kara
    private function getColLetter($n)
    {
        $letters = "";
        while ($n > 0) {
            $m = ($n - 1) % 26;
            $letters = chr(65 + $m) . $letters;
            $n = intval(($n - $m) / 26);
        }
        return $letters;
    }

    private function wrapSheet($rows)
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>' . $rows . '</sheetData></worksheet>';
    }

    private function addStandardXlsxFiles($zip)
    {
        // Styles, Rels, and Content Types (Standard boilerplate)
        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>');
        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet2.xml"/><Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet3.xml"/><Relationship Id="rId4" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet4.xml"/><Relationship Id="rId5" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet5.xml"/><Relationship Id="rId6" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet6.xml"/><Relationship Id="rId7" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/><Relationship Id="rId8" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/></Relationships>');
        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/worksheets/sheet2.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/worksheets/sheet3.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/worksheets/sheet4.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/worksheets/sheet5.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/worksheets/sheet6.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/><Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/></Types>');
        $zip->addFromString('xl/styles.xml', '<?xml version="1.0" encoding="UTF-8"?><styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><fonts count="2"><font><sz val="11"/></font><font><b/><color rgb="FFFFFFFF"/></font></fonts><fills count="3"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FF4472C4"/></patternFill></fill></fills><borders count="1"><border><left/><right/><top/><bottom/></border></borders><cellXfs count="2"><xf fontId="0" fillId="0"/><xf fontId="1" fillId="2" applyFont="1" applyFill="1"/></cellXfs></styleSheet>');
    }

    /**
     * Bulk Upload Products from Excel (XLSX/CSV)
     */
    public function bulkUpload(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,csv,txt|max:5120',
        ]);

        try {
            Log::info('Product Bulk Upload Started', [
                'user_id' => auth()->id(),
                'file'    => $request->file('excel_file')->getClientOriginalName(),
            ]);

            $file      = $request->file('excel_file');
            $extension = strtolower($file->getClientOriginalExtension());
            $data      = [];

            // --- 1. Extraction Logic ---
            if ($extension === 'xlsx') {
                $zip = new \ZipArchive();
                $zip->open($file->getRealPath());
                $sharedStrings = [];
                $ssXml = $zip->getFromName('xl/sharedStrings.xml');
                if ($ssXml) {
                    $ss = simplexml_load_string($ssXml);
                    foreach ($ss->si as $si) {
                        $sharedStrings[] = isset($si->r) ? implode('', array_map(fn($r) => (string)$r->t, iterator_to_array($si->r))) : (string)$si->t;
                    }
                }
                $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
                $zip->close();

                if ($sheetXml) {
                    $sheet = simplexml_load_string($sheetXml);
                    foreach ($sheet->sheetData->row as $row) {
                        $rowData = [];
                        foreach ($row->c as $cell) {
                            $type = (string)$cell['t'];
                            $value = (string)$cell->v;
                            $rowData[] = ($type === 's') ? ($sharedStrings[(int)$value] ?? '') : $value;
                        }
                        $data[] = $rowData;
                    }
                }
                array_shift($data); // Remove Header
            } else {
                $handle = fopen($file->getRealPath(), 'r');
                fgetcsv($handle);
                while (($row = fgetcsv($handle)) !== false) {
                    $data[] = $row;
                }
                fclose($handle);
            }

            // --- 2. Processing Logic ---
            $successCount = 0;
            $skippedCount = 0;

            foreach ($data as $rowIndex => $row) {
                if (empty(array_filter($row))) continue;

                try {
                    $categoryName    = trim($row[0] ?? '');
                    $subCategoryName = trim($row[1] ?? '');
                    $brandName       = trim($row[2] ?? '');
                    $unitLabel       = trim($row[6] ?? '');
                    $gstLabel        = trim($row[11] ?? '');
                    $imageUrl        = trim($row[12] ?? '');

                    // Map Category (Underscores handle karne)
                    $category = Category::where('name', $categoryName)
                        ->orWhere('name', str_replace('_', ' ', $categoryName))
                        ->first();

                    // Map Sub-Category
                    $subCategory = SubCategory::where('category_id', $category?->id)
                        ->where(function ($q) use ($subCategoryName) {
                            $q->where('name', $subCategoryName)
                                ->orWhere('name', str_replace('_', ' ', $subCategoryName));
                        })->first();

                    $brand = Brand::where('name', $brandName)->first();

                    // Extract Short Name from "Kilogram (KG)"
                    preg_match('/\((.*?)\)/', $unitLabel, $unitMatches);
                    $unitShortName = $unitMatches[1] ?? null;
                    $unit = Unit::where('short_name', $unitShortName)->first();

                    // Extract GST % from "GST 5% (5%)"
                    preg_match('/(\d+)%/', $gstLabel, $taxMatches);
                    $gstValue = $taxMatches[1] ?? null;
                    $tax = Tax::where('gst', $gstValue)->first();

                    if (!$category || !$subCategory || !$brand || !$unit || !$tax) {
                        Log::warning("Skipping Row #$rowIndex: Data mismatch", ['cat' => $categoryName, 'sub' => $subCategoryName]);
                        $skippedCount++;
                        continue;
                    }

                    // Calculations
                    $retailerPrice = (float)($row[9] ?? 0);
                    $gstAmount     = ($retailerPrice * ($tax->gst ?? 0)) / 100;
                    $finalPrice    = $retailerPrice + $gstAmount;

                    // --- Image Handling (Base64 + URL) ---
                    $savedImages = [];
                    if (!empty($imageUrl)) {
                        $imgName = time() . '_' . uniqid();

                        if (str_starts_with($imageUrl, 'data:image')) {
                            // Base64 process
                            $parts = explode(";base64,", $imageUrl);
                            $imgData = base64_decode($parts[1]);
                            $ext = str_contains($parts[0], 'png') ? 'png' : 'jpg';
                            $fileName = $imgName . '.' . $ext;
                            Storage::disk('public')->put('products/' . $fileName, $imgData);
                            $savedImages[] = $fileName;
                        } elseif (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                            // URL process
                            $contents = file_get_contents($imageUrl);
                            $fileName = $imgName . '.jpg';
                            Storage::disk('public')->put('products/' . $fileName, $contents);
                            $savedImages[] = $fileName;
                        }
                    }

                    Product::create([
                        'category_id'     => $category->id,
                        'sub_category_id' => $subCategory->id,
                        'brand_id'        => $brand->id,
                        'name'            => trim($row[3] ?? 'Unnamed'),
                        'barcode'         => trim($row[4] ?? null),
                        'description'     => trim($row[5] ?? null),
                        'unit_id'         => $unit->id,
                        'unit_value'      => (float)($row[7] ?? 0),
                        'base_price'      => (float)($row[8] ?? 0),
                        'retailer_price'  => $retailerPrice,
                        'mrp'             => (float)($row[10] ?? 0),
                        'tax_id'          => $tax->id,
                        'gst_percentage'  => $tax->gst,
                        'gst_amount'      => round($gstAmount, 2),
                        'final_price'     => round($finalPrice, 2),
                        'product_images'  => $savedImages,
                    ]);

                    $successCount++;
                } catch (\Exception $e) {
                    Log::error("Row $rowIndex failed: " . $e->getMessage());
                    $skippedCount++;
                }
            }

            return redirect()->route('product.index')
                ->with('success', "$successCount products added, $skippedCount skipped.");
        } catch (\Throwable $e) {
            Log::error('Critical Upload Error: ' . $e->getMessage());
            return back()->with('error', 'File process karta yet nahi ahe.');
        }
    }
}
