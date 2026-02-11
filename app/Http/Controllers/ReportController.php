<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $mesasCount = config('menu.mesas_count', 8);
        $dateFrom = $request->get('date_from') ? Carbon::parse($request->date_from)->startOfDay() : null;
        $dateTo = $request->get('date_to') ? Carbon::parse($request->date_to)->endOfDay() : null;

        $queryAll = Order::query();
        $queryCompleted = Order::where('status', Order::STATUS_COMPLETED);

        if ($dateFrom) {
            $queryAll->where('created_at', '>=', $dateFrom);
            $queryCompleted->where('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $queryAll->where('created_at', '<=', $dateTo);
            $queryCompleted->where('created_at', '<=', $dateTo);
        }

        $pedidos = $queryAll->get();
        $pedidosConcluidos = $queryCompleted->get();

        // Lucros = apenas pedidos concluídos (vendidos / mesa encerrada)
        $lucros = $pedidosConcluidos->sum('total_price');
        $totalPedidos = $pedidos->count();
        $vendidos = $pedidosConcluidos->count();
        $naoVendidos = $pedidos->whereIn('status', [Order::STATUS_PENDING, Order::STATUS_PROCESSING])->count();
        $cancelados = $pedidos->where('status', Order::STATUS_CANCELLED)->count();

        // Faturamento no período (só concluídos = realizado)
        $faturamentoPeriodo = $lucros;

        // Por mesa
        $porMesa = [];
        for ($mesa = 1; $mesa <= $mesasCount; $mesa++) {
            $pMesa = Order::where('mesa', $mesa);
            if ($dateFrom) $pMesa->where('created_at', '>=', $dateFrom);
            if ($dateTo) $pMesa->where('created_at', '<=', $dateTo);
            $lista = $pMesa->orderBy('created_at', 'desc')->get();
            $totalMesa = $lista->sum('total_price');
            $concluidosMesa = $lista->where('status', Order::STATUS_COMPLETED)->sum('total_price');
            $porMesa[$mesa] = [
                'pedidos' => $lista,
                'total' => $totalMesa,
                'concluidos_valor' => $concluidosMesa,
                'quantidade' => $lista->count(),
                'tem_abertos' => $lista->whereIn('status', [Order::STATUS_PENDING, Order::STATUS_PROCESSING])->count() > 0,
            ];
        }

        // Itens mais vendidos (apenas concluídos)
        $itensVendidos = [];
        foreach ($pedidosConcluidos as $order) {
            foreach ($order->items ?? [] as $item) {
                $nome = $item['name'] ?? 'Item';
                if (!isset($itensVendidos[$nome])) {
                    $itensVendidos[$nome] = ['qtd' => 0, 'valor' => 0];
                }
                $qtd = (int)($item['quantity'] ?? 0);
                $preco = (float)($item['unit_price'] ?? 0);
                $itensVendidos[$nome]['qtd'] += $qtd;
                $itensVendidos[$nome]['valor'] += $qtd * $preco;
            }
        }
        arsort($itensVendidos);

        // Resumos: hoje, esta semana, este mês
        $hoje = Order::where('status', Order::STATUS_COMPLETED)->whereDate('created_at', today())->get()->sum('total_price');
        $semana = Order::where('status', Order::STATUS_COMPLETED)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->get()->sum('total_price');
        $mes = Order::where('status', Order::STATUS_COMPLETED)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->get()->sum('total_price');

        // Pedidos por status
        $porStatus = [
            'Pendente' => $pedidos->where('status', Order::STATUS_PENDING)->count(),
            'Em processamento' => $pedidos->where('status', Order::STATUS_PROCESSING)->count(),
            'Concluído' => $pedidos->where('status', Order::STATUS_COMPLETED)->count(),
            'Cancelado' => $pedidos->where('status', Order::STATUS_CANCELLED)->count(),
        ];

        return view('reports.index', compact(
            'porMesa', 'mesasCount', 'lucros', 'totalPedidos', 'vendidos', 'naoVendidos', 'cancelados',
            'faturamentoPeriodo', 'itensVendidos', 'hoje', 'semana', 'mes', 'porStatus',
            'dateFrom', 'dateTo'
        ));
    }

    /**
     * Tela de fechar venda: mostra resumo da mesa (pedidos abertos, total, pagamento) para confirmar e liberar a mesa
     */
    public function fecharVenda(int $mesa)
    {
        if ($mesa < 1 || $mesa > config('menu.mesas_count', 8)) {
            return redirect()->route('reports.index')->with('error', 'Mesa inválida.');
        }

        $pedidos = Order::with('user')
            ->where('mesa', $mesa)
            ->whereIn('status', [Order::STATUS_PENDING, Order::STATUS_PROCESSING])
            ->orderBy('created_at')
            ->get();

        if ($pedidos->isEmpty()) {
            return redirect()->route('reports.index')
                ->with('info', "Mesa $mesa não possui pedidos abertos para fechar.");
        }

        $totalMesa = $pedidos->sum('total_price');
        return view('reports.fechar-venda', compact('mesa', 'pedidos', 'totalMesa'));
    }

    /**
     * Encerrar mesa: confirma fechamento, marca pedidos como concluídos → entram nos lucros e mesa fica disponível
     */
    public function encerrarMesa(int $mesa)
    {
        if ($mesa < 1 || $mesa > config('menu.mesas_count', 8)) {
            return redirect()->route('reports.index')->with('error', 'Mesa inválida.');
        }

        $orders = Order::where('mesa', $mesa)
            ->whereIn('status', [Order::STATUS_PENDING, Order::STATUS_PROCESSING])
            ->get();
        $atualizados = 0;
        foreach ($orders as $order) {
            $order->update(['status' => Order::STATUS_COMPLETED]);
            $atualizados++;
        }

        return redirect()->route('reports.index')
            ->with('success', "Mesa $mesa encerrada. Venda fechada: $atualizados pedido(s) concluído(s). Mesa disponível para novo cliente.");
    }
}
