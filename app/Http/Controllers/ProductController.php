<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Material;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['brand', 'material']);

        if ($request->has('brand_id') && $request->brand_id) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->has('material_id') && $request->material_id) {
            $query->where('material_id', $request->material_id);
        }

        if ($request->has('min_price') && is_numeric($request->min_price)) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price') && is_numeric($request->max_price)) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
        }

        $sortField = $request->sort_by ?? 'id';
        $sortDirection = $request->sort_direction ?? 'asc';
        $query->orderBy($sortField, $sortDirection);

        $products = $query->paginate(10);

        $brands = Brand::orderBy('name')->get();
        $materials = Material::orderBy('name')->get();

        return view('products.index', compact('products', 'brands', 'materials'));
    }

    public function edit(Product $product)
    {
        $brands = Brand::orderBy('name')->get();
        $materials = Material::orderBy('name')->get();

        return view('products.edit', compact('product', 'brands', 'materials'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255',
            'brand_id' => 'required|exists:brands,id',
            'material_id' => 'required|exists:materials,id',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produkt byl úspěšně aktualizován.');
    }

    public function export(Request $request)
    {
        $query = Product::with(['brand', 'material']);

        if ($request->has('brand_id') && $request->brand_id) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->has('material_id') && $request->material_id) {
            $query->where('material_id', $request->material_id);
        }

        if ($request->has('min_price') && is_numeric($request->min_price)) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price') && is_numeric($request->max_price)) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
        }

        $sortField = $request->sort_by ?? 'id';
        $sortDirection = $request->sort_direction ?? 'asc';
        $query->orderBy($sortField, $sortDirection);

        $products = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="produkty.csv"',
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Značka', 'Materiál', 'Kód', 'Cena', 'Popis']);

            foreach ($products as $product) {
                fputcsv($file, [
                    $product->brand->name,
                    $product->material->name,
                    $product->code,
                    $product->price,
                    $product->description,
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
