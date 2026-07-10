<template>
  <div class="cart-page">
    <!-- Hero / header minimalista -->
    <div class="cart__hero">
      <div class="cart__hero-bg"></div>
      <div class="cart__hero-content">
        <p class="cart__eyebrow">/ TU SELECCIÓN</p>
        <h1 class="cart__title">
          Bolsa de<br />
          <span class="cart__title--light">Compras</span>
        </h1>
      </div>
    </div>

    <div class="cart__inner" v-reveal>
      <!-- Lista de productos -->
      <div v-if="cartItems.length" class="cart__layout">
        <div class="cart__items">
          <div
            v-for="item in cartItems"
            :key="item.id"
            class="cart-item"
          >
            <div class="cart-item__image-wrap">
              <img :src="item.image" :alt="item.name" class="cart-item__image" />
            </div>

            <div class="cart-item__details">
              <div>
                <p class="cart-item__category">{{ item.category }}</p>
                <p class="cart-item__name">{{ item.name }}</p>
                <p class="cart-item__price">{{ formatPrice(item.price) }}</p>
              </div>

              <div class="cart-item__actions">
                <div class="cart-item__quantity">
                  <button
                    class="qty-btn"
                    @click="updateQuantity(item.id, item.quantity - 1)"
                    :disabled="item.quantity <= 1"
                  >
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                  </button>
                  <span class="qty-value">{{ item.quantity }}</span>
                  <button
                    class="qty-btn"
                    @click="updateQuantity(item.id, item.quantity + 1)"
                  >
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <line x1="12" y1="5" x2="12" y2="19"/>
                      <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                  </button>
                </div>

                <button
                  class="cart-item__remove"
                  @click="removeItem(item.id)"
                >
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                  </svg>
                  <span>Eliminar</span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Resumen del carrito -->
        <div class="cart__summary">
          <div class="summary__card">
            <p class="summary__title">Resumen del Pedido</p>

            <div class="summary__row">
              <span>Subtotal</span>
              <span>{{ formatPrice(subtotal) }}</span>
            </div>
            <div class="summary__row">
              <span>Envío</span>
              <span v-if="subtotal > 200000" class="shipping--free">Gratis</span>
              <span v-else>{{ formatPrice(15000) }}</span>
            </div>
            <div class="summary__row summary__row--total">
              <span>Total</span>
              <span class="total-amount">{{ formatPrice(total) }}</span>
            </div>

            <button v-if="isLoggedIn" class="checkout-btn" @click="proceedToCheckout">
              Continuar con la compra
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="5" y1="12" x2="19" y2="12"/>
                <polyline points="12 5 19 12 12 19"/>
              </svg>
            </button>

            <button v-else class="checkout-btn checkout-btn--login" @click="goToLogin">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/>
                <polyline points="10 17 15 12 10 7"/>
                <line x1="15" y1="12" x2="3" y2="12"/>
              </svg>
              Inicia sesión para comprar
            </button>
            <p v-if="!isLoggedIn" class="summary__login-hint">
              Debes iniciar sesión para continuar con tu compra.
            </p>

            <p class="summary__note">
              Envío gratis en pedidos superiores a $200.000
            </p>
          </div>
        </div>
      </div>

      <!-- Estado vacío -->
      <div v-else class="cart__empty">
        <div class="empty__icon">
          <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
            <line x1="3" y1="6" x2="21" y2="6"/>
            <path d="M16 10a4 4 0 0 1-8 0"/>
          </svg>
        </div>
        <p class="empty__title">Tu bolsa está vacía</p>
        <p class="empty__subtitle">Descubre piezas que importan.</p>
        <router-link to="/products" class="empty__btn">
          Comprar Colección
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="5" y1="12" x2="19" y2="12"/>
            <polyline points="12 5 19 12 12 19"/>
          </svg>
        </router-link>
      </div>

      <!-- Alerta global de stock -->
      <transition name="fade-slide">
        <div v-if="stockAlertMsg" class="stock-alert-toast">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
          </svg>
          <span>{{ stockAlertMsg }}</span>
        </div>
      </transition>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { updateCartCount } from '../cartState'

const router = useRouter()

const cartItems = ref([])
const stockAlertMsg = ref('')
const stockAlertTimeout = ref(null)

const showStockAlert = (msg) => {
  stockAlertMsg.value = msg
  if (stockAlertTimeout.value) clearTimeout(stockAlertTimeout.value)
  stockAlertTimeout.value = setTimeout(() => {
    stockAlertMsg.value = ''
  }, 4000)
}

