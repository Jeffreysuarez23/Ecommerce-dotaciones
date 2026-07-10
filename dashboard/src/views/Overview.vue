<template>
  <div class="overview-view">
    
    <!-- PAGE HEADER -->
    <header class="page-header">
      <div class="page-header__title-wrap">
        <p>/ Consola de Control</p>
        <h1 class="title-serif">Resumen General</h1>
      </div>
      <div class="page-header__actions">
        <span class="badge badge--info">{{ todayDateString }}</span>
      </div>
    </header>

    <!-- KPI METRICS GRID -->
    <section class="grid-4">
      
      <!-- Metric 1: Total Ventas -->
      <div class="card metric-card">
        <div class="metric-card__header">
          <span class="metric-card__title">Total Ventas</span>
          <span class="metric-card__icon text-success">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="12" y1="1" x2="12" y2="23"></line>
              <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
            </svg>
          </span>
        </div>
        <div class="metric-card__body">
          <h2 class="metric-card__value">${{ formatMoney(totalSales) }}</h2>
          <div class="metric-card__trend">
            <span class="trend-indicator trend-indicator--up">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                <polyline points="18 15 12 9 6 15"></polyline>
              </svg>
              12.4%
            </span>
            <span class="metric-card__period">vs mes anterior</span>
          </div>
        </div>
        <!-- Sparkline -->
        <div class="metric-card__sparkline">
          <svg viewBox="0 0 100 20" class="sparkline-svg">
            <path d="M0,15 Q15,5 30,12 T60,6 T90,14 L100,8" fill="none" stroke="var(--color-success)" stroke-width="2"></path>
          </svg>
        </div>
      </div>

      <!-- Metric 2: Pedidos Activos -->
      <div class="card metric-card">
        <div class="metric-card__header">
          <span class="metric-card__title">Pedidos Totales</span>
          <span class="metric-card__icon text-pending">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
              <line x1="3" y1="6" x2="21" y2="6"></line>
            </svg>
          </span>
        </div>
        <div class="metric-card__body">
          <h2 class="metric-card__value">{{ totalOrdersCount }}</h2>
          <div class="metric-card__trend">
            <span class="trend-indicator trend-indicator--up">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                <polyline points="18 15 12 9 6 15"></polyline>
              </svg>
              8.1%
            </span>
            <span class="metric-card__period">este mes</span>
          </div>
        </div>
        <!-- Sparkline -->
        <div class="metric-card__sparkline">
          <svg viewBox="0 0 100 20" class="sparkline-svg">
            <path d="M0,10 Q20,18 40,8 T80,12 L100,5" fill="none" stroke="var(--color-pending)" stroke-width="2"></path>
          </svg>
        </div>
      </div>

      <!-- Metric 3: Lonas Activas -->
      <div class="card metric-card">
        <div class="metric-card__header">
          <span class="metric-card__title">Lonas en Inventario</span>
          <span class="metric-card__icon text-accent">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
              <polyline points="2 17 12 22 22 17"></polyline>
            </svg>
          </span>
        </div>
        <div class="metric-card__body">
          <h2 class="metric-card__value">{{ totalLonasCount }}</h2>
          <div class="metric-card__trend">
            <span class="trend-indicator trend-indicator--flat">
              0.0%
            </span>
            <span class="metric-card__period">sin variaciones</span>
          </div>
        </div>
        <!-- Sparkline -->
        <div class="metric-card__sparkline">
          <svg viewBox="0 0 100 20" class="sparkline-svg">
            <path d="M0,10 L20,10 L40,10 L60,10 L80,10 L100,10" fill="none" stroke="var(--color-accent)" stroke-width="2"></path>
          </svg>
        </div>
      </div>

      <!-- Metric 4: Inactive Lonas -->
      <div class="card metric-card">
        <div class="metric-card__header">
          <span class="metric-card__title">Lonas inactivas</span>
          <span class="metric-card__icon text-danger">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="15" y1="9" x2="9" y2="15"></line>
              <line x1="9" y1="9" x2="15" y2="15"></line>
            </svg>
          </span>
        </div>
        <div class="metric-card__body">
          <h2 class="metric-card__value">{{ inactiveLonasCount }}</h2>
          <div class="metric-card__trend">
            <span class="trend-indicator trend-indicator--down" v-if="inactiveLonasCount > 0">
              Desactivadas
            </span>
            <span class="trend-indicator trend-indicator--up" v-else>
              Ninguna
            </span>
          </div>
        </div>
        <!-- Sparkline -->
        <div class="metric-card__sparkline" v-if="inactiveLonasCount > 0">
          <svg viewBox="0 0 100 20" class="sparkline-svg">
            <path d="M0,15 L20,10 L40,15 L60,10 L80,15 L100,10" fill="none" stroke="var(--color-danger)" stroke-width="2"></path>
          </svg>
        </div>
        <div class="metric-card__sparkline" v-else>
          <svg viewBox="0 0 100 20" class="sparkline-svg">
            <path d="M0,15 L100,15" fill="none" stroke="var(--color-success)" stroke-width="2"></path>
          </svg>
        </div>
      </div>

    </section>

    <!-- CHARTS SECTION -->
    <section class="grid-2">
      
      <!-- Chart 1: Sales Performance -->
      <div class="card chart-card">
        <h3 class="chart-card__title">Rendimiento de Ventas (Últimos 7 Días)</h3>
        <p class="chart-card__subtitle">Ventas diarias en miles de COP</p>
        <div class="chart-container">
          <!-- Elegant Custom SVG Chart -->
          <svg viewBox="0 0 500 200" class="main-chart-svg">
            <!-- Grid Lines -->
            <line x1="40" y1="30" x2="480" y2="30" stroke="var(--color-border)" stroke-width="1" stroke-dasharray="4"></line>
            <line x1="40" y1="80" x2="480" y2="80" stroke="var(--color-border)" stroke-width="1" stroke-dasharray="4"></line>
            <line x1="40" y1="130" x2="480" y2="130" stroke="var(--color-border)" stroke-width="1" stroke-dasharray="4"></line>
            <line x1="40" y1="180" x2="480" y2="180" stroke="var(--color-border)" stroke-width="1.5"></line>

            <!-- Line Path -->
            <path :d="salesPath" fill="none" stroke="var(--color-accent)" stroke-width="3" stroke-linecap="round"></path>
            
            <!-- Area Gradient Fill -->
            <path :d="salesAreaPath" fill="rgba(122, 106, 83, 0.08)"></path>

            <!-- Chart Nodes -->
            <circle v-for="(d, i) in apiResumen.ventas_7_dias" :key="'node-'+i" :cx="getPointX(i)" :cy="getPointY(d.total)" r="4" fill="var(--bg-card)" stroke="var(--color-accent)" stroke-width="2"></circle>

            <!-- Labels X -->
            <text v-for="(d, i) in apiResumen.ventas_7_dias" :key="'label-'+i" :x="getPointX(i)" y="195" text-anchor="middle" font-size="10" fill="var(--text-muted)">{{ getDayName(d.fecha) }}</text>

            <!-- Labels Y -->
            <text v-for="label in yAxisLabels" :key="'y-'+label.y" x="30" :y="label.y" text-anchor="end" font-size="9" fill="var(--text-muted)">{{ label.text }}</text>
          </svg>
        </div>
      </div>

      <!-- Chart 2: Order Distribution -->
      <div class="card chart-card">
        <h3 class="chart-card__title">Distribución de Pedidos por Estado</h3>
        <p class="chart-card__subtitle">Cantidad de pedidos históricos</p>
        <div class="chart-container">
          <svg viewBox="0 0 500 200" class="main-chart-svg">
            <line x1="40" y1="180" x2="480" y2="180" stroke="var(--color-border)" stroke-width="1.5"></line>
            
            <!-- Bar Chart -->
            <!-- Pendiente: 1 -->
            <rect x="75" y="130" width="40" height="50" rx="4" fill="var(--color-pending)" opacity="0.85"></rect>
            <!-- Procesando: 1 -->
            <rect x="175" y="130" width="40" height="50" rx="4" fill="var(--color-accent)" opacity="0.85"></rect>
            <!-- Entregado: 1 -->
            <rect x="275" y="130" width="40" height="50" rx="4" fill="var(--color-success)" opacity="0.85"></rect>
            <!-- Devuelta: 1 -->
            <rect x="375" y="130" width="40" height="50" rx="4" fill="var(--color-danger)" opacity="0.85"></rect>

            <!-- Values labels inside/above bars -->
            <text x="95" y="120" text-anchor="middle" font-size="12" font-weight="600" fill="var(--text-primary)">{{ countOrdersByStatus('pendiente') }}</text>
            <text x="195" y="120" text-anchor="middle" font-size="12" font-weight="600" fill="var(--text-primary)">{{ countOrdersByStatus('procesando') }}</text>
            <text x="295" y="120" text-anchor="middle" font-size="12" font-weight="600" fill="var(--text-primary)">{{ countOrdersByStatus('entregado') }}</text>
            <text x="395" y="120" text-anchor="middle" font-size="12" font-weight="600" fill="var(--text-primary)">{{ countOrdersByStatus('devuelta') }}</text>

            <!-- Labels X -->
            <text x="95" y="195" text-anchor="middle" font-size="11" fill="var(--text-secondary)">Pendiente</text>
            <text x="195" y="195" text-anchor="middle" font-size="11" fill="var(--text-secondary)">Procesando</text>
            <text x="295" y="195" text-anchor="middle" font-size="11" fill="var(--text-secondary)">Entregado</text>
            <text x="395" y="195" text-anchor="middle" font-size="11" fill="var(--text-secondary)">Devuelta</text>
          </svg>
        </div>
      </div>

    </section>

    <!-- TABLES & NOTIFICATIONS SECTION -->
    <section class="grid-3" style="grid-template-columns: 2fr 1fr;">
      
      <!-- Recent Orders List -->
      <div class="card table-card">
        <div class="table-card__header">
          <h3 class="card-title">Pedidos Recientes</h3>
          <router-link to="/orders" class="view-all-link">Ver todo</router-link>
        </div>
        <div class="table-wrap">
          <table class="table-custom">
            <thead>
              <tr>
                <th>Nº Pedido</th>
                <th>Cliente</th>
                <th>Tipo</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Acción</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="order in recentOrders" :key="order.id">
                <td style="font-weight: 600;">{{ order.numero }}</td>
                <td>{{ order.usuario ? order.usuario.nombre : 'Usuario #' + order.usuario_id }}</td>
                <td>
                  <span class="badge badge--info">{{ order.tipo_precio }}</span>
                </td>
                <td style="font-weight: 500;">${{ formatMoney(order.total) }}</td>
                <td>
                  <span :class="['badge', getStatusBadgeClass(order.estado)]">
                    {{ order.estado }}
                  </span>
                </td>
                <td>
                  <button class="btn-text-action" @click="viewOrderDetails(order)">Detalle</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Notifications & Alert Feed -->
      <div class="card notif-card">
        <div class="notif-card__header">
          <h3 class="card-title">Notificaciones</h3>
          <button class="btn-text-action" v-if="unreadNotifications.length > 0" @click="clearAllNotifs">Limpiar</button>
        </div>
        <div class="notif-list">
          <div v-if="unreadNotifications.length === 0" class="notif-empty">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-muted">
              <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
              <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            <p>No tienes notificaciones pendientes</p>
          </div>
          <div 
            v-else 
            v-for="notif in unreadNotifications" 
            :key="notif.id" 
            class="notif-item" 
            :class="`notif-item--${notif.tipo}`"
            @click="markAsRead(notif.id)"
          >
            <div class="notif-item__dot"></div>
            <div class="notif-item__content">
              <p class="notif-item__title">{{ notif.titulo }}</p>
              <p class="notif-item__message">{{ notif.mensaje }}</p>
              <span class="notif-item__time">{{ formatDate(notif.creado_en) }}</span>
            </div>
            <button class="notif-item__close" @click.stop="markAsRead(notif.id)">×</button>
          </div>
        </div>
      </div>

    </section>

    <!-- DRAWER FOR ORDER DETAIL -->
    <div class="drawer-backdrop" :class="{ active: showDrawer }" @click="showDrawer = false"></div>
    <div class="drawer" :class="{ active: showDrawer }">
      <div class="drawer__header" v-if="selectedOrder">
        <div>
          <span class="badge badge--info" style="margin-bottom: 6px;">{{ selectedOrder.numero }}</span>
          <h2 class="title-serif">Detalle del Pedido</h2>
        </div>
        <button class="drawer__close" @click="showDrawer = false">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <div class="drawer__body" v-if="selectedOrder">
        <!-- Status Box -->
        <div class="drawer-section status-box">
          <span class="status-box__label">Estado del Pedido</span>
          <span :class="['badge', getStatusBadgeClass(selectedOrder.estado)]" style="font-size: 13px; padding: 6px 16px;">
            {{ selectedOrder.estado }}
          </span>
        </div>

        <!-- Customer Summary -->
        <div class="drawer-section">
          <h3>Información del Cliente</h3>
          <div class="info-grid">
            <div class="info-row">
              <span class="info-label">Nombre:</span>
              <span class="info-value">{{ getOrderUser(selectedOrder.usuario_id)?.nombre }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">Email:</span>
              <span class="info-value">{{ getOrderUser(selectedOrder.usuario_id)?.email }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">Contacto:</span>
              <span class="info-value">{{ getOrderUser(selectedOrder.usuario_id)?.telefono || 'No registra' }}</span>
            </div>
          </div>
        </div>

        <!-- Items Table -->
        <div class="drawer-section">
          <h3>Artículos Comprados</h3>
          <div class="items-list">
            <div v-for="item in getOrderItems(selectedOrder.id)" :key="item.id" class="item-row">
              <div class="item-details">
                <span class="item-name">{{ getVariantName(item.variante_id) }}</span>
                <span class="item-meta">SKU: {{ getVariantSku(item.variante_id) }} | Cantidad: {{ item.cantidad }}</span>
              </div>
              <span class="item-price">${{ formatMoney(item.total_linea) }}</span>
            </div>
          </div>
        </div>

        <!-- Financial Summary -->
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
            <span>Total</span>
            <span>${{ formatMoney(selectedOrder.total) }}</span>
          </div>
        </div>

        <!-- Shipping details if any -->
        <div class="drawer-section" v-if="getOrderShipment(selectedOrder.id)">
          <h3>Información de Envío</h3>
          <div class="info-grid">
            <div class="info-row">
              <span class="info-label">Transportadora:</span>
              <span class="info-value">{{ getOrderShipment(selectedOrder.id).transportadora }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">Guía de rastreo:</span>
              <span class="info-value" style="font-family: monospace;">{{ getOrderShipment(selectedOrder.id).guia }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { state, actions } from '../store/state.js'
import axios from 'axios'

const apiResumen = ref({
  lonas_activas: 0,
  lonas_inactivas: 0,
  ventas_7_dias: [],
  pedidos_recientes: []
})

const unreadNotifications = ref([])

const fetchNotifications = async () => {
  try {
    const res = await axios.get('http://localhost:8000/api/notificaciones')
    unreadNotifications.value = res.data.filter(n => !n.leido_en)
  } catch (error) {
    console.error('Error fetching notifications:', error)
  }
}

onMounted(async () => {
  try {
    // Request is intercepted by main.js to add Bearer token
    const res = await axios.get('http://localhost:8000/api/dashboard/resumen')
    apiResumen.value = res.data
  } catch (error) {
    console.error('Error fetching dashboard summary:', error)
  }
  
  fetchNotifications()
  window.addEventListener('notifications-updated', fetchNotifications)
})

onUnmounted(() => {
  window.removeEventListener('notifications-updated', fetchNotifications)
})

// Date string helper
const todayDateString = computed(() => {
  const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }
  return new Date().toLocaleDateString('es-ES', options)
})

// Metrics computation
const totalSales = computed(() => {
  return apiResumen.value.ventas_totales || 0
})

const totalOrdersCount = computed(() => apiResumen.value.total_ordenes || 0)
const totalLonasCount = computed(() => apiResumen.value.lonas_activas)
const inactiveLonasCount = computed(() => apiResumen.value.lonas_inactivas)

const recentOrders = computed(() => {
  return apiResumen.value.pedidos_recientes || []
})

// Chart computation logic
const maxSales = computed(() => {
  if (!apiResumen.value.ventas_7_dias || apiResumen.value.ventas_7_dias.length === 0) return 600000
  const max = Math.max(...apiResumen.value.ventas_7_dias.map(d => Number(d.total)))
  return max > 0 ? max * 1.2 : 600000
})

const getPointY = (value) => {
  return 180 - (value / maxSales.value) * 150
}

const getPointX = (index) => {
  return 40 + (index * 70)
}

const salesPath = computed(() => {
  if (!apiResumen.value.ventas_7_dias) return ''
  return apiResumen.value.ventas_7_dias.map((d, i) => {
    return `${i === 0 ? 'M' : 'L'}${getPointX(i)},${getPointY(d.total)}`
  }).join(' ')
})

const salesAreaPath = computed(() => {
  if (!apiResumen.value.ventas_7_dias || apiResumen.value.ventas_7_dias.length === 0) return ''
  return salesPath.value + ` L${getPointX(apiResumen.value.ventas_7_dias.length - 1)},180 L40,180 Z`
})

const getDayName = (dateStr) => {
  const date = new Date(dateStr + 'T00:00:00')
  return date.toLocaleDateString('es-CO', { weekday: 'short' }).charAt(0).toUpperCase() + date.toLocaleDateString('es-CO', { weekday: 'short' }).slice(1, 3)
}

const yAxisLabels = computed(() => {
  const m = maxSales.value
  return [
    { y: 34, text: (m/1000).toFixed(0) + 'k' },
    { y: 84, text: ((m*0.66)/1000).toFixed(0) + 'k' },
    { y: 134, text: ((m*0.33)/1000).toFixed(0) + 'k' },
    { y: 184, text: '0' }
  ]
})

// Helper methods
const formatMoney = (amount) => {
  return Number(amount).toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 })
}

const getUserName = (userId) => {
  const user = state.usuarios.find(u => u.id === userId)
  return user ? user.nombre : `Usuario #${userId}`
}

const getOrderUser = (userId) => {
  return state.usuarios.find(u => u.id === userId)
}

const countOrdersByStatus = (status) => {
  if (!apiResumen.value.ordenes_por_estado) return 0
  const stat = apiResumen.value.ordenes_por_estado.find(o => o.estado === status)
  return stat ? stat.total : 0
}

const getStatusBadgeClass = (status) => {
  switch (status) {
    case 'entregado': return 'badge--success'
    case 'pendiente': return 'badge--pending'
    case 'procesando': return 'badge--info'
    case 'devuelta': return 'badge--danger'
    case 'cancelada': return 'badge--danger'
    case 'enviado': return 'badge--success'
    default: return ''
  }
}

const formatDate = (dateStr) => {
  const date = new Date(dateStr)
  return date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' }) + ' - ' + date.toLocaleDateString('es-ES', { month: 'short', day: 'numeric' })
}

// Notification handlers
const markAsRead = async (id) => {
  try {
    await axios.put(`http://localhost:8000/api/notificaciones/${id}/leer`)
    await fetchNotifications()
    window.dispatchEvent(new Event('notifications-updated'))
  } catch (err) {
    console.error('Error marking as read:', err)
  }
}

const clearAllNotifs = async () => {
  try {
    const promises = unreadNotifications.value.map(n => axios.put(`http://localhost:8000/api/notificaciones/${n.id}/leer`))
    await Promise.all(promises)
    await fetchNotifications()
    window.dispatchEvent(new Event('notifications-updated'))
  } catch (err) {
    console.error('Error clearing notifs:', err)
  }
}

// Drawer management for Order Details
const showDrawer = ref(false)
const selectedOrder = ref(null)

const viewOrderDetails = (order) => {
  selectedOrder.value = order
  showDrawer.value = true
}

const getOrderItems = (orderId) => {
  return state.orden_items.filter(oi => oi.orden_id === orderId)
}

const getVariantName = (variantId) => {
  const variant = state.variantes_producto.find(v => v.id === variantId)
  if (!variant) return 'Producto Desconocido'
  const product = state.productos.find(p => p.id === variant.producto_id)
  return product ? `${product.nombre} (${variant.color} / Talla ${variant.talla})` : 'Producto Desconocido'
}

const getVariantSku = (variantId) => {
  const variant = state.variantes_producto.find(v => v.id === variantId)
  return variant ? variant.sku : 'N/A'
}

const getOrderShipment = (orderId) => {
  return state.envios.find(e => e.orden_id === orderId)
}
</script>

<style scoped>
.overview-view {
  display: flex;
  flex-direction: column;
  gap: 32px;
}

/* KPI metric cards */
.metric-card {
  position: relative;
  display: flex;
  flex-direction: column;
  gap: 16px;
  overflow: hidden;
}

.metric-card__header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.metric-card__title {
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--text-secondary);
}

