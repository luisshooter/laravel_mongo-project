<?php

namespace App\Http\Controllers;

use App\Models\Order;
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
        return view('orders.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mesa' => 'required|integer|min:1|max:8',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|string',
            'items.*.name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'status' => 'required|in:pending,processing,completed,cancelled',
            'payment_method' => 'required|in:dinheiro,cartao',
            'notes' => 'nullable|string|max:1000',
        ], [
            'mesa.required' => 'Selecione a mesa.',
            'mesa.min' => 'Mesa inválida.',
            'mesa.max' => 'O restaurante possui 8 mesas.',
            'items.required' => 'Adicione ao menos um item ao pedido.',
            'items.min' => 'Adicione ao menos um item ao pedido.',
            'payment_method.required' => 'Selecione a forma de pagamento.',
            'payment_method.in' => 'Forma de pagamento inválida.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $items = [];
        foreach ($request->items as $item) {
            if (($item['quantity'] ?? 0) < 1) {
                continue;
            }
            $items[] = [
                'id' => $item['id'],
                'name' => $item['name'],
                'quantity' => (int) $item['quantity'],
                'unit_price' => (float) $item['unit_price'],
            ];
        }

        if (empty($items)) {
            return back()->withErrors(['items' => 'Adicione ao menos um item com quantidade maior que zero.'])->withInput();
        }

        Order::create([
            'user_id' => Auth::id(),
            'mesa' => (int) $request->mesa,
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

        return view('orders.edit', compact('order'));
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        if (Auth::user()->permission_level === 1 && $order->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        $validator = Validator::make($request->all(), [
            'mesa' => 'required|integer|min:1|max:8',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|string',
            'items.*.name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'status' => 'required|in:pending,processing,completed,cancelled',
            'payment_method' => 'required|in:dinheiro,cartao',
            'notes' => 'nullable|string|max:1000',
        ], [
            'mesa.required' => 'Selecione a mesa.',
            'items.required' => 'Adicione ao menos um item ao pedido.',
            'payment_method.required' => 'Selecione a forma de pagamento.',
            'payment_method.in' => 'Forma de pagamento inválida.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $items = [];
        foreach ($request->items as $item) {
            if (($item['quantity'] ?? 0) < 1) continue;
            $items[] = [
                'id' => $item['id'],
                'name' => $item['name'],
                'quantity' => (int) $item['quantity'],
                'unit_price' => (float) $item['unit_price'],
            ];
        }

        if (empty($items)) {
            return back()->withErrors(['items' => 'Adicione ao menos um item.'])->withInput();
        }

        $order->update([
            'mesa' => (int) $request->mesa,
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

        $order->delete();
        return redirect()->route('orders.index')
            ->with('success', 'Pedido removido com sucesso!');
    }
}