const fetchCart = async () => {
  const cartId = localStorage.getItem('carrito_id')
  if (!cartId) return

  try {
    const { data } = await axios.get(`http://localhost:8000/api/carritos/${cartId}`)
    if (data && data.items) {
      cartItems.value = data.items.map(item => {
        const v = item.variante
        const p = v.producto || {}
        
        let extra = parseFloat(v.precio_extra) || 0
        let desc = parseInt(v.descuento) || 0
        let base = parseFloat(p.precio_minorista || 0) + extra
        let finalPrice = base * (1 - desc / 100)

        let image = 'https://via.placeholder.com/400'
        
        if (p.imagenes && p.imagenes.length > 0) {
          const cover = p.imagenes.find(img => img.es_portada === 1)
          image = cover ? cover.url : p.imagenes[0].url
        }

        return {
          id: item.id, // CarritoItem ID
          name: `${p.nombre || 'Producto'} (${v.color} - ${v.talla})`,
          category: p.categoria ? p.categoria.nombre : 'General',
          price: finalPrice,
          quantity: item.cantidad,
          stock: v.stock || 0,
          image: image
        }
      })
    }
  } catch (error) {
    console.error('Error fetching cart:', error)
  }
}

onMounted(() => {
  fetchCart()
})

const subtotal = computed(() => {
  return cartItems.value.reduce((sum, item) => sum + (item.price * item.quantity), 0)
})

const shippingCost = computed(() => {
  if (cartItems.value.length === 0) return 0
  return subtotal.value > 200000 ? 0 : 15000
})

const total = computed(() => {
  return subtotal.value + shippingCost.value
})

const formatPrice = (value) => {
  if (value === undefined || value === null) return '$ 0'
  return new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 0
  }).format(value)
}

const updateQuantity = async (id, newQuantity) => {
  if (newQuantity < 1) return
  const item = cartItems.value.find(i => i.id === id)
  if (item) {
    if (newQuantity > item.stock) {
      showStockAlert(`Solo quedan ${item.stock} unidades disponibles de "${item.name}".`)
      return
    }
    
    item.quantity = newQuantity
    try {
      await axios.put(`http://localhost:8000/api/carrito-items/${id}`, { cantidad: newQuantity })
      await updateCartCount()
    } catch (error) {
      console.error('Error updating quantity:', error)
    }
  }
}

const removeItem = async (id) => {
  try {
    await axios.delete(`http://localhost:8000/api/carrito-items/${id}`)
    cartItems.value = cartItems.value.filter(i => i.id !== id)
    await updateCartCount()
  } catch (error) {
    console.error('Error removing item:', error)
  }
}

const isLoggedIn = computed(() => {
  return !!localStorage.getItem('auth_token')
})

const proceedToCheckout = () => {
  router.push('/checkout')
}

const goToLogin = () => {
  router.push('/login')
}
</script>

<style scoped>
/* ── Estilos alineados con el sistema de diseño de la home ── */
.cart-page {
  font-family: 'Cormorant Garamond', 'Georgia', serif;
  padding-top: 100px;
  background: #faf8f4;
  min-height: 100vh;
}

/* Hero minimalista */
.cart__hero {
  position: relative;
  background: #1a1a1a;
  padding: 80px 40px 60px;
  margin-bottom: 0;
}

.cart__hero-bg {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
  opacity: 0.95;
}

.cart__hero-content {
  position: relative;
  z-index: 2;
  max-width: 1400px;
  margin: 0 auto;
}

.cart__eyebrow {
  font-family: 'Inter', 'Helvetica Neue', sans-serif;
  font-size: 11px;
  font-weight: 500;
  letter-spacing: 0.15em;
  color: rgba(255,255,255,0.5);
  text-transform: uppercase;
  margin: 0 0 12px 0;
}

.cart__title {
  font-size: clamp(42px, 5vw, 72px);
  font-weight: 800;
  line-height: 1;
  margin: 0;
  color: white;
}

.cart__title--light {
  font-weight: 300;
  font-style: italic;
}

/* Layout principal */
.cart__inner {
  max-width: 1400px;
  margin: 0 auto;
  padding: 60px 40px;
}

.cart__layout {
  display: grid;
  grid-template-columns: 1fr 380px;
  gap: 60px;
}

/* Items del carrito */
.cart__items {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.cart-item {
  display: flex;
  gap: 20px;
  padding-bottom: 16px;
  border-bottom: 1px solid #e0d8cc;
  align-items: center;
}

.cart-item__image-wrap {
  width: 70px;
  height: 90px;
  flex-shrink: 0;
  border-radius: 6px;
  overflow: hidden;
  background: #f0ebe3;
}

.cart-item__image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.4s ease;
}

.cart-item:hover .cart-item__image {
  transform: scale(1.05);
}

