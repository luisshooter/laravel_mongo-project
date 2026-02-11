@extends('layouts.app')

@section('title', 'Pedidos')

@section('content')
<div class="page-header mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="page-title"><i class="bi bi-cart3"></i> Pedidos</h1>
            <p class="text-muted mb-0">Cadastro e acompanhamento por mesa</p>
        </div>
        <a href="{{ route('orders.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Novo pedido
        </a>
    </div>
</div>

<div class="card card-modern">
    <div class="card-body">
        @if($orders->count() > 0)
            <div class="table-responsive">
                <table class="table table-modern table-hover">
                    <thead>
                        <tr>
                            <th>Mesa</th>
                            <th>Itens (resumo)</th>
                            <th>Total</th>
                            <th>Status</th>
                            @if(Auth::user()->permission_level >= 2)
                                <th>Criado por</th>
                            @endif
                            <th>Data</th>
                            <th width="150">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td><strong>Mesa {{ $order->mesa }}</strong></td>
                                <td>
                                    @if(is_array($order->items))
                                        @foreach(array_slice($order->items, 0, 3) as $it)
                                            <small>{{ $it['name'] }} ({{ $it['quantity'] }})</small>
                                            @if(!$loop->last) · @endif
                                        @endforeach
                                        @if(count($order->items) > 3)
                                            <small class="text-muted">+{{ count($order->items) - 3 }} itens</small>
                                        @endif
                                    @else
                                        —
                                    @endif
                                </td>
                                <td><strong>R$ {{ number_format($order->total_price, 2, ',', '.') }}</strong></td>
                                <td>
                                    <span class="badge bg-{{ $order->status_badge }}">{{ $order->status_label }}</span>
                                </td>
                                @if(Auth::user()->permission_level >= 2)
                                    <td><small>{{ $order->user->name ?? '-' }}</small></td>
                                @endif
                                <td><small>{{ $order->formatted_created_at }}</small></td>
                                <td>
                                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-info" title="Ver"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-sm btn-warning" title="Editar"><i class="bi bi-pencil"></i></a>
                                    @if(Auth::user()->permission_level >= 2)
                                        <form action="{{ route('orders.destroy', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Excluir este pedido?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Excluir"><i class="bi bi-trash"></i></button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info mb-0">
                <i class="bi bi-info-circle"></i> Nenhum pedido encontrado.
            </div>
        @endif
    </div>
</div>
@endsection
