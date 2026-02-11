# Sistema de Pedidos - Laravel + MongoDB

Sistema avançado de gerenciamento de pedidos desenvolvido com Laravel 10 e MongoDB, implementando arquitetura MVC, autenticação e controle de permissões.

## 🚀 Características

- **Arquitetura MVC** completa e organizada
- **Autenticação** de usuários com sessões
- **Controle de Permissões** em 3 níveis:
  - **Nível 1 - Usuário**: Acesso aos próprios pedidos
  - **Nível 2 - Gerente**: Acesso total aos pedidos (visualizar, criar, editar, deletar)
  - **Nível 3 - Administrador**: Gerenciamento de usuários (SEM acesso aos pedidos)
- **MongoDB** como banco de dados NoSQL
- **Interface responsiva** com Bootstrap 5
- **Validação** de formulários no backend
- **Dashboard** com estatísticas personalizadas por perfil

## 📋 Pré-requisitos

- PHP >= 8.1
- Composer
- MongoDB >= 5.0
- Extensão PHP MongoDB (`php-mongodb`)

## 🔧 Instalação

### 1. Instalar dependências do Composer

```bash
composer install
```

### 2. Configurar MongoDB

Certifique-se de que o MongoDB está rodando. Edite o arquivo `.env`:

```env
DB_CONNECTION=mongodb
DB_HOST=127.0.0.1
DB_PORT=27017
DB_DATABASE=laravel_orders
DB_USERNAME=
DB_PASSWORD=
```

### 3. Gerar chave da aplicação

```bash
php artisan key:generate
```

### 4. Popular o banco de dados

```bash
php artisan db:seed
```

Isso criará 3 usuários de teste:
- **Administrador**: admin@test.com / 123456
- **Gerente**: manager@test.com / 123456
- **Usuário**: user@test.com / 123456

### 5. Iniciar o servidor

```bash
php artisan serve
```

Acesse: http://localhost:8000

## 📁 Estrutura do Projeto

```
laravel-mongo-project/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php       # Autenticação (login/logout)
│   │   │   ├── DashboardController.php  # Dashboard com estatísticas
│   │   │   ├── OrderController.php      # CRUD de pedidos
│   │   │   └── UserController.php       # CRUD de usuários
│   │   └── Middleware/
│   │       └── CheckPermission.php      # Middleware de permissões
│   └── Models/
│       ├── User.php                     # Model de usuários
│       └── Order.php                    # Model de pedidos
├── config/
│   ├── database.php                     # Configuração do MongoDB
│   └── auth.php                         # Configuração de autenticação
├── database/
│   └── seeders/
│       └── DatabaseSeeder.php           # Seed de dados de teste
├── resources/
│   └── views/
│       ├── auth/
│       │   └── login.blade.php          # Tela de login
│       ├── users/
│       │   ├── index.blade.php          # Lista de usuários
│       │   ├── create.blade.php         # Criar usuário
│       │   └── edit.blade.php           # Editar usuário
│       ├── orders/
│       │   ├── index.blade.php          # Lista de pedidos
│       │   ├── create.blade.php         # Criar pedido
│       │   ├── edit.blade.php           # Editar pedido
│       │   └── show.blade.php           # Visualizar pedido
│       ├── layouts/
│       │   └── app.blade.php            # Layout principal
│       └── dashboard.blade.php          # Dashboard
└── routes/
    └── web.php                          # Rotas da aplicação
```

## 🔐 Sistema de Permissões

### Administrador (Nível 3)
- ✅ Gerenciar usuários (criar, editar, deletar)
- ✅ Ver estatísticas de usuários no dashboard
- ❌ **NÃO tem acesso** aos pedidos

### Gerente (Nível 2)
- ✅ Ver TODOS os pedidos de todos os usuários
- ✅ Criar novos pedidos
- ✅ Editar qualquer pedido
- ✅ Deletar qualquer pedido
- ✅ Ver estatísticas de pedidos no dashboard

### Usuário (Nível 1)
- ✅ Ver apenas SEUS próprios pedidos
- ✅ Criar novos pedidos
- ✅ Editar apenas seus pedidos
- ❌ **NÃO pode** deletar pedidos
- ✅ Ver estatísticas dos seus pedidos no dashboard

## 🗂️ Funcionalidades por Módulo

### Autenticação
- Login com email e senha
- Logout seguro
- Proteção de rotas com middleware
- Sessões persistentes (remember me)

