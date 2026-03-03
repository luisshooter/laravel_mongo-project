@extends('layouts.app')

@section('title', 'Novo Pedido')

@section('content')
<div class="row">
    <div class="col-lg-10 offset-lg-1">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="bi bi-cart-plus"></i> Cadastrar Pedido – Restaurante</h4>
            </div>
            <div class="card-body">
                @php
                    $vueProps = [
                        'action' => route('orders.store'),
                        'csrf' => csrf_token(),
                        'method' => 'POST',
                        'backUrl' => route('orders.index'),
                        'submitLabel' => 'Cadastrar Pedido',
                        'mesasCount' => config('menu.mesas_count', 8),
                        'statuses' => \App\Models\Order::getStatuses(),
                        'paymentMethods' => \App\Models\Order::getPaymentMethods(),
                        'menu' => [
                            'pratos' => config('menu.pratos', []),
                            'drinks' => config('menu.drinks', []),
                            'refrigerantes' => config('menu.refrigerantes', []),
                        ],
                        'initial' => [
                            'mesa' => old('mesa', ''),
                            'status' => old('status', 'pending'),
                            'payment_method' => old('payment_method', 'dinheiro'),
                            'notes' => old('notes', ''),
                            'itemsById' => [],
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
