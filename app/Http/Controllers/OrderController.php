<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user')->orderBy('created_at', 'desc');

        if (Auth::user()->permission_level === 1) {
            $query->where('user_id', Auth::id());
        }

        // Por padrão: só pedidos ativos (pendente/processando). Mesa encerrada = pedidos concluídos não aparecem = mesa disponível
        $incluirEncerrados = $request->boolean('encerrados');
        if (!$incluirEncerrados) {
            $query->whereIn('status', [Order::STATUS_PENDING, Order::STATUS_PROCESSING]);
        }

        $orders = $query->get();
        return view('orders.index', compact('orders', 'incluirEncerrados'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->get();
        $productsByCategory = [];
        $categories = Product::getCategories();
        
        foreach ($categories as $key => $label) {
            $productsByCategory[$key] = [
                'label' => $label,
                'items' => $products->where('category', $key)->values()
            ];
        }

        return view('orders.create', compact('productsByCategory'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
            'customer_cpf' => 'nullable|string|max:20',
            'customer_address' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|string',
            'items.*.name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'status' => 'required|in:pending,processing,completed,cancelled',
            'payment_method' => 'required|in:dinheiro,cartao',
            'notes' => 'nullable|string|max:1000',
        ], [
            'customer_name.required' => 'Informe o nome do cliente.',
            'customer_name.max' => 'Nome do cliente muito longo.',
            'items.required' => 'Adicione ao menos um item ao pedido.',
            'items.min' => 'Adicione ao menos um item ao pedido.',
            'payment_method.required' => 'Selecione a forma de pagamento.',
            'payment_method.in' => 'Forma de pagamento inválida.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $items = [];
        $productsToUpdate = [];
        foreach ($request->items as $item) {
            if (($item['quantity'] ?? 0) < 1) {
                continue;
            }
            $product = \App\Models\Product::find($item['id']);
            if (!$product) {
                return back()->withErrors(['items' => "Produto {$item['name']} não encontrado."])->withInput();
            }
            if ($product->stock < $item['quantity']) {
                return back()->withErrors(['items' => "Estoque insuficiente para {$item['name']} (disp: {$product->stock})."])->withInput();
            }
            $items[] = [
                'id' => $item['id'],
                'name' => $item['name'],
                'quantity' => (int) $item['quantity'],
                'unit_price' => (float) $item['unit_price'],
            ];
            $product->stock -= (int) $item['quantity'];
            $productsToUpdate[] = $product;
        }

        if (empty($items)) {
            return back()->withErrors(['items' => 'Adicione ao menos um item com quantidade maior que zero.'])->withInput();
        }

        foreach ($productsToUpdate as $p) {
            $p->save();
        }

        Order::create([
            'user_id' => Auth::id(),
            'customer_name' => $request->customer_name,
            'customer_cpf' => $request->customer_cpf,
            'customer_address' => $request->customer_address,
            'items' => $items,
            'status' => $request->status,
            'payment_method' => $request->payment_method,
            'notes' => $request->notes,
        ]);

        return redirect()->route('orders.index')
            ->with('success', 'Pedido cadastrado com sucesso!');
    }

    public function show($id)
    {
        $order = Order::with('user')->findOrFail($id);

        if (Auth::user()->permission_level === 1 && $order->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        return view('orders.show', compact('order'));
    }

    public function edit($id)
    {
        $order = Order::findOrFail($id);

        if (Auth::user()->permission_level === 1 && $order->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        $products = Product::where('is_active', true)->get();
        $productsByCategory = [];
        $categories = Product::getCategories();
        
        foreach ($categories as $key => $label) {
            $productsByCategory[$key] = [
                'label' => $label,
                'items' => $products->where('category', $key)->values()
            ];
        }

        return view('orders.edit', compact('order', 'productsByCategory'));
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        if (Auth::user()->permission_level === 1 && $order->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
            'customer_cpf' => 'nullable|string|max:20',
            'customer_address' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|string',
            'items.*.name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'status' => 'required|in:pending,processing,completed,cancelled',
            'payment_method' => 'required|in:dinheiro,cartao',
            'notes' => 'nullable|string|max:1000',
        ], [
            'customer_name.required' => 'Informe o nome do cliente.',
            'items.required' => 'Adicione ao menos um item ao pedido.',
            'payment_method.required' => 'Selecione a forma de pagamento.',
            'payment_method.in' => 'Forma de pagamento inválida.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $oldItems = $order->items ?? [];
        $newItemsReq = [];
        foreach ($request->items as $item) {
            if (($item['quantity'] ?? 0) < 1) continue;
            $newItemsReq[] = $item;
        }
        
        if (empty($newItemsReq)) {
            return back()->withErrors(['items' => 'Adicione ao menos um item.'])->withInput();
        }

        $stockChanges = [];
        foreach ($oldItems as $old) {
            $stockChanges[$old['id']] = - $old['quantity'];
        }
        foreach ($newItemsReq as $new) {
            if (!isset($stockChanges[$new['id']])) $stockChanges[$new['id']] = 0;
            $stockChanges[$new['id']] += $new['quantity'];
        }

        $productsToUpdate = [];
        foreach ($stockChanges as $id => $change) {
            if ($change == 0) continue;
            $product = \App\Models\Product::find($id);
            if ($product) {
                if ($product->stock - $change < 0) {
                    return back()->withErrors(['items' => "Estoque insuficiente para {$product->name}."])->withInput();
                }
                $product->stock -= $change;
                $productsToUpdate[] = $product;
            }
        }

        foreach ($productsToUpdate as $p) {
            $p->save();
        }

        $items = [];
        foreach ($newItemsReq as $new) {
            $items[] = [
                'id' => $new['id'],
                'name' => $new['name'],
                'quantity' => (int) $new['quantity'],
                'unit_price' => (float) $new['unit_price'],
            ];
        }

        $order->update([
            'customer_name' => $request->customer_name,
            'customer_cpf' => $request->customer_cpf,
            'customer_address' => $request->customer_address,
            'items' => $items,
            'status' => $request->status,
            'payment_method' => $request->payment_method,
            'notes' => $request->notes,
        ]);

        return redirect()->route('orders.index')
            ->with('success', 'Pedido atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);

        if (Auth::user()->permission_level === 1) {
            abort(403, 'Apenas Gerente ou Administrador podem excluir pedidos.');
        }

        if (is_array($order->items)) {
            foreach ($order->items as $item) {
                $p = \App\Models\Product::find($item['id']);
                if ($p) {
                    $p->stock += $item['quantity'];
                    $p->save();
                }
            }
        }
        $order->delete();
        return redirect()->route('orders.index')
            ->with('success', 'Pedido removido com sucesso!');
    }
}