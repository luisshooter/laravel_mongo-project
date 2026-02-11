@extends('layouts.app')

@section('title', 'Editar Usuário')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="bi bi-pencil"></i> Editar Usuário</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Nome Completo <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $user->name) }}"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $user->email) }}"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Nova Senha</label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password">
                            <div class="form-text">Deixe em branco para manter a senha atual</div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Confirmar Nova Senha</label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password_confirmation" 
                                   name="password_confirmation">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="permission_level" class="form-label">Nível de Permissão <span class="text-danger">*</span></label>
                        <select class="form-select @error('permission_level') is-invalid @enderror" 
                                id="permission_level" 
                                name="permission_level" 
                                required>
                            <option value="">Selecione...</option>
                            <option value="1" {{ old('permission_level', $user->permission_level) == 1 ? 'selected' : '' }}>
                                Usuário (Acesso a Pedidos)
                            </option>
                            <option value="2" {{ old('permission_level', $user->permission_level) == 2 ? 'selected' : '' }}>
                                Gerente (Acesso Total a Pedidos)
                            </option>
                            <option value="3" {{ old('permission_level', $user->permission_level) == 3 ? 'selected' : '' }}>
                                Administrador (Gerenciar Usuários)
                            </option>
                        </select>
                        @error('permission_level')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" 
                               class="form-check-input" 
                               id="active" 
                               name="active"
                               {{ old('active', $user->active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="active">
                            Usuário Ativo
                        </label>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Voltar
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save"></i> Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