.cart-item__details {
  flex: 1;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.cart-item__category {
  font-family: 'Inter', sans-serif;
  font-size: 10px;
  font-weight: 500;
  letter-spacing: 0.1em;
  color: #aaa;
  margin: 0 0 4px 0;
}

.cart-item__name {
  font-size: 15px;
  font-weight: 500;
  color: #1a1a1a;
  margin: 0 0 4px 0;
}

.cart-item__price {
  font-size: 14px;
  font-weight: 600;
  color: #6b5f4e;
  margin: 0;
}

.cart-item__actions {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 20px;
}

.cart-item__quantity {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #f5f0e8;
  border-radius: 100px;
  padding: 4px 8px;
}

.qty-btn {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: white;
  border: 1px solid #d0cdc8;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
  color: #1a1a1a;
}

.qty-btn:hover:not(:disabled) {
  background: #1a1a1a;
  color: white;
  border-color: #1a1a1a;
}

.qty-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.qty-value {
  font-family: 'Inter', sans-serif;
  font-weight: 500;
  min-width: 24px;
  text-align: center;
}

.cart-item__remove {
  display: flex;
  align-items: center;
  gap: 4px;
  background: none;
  border: none;
  font-family: 'Inter', sans-serif;
  font-size: 11px;
  font-weight: 500;
  color: #999;
  cursor: pointer;
  transition: color 0.2s ease;
}

.cart-item__remove:hover {
  color: #c44a2c;
}

/* Resumen */
.cart__summary {
  position: sticky;
  top: 100px;
  align-self: start;
}

.summary__card {
  background: white;
  border-radius: 16px;
  padding: 32px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}

.summary__title {
  font-size: 20px;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0 0 28px 0;
  letter-spacing: -0.02em;
}

.summary__row {
  display: flex;
  justify-content: space-between;
  padding: 12px 0;
  font-family: 'Inter', sans-serif;
  font-size: 14px;
  color: #555;
  border-bottom: 1px solid #f0ebe3;
}

.summary__row--total {
  border-bottom: none;
  padding-top: 20px;
  margin-top: 8px;
  font-weight: 700;
  font-size: 18px;
  color: #1a1a1a;
}

.total-amount {
  color: #6b5f4e;
  font-size: 20px;
  font-weight: 700;
}

.shipping--free {
  color: #7a9e7e;
  font-weight: 500;
}

.checkout-btn {
  width: 100%;
  background: #1a1a1a;
  color: white;
  border: none;
  padding: 16px 24px;
  border-radius: 100px;
  font-family: 'Cormorant Garamond', serif;
  font-size: 16px;
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  cursor: pointer;
  transition: all 0.25s ease;
  margin: 24px 0 16px;
}

.checkout-btn:hover {
  background: #6b5f4e;
  transform: translateY(-1px);
}

.summary__note {
  font-family: 'Inter', sans-serif;
  font-size: 11px;
  color: #aaa;
  text-align: center;
  margin: 0;
}

.checkout-btn--login {
  background: linear-gradient(135deg, #6b5f4e 0%, #8a7d6b 100%);
}

.checkout-btn--login:hover {
  background: linear-gradient(135deg, #5a5040 0%, #7a6d5b 100%);
}

.summary__login-hint {
  font-family: 'Inter', sans-serif;
  font-size: 12px;
  color: #b08d57;
  text-align: center;
  margin: 0 0 12px 0;
  padding: 8px 12px;
  background: #fdf8f0;
  border-radius: 8px;
  border: 1px solid #f0e6d3;
}

/* Estado vacío */
.cart__empty {
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

/* Responsive */
@media (max-width: 900px) {
  .cart__layout {
    grid-template-columns: 1fr;
    gap: 40px;
  }

  .cart__inner {
    padding: 40px 24px;
  }

  .cart__hero {
    padding: 60px 24px 40px;
  }

  .cart-item {
    flex-direction: column;
    gap: 16px;
  }

  .cart-item__image-wrap {
    width: 100%;
    height: auto;
    aspect-ratio: 3 / 4;
    max-width: 200px;
  }

  .cart-item__details {
    flex-direction: column;
    gap: 16px;
  }

  .cart-item__actions {
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    width: 100%;
  }
}

/* Alerta de Límite de Stock */
.stock-alert-toast {
  position: fixed;
  bottom: 40px;
  left: 50%;
  transform: translateX(-50%);
  background: #1a1a1a;
  color: white;
  padding: 16px 24px;
  border-radius: 100px;
  display: flex;
  align-items: center;
  gap: 12px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  z-index: 1000;
  font-family: 'Inter', sans-serif;
  font-size: 14px;
  font-weight: 500;
  pointer-events: none;
}

.stock-alert-toast svg {
  color: #ef4444;
}

.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.fade-slide-enter-from,
.fade-slide-leave-to {
  opacity: 0;
  transform: translate(-50%, 20px);
}
</style>