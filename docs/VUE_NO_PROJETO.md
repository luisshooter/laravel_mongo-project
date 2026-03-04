# Vue.js no projeto

O projeto usa **Vue 3** com **Vite** e **Bootstrap-Vue-3** (Bootstrap 5 como componentes Vue) integrados ao Laravel. O Vue gerencia parte das telas: formulário de pedidos (criar/editar) e os cards de estatísticas do dashboard.

## Onde o Vue é usado

- **Pedidos – Criar / Editar** (`/orders/create`, `/orders/edit`): formulário completo (mesa, status, pagamento, itens do cardápio, total em tempo real) é o componente `OrderForm.vue`. O envio continua sendo POST/PUT para o Laravel; a validação e erros são exibidos pelo Vue.
- **Dashboard** (`/dashboard`): os quatro cards de estatísticas (Usuários/Ativos/Pedidos ou Pedidos/Pendentes/Processando/Concluídos) são renderizados pelo componente `DashboardStats.vue` com dados passados pelo Blade.

## Instalação

```bash
npm install
```

## Desenvolvimento

Com o servidor Laravel rodando (`php artisan serve`), em outro terminal:

```bash
npm run dev
```

O Vite observa alterações em `resources/js` e `resources/views`.

## Build para produção

```bash
npm run build
```

Os arquivos são gerados em `public/build`. O helper `@vite()` no Blade carrega esses arquivos.

## Bootstrap + Vue

Foi instalado o **bootstrap-vue-3**, que fornece componentes Vue para Bootstrap 5. O plugin é registrado em `app.js` com `createApp(...).use(BootstrapVue3)`.

**Componentes usados:**
- **OrderForm:** `BAlert`, `BRow`, `BCol`, `BFormSelect`, `BFormInput`, `BButton`
- **DashboardStats:** `BRow`, `BCol`, `BCard`, `BCardBody`

O layout continua carregando o Bootstrap 5 por CDN; o CSS do bootstrap-vue-3 é importado no bundle para estilos dos componentes. Você pode usar qualquer componente do [bootstrap-vue-3](https://github.com/cdmoro/bootstrap-vue-3) (BModal, BDropdown, BTable, etc.) nos seus `.vue`.

## Estrutura

- `resources/js/app.js` – Entrada: importa BootstrapVue3 e CSS; monta `OrderForm` em `#order-form-app`, `DashboardStats` em `#dashboard-vue`, ou `App` em `#app`.
- `resources/js/App.vue` – Componente padrão quando não há outro mount point.
- `resources/js/components/OrderForm.vue` – Formulário de pedido (create/edit) com componentes Bootstrap-Vue.
- `resources/js/components/DashboardStats.vue` – Cards de estatísticas do dashboard com BCard/BRow/BCol.

## Usando componentes

Em `App.vue` (ou em qualquer `.vue`):

```vue
<script setup>
import MeuComponente from '@/components/MeuComponente.vue';
</script>
<template>
  <MeuComponente />
</template>
```

O alias `@` aponta para `resources/js`.

## Dados do Laravel no Vue

Para passar dados do Blade para o Vue, use atributos no elemento `#app`:

```blade
<div id="app" data-user="{{ json_encode(auth()->user()) }}"></div>
```

No Vue, leia em `onMounted` com `document.getElementById('app').dataset.user` ou use um global.

## Rotas e API

Para chamar rotas Laravel a partir do Vue (axios/fetch):

- URLs: use `route('nome')` no Blade e passe ao Vue, ou defina um objeto de rotas em uma tag `script` com `window.routes`.
- API: pode usar as rotas em `routes/api.php` ou as mesmas rotas web com sessão/CSRF (inclua o token no cabeçalho ou no body).
