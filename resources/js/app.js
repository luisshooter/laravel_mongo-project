import { createApp } from 'vue';
import App from './App.vue';
import OrderForm from './components/OrderForm.vue';
import DashboardStats from './components/DashboardStats.vue';

function mountVue() {
  const orderFormEl = document.getElementById('order-form-app');
  if (orderFormEl) {
    const propsRaw = orderFormEl.getAttribute('data-props');
    const props = propsRaw ? JSON.parse(propsRaw) : {};
    createApp(OrderForm, props).mount(orderFormEl);
    return;
  }

  const dashboardEl = document.getElementById('dashboard-vue');
  if (dashboardEl) {
    const propsRaw = dashboardEl.getAttribute('data-props');
    const props = propsRaw ? JSON.parse(propsRaw) : {};
    createApp(DashboardStats, props).mount(dashboardEl);
    return;
  }

  const appEl = document.getElementById('app');
  if (appEl) {
    createApp(App).mount(appEl);
  }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', mountVue);
} else {
  mountVue();
}
