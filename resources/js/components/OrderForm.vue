<template>
  <div class="order-form-vue">
    <BAlert v-if="Object.keys(errors).length" variant="danger" class="mb-3" show>
      <ul class="mb-0 list-unstyled">
        <li v-for="(msgs, field) in errors" :key="field">
          <span v-for="(m, i) in (Array.isArray(msgs) ? msgs : [msgs])" :key="i">{{ m }}</span>
        </li>
      </ul>
    </BAlert>
    <form :action="action" method="POST" @submit="onSubmit">
      <input type="hidden" name="_token" :value="csrf" />
      <input v-if="method !== 'POST'" type="hidden" name="_method" value="PUT" />
      <input type="hidden" name="mesa" :value="form.mesa" />
      <input type="hidden" name="status" :value="form.status" />
      <input type="hidden" name="payment_method" :value="form.payment_method" />
      <input type="hidden" name="notes" :value="form.notes" />

      <BRow class="mb-4">
        <BCol cols="12" md="3">
          <label for="vue-mesa" class="form-label">Mesa <span class="text-danger">*</span></label>
          <BFormSelect id="vue-mesa" v-model="form.mesa" :options="mesaOptions" required />
        </BCol>
        <BCol cols="12" md="3">
          <label for="vue-status" class="form-label">Status</label>
          <BFormSelect id="vue-status" v-model="form.status" :options="statusOptions" />
        </BCol>
        <BCol cols="12" md="3">
          <label for="vue-payment" class="form-label">Forma de pagamento <span class="text-danger">*</span></label>
          <BFormSelect id="vue-payment" v-model="form.payment_method" :options="paymentOptions" required />
        </BCol>
        <BCol cols="12" md="3">
          <label for="vue-notes" class="form-label">Observações</label>
          <BFormInput id="vue-notes" v-model="form.notes" type="text" placeholder="Ex: sem cebola" />
        </BCol>
      </BRow>

      <h5 class="border-bottom pb-2 mb-3"><i class="bi bi-egg-fried"></i> Pratos Italianos</h5>
      <div class="table-responsive mb-4">
        <table class="table table-sm">
          <thead><tr><th>Prato</th><th>Preço</th><th style="width:120px">Qtd</th></tr></thead>
          <tbody>
            <tr v-for="item in menu.pratos" :key="item.id">
              <td>{{ item.name }}</td>
              <td>R$ {{ formatPrice(item.price) }}</td>
              <td><BFormInput v-model.number="quantities[item.id]" type="number" min="0" size="sm" /></td>
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
              <td><BFormInput v-model.number="quantities[item.id]" type="number" min="0" size="sm" /></td>
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
              <td><BFormInput v-model.number="quantities[item.id]" type="number" min="0" size="sm" /></td>
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
        <BButton variant="secondary" :href="backUrl" tag="a"><i class="bi bi-arrow-left"></i> Voltar</BButton>
        <BButton type="submit" variant="success" :disabled="computedItems.length === 0">
          <i class="bi bi-save"></i> {{ submitLabel }}
        </BButton>
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

const mesaOptions = computed(() => {
  const opts = [{ value: '', text: 'Selecione a mesa' }];
  for (let m = 1; m <= props.mesasCount; m++) opts.push({ value: m, text: `Mesa ${m}` });
  return opts;
});
const statusOptions = computed(() =>
  Object.entries(props.statuses).map(([value, text]) => ({ value, text }))
);
const paymentOptions = computed(() => [
  { value: '', text: 'Selecione' },
  ...Object.entries(props.paymentMethods).map(([value, text]) => ({ value, text })),
]);

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
