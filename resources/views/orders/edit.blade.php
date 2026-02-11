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
                <form action="{{ route('orders.update', $order->id) }}" method="POST" id="formOrder">
                    @csrf
                    @method('PUT')

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label for="mesa" class="form-label">Mesa <span class="text-danger">*</span></label>
                            <select class="form-select @error('mesa') is-invalid @enderror" id="mesa" name="mesa" required>
                                @for($m = 1; $m <= config('menu.mesas_count', 8); $m++)
                                    <option value="{{ $m }}" {{ old('mesa', $order->mesa) == $m ? 'selected' : '' }}>Mesa {{ $m }}</option>
                                @endfor
                            </select>
                            @error('mesa')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                @foreach(\App\Models\Order::getStatuses() as $key => $label)
                                    <option value="{{ $key }}" {{ old('status', $order->status) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="notes" class="form-label">Observações</label>
                            <input type="text" class="form-control" id="notes" name="notes" value="{{ old('notes', $order->notes) }}">
                        </div>
                    </div>

                    @php
                        $currentQtys = [];
                        foreach ($order->items ?? [] as $it) {
                            $currentQtys[$it['id']] = $it['quantity'];
                        }
                    @endphp

                    <h5 class="border-bottom pb-2 mb-3">Pratos Italianos</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-sm">
                            <thead><tr><th>Prato</th><th>Preço</th><th style="width:120px">Qtd</th></tr></thead>
                            <tbody>
                                @foreach(config('menu.pratos', []) as $item)
                                    <tr>
                                        <td>{{ $item['name'] }}</td>
                                        <td>R$ {{ number_format($item['price'], 2, ',', '.') }}</td>
                                        <td>
                                            <input type="number" min="0" class="form-control form-control-sm item-qty"
                                                   value="{{ $currentQtys[$item['id']] ?? 0 }}"
                                                   data-id="{{ $item['id'] }}" data-name="{{ $item['name'] }}" data-price="{{ $item['price'] }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <h5 class="border-bottom pb-2 mb-3">Drinks</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-sm">
                            <thead><tr><th>Drink</th><th>Preço</th><th style="width:120px">Qtd</th></tr></thead>
                            <tbody>
                                @foreach(config('menu.drinks', []) as $item)
                                    <tr>
                                        <td>{{ $item['name'] }}</td>
                                        <td>R$ {{ number_format($item['price'], 2, ',', '.') }}</td>
                                        <td>
                                            <input type="number" min="0" class="form-control form-control-sm item-qty"
                                                   value="{{ $currentQtys[$item['id']] ?? 0 }}"
                                                   data-id="{{ $item['id'] }}" data-name="{{ $item['name'] }}" data-price="{{ $item['price'] }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <h5 class="border-bottom pb-2 mb-3">Refrigerantes</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-sm">
                            <thead><tr><th>Refrigerante</th><th>Preço</th><th style="width:120px">Qtd</th></tr></thead>
                            <tbody>
                                @foreach(config('menu.refrigerantes', []) as $item)
                                    <tr>
                                        <td>{{ $item['name'] }}</td>
                                        <td>R$ {{ number_format($item['price'], 2, ',', '.') }}</td>
                                        <td>
                                            <input type="number" min="0" class="form-control form-control-sm item-qty"
                                                   value="{{ $currentQtys[$item['id']] ?? 0 }}"
                                                   data-id="{{ $item['id'] }}" data-name="{{ $item['name'] }}" data-price="{{ $item['price'] }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <p class="text-muted small">Total: <strong id="totalDisplay">R$ 0,00</strong></p>
                    <div id="itemsContainer"></div>

                    <hr>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('orders.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Voltar</a>
                        <button type="submit" class="btn btn-success" id="btnSubmit"><i class="bi bi-save"></i> Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.querySelectorAll('.item-qty').forEach(function(inp) { inp.addEventListener('change', buildItems); });
function buildItems() {
    var container = document.getElementById('itemsContainer');
    container.innerHTML = '';
    var total = 0, count = 0;
    document.querySelectorAll('.item-qty').forEach(function(inp) {
        var qty = parseInt(inp.value, 10) || 0;
        if (qty > 0) {
            var price = parseFloat(inp.dataset.price);
            total += qty * price;
            container.appendChild(createHidden('items[' + count + '][id]', inp.dataset.id));
            container.appendChild(createHidden('items[' + count + '][name]', inp.dataset.name));
            container.appendChild(createHidden('items[' + count + '][quantity]', qty));
            container.appendChild(createHidden('items[' + count + '][unit_price]', price));
            count++;
        }
    });
    document.getElementById('totalDisplay').textContent = 'R$ ' + total.toFixed(2).replace('.', ',');
    document.getElementById('btnSubmit').disabled = count === 0;
}
function createHidden(n, v) { var i = document.createElement('input'); i.type = 'hidden'; i.name = n; i.value = v; return i; }
buildItems();
</script>
@endsection