### Gerenciamento de Usuários (Admin)
- Listagem de todos os usuários
- Cadastro de novos usuários
- Edição de usuários existentes
- Ativação/desativação de usuários
- Alteração de níveis de permissão
- Exclusão de usuários

### Gerenciamento de Pedidos
- Listagem de pedidos (filtrada por permissão)
- Cadastro de novos pedidos
- Edição de pedidos
- Visualização detalhada de pedidos
- Exclusão de pedidos (apenas Gerente)
- Cálculo automático do total
- Status do pedido (Pendente, Processando, Concluído, Cancelado)

### Dashboard
- Estatísticas personalizadas por nível de permissão
- Cards com métricas importantes
- Listagem de registros recentes
- Interface responsiva e intuitiva

## 🎨 Interface

- Design moderno e responsivo
- Bootstrap 5 com ícones Bootstrap Icons
- Feedback visual (alertas de sucesso/erro)
- Navegação intuitiva
- Badges coloridos para status
- Formulários validados

## 🔒 Segurança

- Senhas criptografadas com Hash
- Proteção CSRF em todos os formulários
- Validação de dados no backend
- Middleware de autenticação
- Middleware de permissões customizado
- Sanitização de inputs

## 📊 Models e Collections MongoDB

### Users Collection
```javascript
{
  "_id": ObjectId,
  "name": String,
  "email": String (unique),
  "password": String (hashed),
  "permission_level": Integer (1, 2, 3),
  "active": Boolean,
  "created_at": DateTime,
  "updated_at": DateTime
}
```

### Orders Collection
```javascript
{
  "_id": ObjectId,
  "user_id": ObjectId,
  "customer_name": String,
  "customer_email": String,
  "product_name": String,
  "quantity": Integer,
  "unit_price": Decimal,
  "total_price": Decimal (calculado automaticamente),
  "status": String (pending, processing, completed, cancelled),
  "notes": String (opcional),
  "created_at": DateTime,
  "updated_at": DateTime
}
```

## 🛣️ Rotas Principais

### Públicas
- `GET /login` - Tela de login
- `POST /login` - Processar login

### Autenticadas
- `GET /dashboard` - Dashboard
- `POST /logout` - Logout

### Usuários (Admin apenas)
- `GET /users` - Lista de usuários
- `GET /users/create` - Formulário de cadastro
- `POST /users` - Salvar novo usuário
- `GET /users/{id}/edit` - Formulário de edição
- `PUT /users/{id}` - Atualizar usuário
- `DELETE /users/{id}` - Deletar usuário

### Pedidos (Usuários e Gerentes)
- `GET /orders` - Lista de pedidos
- `GET /orders/create` - Formulário de cadastro
- `POST /orders` - Salvar novo pedido
- `GET /orders/{id}` - Ver detalhes
- `GET /orders/{id}/edit` - Formulário de edição
- `PUT /orders/{id}` - Atualizar pedido
- `DELETE /orders/{id}` - Deletar pedido (apenas Gerente)

## 🧪 Testando o Sistema

1. **Login como Administrador**
   - Email: admin@test.com
   - Senha: 123456
   - Acesse o menu "Usuários"

2. **Login como Gerente**
   - Email: manager@test.com
   - Senha: 123456
   - Acesse o menu "Pedidos" (verá todos os pedidos)

3. **Login como Usuário**
   - Email: user@test.com
   - Senha: 123456
   - Acesse o menu "Pedidos" (verá apenas seus pedidos)

## 📝 Observações Importantes

- Administradores **NÃO** têm acesso ao módulo de pedidos
- Usuários comuns **NÃO** podem deletar pedidos
- Todos os preços são calculados automaticamente
- As validações impedem dados incorretos
- O sistema é totalmente responsivo

## 🔄 Melhorias Futuras

- [ ] Exportação de relatórios (PDF, Excel)
- [ ] Gráficos e análises avançadas
- [ ] API RESTful
- [ ] Sistema de notificações
- [ ] Logs de auditoria
- [ ] Recuperação de senha
- [ ] Upload de anexos aos pedidos
- [ ] Integração com sistemas de pagamento

## 📄 Licença

Este é um projeto de demonstração para fins educacionais.

## 👨‍💻 Autor

Desenvolvido como exemplo de sistema Laravel + MongoDB com controle de permissões avançado.
