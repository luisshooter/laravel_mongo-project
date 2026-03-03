<template>
  <div class="order-form-vue">
    <div v-if="Object.keys(errors).length" class="alert alert-danger mb-3">
      <ul class="mb-0 list-unstyled">
        <li v-for="(msgs, field) in errors" :key="field">
          <span v-for="(m, i) in (Array.isArray(msgs) ? msgs : [msgs])" :key="i">{{ m }}</span>
        </li>
      </ul>
    </div>
    <form :action="action" method="POST" @submit="onSubmit">
      <input type="hidden" name="_token" :value="csrf" />
      <input v-if="method !== 'POST'" type="hidden" name="_method" value="PUT" />

      <div class="row mb-4">
        <div class="col-md-3">
          <label for="vue-mesa" class="form-label">Mesa <span class="text-danger">*</span></label>
          <select id="vue-mesa" v-model="form.mesa" name="mesa" class="form-select" required>
            <option value="">Selecione a mesa</option>
            <option v-for="m in mesasCount" :key="m" :value="m">Mesa {{ m }}</option>
          </select>
        </div>
        <div class="col-md-3">
          <label for="vue-status" class="form-label">Status</label>
          <select id="vue-status" v-model="form.status" name="status" class="form-select">
            <option v-for="(label, key) in statuses" :key="key" :value="key">{{ label }}</option>
          </select>
        </div>
        <div class="col-md-3">
          <label for="vue-payment" class="form-label">Forma de pagamento <span class="text-danger">*</span></label>
          <select id="vue-payment" v-model="form.payment_method" name="payment_method" class="form-select" required>
            <option value="">Selecione</option>
            <option v-for="(label, key) in paymentMethods" :key="key" :value="key">{{ label }}</option>
          </select>
        </div>
        <div class="col-md-3">
          <label for="vue-notes" class="form-label">Observações</label>
          <input id="vue-notes" v-model="form.notes" type="text" name="notes" class="form-control" placeholder="Ex: sem cebola" />
        </div>
      </div>

      <h5 class="border-bottom pb-2 mb-3"><i class="bi bi-egg-fried"></i> Pratos Italianos</h5>
      <div class="table-responsive mb-4">
        <table class="table table-sm">
          <thead><tr><th>Prato</th><th>Preço</th><th style="width:120px">Qtd</th></tr></thead>
          <tbody>
            <tr v-for="item in menu.pratos" :key="item.id">
              <td>{{ item.name }}</td>
              <td>R$ {{ formatPrice(item.price) }}</td>
              <td>
                <input
                  v-model.number="quantities[item.id]"
                  type="number"
                  min="0"
                  class="form-control form-control-sm"
                />
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <h5 class="border-bottom pb-2 mb-3"><i class="bi bi-cup-straw"></i> Drinks</h5>
      <div class="table-responsive mb-4">
        <table class="table table-sm">
          <thead><tr><th>Drink</th><th>Preço</th><th style="width:120px">Qtd</th></tr></thead>
          <tbody>
            <tr v-for="item in menu.drinks" :key="item.id">
              <td>{{ item.name }}</td>
              <td>R$ {{ formatPrice(item.price) }}</td>
              <td>
                <input
                  v-model.number="quantities[item.id]"
                  type="number"
                  min="0"
                  class="form-control form-control-sm"
                />
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <h5 class="border-bottom pb-2 mb-3"><i class="bi bi-droplet"></i> Refrigerantes</h5>
      <div class="table-responsive mb-4">
        <table class="table table-sm">
          <thead><tr><th>Refrigerante</th><th>Preço</th><th style="width:120px">Qtd</th></tr></thead>
          <tbody>
            <tr v-for="item in menu.refrigerantes" :key="item.id">
              <td>{{ item.name }}</td>
              <td>R$ {{ formatPrice(item.price) }}</td>
              <td>
                <input
                  v-model.number="quantities[item.id]"
                  type="number"
                  min="0"
                  class="form-control form-control-sm"
                />
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <p class="text-muted small">Total: <strong>{{ formattedTotal }}</strong></p>
      <template v-for="(item, index) in computedItems" :key="index">
        <input type="hidden" :name="`items[${index}][id]`" :value="item.id" />
        <input type="hidden" :name="`items[${index}][name]`" :value="item.name" />
        <input type="hidden" :name="`items[${index}][quantity]`" :value="item.quantity" />
        <input type="hidden" :name="`items[${index}][unit_price]`" :value="item.unit_price" />
      </template>

      <hr />
      <div class="d-flex justify-content-between">
        <a :href="backUrl" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Voltar</a>
        <button type="submit" class="btn btn-success" :disabled="computedItems.length === 0">
          <i class="bi bi-save"></i> {{ submitLabel }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';

const props = defineProps({
  action: { type: String, required: true },
  csrf: { type: String, required: true },
  method: { type: String, default: 'POST' },
  backUrl: { type: String, required: true },
  submitLabel: { type: String, default: 'Cadastrar Pedido' },
  mesasCount: { type: Number, default: 8 },
  statuses: { type: Object, default: () => ({}) },
  paymentMethods: { type: Object, default: () => ({}) },
  menu: {
    type: Object,
    default: () => ({ pratos: [], drinks: [], refrigerantes: [] }),
  },
  initial: {
    type: Object,
    default: () => ({
      mesa: '',
      status: 'pending',
      payment_method: 'dinheiro',
      notes: '',
      itemsById: {},
    }),
  },
  errors: { type: Object, default: () => ({}) },
});

const form = reactive({
  mesa: props.initial.mesa || '',
  status: props.initial.status || 'pending',
  payment_method: props.initial.payment_method || 'dinheiro',
  notes: props.initial.notes || '',
});

const quantities = ref({});

function initQuantities() {
  const all = [...(props.menu.pratos || []), ...(props.menu.drinks || []), ...(props.menu.refrigerantes || [])];
  const next = {};
  all.forEach((item) => {
    next[item.id] = props.initial.itemsById && props.initial.itemsById[item.id] != null
      ? props.initial.itemsById[item.id]
      : 0;
  });
  quantities.value = next;
}
initQuantities();

const computedItems = computed(() => {
  const items = [];
  const q = quantities.value;
  const all = [
    ...(props.menu.pratos || []),
    ...(props.menu.drinks || []),
    ...(props.menu.refrigerantes || []),
  ];
  all.forEach((item) => {
    const qty = Number(q[item.id]) || 0;
    if (qty > 0) {
      items.push({
        id: item.id,
        name: item.name,
        quantity: qty,
        unit_price: Number(item.price),
      });
    }
  });
  return items;
});

const total = computed(() => {
  return computedItems.value.reduce((acc, it) => acc + it.quantity * it.unit_price, 0);
});

const formattedTotal = computed(() => {
  return 'R$ ' + total.value.toFixed(2).replace('.', ',');
});

function formatPrice(price) {
  return Number(price).toFixed(2).replace('.', ',');
}

function onSubmit() {
  // formulário submete normalmente; Laravel processa
}
</script>
