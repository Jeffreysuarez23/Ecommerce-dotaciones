<template>
  <div class="pedidos-page">

    <!-- ─── HERO ── -->
    <div class="pedidos__hero">
      <div class="pedidos__hero-bg"></div>
      <div class="pedidos__hero-content">
        <p class="pedidos__eyebrow">/ TU HISTORIAL</p>
        <h1 class="pedidos__title">
          Mis<br />
          <span class="pedidos__title--light">Pedidos</span>
        </h1>
      </div>
    </div>

    <!-- ─── BODY ── -->
    <div class="pedidos__body">

      <!-- Loading -->
      <div v-if="loading" class="pedidos__loading">
        <div class="spinner"></div>
        <p>Cargando pedidos...</p>
      </div>

      <!-- Empty -->
      <div v-else-if="ordenes.length === 0" class="pedidos__empty">
        <div class="empty__icon">
          <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
            <line x1="3" y1="6" x2="21" y2="6"/>
            <path d="M16 10a4 4 0 0 1-8 0"/>
          </svg>
        </div>
        <p class="empty__title">No tienes pedidos aún</p>
        <p class="empty__subtitle">Descubre nuestra colección y realiza tu primera compra.</p>
        <router-link to="/products" class="empty__btn">
          Explorar Productos
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
          </svg>
        </router-link>
      </div>

      <!-- Orders List -->
      <div v-else class="pedidos__list">
        <div
          v-for="orden in ordenes"
          :key="orden.id"
          class="pedido-card"
        >
          <!-- Card Header -->
          <div class="pedido-card__header" @click="toggleOrden(orden.id)">
            <div class="pedido-card__header-left">
              <p class="pedido-card__numero">{{ orden.numero }}</p>
              <p class="pedido-card__fecha">{{ formatDate(orden.creado_en) }}</p>
            </div>
            <div class="pedido-card__header-center">
              <span class="badge" :class="'badge--' + estadoClass(orden.estado)">{{ orden.estado }}</span>
              <span class="badge" :class="'badge--' + pagoClass(orden)">{{ pagoEstado(orden) }}</span>
            </div>
            <div class="pedido-card__header-right">
              <p class="pedido-card__total">{{ formatPrice(orden.total) }}</p>
              <svg
                class="pedido-card__arrow"
                :class="{ 'pedido-card__arrow--open': expandedId === orden.id }"
                width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
              >
                <polyline points="6 9 12 15 18 9"/>
              </svg>
            </div>
          </div>

          <!-- Card Body (expandable) -->
          <Transition name="expand">
            <div v-if="expandedId === orden.id" class="pedido-card__body">

              <!-- Items -->
              <div class="pedido-detail">
                <p class="pedido-detail__label">PRODUCTOS</p>
                <div class="pedido-items">
                  <div v-for="item in orden.items" :key="item.id" class="pedido-item">
                    <div class="pedido-item__img-wrap">
                      <img :src="getImage(item)" :alt="getProductName(item)" class="pedido-item__img" />
                    </div>
                    <div class="pedido-item__info">
                      <p class="pedido-item__name">{{ getProductName(item) }}</p>
                      <p class="pedido-item__variant">{{ getVariant(item) }}</p>
                      <p class="pedido-item__qty">Cantidad: {{ item.cantidad }}</p>
                    </div>
                    <div class="pedido-item__prices">
                      <p class="pedido-item__unit">{{ formatPrice(item.precio_unitario) }} c/u</p>
                      <p class="pedido-item__line-total">{{ formatPrice(item.total_linea) }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Summary -->
              <div class="pedido-summary">
                <div class="pedido-summary__row">
                  <span>Subtotal</span>
                  <span>{{ formatPrice(orden.subtotal) }}</span>
                </div>
                <div v-if="parseFloat(orden.descuento) > 0" class="pedido-summary__row pedido-summary__row--discount">
                  <span>Descuento</span>
                  <span>-{{ formatPrice(orden.descuento) }}</span>
                </div>
                <div class="pedido-summary__row">
                  <span>Envío</span>
                  <span v-if="parseFloat(orden.envio_costo) === 0" class="shipping--free">Gratis</span>
                  <span v-else>{{ formatPrice(orden.envio_costo) }}</span>
                </div>
                <div class="pedido-summary__row pedido-summary__row--total">
                  <span>Total</span>
                  <span class="total-amount">{{ formatPrice(orden.total) }}</span>
                </div>
              </div>

              <!-- Info Row -->
              <div class="pedido-info-grid">
                <div class="pedido-info-block">
                  <p class="pedido-info-block__label">MÉTODO DE PAGO</p>
                  <p class="pedido-info-block__value">{{ pagoMetodo(orden) }}</p>
                </div>
                <div class="pedido-info-block">
                  <p class="pedido-info-block__label">DIRECCIÓN DE ENVÍO</p>
                  <p class="pedido-info-block__value" v-if="orden.direccion">
                    {{ orden.direccion.direccion }}, {{ orden.direccion.ciudad }}, {{ orden.direccion.departamento }}
                  </p>
                  <p class="pedido-info-block__value" v-else>No disponible</p>
                </div>
                <div class="pedido-info-block" v-if="orden.envio">
                  <p class="pedido-info-block__label">TRACKING</p>
                  <p class="pedido-info-block__value">{{ orden.envio.guia }} — {{ orden.envio.estado }}</p>
                </div>
              </div>
            </div>
          </Transition>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const ordenes = ref([])
const loading = ref(true)
const expandedId = ref(null)

const fetchPedidos = async () => {
  const token = localStorage.getItem('auth_token')
  if (!token) {
    router.push('/login')
    return
  }

  try {
    const { data } = await axios.get((import.meta.env.VITE_API_URL || 'http://localhost:8000/api') + '/mis-pedidos', {
      headers: { Authorization: `Bearer ${token}` }
    })
    ordenes.value = data.data || []
  } catch (error) {
    console.error('Error fetching pedidos:', error)
    if (error.response?.status === 401) {
      localStorage.removeItem('auth_token')
      localStorage.removeItem('auth_user')
      router.push('/login')
    }
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchPedidos()
})

const toggleOrden = (id) => {
  expandedId.value = expandedId.value === id ? null : id
}

const formatPrice = (value) => {
  if (!value) return '$ 0'
  return new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 0
  }).format(value)
}

