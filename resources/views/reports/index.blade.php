@extends('layouts.app')

@section('title', 'Relatórios')

@section('content')
<div class="page-header mb-4">
    <h1 class="page-title"><i class="bi bi-graph-up-arrow"></i> Relatórios</h1>
    <p class="text-muted mb-0">Valores reais de vendas, lucros e encerramento de mesas</p>
</div>

{{-- Filtro por período --}}
<div class="card card-modern mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Data inicial</label>
                <input type="date" name="date_from" class="form-control" value="{{ $dateFrom?->format('Y-m-d') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Data final</label>
                <input type="date" name="date_to" class="form-control" value="{{ $dateTo?->format('Y-m-d') }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary me-2"><i class="bi bi-funnel"></i> Filtrar</button>
                <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">Limpar</a>
            </div>
        </form>
    </div>
</div>

{{-- Lucros e resumo rápido --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-modern card-accent-success h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-uppercase small fw-semibold text-muted mb-1">Lucros (vendido)</p>
                        <h2 class="mb-0 fw-bold">R$ {{ number_format($lucros, 2, ',', '.') }}</h2>
                        <small class="text-muted">Pedidos concluídos / mesa encerrada</small>
                    </div>
                    <i class="bi bi-currency-dollar display-6 opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-modern h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-uppercase small fw-semibold text-muted mb-1">Hoje</p>
                        <h2 class="mb-0 fw-bold">R$ {{ number_format($hoje, 2, ',', '.') }}</h2>
                    </div>
                    <i class="bi bi-calendar-day display-6 opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-modern h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-uppercase small fw-semibold text-muted mb-1">Esta semana</p>
                        <h2 class="mb-0 fw-bold">R$ {{ number_format($semana, 2, ',', '.') }}</h2>
                    </div>
                    <i class="bi bi-calendar-week display-6 opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-modern h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-uppercase small fw-semibold text-muted mb-1">Este mês</p>
                        <h2 class="mb-0 fw-bold">R$ {{ number_format($mes, 2, ',', '.') }}</h2>
                    </div>
                    <i class="bi bi-calendar-month display-6 opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Vendido x Não vendido + Por status --}}
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card card-modern h-100">
            <div class="card-header card-header-modern">
                <h5 class="mb-0"><i class="bi bi-pie-chart"></i> Vendido x Não vendido</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><span class="badge bg-success me-2">Vendido</span> Concluídos</span>
                        <strong>{{ $vendidos }} pedidos · R$ {{ number_format($lucros, 2, ',', '.') }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><span class="badge bg-warning me-2">Aberto</span> Pendente / Processando</span>
                        <strong>{{ $naoVendidos }} pedidos</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><span class="badge bg-danger me-2">Cancelado</span></span>
                        <strong>{{ $cancelados }} pedidos</strong>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-modern h-100">
            <div class="card-header card-header-modern">
                <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Pedidos por status</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @foreach($porStatus as $label => $qtd)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $label }}
                            <span class="badge bg-secondary rounded-pill">{{ $qtd }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- Itens mais vendidos --}}
<div class="card card-modern mb-4">
    <div class="card-header card-header-modern">
        <h5 class="mb-0"><i class="bi bi-trophy"></i> Itens mais vendidos (concluídos)</h5>
    </div>
    <div class="card-body">
        @if(count($itensVendidos) > 0)
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead><tr><th>Item</th><th>Quantidade</th><th>Valor total</th></tr></thead>
                    <tbody>
                        @foreach(array_slice($itensVendidos, 0, 15, true) as $nome => $dados)
                            <tr>
                                <td>{{ $nome }}</td>
                                <td>{{ $dados['qtd'] }}</td>
                                <td>R$ {{ number_format($dados['valor'], 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted mb-0">Nenhum item vendido no período.</p>
        @endif
    </div>
</div>

{{-- Por mesa + Encerrar mesa --}}
<div class="card card-modern">
    <div class="card-header card-header-modern">
        <h5 class="mb-0"><i class="bi bi-table"></i> Por mesa · Encerrar mesa lança nos lucros</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @for($mesa = 1; $mesa <= $mesasCount; $mesa++)
                @php $d = $porMesa[$mesa] ?? ['pedidos' => collect(), 'total' => 0, 'concluidos_valor' => 0, 'quantidade' => 0, 'tem_abertos' => false]; @endphp
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="card card-modern h-100 {{ $d['tem_abertos'] ? 'border-warning' : '' }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0 fw-bold">Mesa {{ $mesa }}</h6>
                                @if($d['tem_abertos'])
                                    <a href="{{ route('reports.fechar-venda', $mesa) }}" class="btn btn-sm btn-success"><i class="bi bi-cash-coin"></i> Fechar venda</a>
                                @else
                                    <span class="badge bg-success">Disponível</span>
                                @endif
                            </div>
                            <p class="small text-muted mb-1">{{ $d['quantidade'] }} pedido(s)</p>
                            <p class="mb-0"><strong>R$ {{ number_format($d['total'], 2, ',', '.') }}</strong></p>
                            <p class="small text-success mb-0">Já nos lucros: R$ {{ number_format($d['concluidos_valor'], 2, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</div>
@endsection
