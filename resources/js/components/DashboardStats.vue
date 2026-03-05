<template>
  <div class="dashboard-stats mb-4">
    <BRow class="g-3">
      <template v-if="isAdmin">
        <BCol sm="6" xl="3">
          <BCard class="card-modern stat-card border-0" bg-variant="primary" text-variant="white">
            <BCardBody>
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <p class="text-uppercase small mb-1 opacity-90">Usuários</p>
                  <h2 class="mb-0 fw-bold">{{ stats.total_users }}</h2>
                </div>
                <i class="bi bi-people stat-icon"></i>
              </div>
            </BCardBody>
          </BCard>
        </BCol>
        <BCol sm="6" xl="3">
          <BCard class="card-modern stat-card border-0" bg-variant="success" text-variant="white">
            <BCardBody>
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <p class="text-uppercase small mb-1 opacity-90">Ativos</p>
                  <h2 class="mb-0 fw-bold">{{ stats.active_users }}</h2>
                </div>
                <i class="bi bi-person-check stat-icon"></i>
              </div>
            </BCardBody>
          </BCard>
        </BCol>
        <BCol sm="6" xl="3">
          <BCard class="card-modern stat-card border-0" bg-variant="info" text-variant="white">
            <BCardBody>
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <p class="text-uppercase small mb-1 opacity-90">Vendas (Pedidos)</p>
                  <h2 class="mb-0 fw-bold">{{ stats.total_orders }}</h2>
                </div>
                <i class="bi bi-shop stat-icon"></i>
              </div>
            </BCardBody>
          </BCard>
        </BCol>
        <BCol sm="6" xl="3">
          <BCard class="card-modern stat-card border-0" bg-variant="dark" text-variant="white">
            <BCardBody>
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <p class="text-uppercase small mb-1 opacity-90">Nível</p>
                  <h2 class="mb-0 fw-bold">Admin</h2>
                </div>
                <i class="bi bi-shield-check stat-icon"></i>
              </div>
            </BCardBody>
          </BCard>
        </BCol>
      </template>
      <template v-else>
        <BCol sm="6" xl="3">
          <BCard class="card-modern stat-card border-0" bg-variant="primary" text-variant="white">
            <BCardBody>
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <p class="text-uppercase small mb-1 opacity-90">Vendas (Pedidos)</p>
                  <h2 class="mb-0 fw-bold">{{ stats.total_orders }}</h2>
                </div>
                <i class="bi bi-shop stat-icon"></i>
              </div>
            </BCardBody>
          </BCard>
        </BCol>
        <BCol sm="6" xl="3">
          <BCard class="card-modern stat-card border-0" bg-variant="warning" text-variant="dark">
            <BCardBody>
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <p class="text-uppercase small mb-1 opacity-90">Pendentes</p>
                  <h2 class="mb-0 fw-bold">{{ stats.pending_orders }}</h2>
                </div>
                <i class="bi bi-clock stat-icon"></i>
              </div>
            </BCardBody>
          </BCard>
        </BCol>
        <BCol sm="6" xl="3">
          <BCard class="card-modern stat-card border-0" bg-variant="info" text-variant="white">
            <BCardBody>
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <p class="text-uppercase small mb-1 opacity-90">Montando / Preparando</p>
                  <h2 class="mb-0 fw-bold">{{ stats.processing_orders }}</h2>
                </div>
                <i class="bi bi-flower2 stat-icon"></i>
              </div>
            </BCardBody>
          </BCard>
        </BCol>
        <BCol sm="6" xl="3">
          <BCard class="card-modern stat-card border-0" bg-variant="success" text-variant="white">
            <BCardBody>
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <p class="text-uppercase small mb-1 opacity-90">Concluídos</p>
                  <h2 class="mb-0 fw-bold">{{ stats.completed_orders }}</h2>
                </div>
                <i class="bi bi-check-circle stat-icon"></i>
              </div>
            </BCardBody>
          </BCard>
        </BCol>
      </template>
    </BRow>
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
