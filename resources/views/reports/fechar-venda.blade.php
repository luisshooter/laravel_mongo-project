@extends('layouts.app')

@section('title', 'Fechar venda – Mesa ' . $mesa)

@section('content')
<div class="page-header mb-4">
    <h1 class="page-title"><i class="bi bi-cash-coin"></i> Fechar venda – Mesa {{ $mesa }}</h1>
    <p class="text-muted mb-0">Confirme o resumo e feche a venda para liberar a mesa</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card card-modern mb-4">
            <div class="card-header card-header-modern">
                <h5 class="mb-0"><i class="bi bi-cart-check"></i> Pedidos da mesa</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-modern table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Itens</th>
                                <th>Pagamento</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pedidos as $order)
                                <tr>
                                    <td>
                                        @if(is_array($order->items))
                                            @foreach($order->items as $it)
                                                <span class="d-block small">{{ $it['name'] ?? '-' }} · {{ $it['quantity'] ?? 0 }}x R$ {{ number_format($it['unit_price'] ?? 0, 2, ',', '.') }}</span>
                                            @endforeach
                                        @else — @endif
                                    </td>
                                    <td><span class="badge bg-secondary">{{ $order->payment_label }}</span></td>
                                    <td class="text-end fw-bold">R$ {{ number_format($order->total_price, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card card-modern border-primary">
            <div class="card-header card-header-modern bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-receipt"></i> Total a receber</h5>
            </div>
            <div class="card-body">
                <p class="display-6 fw-bold text-primary mb-4">R$ {{ number_format($totalMesa, 2, ',', '.') }}</p>
                <p class="small text-muted mb-3">Ao confirmar, os {{ $pedidos->count() }} pedido(s) serão concluídos, o valor entrará nos lucros e a <strong>mesa {{ $mesa }} ficará disponível</strong> para outro cliente.</p>
                <form action="{{ route('reports.encerrar-mesa', $mesa) }}" method="POST" onsubmit="return confirm('Confirmar fechamento da venda? A mesa {{ $mesa }} será liberada.');">
                    @csrf
                    <button type="submit" class="btn btn-success btn-lg w-100">
                        <i class="bi bi-check-circle-fill me-2"></i> Fechar venda e liberar mesa
                    </button>
                </form>
                <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary w-100 mt-2"><i class="bi bi-arrow-left"></i> Voltar</a>
            </div>
        </div>
    </div>
</div>
@endsection
