<template>
  <div class="dashboard-stats row g-3 mb-4">
    <template v-if="isAdmin">
      <div class="col-sm-6 col-xl-3">
        <div class="card card-modern stat-card bg-primary text-white border-0">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <p class="text-uppercase small mb-1 opacity-90">Usuários</p>
                <h2 class="mb-0 fw-bold">{{ stats.total_users }}</h2>
              </div>
              <i class="bi bi-people stat-icon"></i>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-xl-3">
        <div class="card card-modern stat-card bg-success text-white border-0">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <p class="text-uppercase small mb-1 opacity-90">Ativos</p>
                <h2 class="mb-0 fw-bold">{{ stats.active_users }}</h2>
              </div>
              <i class="bi bi-person-check stat-icon"></i>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-xl-3">
        <div class="card card-modern stat-card bg-info text-white border-0">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <p class="text-uppercase small mb-1 opacity-90">Pedidos</p>
                <h2 class="mb-0 fw-bold">{{ stats.total_orders }}</h2>
              </div>
              <i class="bi bi-cart3 stat-icon"></i>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-xl-3">
        <div class="card card-modern stat-card bg-dark text-white border-0">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <p class="text-uppercase small mb-1 opacity-90">Nível</p>
                <h2 class="mb-0 fw-bold">Admin</h2>
              </div>
              <i class="bi bi-shield-check stat-icon"></i>
            </div>
          </div>
        </div>
      </div>
    </template>
    <template v-else>
      <div class="col-sm-6 col-xl-3">
        <div class="card card-modern stat-card bg-primary text-white border-0">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <p class="text-uppercase small mb-1 opacity-90">Pedidos</p>
                <h2 class="mb-0 fw-bold">{{ stats.total_orders }}</h2>
              </div>
              <i class="bi bi-cart3 stat-icon"></i>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-xl-3">
        <div class="card card-modern stat-card bg-warning text-dark border-0">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <p class="text-uppercase small mb-1 opacity-90">Pendentes</p>
                <h2 class="mb-0 fw-bold">{{ stats.pending_orders }}</h2>
              </div>
              <i class="bi bi-clock stat-icon"></i>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-xl-3">
        <div class="card card-modern stat-card bg-info text-white border-0">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <p class="text-uppercase small mb-1 opacity-90">Processando</p>
                <h2 class="mb-0 fw-bold">{{ stats.processing_orders }}</h2>
              </div>
              <i class="bi bi-arrow-repeat stat-icon"></i>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-xl-3">
        <div class="card card-modern stat-card bg-success text-white border-0">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <p class="text-uppercase small mb-1 opacity-90">Concluídos</p>
                <h2 class="mb-0 fw-bold">{{ stats.completed_orders }}</h2>
              </div>
              <i class="bi bi-check-circle stat-icon"></i>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
defineProps({
  stats: {
    type: Object,
    required: true,
    default: () => ({
      total_users: 0,
      active_users: 0,
      total_orders: 0,
      pending_orders: 0,
      processing_orders: 0,
      completed_orders: 0,
    }),
  },
  isAdmin: { type: Boolean, default: false },
});
</script>
