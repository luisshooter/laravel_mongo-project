@extends('layouts.app')

@section('title', 'Editar Pedido')

@section('content')
<div class="row">
    <div class="col-lg-10 offset-lg-1">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="bi bi-pencil"></i> Editar Pedido – Mesa {{ $order->mesa }}</h4>
            </div>
            <div class="card-body">
                @php
                    $currentQtys = [];
                    foreach ($order->items ?? [] as $it) {
                        $currentQtys[$it['id']] = (int) ($it['quantity'] ?? 0);
                    }
                    $vueProps = [
                        'action' => route('orders.update', $order->id),
                        'csrf' => csrf_token(),
                        'method' => 'PUT',
                        'backUrl' => route('orders.index'),
                        'submitLabel' => 'Salvar',
                        'mesasCount' => config('menu.mesas_count', 8),
                        'statuses' => \App\Models\Order::getStatuses(),
                        'paymentMethods' => \App\Models\Order::getPaymentMethods(),
                        'menu' => [
                            'pratos' => config('menu.pratos', []),
                            'drinks' => config('menu.drinks', []),
                            'refrigerantes' => config('menu.refrigerantes', []),
                        ],
                        'initial' => [
                            'mesa' => old('mesa', $order->mesa),
                            'status' => old('status', $order->status),
                            'payment_method' => old('payment_method', $order->payment_method ?? 'dinheiro'),
                            'notes' => old('notes', $order->notes ?? ''),
                            'itemsById' => $currentQtys,
                        ],
                        'errors' => $errors->getMessageBag()->getMessages(),
                    ];
                @endphp
                <div id="order-form-app" data-props="{{ e(json_encode($vueProps)) }}"></div>
            </div>
        </div>
    </div>
</div>
@endsection
