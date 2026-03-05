<template>
  <div class="order-form-vue">
    <form :action="action" method="POST" @submit="onSubmit">
      <input type="hidden" name="_token" :value="csrf" />
      <input v-if="method !== 'POST'" type="hidden" name="_method" value="PUT" />
      <input type="hidden" name="customer_name" :value="form.customer_name" />
      <input type="hidden" name="customer_cpf" :value="form.customer_cpf" />
      <input type="hidden" name="customer_address" :value="form.customer_address" />
      <input type="hidden" name="status" :value="form.status" />
      <input type="hidden" name="payment_method" :value="form.payment_method" />
      <input type="hidden" name="notes" :value="form.notes" />

      <!-- Render the hidden inputs for the items list to send to Laravel -->
      <template v-for="(qty, id) in quantities" :key="'hidden-'+id">
        <template v-if="qty > 0">
           <input type="hidden" :name="`items[${id}][id]`" :value="id" />
           <input type="hidden" :name="`items[${id}][name]`" :value="getItem(id).name" />
           <input type="hidden" :name="`items[${id}][quantity]`" :value="qty" />
           <input type="hidden" :name="`items[${id}][unit_price]`" :value="getItem(id).price" />
        </template>
      </template>

      <h5 class="border-bottom pb-2 mb-3"><i class="bi bi-person-lines-fill"></i> Dados do Cliente</h5>
      <BRow class="mb-4">
        <BCol cols="12" md="4">
          <label for="vue-customer_name" class="form-label">Nome do Cliente <span class="text-danger">*</span></label>
          <BFormInput id="vue-customer_name" v-model="form.customer_name" type="text" placeholder="Nome completo" required />
        </BCol>
        <BCol cols="12" md="4">
          <label for="vue-customer_cpf" class="form-label">CPF</label>
          <BFormInput id="vue-customer_cpf" v-model="form.customer_cpf" type="text" placeholder="Opcional" />
        </BCol>
        <BCol cols="12" md="4">
          <label for="vue-customer_address" class="form-label">Endereço de Entrega</label>
          <BFormInput id="vue-customer_address" v-model="form.customer_address" type="text" placeholder="Rua, Número, Bairro" />
        </BCol>
      </BRow>

      <h5 class="border-bottom pb-2 mb-3"><i class="bi bi-basket"></i> Produtos Disponíveis</h5>

      <template v-for="(group, key) in menu" :key="key">
        <h6 class="mt-4 mb-2 text-primary">
          <i class="bi bi-tag-fill me-2"></i> {{ group.label }}
        </h6>
        <div class="table-responsive mb-4">
          <table class="table table-sm table-modern align-middle">
            <thead class="bg-light">
              <tr>
                <th>Item</th>
                <th>Preço</th>
                <th>Estoque</th>
                <th style="width:140px">Quantidade</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="!group.items.length">
                <td colspan="4" class="text-muted text-center py-3">Nenhum item nesta categoria.</td>
              </tr>
              <tr v-for="item in group.items" :key="item.id">
                <td>{{ item.name }}</td>
                <td>R$ {{ formatPrice(item.price) }}</td>
                <td>
                  <span v-if="item.stock > 0 || quantities[item.id] > 0" class="badge bg-success">
                     {{ item.stock }} dispon.
                     <span v-if="initial.itemsById && initial.itemsById[item.id]">
                       ({{ initial.itemsById[item.id] }} no ped.)
                     </span>
                  </span>
                  <span v-else class="badge bg-danger">Esgotado</span>
                </td>
                <td>
                  <BFormInput
                    v-model.number="quantities[item.id]"
                    type="number"
                    min="0"
                    :max="item.stock + (initial.itemsById && initial.itemsById[item.id] ? initial.itemsById[item.id] : 0)"
                    :disabled="item.stock <= 0 && !(initial.itemsById && initial.itemsById[item.id] > 0)"
                    size="sm"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </template>

      <p class="text-end text-muted small mb-4">
        Total do Pedido: <strong class="fs-5 text-dark">R$ {{ formatPrice(totalPrice) }}</strong>
      </p>

      <h5 class="border-bottom pb-2 mb-3"><i class="bi bi-gear"></i> Detalhes Finais</h5>
      <BRow class="mb-4">
        <BCol cols="12" md="4">
          <label for="vue-status" class="form-label">Status do Pedido <span class="text-danger">*</span></label>
          <BFormSelect id="vue-status" v-model="form.status" :options="statusOptions" required />
        </BCol>
        <BCol cols="12" md="4">
          <label for="vue-payment" class="form-label">Forma de pagamento <span class="text-danger">*</span></label>
          <BFormSelect id="vue-payment" v-model="form.payment_method" :options="paymentOptions" required />
        </BCol>
        <BCol cols="12" md="4">
          <label for="vue-notes" class="form-label">Observações</label>
          <BFormInput id="vue-notes" v-model="form.notes" type="text" placeholder="Ex: Entregar à tarde" />
        </BCol>
      </BRow>

      <hr />
      
      <div v-if="localErrors.length" class="alert alert-danger mb-4">
        <ul class="mb-0">
          <li v-for="(err, i) in localErrors" :key="i">{{ err }}</li>
        </ul>
      </div>

      <div class="d-flex justify-content-between">
        <BButton tag="a" :href="backUrl" variant="outline-secondary">
          <i class="bi bi-arrow-left"></i> Cancelar
        </BButton>
        <BButton type="submit" variant="success" class="shadow-sm">
          <i class="bi bi-save"></i> {{ submitLabel }}
        </BButton>
      </div>

    </form>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';

