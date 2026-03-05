@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="page-header mb-4">
        <h1 class="page-title"><i class="bi bi-speedometer2"></i> Dashboard</h1>
        <p class="text-muted mb-0">Visão geral da floricultura</p>
    </div>

    @php
        $dashboardProps = [
            'stats' => [
                'total_users' => $stats['total_users'] ?? 0,
                'active_users' => $stats['active_users'] ?? 0,
                'total_orders' => $stats['total_orders'] ?? 0,
                'pending_orders' => $stats['pending_orders'] ?? 0,
                'processing_orders' => $stats['processing_orders'] ?? 0,
                'completed_orders' => $stats['completed_orders'] ?? 0,
            ],
            'isAdmin' => $user->hasMaxPermission(),
        ];
    @endphp
    <div id="dashboard-vue" data-props="{{ e(json_encode($dashboardProps)) }}"></div>

    <div class="row g-3 mb-4">
        @if ($user->hasMaxPermission())
            <div class="col-12">
                <div class="card card-modern">
                    <div class="card-header card-header-modern">
                        <h5 class="mb-0"><i class="bi bi-person-plus me-2"></i> Usuários recentes</h5>
                    </div>
                    <div class="card-body">
                        @if ($stats['recent_users']->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-modern table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th>Email</th>
                                            <th>Permissão</th>
                                            <th>Status</th>
                                            <th>Cadastro</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($stats['recent_users'] as $recentUser)
                                            <tr>
                                                <td>{{ $recentUser->name }}</td>
                                                <td>{{ $recentUser->email }}</td>
                                                <td><span class="badge bg-primary">{{ $recentUser->permission_name }}</span>
                                                </td>
                                                <td>
                                                    @if ($recentUser->active)
                                                    <span class="badge bg-success">Ativo</span>@else<span
                                                            class="badge bg-secondary">Inativo</span>
                                                    @endif
                                                </td>
                                                <td>{{ $recentUser->created_at->format('d/m/Y H:i') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted mb-0">Nenhum usuário cadastrado.</p>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="col-12">
                <div class="card card-modern">
                    <div class="card-header card-header-modern d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i> Pedidos recentes</h5>
                        <a href="{{ route('orders.index') }}" class="btn btn-sm btn-primary">Ver todos</a>
                    </div>
                    <div class="card-body">
                        @if ($stats['recent_orders']->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-modern table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Atendimento</th>
                                            <th>Itens</th>
                                            <th>Total</th>
                                            <th>Pagamento</th>
                                            <th>Status</th>
                                            <th>Data</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($stats['recent_orders'] as $order)
                                            <tr>
                                                <td><strong>Atend. {{ $order->mesa }}</strong></td>
                                                <td>
                                                    @if (is_array($order->items))
                                                        @foreach (array_slice($order->items, 0, 2) as $it)
                                                            <small>{{ $it['name'] }} ({{ $it['quantity'] }})</small>
                                                            @if (!$loop->last)
                                                                ·
                                                            @endif
                                                        @endforeach
                                                        @if (count($order->items) > 2)
                                                            <small class="text-muted">+{{ count($order->items) - 2 }}</small>
                                                        @endif
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td>R$ {{ number_format($order->total_price, 2, ',', '.') }}</td>
                                                <td><span class="badge bg-secondary">{{ $order->payment_label }}</span>
                                                </td>
                                                <td><span
                                                        class="badge bg-{{ $order->status_badge }}">{{ $order->status_label }}</span>
                                                </td>
                                                <td>{{ $order->formatted_created_at }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted mb-0">Nenhum pedido cadastrado.</p>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
