@extends('layouts.app')

@section('title', 'Detalhes do Pedido')

@section('content')
<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="bi bi-eye"></i> Pedido – Mesa {{ $order->mesa }}</h4>
                <span class="badge bg-{{ $order->status_badge }} fs-6">{{ $order->status_label }}</span>
            </div>
            <div class="card-body">
                <h5 class="border-bottom pb-2 mb-3">Itens do pedido</h5>
                <table class="table table-sm">
                    <thead><tr><th>Item</th><th>Qtd</th><th>Preço unit.</th><th>Subtotal</th></tr></thead>
                    <tbody>
                        @foreach($order->items ?? [] as $it)
                            <tr>
                                <td>{{ $it['name'] ?? '-' }}</td>
                                <td>{{ $it['quantity'] ?? 0 }}</td>
                                <td>R$ {{ number_format($it['unit_price'] ?? 0, 2, ',', '.') }}</td>
                                <td>R$ {{ number_format(($it['quantity'] ?? 0) * ($it['unit_price'] ?? 0), 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <p class="text-end mb-0"><strong>Total: R$ {{ number_format($order->total_price, 2, ',', '.') }}</strong></p>

                @if($order->notes)
                    <div class="mt-3">
                        <h6 class="border-bottom pb-1">Observações</h6>
                        <p class="text-muted mb-0">{{ $order->notes }}</p>
                    </div>
                @endif

                <div class="mt-4 pt-3 border-top">
                    @if(Auth::user()->permission_level >= 2 && $order->user)
                        <p class="mb-1"><strong>Criado por:</strong> {{ $order->user->name }}</p>
                    @endif
                    <p class="mb-1"><strong>Data:</strong> {{ $order->formatted_created_at }}</p>
                </div>

                <hr>
                <div class="d-flex justify-content-between">
                    <a href="{{ route('orders.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Voltar</a>
                    <div>
                        <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-warning"><i class="bi bi-pencil"></i> Editar</a>
                        @if(Auth::user()->permission_level >= 2)
                            <form action="{{ route('orders.destroy', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Excluir este pedido?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"><i class="bi bi-trash"></i> Excluir</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
