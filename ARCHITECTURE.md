# 🏗️ Arquitetura do Sistema

## Visão Geral

Este projeto segue rigorosamente o padrão **MVC (Model-View-Controller)** do Laravel, com algumas adaptações para trabalhar com MongoDB.

## Camadas da Aplicação

### 1. Models (Camada de Dados)

**Localização**: `app/Models/`

#### User.php
- Responsabilidade: Gerenciar dados de usuários
- Conexão: MongoDB (collection 'users')
- Características:
  - Implementa autenticação
  - Hash automático de senhas
  - Validação de permissões
  - Relacionamento com pedidos

#### Order.php
- Responsabilidade: Gerenciar dados de pedidos
- Conexão: MongoDB (collection 'orders')
- Características:
  - Cálculo automático de totais
  - Gerenciamento de status
  - Relacionamento com usuários
  - Formatação de dados

### 2. Controllers (Camada de Lógica)

**Localização**: `app/Http/Controllers/`

#### AuthController.php
- **Responsabilidade**: Autenticação de usuários
- **Métodos**:
  - `showLogin()`: Exibe formulário de login
  - `login()`: Processa login
  - `logout()`: Encerra sessão

#### UserController.php
- **Responsabilidade**: CRUD de usuários (apenas Admin)
- **Métodos**:
  - `index()`: Lista usuários
  - `create()`: Formulário de criação
  - `store()`: Salva novo usuário
  - `edit()`: Formulário de edição
  - `update()`: Atualiza usuário
  - `destroy()`: Remove usuário

#### OrderController.php
- **Responsabilidade**: CRUD de pedidos (Usuários e Gerentes)
- **Métodos**:
  - `index()`: Lista pedidos (filtrado por permissão)
  - `create()`: Formulário de criação
  - `store()`: Salva novo pedido
  - `show()`: Exibe detalhes
  - `edit()`: Formulário de edição
  - `update()`: Atualiza pedido
  - `destroy()`: Remove pedido (apenas Gerente)

#### DashboardController.php
- **Responsabilidade**: Dashboard personalizado por perfil
- **Métodos**:
  - `index()`: Exibe estatísticas baseadas em permissões

### 3. Views (Camada de Apresentação)

**Localização**: `resources/views/`

#### Layout Principal
- `layouts/app.blade.php`: Template base com navegação

#### Módulos
- **auth/**: Telas de autenticação
- **users/**: CRUD de usuários
- **orders/**: CRUD de pedidos
- **dashboard.blade.php**: Dashboard

### 4. Middleware (Camada de Proteção)

**Localização**: `app/Http/Middleware/`

#### CheckPermission.php
- **Responsabilidade**: Controlar acesso por nível de permissão
- **Uso**: Proteger rotas específicas

### 5. Routes (Camada de Roteamento)

**Localização**: `routes/web.php`

Organização:
```
Públicas
├── Login

Autenticadas
├── Dashboard
├── Logout
├── Usuários (Admin apenas)
└── Pedidos (Usuários e Gerentes)
```

## Fluxo de Dados

### Exemplo: Criar um Pedido

```
1. User acessa /orders/create
   ↓
2. Router verifica autenticação (middleware 'auth')
   ↓
3. Router verifica permissão (middleware 'check.permission:1,2')
   ↓
4. OrderController::create() é executado
   ↓
5. View orders/create.blade.php é renderizada
   ↓
6. User preenche formulário e submete
   ↓
7. POST /orders é enviado
   ↓
8. OrderController::store() valida dados
   ↓
9. Model Order::create() salva no MongoDB
   ↓
10. Redirect para /orders com mensagem de sucesso
```

## Sistema de Permissões

### Implementação em Camadas

#### 1. Model Layer (User.php)
```php
- hasMaxPermission(): bool
- canAccessOrders(): bool
- getPermissionNameAttribute(): string
```

#### 2. Middleware Layer (CheckPermission.php)
```php
- Verifica autenticação
- Valida nível de permissão
- Redireciona se não autorizado
```

#### 3. Controller Layer
```php
// Verificação adicional
if (Auth::user()->hasMaxPermission()) {
    return redirect()->route('dashboard')
        ->with('error', 'Sem permissão');
}
```

#### 4. View Layer
```blade
@if(Auth::user()->hasMaxPermission())
    {{-- Menu Admin --}}
@else
    {{-- Menu Usuários/Gerentes --}}
@endif
```

## Integração com MongoDB

### Driver
- Package: `mongodb/laravel-mongodb`
- Versão: ^4.0

### Configuração
```php
// config/database.php
'connections' => [
    'mongodb' => [
        'driver' => 'mongodb',
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', 27017),
        'database' => env('DB_DATABASE'),
        // ...
    ],
]
```

### Models
```php
use MongoDB\Laravel\Eloquent\Model;

class User extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'users';
    // ...
}
```

## Segurança

### 1. Autenticação
- Sessões gerenciadas pelo Laravel
- Senhas com hash bcrypt
- CSRF protection em formulários

### 2. Autorização
- Middleware personalizado
- Verificação em múltiplas camadas
- Redirecionamentos seguros

### 3. Validação
- Backend validation em todos os forms
- Mensagens de erro traduzidas
- Sanitização de inputs

### 4. SQL/NoSQL Injection
- Eloquent ORM protege contra injeção
- Queries parametrizadas
- Validação de tipos

## Padrões de Código

### PSR-12
- Seguido em todos os arquivos PHP
- Indentação: 4 espaços
- Namespaces organizados

### Blade Templates
- Diretivas Laravel (@if, @foreach, etc.)
- Components reutilizáveis
- Escape automático de XSS

### Comentários
- Docblocks em métodos públicos
- Comentários explicativos em lógica complexa

## Escalabilidade

### Horizontal
- MongoDB suporta sharding
- Stateless controllers
- Cache de queries possível

### Vertical
- Otimização de queries
- Eager loading para relações
- Indexes no MongoDB

## Manutenibilidade

### Separação de Responsabilidades
- Controllers magros
- Models ricos em lógica de negócio
- Views apenas para apresentação

### Código Reutilizável
- Traits para funcionalidades comuns
- Helper functions
- Middleware compartilhado

### Testabilidade
- Injeção de dependências
- Interfaces para contratos
- Seeders para dados de teste

## Próximas Melhorias Arquiteturais

1. **Repository Pattern**: Abstrair acesso ao banco
2. **Service Layer**: Lógica de negócio complexa
3. **Events & Listeners**: Para ações assíncronas
4. **Jobs & Queues**: Processos em background
5. **API RESTful**: Separar frontend/backend
6. **Cache Layer**: Redis para performance
7. **Logs Estruturados**: Monitoramento avançado

## Conclusão

Esta arquitetura fornece:
- ✅ Separação clara de responsabilidades
- ✅ Facilidade de manutenção
- ✅ Segurança em múltiplas camadas
- ✅ Escalabilidade
- ✅ Testabilidade
- ✅ Código limpo e organizado
