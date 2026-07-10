<template>
  <div class="orders-view">
    
    <!-- PAGE HEADER -->
    <header class="page-header">
      <div class="page-header__title-wrap">
        <p>/ Registro de Operaciones</p>
        <h1 class="title-serif">Gestión de Pedidos</h1>
      </div>
      <div class="page-header__actions">
        <span class="badge badge--info">{{ activeOrdersCount }} pedidos pendientes</span>
      </div>
    </header>

    <!-- STATUS TABS NAVIGATION -->
    <section class="tabs-nav">
      <button 
        v-for="tab in statusTabs" 
        :key="tab.value" 
        :class="['tab-item', { 'tab-item--active': activeTab === tab.value }]"
        @click="activeTab = tab.value"
      >
        {{ tab.label }}
        <span class="tab-item__badge" :class="`badge--${tab.badgeColor}`">
          {{ getOrdersCountByStatus(tab.value) }}
        </span>
      </button>
    </section>

    <!-- GLOBAL ALERTS -->
    <div v-if="errorMsg && !showDrawer" class="alert alert--error" style="margin-top: 24px; margin-bottom: 0;">{{ errorMsg }}</div>

    <!-- ORDERS TABLE -->
    <section class="card table-card">
      <div class="table-wrap">
        <table class="table-custom">
          <thead>
            <tr>
              <th>Código Pedido</th>
              <th>Fecha</th>
              <th>Cliente</th>
              <th>Precio Tipo</th>
              <th>Items</th>
              <th>Total</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in filteredOrders" :key="order.id">
              <td style="font-weight: 700;">{{ order.numero }}</td>
              <td>{{ formatDateString(order.creado_en) }}</td>
              <td>{{ getUserName(order) }}</td>
              <td>
                <span class="badge badge--info">{{ order.tipo_precio }}</span>
              </td>
              <td>{{ getOrderItemsCount(order) }} uds</td>
              <td style="font-weight: 600;">${{ formatMoney(order.total) }}</td>
              <td>
                <span :class="['badge', getStatusBadgeClass(order.estado)]">
                  {{ order.estado }}
                </span>
              </td>
              <td>
                <button class="btn btn--secondary btn--sm" style="padding: 6px 14px;" @click="openOrderDetail(order)">
                  Detalle / Editar
                </button>
              </td>
            </tr>
            <tr v-if="filteredOrders.length === 0">
              <td colspan="8" class="text-center text-muted" style="text-align: center; padding: 40px 20px;">
                No se encontraron pedidos en esta sección.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <!-- DETAIL & ACTIONS DRAWER -->
    <div class="drawer-backdrop" :class="{ active: showDrawer }" @click="showDrawer = false"></div>
    <div class="drawer" :class="{ active: showDrawer }">
      <div class="drawer__header" v-if="selectedOrder">
        <div>
          <div style="display: flex; gap: 8px; align-items: center; margin-bottom: 8px;">
            <span class="badge badge--info">{{ selectedOrder.numero }}</span>
            <span :class="['badge', getStatusBadgeClass(selectedOrder.estado)]" style="text-transform: capitalize;">{{ selectedOrder.estado }}</span>
          </div>
          <h2 class="title-serif">Modificar Pedido</h2>
        </div>
        <button class="drawer__close" @click="showDrawer = false">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <div class="drawer__body" v-if="selectedOrder">
        
        <!-- DRAWER ALERTS -->
        <div v-if="drawerErrorMsg" class="alert alert--error" style="margin-bottom: 16px;">
          {{ drawerErrorMsg }}
        </div>
        <div v-if="drawerSuccessMsg" class="alert alert--success" style="margin-bottom: 16px;">
          {{ drawerSuccessMsg }}
        </div>

        <!-- STATUS UPDATE AREA -->
        <div class="drawer-section status-update-box">
          <h3>Cambiar Estado del Pedido</h3>
          <div class="form-group" style="margin-bottom: 0;">
            <select class="select-input" :value="selectedOrder.estado" @change="onStatusChange">
              <option value="pendiente">Pendiente (Por confirmar)</option>
              <option value="pagado">Pagado (En fila)</option>
              <option value="procesando">Procesando (En fabricación)</option>
              <option value="enviado">Enviado (Despachado)</option>
              <option value="entregado">Entregado (Completado)</option>
              <option value="devuelta">Devuelta (Devolución)</option>
              <option value="cancelada">Cancelada</option>
            </select>
          </div>
        </div>

        <!-- SHIPPING DISPATCH DETAIL PANEL (Visible when order is 'enviado' or updated to 'enviado') -->
        <div class="drawer-section shipping-dispatch-box" v-if="shippingFormVisible">
          <h3>Detalle de Despacho / Guía</h3>
          
          <div class="form-group">
            <label>Transportadora *</label>
            <input type="text" class="input-text" placeholder="Ej: Servientrega, Envía, Coordinadora" v-model="shippingForm.transportadora" />
          </div>

          <div class="form-group">
            <label>Número de Guía (Rastreo) *</label>
            <input type="text" class="input-text" placeholder="Ej: GUIA1234567" v-model="shippingForm.guia" />
          </div>

          <button type="button" class="btn btn--primary btn--sm" @click="saveShippingDetails">
            Confirmar Despacho
          </button>
        </div>

        <!-- CLIENT INFO -->
        <div class="drawer-section">
          <h3>Detalles del Cliente</h3>
          <div class="info-grid">
            <div class="info-row">
              <span class="info-label">Nombre:</span>
              <span class="info-value">{{ selectedOrder.usuario?.nombre }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">Email:</span>
              <span class="info-value">{{ selectedOrder.usuario?.email }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">Teléfono:</span>
              <span class="info-value">{{ selectedOrder.usuario?.telefono || 'No registra' }}</span>
            </div>
            <template v-if="selectedOrder.direccion">
              <div class="info-row" style="margin-top: 4px; padding-top: 12px; border-top: 1px dashed var(--color-border);">
                <span class="info-label" style="font-weight: 700; color: var(--color-accent); font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em;">Datos de Envío</span>
              </div>
              <div class="info-row">
                <span class="info-label">Recibe:</span>
                <span class="info-value">{{ selectedOrder.direccion.nombre_recibe || selectedOrder.usuario?.nombre }}</span>
              </div>
              <div class="info-row" v-if="selectedOrder.direccion.telefono">
                <span class="info-label">Tel. Contacto:</span>
                <span class="info-value">{{ selectedOrder.direccion.telefono }}</span>
              </div>
              <div class="info-row">
                <span class="info-label">Dirección:</span>
                <span class="info-value" style="text-align: right;">{{ selectedOrder.direccion.direccion }}<br v-if="selectedOrder.direccion.referencia"><span v-if="selectedOrder.direccion.referencia" style="font-size: 11px; color: var(--text-muted);">({{ selectedOrder.direccion.referencia }})</span></span>
              </div>
              <div class="info-row">
                <span class="info-label">Ubicación:</span>
                <span class="info-value">{{ selectedOrder.direccion.ciudad }}, {{ selectedOrder.direccion.departamento }}<template v-if="selectedOrder.direccion.codigo_postal"> - {{ selectedOrder.direccion.codigo_postal }}</template></span>
              </div>
            </template>
            <div class="info-row" v-if="selectedOrder.notas_cliente" :style="selectedOrder.direccion ? 'margin-top: 4px; padding-top: 12px; border-top: 1px dashed var(--color-border);' : ''">
              <span class="info-label">Notas del Cliente:</span>
              <span class="info-value text-italic" style="text-align: right;">"{{ selectedOrder.notas_cliente }}"</span>
            </div>
          </div>
        </div>

        <!-- LINE ITEMS -->
        <div class="drawer-section">
          <h3>Artículos del Pedido</h3>
          <div class="items-list">
            <div v-for="item in selectedOrder.items" :key="item.id" class="item-row item-row--with-image">
              <div class="item-image">
                <img :src="getVariantImage(item)" alt="Imagen del producto" />
              </div>
              <div class="item-details" style="flex: 1;">
                <span class="item-name">{{ getVariantName(item) }}</span>
                <span class="item-meta">SKU: {{ getVariantSku(item) }} | Cantidad: {{ item.cantidad }}</span>
              </div>
              <span class="item-price">${{ formatMoney(item.total_linea) }}</span>
            </div>
          </div>
        </div>

        <!-- TOTALS -->
        <div class="drawer-section finance-summary">
          <div class="finance-row">
            <span>Subtotal</span>
            <span>${{ formatMoney(selectedOrder.subtotal) }}</span>
          </div>
          <div class="finance-row" v-if="selectedOrder.descuento > 0">
            <span>Descuento</span>
            <span class="text-success">-${{ formatMoney(selectedOrder.descuento) }}</span>
          </div>
          <div class="finance-row">
            <span>Envío</span>
            <span>${{ formatMoney(selectedOrder.envio_costo) }}</span>
          </div>
          <div class="finance-row finance-row--total">
            <span>Total Facturado</span>
            <span>${{ formatMoney(selectedOrder.total) }}</span>
          </div>
        </div>

        <!-- SHIPMENT PREVIEW -->
        <div class="drawer-section" v-if="selectedOrder.envio && !shippingFormVisible">
          <h3>Estado del Envío</h3>
          <div class="info-grid">
            <div class="info-row">
              <span class="info-label">Transportadora:</span>
              <span class="info-value">{{ selectedOrder.envio.transportadora }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">Nº Guía:</span>
              <span class="info-value font-mono">{{ selectedOrder.envio.guia }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">Estado Envío:</span>
              <span class="info-value text-capitalize">{{ selectedOrder.envio.estado }}</span>
            </div>
            <div class="info-row" v-if="selectedOrder.envio.entregado_en">
              <span class="info-label">Entregado el:</span>
              <span class="info-value">{{ selectedOrder.envio.entregado_en }}</span>
            </div>
          </div>
        </div>

      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, reactive, onMounted } from 'vue'
import axios from 'axios'

const api = axios.create({
  baseURL: (import.meta.env.VITE_API_URL || 'http://localhost:8000/api') + ''
})

api.interceptors.request.use(config => {
  const token = localStorage.getItem('auth_token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

// State
const orders = ref([])
const loading = ref(true)

// Alert States
const errorMsg = ref('')
const drawerErrorMsg = ref('')
const drawerSuccessMsg = ref('')

const showGlobalError = (msg) => {
  errorMsg.value = msg
  setTimeout(() => errorMsg.value = '', 5000)
}

const showDrawerError = (msg) => {
  drawerErrorMsg.value = msg
  drawerSuccessMsg.value = ''
  setTimeout(() => drawerErrorMsg.value = '', 5000)
}

const showDrawerSuccess = (msg) => {
  drawerSuccessMsg.value = msg
  drawerErrorMsg.value = ''
  setTimeout(() => drawerSuccessMsg.value = '', 5000)
}

const statusTabs = [
  { label: 'Todos', value: 'all', badgeColor: 'info' },
  { label: 'Pendientes', value: 'pendiente', badgeColor: 'pending' },
  { label: 'Pagados', value: 'pagado', badgeColor: 'success' },
  { label: 'Fabricando', value: 'procesando', badgeColor: 'info' },
  { label: 'Enviados', value: 'enviado', badgeColor: 'success' },
  { label: 'Entregados', value: 'entregado', badgeColor: 'success' },
  { label: 'Devueltos', value: 'devuelta', badgeColor: 'danger' },
  { label: 'Cancelados', value: 'cancelada', badgeColor: 'danger' }
]

const activeTab = ref('all')

// Fetch orders
const fetchOrders = async () => {
  loading.value = true
  try {
    const response = await api.get('/ordenes')
    orders.value = response.data.data || []
  } catch (error) {
    console.error('Error fetching orders:', error)
    showGlobalError('Hubo un error al cargar los pedidos.')
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchOrders()
})

// Statistics computation
const activeOrdersCount = computed(() => {
  return orders.value.filter(o => o.estado === 'pendiente' || o.estado === 'procesando').length
})

const getOrdersCountByStatus = (status) => {
  if (status === 'all') return orders.value.length
  return orders.value.filter(o => o.estado === status).length
}

// Order filter
const filteredOrders = computed(() => {
  if (activeTab.value === 'all') {
    return [...orders.value].sort((a, b) => b.id - a.id)
  }
  return [...orders.value]
    .filter(o => o.estado === activeTab.value)
    .sort((a, b) => b.id - a.id)
})

// Drawer management
const showDrawer = ref(false)
const selectedOrder = ref(null)

const shippingFormVisible = ref(false)
const shippingForm = reactive({
  transportadora: '',
  guia: ''
})

const openOrderDetail = (order) => {
  selectedOrder.value = order
  drawerErrorMsg.value = ''
  drawerSuccessMsg.value = ''
  
  shippingFormVisible.value = order.estado === 'enviado' && !order.envio
  
  if (order.envio) {
    shippingForm.transportadora = order.envio.transportadora
    shippingForm.guia = order.envio.guia
  } else {
    shippingForm.transportadora = ''
    shippingForm.guia = ''
  }
  
  showDrawer.value = true
}

const onStatusChange = async (event) => {
  const newStatus = event.target.value
  const orderId = selectedOrder.value.id
  
  try {
    // Update order status via API
    await api.put(`/ordenes/${orderId}/estado`, { estado: newStatus })
    
    // Update local state
    const orderIndex = orders.value.findIndex(o => o.id === selectedOrder.value.id)
    if (orderIndex !== -1) {
      orders.value[orderIndex].estado = newStatus
      selectedOrder.value.estado = newStatus
      
      // Update shipping form visibility if changed to or from "enviado"
      shippingFormVisible.value = newStatus === 'enviado' && !selectedOrder.value.envio
      
      // If there is an existing shipment, update its status based on the new order status
      if (selectedOrder.value.envio) {
        let shipmentStatus = newStatus === 'entregado' ? 'entregado' : 'enviado'
        if(newStatus === 'cancelada') shipmentStatus = 'fallido'
        
        await api.put(`/envios/${selectedOrder.value.envio.id}/estado`, {
          estado: shipmentStatus
        })
        selectedOrder.value.envio.estado = shipmentStatus
      }
    }
    
    showDrawerSuccess('Estado del pedido actualizado correctamente.')
    window.dispatchEvent(new Event('orders-updated'))
  } catch (error) {
    console.error('Error updating order status:', error)
    showDrawerError('Hubo un error al actualizar el estado.')
    // Reset select value
    event.target.value = selectedOrder.value.estado
  }
}

const saveShippingDetails = async () => {
  if (!shippingForm.transportadora) {
    showDrawerError('Ingresa la transportadora de envío')
    return
  }
  
  try {
    // Note: The backend EnvioController::store ignores the 'guia' parameter and auto-generates it
    // But we still send transportadora
    const response = await api.post('/envios', {
      orden_id: selectedOrder.value.id,
      transportadora: shippingForm.transportadora
    })
    
    const newEnvio = response.data.data
    
    // Once created, the backend sets it to 'preparando'. 
    // We should also change the envio state to 'enviado' since the order is shipped.
    await api.put(`/envios/${newEnvio.id}/estado`, { estado: 'enviado' })
    newEnvio.estado = 'enviado'
    
    // Update local state
    const orderIndex = orders.value.findIndex(o => o.id === selectedOrder.value.id)
    if (orderIndex !== -1) {
      orders.value[orderIndex].envio = newEnvio
      selectedOrder.value.envio = newEnvio
    }
    
    shippingFormVisible.value = false
    showDrawerSuccess(`Guía de despacho registrada exitosamente: ${newEnvio.guia}`)
  } catch (error) {
    console.error('Error saving shipping details:', error)
    showDrawerError(error.response?.data?.message || 'Hubo un error al registrar el envío.')
  }
}

// Helpers
const formatMoney = (amount) => {
  return Number(amount).toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 })
}

const formatDateString = (dateStr) => {
  if (!dateStr) return 'N/A'
  const date = new Date(dateStr)
  return date.toLocaleDateString('es-ES', { year: 'numeric', month: 'short', day: 'numeric' })
}

const getUserName = (order) => {
  return order.usuario ? order.usuario.nombre : `Usuario #${order.usuario_id}`
}

const getOrderItemsCount = (order) => {
  return order.items ? order.items.reduce((sum, item) => sum + item.cantidad, 0) : 0
}

const getStatusBadgeClass = (estado) => {
  const map = {
    pendiente: 'badge--pending',
    pagado: 'badge--success',
    procesando: 'badge--info',
    enviado: 'badge--info',
    entregado: 'badge--success',
    devuelta: 'badge--danger',
    cancelada: 'badge--danger'
  }
  return map[estado] || 'badge--pending'
}

const getVariantName = (item) => {
  const variant = item.variante
  if (!variant) return 'Producto Desconocido'
  const product = variant.producto
  const color = variant.color || 'N/A'
  const talla = variant.talla || 'N/A'
  return product ? `${product.nombre} (${color} / Talla ${talla})` : 'Producto Desconocido'
}

const getVariantSku = (item) => {
  return item.variante ? item.variante.sku : 'N/A'
}

const getVariantImage = (item) => {
  const imagenes = item.variante?.producto?.imagenes
  if (imagenes && imagenes.length > 0) {
    const portada = imagenes.find(img => img.es_portada)
    return portada ? portada.url : imagenes[0].url
  }
  return 'https://via.placeholder.com/80?text=No+Img'
}
</script>

<style scoped>
/* Tabs Navigation styling */
.tabs-nav {
  display: flex;
  gap: 8px;
  border-bottom: 1px solid var(--color-border);
  padding-bottom: 8px;
  overflow-x: auto;
}

.tab-item {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 18px;
  background: transparent;
  border: none;
  border-bottom: 2px solid transparent;
  color: var(--text-secondary);
  font-family: var(--font-sans);
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  white-space: nowrap;
  transition: var(--transition-smooth);
}

.tab-item:hover {
  color: var(--text-primary);
}

.tab-item--active {
  border-bottom-color: var(--color-accent);
  color: var(--text-primary);
}

.tab-item__badge {
  font-size: 9px;
  font-weight: 700;
  padding: 1px 6px;
  border-radius: var(--radius-full);
}

/* Status Change elements */
.status-update-box {
  background-color: var(--bg-sidebar);
  border: 1px solid var(--color-border);
  padding: 18px;
  border-radius: var(--radius-md);
}

.status-update-box h3 {
  margin-bottom: 12px;
}

.shipping-dispatch-box {
  background-color: var(--color-accent-light);
  border: 1px dashed var(--color-accent);
  padding: 18px;
  border-radius: var(--radius-md);
}

.shipping-dispatch-box h3 {
  margin-bottom: 14px;
}

.shipping-dispatch-box .btn {
  margin-top: 10px;
}

/* Detail Drawer row adjustments */
.drawer-section {
  padding-bottom: 24px;
  margin-bottom: 24px;
  border-bottom: 1px solid var(--color-border);
}

.drawer-section:last-child {
  padding-bottom: 0;
  margin-bottom: 0;
  border-bottom: none;
}

.drawer-section h3 {
  font-size: 13px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--text-secondary);
  margin-bottom: 16px;
}

.info-grid {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.info-row {
  display: flex;
  justify-content: space-between;
  font-size: 13px;
}

.info-label {
  color: var(--text-secondary);
}

.info-value {
  font-weight: 500;
  color: var(--text-primary);
}

.text-italic {
  font-style: italic;
  color: var(--text-secondary);
}

.items-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.item-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 13px;
  padding: 8px 0;
}

.item-row--with-image {
  gap: 14px;
  align-items: center;
}

.item-image img {
  width: 52px;
  height: 52px;
  object-fit: cover;
  border-radius: var(--radius-sm);
  background-color: var(--color-border);
  display: block;
}

.item-details {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.item-name {
  font-weight: 600;
  color: var(--text-primary);
}

.item-meta {
  font-size: 11px;
  color: var(--text-muted);
}

.item-price {
  font-weight: 600;
  color: var(--text-primary);
}

.finance-summary {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.finance-row {
  display: flex;
  justify-content: space-between;
  font-size: 13px;
  color: var(--text-secondary);
}

.finance-row--total {
  font-size: 16px;
  font-weight: 700;
  color: var(--text-primary);
  border-top: 1px solid var(--color-border);
  padding-top: 10px;
  margin-top: 4px;
}

.font-mono {
  font-family: monospace;
}

/* Alertas de estilo */
.alert {
  padding: 14px 16px;
  border-radius: 10px;
  margin-bottom: 24px;
  font-size: 14px;
  font-weight: 500;
  animation: fadeIn 0.3s ease;
}

.alert--success {
  background-color: #ecfdf5;
  color: #065f46;
  border: 1px solid #a7f3d0;
}

.alert--error {
  background-color: #fef2f2;
  color: #991b1b;
  border: 1px solid #fecaca;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-6px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
