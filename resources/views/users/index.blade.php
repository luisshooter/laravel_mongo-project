@extends('layouts.app')

@section('title', 'Usuários')

@section('content')
<div class="page-header mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="page-title"><i class="bi bi-people"></i> Usuários</h1>
            <p class="text-muted mb-0">Cadastro e edição (somente Administrador pode editar)</p>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Novo usuário
        </a>
    </div>
</div>

<div class="card card-modern">
    <div class="card-body">
        @if($users->count() > 0)
            <div class="table-responsive">
                <table class="table table-modern table-hover">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Nível de Permissão</th>
                            <th>Status</th>
                            <th>Data de Cadastro</th>
                            <th width="200">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>
                                    <i class="bi bi-person-circle"></i> {{ $user->name }}
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->permission_level == 1)
                                        <span class="badge bg-secondary">Usuário</span>
                                    @elseif($user->permission_level == 2)
                                        <span class="badge bg-primary">Gerente</span>
                                    @else
                                        <span class="badge bg-danger">Administrador</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->active)
                                        <span class="badge bg-success">Ativo</span>
                                    @else
                                        <span class="badge bg-secondary">Inativo</span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                <td class="action-buttons">
                                    @if(Auth::user()->permission_level == 3)
                                        <a href="{{ route('users.edit', $user->id) }}" 
                                           class="btn btn-sm btn-warning" 
                                           title="Editar (somente Admin)">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('users.destroy', $user->id) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-danger" 
                                                    title="Excluir (somente Admin)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted small">Somente o Administrador pode editar/excluir</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info mb-0">
                <i class="bi bi-info-circle"></i> Nenhum usuário cadastrado ainda.
            </div>
        @endif
    </div>
</div>
@endsection