const formatDate = (dateStr) => {
  if (!dateStr) return ''
  const d = new Date(dateStr)
  return d.toLocaleDateString('es-CO', { year: 'numeric', month: 'long', day: 'numeric' })
}

const estadoClass = (estado) => {
  const map = {
    pendiente: 'yellow',
    pagado: 'green',
    confirmada: 'blue',
    procesando: 'blue',
    enviado: 'purple',
    entregado: 'green',
    cancelada: 'red',
    devuelta: 'red'
  }
  return map[estado] || 'yellow'
}

const pagoEstado = (orden) => {
  if (!orden.pago || orden.pago.length === 0) return 'Sin pago'
  return orden.pago[0].estado || 'pendiente'
}

const pagoClass = (orden) => {
  const estado = pagoEstado(orden)
  const map = {
    aprobado: 'green',
    pendiente: 'yellow',
    'Sin pago': 'gray',
    rechazado: 'red',
    reembolsado: 'red'
  }
  return map[estado] || 'yellow'
}

const pagoMetodo = (orden) => {
  if (!orden.pago || orden.pago.length === 0) return 'No registrado'
  return orden.pago[0].metodo || 'No especificado'
}

const getProductName = (item) => {
  return item.variante?.producto?.nombre || 'Producto'
}

const getVariant = (item) => {
  const v = item.variante
  if (!v) return ''
  return [v.color, v.talla].filter(Boolean).join(' — ')
}

const getImage = (item) => {
  const imagenes = item.variante?.producto?.imagenes
  if (imagenes && imagenes.length > 0) {
    const cover = imagenes.find(img => img.es_portada === 1)
    return cover ? cover.url : imagenes[0].url
  }
  return 'https://via.placeholder.com/80x100/f0ebe3/999?text=Sin+imagen'
}
</script>

<style scoped>
/* ── Base ── */
.pedidos-page {
  font-family: 'Cormorant Garamond', 'Georgia', serif;
  padding-top: 100px;
  background: #faf8f4;
  min-height: 100vh;
}

