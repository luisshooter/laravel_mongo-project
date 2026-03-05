@extends('layouts.app')

@section('title', 'Produtos')

@section('content')
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title"><i class="bi bi-box-seam"></i> Produtos</h1>
                <p class="text-muted mb-0">Gerencie as flores e itens da loja</p>
            </div>
            <a href="{{ route('products.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> Novo Produto
            </a>
        </div>
    </div>

    <div class="card card-modern">
        <div class="card-body p-0">
            @if ($products->isEmpty())
                <div class="empty-state">
                    <i class="bi bi-inboxes"></i>
                    <p>Nenhum produto cadastrado ainda.</p>
                    <a href="{{ route('products.create') }}" class="btn btn-outline-primary mt-2">Cadastrar o primeiro</a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-modern table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Nome</th>
                                <th>Categoria</th>
                                <th>Preço</th>
                                <th>Estoque</th>
                                <th>Status</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td>
                                        <strong>{{ $product->name }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $product->category_label }}</span>
                                    </td>
                                    <td>R$ {{ number_format($product->price, 2, ',', '.') }}</td>
                                    <td>
                                        @if ($product->stock <= 0)
                                            <span class="badge bg-danger">Esgotado</span>
                                        @else
                                            {{ $product->stock }} un.
                                        @endif
                                    </td>
                                    <td>
                                        @if ($product->is_active)
                                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> Ativo</span>
                                        @else
                                            <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Inativo</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="{{ route('products.edit', $product->id) }}"
                                                class="btn btn-sm btn-outline-primary" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Tem certeza que deseja remover este produto?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    title="Excluir">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
