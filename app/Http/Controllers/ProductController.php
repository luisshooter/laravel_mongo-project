<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Auth::user()->permission_level < 2) {
                abort(403, 'Acesso não autorizado. Apenas Gerentes e Administradores podem gerenciar produtos.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $products = Product::orderBy('created_at', 'desc')->get();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Product::getCategories();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:' . implode(',', array_keys(Product::getCategories())),
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'O nome do produto é obrigatório.',
            'category.in' => 'Categoria inválida.',
            'price.required' => 'O preço é obrigatório.',
            'price.numeric' => 'O preço deve ser um valor numérico válido.',
            'stock.required' => 'O estoque é obrigatório.',
            'stock.integer' => 'O estoque deve ser um número inteiro.',
            'stock.min' => 'O estoque não pode ser negativo.'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Product::create([
            'name' => $request->name,
            'category' => $request->category,
            'price' => (float) $request->price,
            'stock' => (int) $request->stock,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Produto cadastrado com sucesso!');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Product::getCategories();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:' . implode(',', array_keys(Product::getCategories())),
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'stock.min' => 'O estoque não pode ser negativo.'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $product->update([
            'name' => $request->name,
            'category' => $request->category,
            'price' => (float) $request->price,
            'stock' => (int) $request->stock,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Produto atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produto removido com sucesso!');
    }
}
