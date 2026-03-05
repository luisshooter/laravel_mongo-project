@extends('layouts.app')

@section('title', 'Novo Produto')

@section('content')
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="card card-modern">
                <div class="card-header card-header-modern">
                    <h5 class="mb-0"><i class="bi bi-box-seam"></i> Cadastrar Novo Produto</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('products.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Nome do Produto <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="category" class="form-label">Categoria <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('category') is-invalid @enderror" id="category"
                                    name="category" required>
                                    <option value="">Selecione uma categoria...</option>
                                    @foreach ($categories as $key => $label)
                                        <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>
                                            {{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="price" class="form-label">Preço (R$) <span
                                        class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0"
                                    class="form-control @error('price') is-invalid @enderror" id="price" name="price"
                                    value="{{ old('price') }}" required>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="stock" class="form-label">Estoque <span class="text-danger">*</span></label>
                                <input type="number" min="0"
                                    class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock"
                                    value="{{ old('stock', 0) }}" required>
                                @error('stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4 form-check form-switch border rounded p-3 bg-light">
                            <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" id="is_active"
                                name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label pt-1" for="is_active">
                                <strong>Produto Ativo</strong>
                                <br><small class="text-muted">Apenas produtos ativos aparecem na tela de pedidos vendidos
                                    aos clientes.</small>
                            </label>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-success shadow-sm">
                                <i class="bi bi-save"></i> Salvar Produto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
