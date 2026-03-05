@extends('layouts.app')

@section('title', 'Editar Pedido')

@section('content')
    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <div class="card card-modern">
                <div class="card-header card-header-modern">
                    <h5 class="mb-0"><i class="bi bi-pencil"></i> Editar Pedido #{{ substr($order->_id, -6) }}</h5>
                </div>
                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger mb-4">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div id="vue-order-app">
                        <form action="{{ route('orders.update', $order->id) }}" method="POST" @submit="onSubmit">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="customer_name" :value="form.customer_name" />
                            <input type="hidden" name="customer_cpf" :value="form.customer_cpf" />
                            <input type="hidden" name="customer_address" :value="form.customer_address" />
                            <input type="hidden" name="status" :value="form.status" />
                            <input type="hidden" name="payment_method" :value="form.payment_method" />
                            <input type="hidden" name="notes" :value="form.notes" />

                            <!-- Inputs ocultos para enviar itens ao Laravel -->
                            <template v-for="(qty, id) in quantities" :key="'hidden-' + id">
                                <template v-if="qty > 0">
                                    <input type="hidden" :name="`items[${id}][id]`" :value="id" />
                                    <input type="hidden" :name="`items[${id}][name]`" :value="getItem(id).name" />
                                    <input type="hidden" :name="`items[${id}][quantity]`" :value="qty" />
                                    <input type="hidden" :name="`items[${id}][unit_price]`" :value="getItem(id).price" />
                                </template>
                            </template>

                            <h5 class="border-bottom pb-2 mb-3"><i class="bi bi-person-lines-fill"></i> Dados do Cliente
                            </h5>
                            <div class="row mb-4">
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <label for="vue-customer_name" class="form-label">Nome do Cliente <span
                                            class="text-danger">*</span></label>
                                    <input id="vue-customer_name" v-model="form.customer_name" type="text"
                                        class="form-control" placeholder="Nome completo" required />
                                </div>
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <label for="vue-customer_cpf" class="form-label">CPF</label>
                                    <input id="vue-customer_cpf" v-model="form.customer_cpf" type="text"
                                        class="form-control" placeholder="Opcional" />
                                </div>
                                <div class="col-md-4">
                                    <label for="vue-customer_address" class="form-label">Endereço de Entrega</label>
                                    <input id="vue-customer_address" v-model="form.customer_address" type="text"
                                        class="form-control" placeholder="Rua, Número, Bairro" />
                                </div>
                            </div>

                            <h5 class="border-bottom pb-2 mb-3"><i class="bi bi-basket"></i> Produtos Disponíveis</h5>

                            <template v-for="(group, key) in menu" :key="key">
                                <h6 class="mt-4 mb-2 text-primary">
                                    <i class="bi bi-tag-fill me-2"></i> @{{ group.label }}
                                </h6>
                                <div class="table-responsive mb-4">
                                    <table class="table table-sm table-modern align-middle">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Item</th>
                                                <th>Preço</th>
                                                <th>Estoque Disp.</th>
                                                <th style="width:140px">Quantidade</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-if="!group.items.length">
                                                <td colspan="4" class="text-muted text-center py-3">Nenhum item nesta
                                                    categoria.</td>
                                            </tr>
                                            <tr v-for="item in group.items" :key="item.id">
                                                <td>@{{ item.name }}</td>
                                                <td>R$ @{{ formatPrice(item.price) }}</td>
                                                <td>
                                                    <span v-if="item.stock > 0 || initialItems[item.id] > 0"
                                                        class="badge bg-success">
                                                        @{{ item.stock }} dispon.
                                                        <span v-if="initialItems[item.id] > 0">
                                                            (@{{ initialItems[item.id] }} no ped.)
                                                        </span>
                                                    </span>
                                                    <span v-else class="badge bg-danger">Esgotado</span>
                                                </td>
                                                <td>
                                                    <input v-model.number="quantities[item.id]" type="number"
                                                        class="form-control form-control-sm" min="0"
                                                        :max="item.stock + (initialItems[item.id] || 0)"
                                                        :disabled="item.stock <= 0 && !(initialItems[item.id] > 0)" />
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </template>

                            <p class="text-end text-muted small mb-4">
                                Total do Pedido: <strong class="fs-5 text-dark">R$ @{{ formatPrice(totalPrice) }}</strong>
                            </p>

                            <h5 class="border-bottom pb-2 mb-3"><i class="bi bi-gear"></i> Detalhes Finais</h5>
                            <div class="row mb-4">
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <label for="vue-status" class="form-label">Status do Pedido <span
                                            class="text-danger">*</span></label>
                                    <select id="vue-status" v-model="form.status" class="form-select" required>
                                        <option v-for="(text, value) in statuses" :key="value"
                                            :value="value">@{{ text }}</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <label for="vue-payment" class="form-label">Forma de pagamento <span
                                            class="text-danger">*</span></label>
                                    <select id="vue-payment" v-model="form.payment_method" class="form-select" required>
                                        <option v-for="(text, value) in paymentMethods" :key="value"
                                            :value="value">@{{ text }}</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="vue-notes" class="form-label">Observações</label>
                                    <input id="vue-notes" v-model="form.notes" type="text" class="form-control"
                                        placeholder="Ex: Entregar à tarde" />
                                </div>
                            </div>

                            <hr />

                            <div v-if="localErrors.length" class="alert alert-danger mb-4">
                                <ul class="mb-0">
                                    <li v-for="(err, i) in localErrors" :key="i">@{{ err }}</li>
                                </ul>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary shadow-sm">
                                    <i class="bi bi-save"></i> Atualizar Pedido
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @php
        $itemsById = [];
        if (is_array($order->items)) {
            foreach ($order->items as $item) {
                $itemsById[$item['id']] = $item['quantity'];
            }
        }
    @endphp

    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script>
        const menuData = @json($productsByCategory);
        const statusesData = @json(\App\Models\Order::getStatuses());
        const paymentMethodsData = @json(\App\Models\Order::getPaymentMethods());
        const initialItemsData = @json($itemsById);

        // Build quick lookup & default quantities
        const itemsLookup = {};
        const defaultQuantities = {};

        for (const catId in menuData) {
            if (menuData.hasOwnProperty(catId)) {
                menuData[catId].items.forEach(item => {
                    itemsLookup[item.id] = item;
                    defaultQuantities[item.id] = initialItemsData[item.id] ? initialItemsData[item.id] : 0;
                });
            }
        }

        const app = Vue.createApp({
            data() {
                return {
                    menu: menuData,
                    statuses: statusesData,
                    paymentMethods: paymentMethodsData,
                    initialItems: initialItemsData,
                    form: {
                        customer_name: "{{ old('customer_name', $order->customer_name) }}",
                        customer_cpf: "{{ old('customer_cpf', $order->customer_cpf) }}",
                        customer_address: "{{ old('customer_address', $order->customer_address) }}",
                        status: "{{ old('status', $order->status) }}",
                        payment_method: "{{ old('payment_method', $order->payment_method) }}",
                        notes: "{{ old('notes', $order->notes) }}"
                    },
                    quantities: defaultQuantities,
                    localErrors: []
                };
            },
            computed: {
                totalPrice() {
                    let sum = 0;
                    for (const [id, qty] of Object.entries(this.quantities)) {
                        if (qty > 0 && itemsLookup[id]) {
                            sum += itemsLookup[id].price * qty;
                        }
                    }
                    return sum;
                }
            },
            methods: {
                getItem(id) {
                    return itemsLookup[id] || {};
                },
                formatPrice(value) {
                    const num = Number(value) || 0;
                    return num.toLocaleString('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                },
                onSubmit(e) {
                    this.localErrors = [];
                    if (!this.form.customer_name || this.form.customer_name.trim() === '') {
                        this.localErrors.push('Informe o nome do cliente.');
                    }

                    const hasItems = Object.values(this.quantities).some(q => q > 0);
                    if (!hasItems) {
                        this.localErrors.push('Adicione ao menos um item válido ao pedido.');
                    }

                    if (this.localErrors.length > 0) {
                        e.preventDefault();
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    }
                }
            }
        });

        app.mount('#vue-order-app');
    </script>
@endsection