.metric-card__icon {
  display: flex;
  align-items: center;
  justify-content: center;
}

.text-success { color: var(--color-success); }
.text-pending { color: var(--color-pending); }
.text-accent { color: var(--color-accent); }
.text-danger { color: var(--color-danger); }

.metric-card__value {
  font-size: 28px;
  font-weight: 700;
  color: var(--text-primary);
  line-height: 1.1;
  margin-bottom: 6px;
}

.metric-card__trend {
  display: flex;
  align-items: center;
  gap: 8px;
}

.trend-indicator {
  display: inline-flex;
  align-items: center;
  gap: 3px;
  font-size: 11px;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: var(--radius-full);
}

.trend-indicator--up {
  background-color: var(--color-success-light);
  color: var(--color-success);
}

.trend-indicator--down {
  background-color: var(--color-danger-light);
  color: var(--color-danger);
}

.trend-indicator--flat {
  background-color: var(--color-accent-light);
  color: var(--text-secondary);
}

.metric-card__period {
  font-size: 11px;
  color: var(--text-muted);
}

.metric-card__sparkline {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 20px;
}

.sparkline-svg {
  width: 100%;
  height: 100%;
  display: block;
}

/* Charts */
.chart-card {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.chart-card__title {
  font-size: 16px;
  font-weight: 600;
}

.chart-card__subtitle {
  font-size: 12px;
  color: var(--text-muted);
  margin-bottom: 16px;
}

.chart-container {
  width: 100%;
  height: 220px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.main-chart-svg {
  width: 100%;
  height: 100%;
}

.main-chart-svg text {
  font-family: var(--font-sans);
}

/* Tables and cards */
.table-card {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.table-card__header, .notif-card__header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.card-title {
  font-size: 16px;
  font-weight: 600;
}

.view-all-link, .btn-text-action {
  font-size: 12px;
  font-weight: 600;
  color: var(--color-accent);
  text-decoration: none;
  background: none;
  border: none;
  cursor: pointer;
  transition: var(--transition-smooth);
}

.view-all-link:hover, .btn-text-action:hover {
  color: var(--color-accent-hover);
  text-decoration: underline;
}

.btn-text-action {
  padding: 2px 6px;
}

/* Notifications feed */
.notif-card {
  display: flex;
  flex-direction: column;
  gap: 20px;
  max-height: 450px;
}

.notif-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
  overflow-y: auto;
  flex-grow: 1;
}

.notif-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 12px;
  color: var(--text-muted);
  height: 200px;
  text-align: center;
}

.notif-empty p {
  font-size: 13px;
}

.notif-item {
  position: relative;
  display: flex;
  gap: 12px;
  padding: 12px;
  border-radius: var(--radius-md);
  border: 1px solid var(--color-border);
  background-color: var(--bg-app);
  cursor: pointer;
  transition: var(--transition-smooth);
}

.notif-item:hover {
  border-color: var(--color-accent);
  background-color: var(--bg-card);
}

.notif-item__dot {
  width: 8px;
  height: 8px;
  border-radius: var(--radius-full);
  margin-top: 4px;
  flex-shrink: 0;
}

.notif-item--stock_bajo .notif-item__dot { background-color: var(--color-danger); }
.notif-item--orden .notif-item__dot { background-color: var(--color-pending); }
.notif-item--sistema .notif-item__dot { background-color: var(--color-accent); }

.notif-item__content {
  display: flex;
  flex-direction: column;
  gap: 2px;
  flex-grow: 1;
}

.notif-item__title {
  font-size: 12px;
  font-weight: 700;
  color: var(--text-primary);
}

.notif-item__message {
  font-size: 11px;
  color: var(--text-secondary);
  line-height: 1.3;
}

.notif-item__time {
  font-size: 9px;
  color: var(--text-muted);
  margin-top: 4px;
}

.notif-item__close {
  background: none;
  border: none;
  cursor: pointer;
  color: var(--text-muted);
  font-size: 16px;
  height: 20px;
  width: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: var(--radius-full);
  transition: var(--transition-smooth);
}

.notif-item__close:hover {
  color: var(--text-primary);
  background-color: var(--color-border);
}

/* Detail Drawer items styling */
.drawer-section {
  border-bottom: 1px solid var(--color-border);
  padding-bottom: 20px;
  margin-bottom: 20px;
}

.drawer-section h3 {
  font-size: 13px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--text-secondary);
  margin-bottom: 12px;
}

.status-box {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: var(--bg-sidebar);
  padding: 16px 20px;
  border-radius: var(--radius-md);
  border: 1px solid var(--color-border);
}

.status-box__label {
  font-size: 13px;
  font-weight: 600;
  color: var(--text-secondary);
}

.info-grid {
  display: flex;
  flex-direction: column;
  gap: 8px;
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

.items-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.item-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 13px;
  padding: 8px 0;
}

.item-details {
  display: flex;
  flex-direction: column;
  gap: 2px;
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
</style>