const props = defineProps({
  csrf: { type: String, required: true },
  action: { type: String, required: true },
  method: { type: String, default: 'POST' },
  backUrl: { type: String, required: true },
  submitLabel: { type: String, default: 'Cadastrar Pedido' },
  statuses: { type: Object, default: () => ({}) },
  paymentMethods: { type: Object, default: () => ({}) },
  menu: {
    type: Object,
    default: () => ({})
  },
  initial: {
    type: Object,
    default: () => ({
      customer_name: '',
      customer_cpf: '',
      customer_address: '',
      status: 'pending',
      payment_method: 'dinheiro',
      notes: '',
      itemsById: {}
    })
  },
  errors: {
    type: Object,
    default: () => ({})
  }
});

const form = reactive({
  customer_name: props.initial?.customer_name || '',
  customer_cpf: props.initial?.customer_cpf || '',
  customer_address: props.initial?.customer_address || '',
  status: props.initial?.status || 'pending',
  payment_method: props.initial?.payment_method || 'dinheiro',
  notes: props.initial?.notes || '',
});

const statusOptions = computed(() =>
  Object.entries(props.statuses).map(([value, text]) => ({ value, text }))
);

const paymentOptions = computed(() =>
  Object.entries(props.paymentMethods).map(([value, text]) => ({ value, text }))
);

const localErrors = ref([]);
if (props.errors) {
  Object.values(props.errors).forEach(msgArray => {
    msgArray.forEach(msg => localErrors.value.push(msg));
  });
}

// Map component quantities. Default to whatever is in initial.itemsById.
const quantities = reactive({});
Object.values(props.menu).forEach(group => {
  group.items.forEach(item => {
    quantities[item.id] = props.initial.itemsById && props.initial.itemsById[item.id] 
                          ? props.initial.itemsById[item.id] 
                          : 0;
  });
});

// A flat lookup mapping for quick price/name access
const itemsLookup = computed(() => {
  const lookup = {};
  Object.values(props.menu).forEach(group => {
    group.items.forEach(item => {
      lookup[item.id] = item;
    });
  });
  return lookup;
});

const getItem = (id) => itemsLookup.value[id] || {};

const formatPrice = (value) => {
  const num = Number(value) || 0;
  return num.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};

const totalPrice = computed(() => {
  let sum = 0;
  for (const [id, qty] of Object.entries(quantities)) {
    if (qty > 0) {
      const item = getItem(id);
      if (item && item.price) {
        sum += item.price * qty;
      }
    }
  }
  return sum;
});

const onSubmit = (e) => {
  localErrors.value = [];
  if (!form.customer_name) {
    localErrors.value.push('Informe o nome do cliente.');
  }

  const hasItems = Object.values(quantities).some(q => q > 0);
  if (!hasItems) {
    localErrors.value.push('Adicione ao menos um item ao pedido.');
  }

  if (localErrors.value.length > 0) {
    e.preventDefault();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
};
</script>

<style scoped>
.order-form-vue {
  /* Scope specific styles if needed */
}
</style>