/* ── Hero ── */
.pedidos__hero {
  position: relative;
  background: #1a1a1a;
  padding: 80px 40px 60px;
}

.pedidos__hero-bg {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
  opacity: 0.95;
}

.pedidos__hero-content {
  position: relative;
  z-index: 2;
  max-width: 1400px;
  margin: 0 auto;
}

.pedidos__eyebrow {
  font-family: 'Inter', 'Helvetica Neue', sans-serif;
  font-size: 11px;
  font-weight: 500;
  letter-spacing: 0.15em;
  color: rgba(255,255,255,0.5);
  text-transform: uppercase;
  margin: 0 0 12px 0;
}

.pedidos__title {
  font-size: clamp(42px, 5vw, 72px);
  font-weight: 800;
  line-height: 1;
  margin: 0;
  color: white;
}

.pedidos__title--light {
  font-weight: 300;
  font-style: italic;
}

/* ── Body ── */
.pedidos__body {
  max-width: 1000px;
  margin: 0 auto;
  padding: 48px 40px 100px;
}

/* ── Loading ── */
.pedidos__loading {
  text-align: center;
  padding: 80px 20px;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 3px solid #e0d8cc;
  border-top-color: #1a1a1a;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin: 0 auto 16px;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.pedidos__loading p {
  font-family: 'Inter', sans-serif;
  font-size: 14px;
  color: #999;
}

/* ── Empty ── */
.pedidos__empty {
  text-align: center;
  padding: 80px 40px;
}

.empty__icon {
  margin-bottom: 24px;
  color: #d0cdc8;
}

.empty__title {
  font-size: 24px;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0 0 8px 0;
}

.empty__subtitle {
  font-family: 'Inter', sans-serif;
  font-size: 14px;
  color: #999;
  margin: 0 0 32px 0;
}

.empty__btn {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  background: #1a1a1a;
  color: white;
  padding: 14px 32px;
  border-radius: 100px;
  text-decoration: none;
  font-family: 'Cormorant Garamond', serif;
  font-weight: 600;
  transition: all 0.25s ease;
}

.empty__btn:hover {
  background: #6b5f4e;
  gap: 14px;
}

/* ── Orders List ── */
.pedidos__list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

/* ── Pedido Card ── */
.pedido-card {
  background: white;
  border-radius: 16px;
  box-shadow: 0 2px 16px rgba(0,0,0,0.04);
  overflow: hidden;
  transition: box-shadow 0.3s ease;
}

.pedido-card:hover {
  box-shadow: 0 4px 24px rgba(0,0,0,0.08);
}

.pedido-card__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 24px 28px;
  cursor: pointer;
  transition: background 0.2s ease;
}

.pedido-card__header:hover {
  background: #fdf9f3;
}

.pedido-card__header-left {
  flex: 1;
}

.pedido-card__numero {
  font-family: 'Inter', sans-serif;
  font-size: 14px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 4px 0;
}

.pedido-card__fecha {
  font-family: 'Inter', sans-serif;
  font-size: 12px;
  color: #999;
  margin: 0;
}

.pedido-card__header-center {
  display: flex;
  gap: 8px;
  flex: 1;
  justify-content: center;
}

.pedido-card__header-right {
  display: flex;
  align-items: center;
  gap: 16px;
}

.pedido-card__total {
  font-family: 'Inter', sans-serif;
  font-size: 16px;
  font-weight: 700;
  color: #6b5f4e;
  margin: 0;
}

.pedido-card__arrow {
  transition: transform 0.3s ease;
  color: #aaa;
}

.pedido-card__arrow--open {
  transform: rotate(180deg);
}

/* ── Badges ── */
.badge {
  display: inline-block;
  padding: 4px 14px;
  border-radius: 100px;
  font-family: 'Inter', sans-serif;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
}

.badge--green { background: #ecfdf5; color: #065f46; }
.badge--yellow { background: #fffbeb; color: #92400e; }
.badge--blue { background: #eff6ff; color: #1e40af; }
.badge--purple { background: #f5f3ff; color: #6d28d9; }
.badge--red { background: #fef2f2; color: #991b1b; }
.badge--gray { background: #f3f4f6; color: #6b7280; }

/* ── Card Body ── */
.pedido-card__body {
  padding: 0 28px 28px;
  border-top: 1px solid #f0ebe3;
}

/* ── Items ── */
.pedido-detail {
  margin-top: 20px;
}

.pedido-detail__label {
  font-family: 'Inter', sans-serif;
  font-size: 10px;
  font-weight: 600;
  letter-spacing: 0.12em;
  color: #aaa;
  margin: 0 0 12px 0;
}

.pedido-items {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.pedido-item {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 12px 0;
  border-bottom: 1px solid #f5f0e8;
}

.pedido-item:last-child {
  border-bottom: none;
}

.pedido-item__img-wrap {
  width: 56px;
  height: 72px;
  flex-shrink: 0;
  border-radius: 8px;
  overflow: hidden;
  background: #f0ebe3;
}

.pedido-item__img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.pedido-item__info {
  flex: 1;
}

.pedido-item__name {
  font-size: 15px;
  font-weight: 500;
  color: #1a1a1a;
  margin: 0 0 2px 0;
}

.pedido-item__variant {
  font-family: 'Inter', sans-serif;
  font-size: 12px;
  color: #999;
  margin: 0 0 2px 0;
}

.pedido-item__qty {
  font-family: 'Inter', sans-serif;
  font-size: 12px;
  color: #aaa;
  margin: 0;
}

.pedido-item__prices {
  text-align: right;
}

.pedido-item__unit {
  font-family: 'Inter', sans-serif;
  font-size: 11px;
  color: #aaa;
  margin: 0 0 2px 0;
}

.pedido-item__line-total {
  font-size: 15px;
  font-weight: 600;
  color: #6b5f4e;
  margin: 0;
}

/* ── Summary ── */
.pedido-summary {
  background: #faf8f4;
  border-radius: 12px;
  padding: 20px 24px;
  margin-top: 20px;
}

.pedido-summary__row {
  display: flex;
  justify-content: space-between;
  padding: 6px 0;
  font-family: 'Inter', sans-serif;
  font-size: 14px;
  color: #555;
}

.pedido-summary__row--discount {
  color: #7a9e7e;
}

.pedido-summary__row--total {
  padding-top: 12px;
  margin-top: 8px;
  border-top: 1px solid #e0d8cc;
  font-weight: 700;
  font-size: 16px;
  color: #1a1a1a;
}

.total-amount {
  color: #6b5f4e;
  font-size: 18px;
  font-weight: 700;
}

.shipping--free {
  color: #7a9e7e;
  font-weight: 500;
}

/* ── Info Grid ── */
.pedido-info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
  margin-top: 20px;
}

.pedido-info-block {
  background: #faf8f4;
  border-radius: 12px;
  padding: 16px 20px;
}

.pedido-info-block__label {
  font-family: 'Inter', sans-serif;
  font-size: 10px;
  font-weight: 600;
  letter-spacing: 0.12em;
  color: #aaa;
  margin: 0 0 6px 0;
}

.pedido-info-block__value {
  font-size: 14px;
  color: #444;
  margin: 0;
  line-height: 1.5;
}

/* ── Expand Transition ── */
.expand-enter-active {
  transition: all 0.35s ease;
  max-height: 1000px;
  overflow: hidden;
}

.expand-leave-active {
  transition: all 0.25s ease;
  max-height: 1000px;
  overflow: hidden;
}

.expand-enter-from,
.expand-leave-to {
  max-height: 0;
  opacity: 0;
  padding-top: 0;
  padding-bottom: 0;
}

/* ── Responsive ── */
@media (max-width: 768px) {
  .pedidos__body {
    padding: 32px 20px 60px;
  }

  .pedidos__hero {
    padding: 60px 20px 40px;
  }

  .pedido-card__header {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
    padding: 20px 20px;
  }

  .pedido-card__header-center {
    justify-content: flex-start;
  }

  .pedido-card__header-right {
    width: 100%;
    justify-content: space-between;
  }

  .pedido-card__body {
    padding: 0 20px 20px;
  }

  .pedido-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
  }

  .pedido-item__prices {
    text-align: left;
  }

  .pedido-info-grid {
    grid-template-columns: 1fr;
  }
}
</style>
