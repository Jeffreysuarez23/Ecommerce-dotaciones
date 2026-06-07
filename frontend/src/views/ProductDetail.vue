<template>
  <div class="detail-page">

    <!-- ─── BREADCRUMB ── -->
    <template v-if="!loading && product.id">
      <div class="detail__breadcrumb-wrap">
        <div class="detail__breadcrumb">
          <router-link to="/" class="breadcrumb__link">Inicio</router-link>
          <span class="breadcrumb__sep">/</span>
          <router-link to="/products" class="breadcrumb__link">Productos</router-link>
          <span class="breadcrumb__sep">/</span>
          <span class="breadcrumb__current">{{ product.name }}</span>
        </div>
      </div>

      <!-- ─── PRODUCT LAYOUT ── -->
      <div class="detail__inner">
      <div class="detail__layout">

        <!-- LEFT: Gallery -->
        <div class="detail__gallery">
          <div class="gallery__main">
            <img :src="activeImage" :alt="product.name" class="gallery__main-img" />
            <span
              v-if="product.badge"
              class="gallery__badge"
              :class="`gallery__badge--${product.badgeType}`"
            >
              {{ product.badge }}
            </span>
          </div>
          <div class="gallery__thumbs">
            <button
              v-for="(img, i) in product.images"
              :key="i"
              class="gallery__thumb"
              :class="{ 'gallery__thumb--active': activeImageIndex === i }"
              @click="activeImageIndex = i"
            >
              <img :src="img" :alt="`${product.name} view ${i + 1}`" />
            </button>
          </div>
        </div>

        <!-- RIGHT: Info -->
        <div class="detail__info">
          <p class="detail__category">{{ product.category }}</p>
          <h1 class="detail__name">{{ product.name }}</h1>

          <div class="detail__pricing">
            <span class="detail__price">{{ formatPrice(currentPrice) }}</span>
            <span v-if="currentOriginalPrice" class="detail__original">{{ formatPrice(currentOriginalPrice) }}</span>
            <span v-if="currentDiscountPercent > 0" class="detail__discount">
              -{{ currentDiscountPercent }}%
            </span>
          </div>

          <div class="detail__description" v-html="product.description"></div>

          <div class="detail__divider"></div>

          <!-- Color Selector -->
          <div class="detail__option">
            <p class="detail__option-label">
              Color: <span class="detail__option-value">{{ selectedColor }}</span>
            </p>
            <div class="color-options">
              <button
                v-for="color in product.colors"
                :key="color.name"
                class="color-swatch"
                :class="{ 'color-swatch--active': selectedColor === color.name }"
                :style="{ background: color.hex }"
                :title="color.name"
                @click="selectedColor = color.name"
              >
                <svg v-if="selectedColor === color.name" width="14" height="14" viewBox="0 0 24 24" fill="none" :stroke="color.checkColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="20 6 9 17 4 12"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Size Selector -->
          <div class="detail__option">
            <p class="detail__option-label">
              Talla: <span class="detail__option-value">{{ selectedSize }}</span>
            </p>
            <div class="size-options">
              <button
                v-for="size in availableSizes"
                :key="size.label"
                class="size-btn"
                :class="{
                  'size-btn--active': selectedSize === size.label,
                  'size-btn--disabled': !size.inStock
                }"
                :disabled="!size.inStock"
                @click="selectedSize = size.label"
              >
                <span>{{ size.label }}</span>
                <span v-if="size.stock !== undefined" class="size-btn__stock">{{ size.stock }} uds</span>
              </button>
            </div>
          </div>

          <!-- Quantity -->
          <div class="detail__option">
            <p class="detail__option-label">Cantidad</p>
            <div class="quantity-selector">
              <button class="qty-btn" @click="quantity > 1 && quantity--" :disabled="quantity <= 1">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
              </button>
              <span class="qty-value">{{ quantity }}</span>
              <button class="qty-btn" @click="quantity++">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="12" y1="5" x2="12" y2="19"/>
                  <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
              </button>
            </div>
          </div>

          <div class="detail__divider"></div>

          <!-- Actions -->
          <div class="detail__actions">
            <button class="add-to-cart-btn" @click="addToCart">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/>
              </svg>
              <span v-if="!added">Añadir al Carrito — {{ formatPrice(currentPrice * quantity) }}</span>
              <span v-else>Añadido ✓</span>
            </button>
          </div>

          <!-- Shipping info -->
          <div class="detail__shipping-info">
            <div class="shipping-item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
              </svg>
              <span>Envío gratis por más de $200</span>
            </div>
            <div class="shipping-item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/>
              </svg>
              <span>Devoluciones fáciles (30 días)</span>
            </div>
            <div class="shipping-item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
              </svg>
              <span>Pago seguro</span>
            </div>
          </div>
        </div>

      </div>
    </div>
    </template>
    
    <div v-else-if="loading" class="detail__loading" style="padding: 100px; text-align: center;">
      <p>Cargando detalles del producto...</p>
    </div>
    <div v-else class="detail__loading" style="padding: 100px; text-align: center;">
      <p>Producto no encontrado.</p>
    </div>

  </div>
</template>

<script>
import axios from 'axios'
import { updateCartCount } from '../cartState'

export default {
  name: 'ProductDetailView',
  data() {
    return {
      loading: true,
      error: '',
      activeImageIndex: 0,
      selectedColor: '',
      selectedSize: '',
      quantity: 1,
      added: false,
      product: {
        id: null,
        name: '',
        category: 'General',
        price: 0,
        originalPrice: null,
        badge: null,
        badgeType: '',
        rating: 5.0,
        reviews: 0,
        description: '',
        images: [],
        colors: [],
        sizes: [],
        rawVariants: []
      }
    }
  },
  computed: {
    activeImage() {
      return this.product.images[this.activeImageIndex] || 'https://images.unsplash.com/photo-1551488831-00ddcb6c6bd3?w=800&q=80'
    },
    currentPrice() {
      let extra = 0
      let descuento = 0
      if (this.selectedColor && this.selectedSize && this.product.rawVariants) {
        const variant = this.product.rawVariants.find(v => v.color === this.selectedColor && v.talla === this.selectedSize)
        if (variant) {
          if (variant.precio_extra) extra = parseFloat(variant.precio_extra) || 0
          if (variant.descuento) descuento = parseInt(variant.descuento) || 0
        }
      }
      const base = this.product.price + extra
      return base * (1 - descuento / 100)
    },
    currentOriginalPrice() {
      let extra = 0
      let descuento = 0
      if (this.selectedColor && this.selectedSize && this.product.rawVariants) {
        const variant = this.product.rawVariants.find(v => v.color === this.selectedColor && v.talla === this.selectedSize)
        if (variant) {
          if (variant.precio_extra) extra = parseFloat(variant.precio_extra) || 0
          if (variant.descuento) descuento = parseInt(variant.descuento) || 0
        }
      }
      const base = this.product.price + extra
      if (descuento > 0) return base
      return null
    },
    currentDiscountPercent() {
      let descuento = 0
      if (this.selectedColor && this.selectedSize && this.product.rawVariants) {
        const variant = this.product.rawVariants.find(v => v.color === this.selectedColor && v.talla === this.selectedSize)
        if (variant && variant.descuento) {
          descuento = parseInt(variant.descuento) || 0
        }
      }
      return descuento
    },
    // Dynamically compute available sizes based on selected color
    availableSizes() {
      if (!this.selectedColor) return this.product.sizes
      
      const sizesForColor = this.product.sizes.filter(s => s.color.toUpperCase() === this.selectedColor.toUpperCase())
      
      return sizesForColor.length > 0 ? sizesForColor : this.product.sizes
    }
  },
  watch: {
    // When selected color changes, select first available size
    selectedColor(newColor) {
      if (newColor && this.availableSizes.length > 0) {
        const firstInStock = this.availableSizes.find(s => s.inStock)
        this.selectedSize = firstInStock ? firstInStock.label : ''
      }
    }
  },
  mounted() {
    this.fetchProduct()
  },
  methods: {
    async fetchProduct() {
      const id = parseInt(this.$route.params.id)
      this.loading = true
      this.error = ''
      try {
        const { data } = await axios.get(`http://localhost:8000/api/productos/${id}`)
        
        const minorista = parseFloat(data.precio_minorista) || 0
        
        let badge = null
        if (!data.publicado) badge = 'Inactivo'
        
        // Extract unique colors from variantes
        const colorsMap = {}
        if (data.variantes) {
          data.variantes.forEach(v => {
            if (v.color) {
              if (!colorsMap[v.color] || (v.color_hex && !colorsMap[v.color].hasCustomHex)) {
                 const cData = this.getColorData(v.color)
                 let checkColor = cData.check
                 let finalHex = cData.hex
                 let hasCustomHex = false
                 
                 if (v.color_hex) {
                    finalHex = v.color_hex
                    hasCustomHex = true
                    const hex = v.color_hex.replace('#', '')
                    if (hex.length === 6) {
                      const r = parseInt(hex.substr(0, 2), 16)
                      const g = parseInt(hex.substr(2, 2), 16)
                      const b = parseInt(hex.substr(4, 2), 16)
                      const yiq = ((r * 299) + (g * 587) + (b * 114)) / 1000
                      checkColor = (yiq >= 128) ? '#1a1a1a' : '#fff'
                    }
                 }
                 
                 colorsMap[v.color] = { 
                   name: v.color, 
                   hex: finalHex, 
                   checkColor: checkColor,
                   hasCustomHex: hasCustomHex
                 }
              }
            }
          })
        }
        const colors = Object.values(colorsMap)
        
        // Extract sizes from raw variants, using variante_producto stock ALWAYS
        const sizesMapUnique = {}
        if (data.variantes) {
          data.variantes.forEach(v => {
            const vColor = (v.color && v.color !== 'Defecto') ? v.color : (v.lona ? v.lona.color : v.color)
            const vColorUpper = vColor ? vColor.toUpperCase() : 'DEFAULT'
            
            if (v.talla) {
              const key = `${vColorUpper}-${v.talla}`
              // Siempre tomamos v.stock (de variante_producto) como pidió el usuario
              if (!sizesMapUnique[key]) {
                sizesMapUnique[key] = { label: v.talla, color: vColor, stock: v.stock, inStock: v.stock > 0 }
              }
            }
          })
        }
        const sizes = Object.values(sizesMapUnique)
        
        let coverIndex = 0
        if (data.imagenes && data.imagenes.length > 0) {
          const idx = data.imagenes.findIndex(img => img.es_portada === 1)
          if (idx !== -1) coverIndex = idx
        }
        
        this.product = {
          id: data.id,
          name: data.nombre,
          category: data.categoria ? data.categoria.nombre : 'General',
          price: minorista,
          originalPrice: null,
          badge: badge,
          badgeType: badge === 'Oferta' ? 'dark' : 'outline',
          rating: 5.0,
          reviews: 0,
          description: data.descripcion || 'Producto sin descripción detallada.',
          images: data.imagenes && data.imagenes.length > 0 ? data.imagenes.map(img => img.url) : [],
          colors: colors,
          sizes: sizes,
          rawVariants: data.variantes || []
        }
        
        this.activeImageIndex = coverIndex
        
        // Setup initial defaults
        if (colors.length > 0) this.selectedColor = colors[0].name
        if (sizes.length > 0) this.selectedSize = sizes.find(s => s.inStock)?.label || sizes[0].label
        
      } catch (err) {
        console.error('Error fetching product:', err)
        this.error = 'No se pudo cargar el producto'
      } finally {
        this.loading = false
      }
    },
    async addToCart() {
      if (!this.selectedSize || !this.selectedColor) return
      
      const variant = this.product.rawVariants.find(v => v.color === this.selectedColor && v.talla === this.selectedSize)
      if (!variant) {
        alert('Variante no encontrada')
        return
      }

      try {
        let cartId = localStorage.getItem('carrito_id')
        
        // Si no existe, creamos el carrito
        if (!cartId) {
          const { data } = await axios.post('http://localhost:8000/api/carritos', {
            session_id: Date.now().toString()
          })
          cartId = data.data.id
          localStorage.setItem('carrito_id', cartId)
        }

        // Agregamos el item al carrito
        await axios.post(`http://localhost:8000/api/carritos/${cartId}/items`, {
          variante_id: variant.id,
          cantidad: this.quantity
        })

        // Actualizar el contador global del carrito
        await updateCartCount()

        this.added = true
        this.$emit('add-to-cart', {
          product: this.product,
          variant_id: variant.id,
          color: this.selectedColor,
          size: this.selectedSize,
          quantity: this.quantity
        })
        
        setTimeout(() => { this.added = false }, 2500)
      } catch (error) {
        console.error('Error al agregar al carrito:', error)
        alert('Hubo un error al agregar el producto al carrito.')
      }
    },
    formatPrice(value) {
      if (!value) return '$ 0'
      return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 0
      }).format(value)
    },
    getColorData(name) {
      if (!name) return { hex: '#cccccc', check: '#1a1a1a' }
      const n = name.trim().toUpperCase()
      
      const map = {
        'BLANCO': { hex: '#FFFFFF', check: '#1a1a1a' },
        'NEGRO': { hex: '#1a1a1a', check: '#fff' },
        'AZUL CLARO': { hex: '#ADD8E6', check: '#1a1a1a' },
        'AZUL OSCURO': { hex: '#00008B', check: '#fff' },
        'AZUL MARINO': { hex: '#000080', check: '#fff' },
        'AZUL': { hex: '#1D4ED8', check: '#fff' }, // standard blue
        'ROJO': { hex: '#DC2626', check: '#fff' },
        'VERDE': { hex: '#15803D', check: '#fff' },
        'AMARILLO': { hex: '#FACC15', check: '#1a1a1a' },
        'NARANJA': { hex: '#F97316', check: '#1a1a1a' },
        'GRIS': { hex: '#9CA3AF', check: '#1a1a1a' },
        'CAFE': { hex: '#78350F', check: '#fff' },
        'MARRON': { hex: '#78350F', check: '#fff' },
        'ROSA': { hex: '#F472B6', check: '#1a1a1a' },
        'ROSADO': { hex: '#F472B6', check: '#1a1a1a' },
        'MORADO': { hex: '#7E22CE', check: '#fff' },
        'VIOLETA': { hex: '#A855F7', check: '#fff' },
        'BEIGE': { hex: '#F5F5DC', check: '#1a1a1a' },
        'VINO': { hex: '#4e070c', check: '#fff' },
        'ORO': { hex: '#D4AF37', check: '#1a1a1a' }
      }
      
      for (const key in map) {
        if (n.includes(key)) return map[key]
      }
      
      return { hex: '#cccccc', check: '#1a1a1a' } // default fallback
    }
  }
}
</script>

<style scoped>
.detail-page {
  padding-top: 100px;
  min-height: 100vh;
  background: #fff;
  font-family: 'Cormorant Garamond', 'Georgia', serif;
  color: #1a1a1a;
}

/* ── Breadcrumb ── */
.detail__breadcrumb-wrap {
  background: #faf8f4;
  border-bottom: 1px solid #ede8de;
  padding: 16px 40px;
}

.detail__breadcrumb {
  max-width: 1400px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  gap: 8px;
  font-family: 'Inter', sans-serif;
  font-size: 12px;
}

.breadcrumb__link {
  color: #999;
  text-decoration: none;
  transition: color 0.2s;
}

.breadcrumb__link:hover {
  color: #1a1a1a;
}

.breadcrumb__sep {
  color: #ddd;
}

.breadcrumb__current {
  color: #1a1a1a;
  font-weight: 600;
}

/* ── Layout ── */
.detail__inner {
  max-width: 1200px;
  margin: 0 auto;
  padding: 36px 40px 80px;
}

.detail__layout {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 56px;
  align-items: start;
}

/* ── Gallery ── */
.gallery__main {
  position: relative;
  border-radius: 12px;
  overflow: hidden;
  background: #f5f3ef;
  aspect-ratio: 1 / 1;
  max-height: 480px;
  margin-bottom: 12px;
}

.gallery__main-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.6s ease;
}

.gallery__main:hover .gallery__main-img {
  transform: scale(1.03);
}

.gallery__badge {
  position: absolute;
  top: 14px;
  left: 14px;
  padding: 5px 14px;
  border-radius: 100px;
  font-family: 'Inter', sans-serif;
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 0.03em;
  z-index: 2;
}

.gallery__badge--dark { background: #1a1a1a; color: white; }
.gallery__badge--light { background: #f5f0e8; color: #7a6a50; border: 1px solid #e0d5c0; }
.gallery__badge--outline { background: white; color: #1a1a1a; border: 1px solid #d0cdc8; }

.gallery__thumbs {
  display: flex;
  gap: 12px;
}

.gallery__thumb {
  width: 64px;
  height: 80px;
  border-radius: 8px;
  overflow: hidden;
  border: 2px solid transparent;
  background: #f5f3ef;
  cursor: pointer;
  padding: 0;
  transition: border-color 0.2s ease, opacity 0.2s ease;
}

.gallery__thumb img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.gallery__thumb--active {
  border-color: #1a1a1a;
}

.gallery__thumb:not(.gallery__thumb--active):hover {
  opacity: 0.7;
}

/* ── Info ── */
.detail__category {
  font-family: 'Inter', sans-serif;
  font-size: 11px;
  font-weight: 500;
  letter-spacing: 0.15em;
  color: #aaa;
  text-transform: uppercase;
  margin: 0 0 10px 0;
}

.detail__name {
  font-size: clamp(22px, 2.5vw, 30px);
  font-weight: 700;
  line-height: 1.2;
  margin: 0 0 14px 0;
  color: #1a1a1a;
}

.detail__pricing {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 12px;
}

.detail__price {
  font-size: 22px;
  font-weight: 700;
  color: #1a1a1a;
}

.detail__original {
  font-size: 15px;
  color: #bbb;
  text-decoration: line-through;
}

.detail__discount {
  font-family: 'Inter', sans-serif;
  font-size: 12px;
  font-weight: 600;
  background: #fce4e4;
  color: #c0392b;
  padding: 4px 10px;
  border-radius: 100px;
}


.detail__stars {
  display: flex;
  gap: 2px;
}

.star {
  font-size: 16px;
  color: #e0d8cc;
}

.star--filled {
  color: #f5a623;
}


.detail__description {
  font-size: 14px;
  line-height: 1.65;
  color: #666;
  margin: 0;
}

.detail__divider {
  height: 1px;
  background: #f0ebe3;
  margin: 20px 0;
}

/* ── Options ── */
.detail__option {
  margin-bottom: 18px;
}

.detail__option-label {
  font-family: 'Inter', sans-serif;
  font-size: 13px;
  font-weight: 500;
  color: #444;
  margin: 0 0 8px 0;
}

.detail__option-value {
  font-weight: 600;
  color: #1a1a1a;
}

/* Colors */
.color-options {
  display: flex;
  gap: 10px;
}

.color-swatch {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  border: 2px solid transparent;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0;
  transition: all 0.2s ease;
  box-shadow: inset 0 0 0 1px rgba(0,0,0,0.08);
}

.color-swatch:hover {
  transform: scale(1.1);
}

.color-swatch--active {
  transform: scale(1.1);
  border-color: rgba(0,0,0,0.15);
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

/* Sizes */
.size-options {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.size-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  height: 38px;
  padding: 0 14px;
  border: 1px solid #e5e0d8;
  border-radius: 6px;
  background: #f5f3ef;
  font-family: 'Inter', sans-serif;
  font-size: 13px;
  font-weight: 600;
  color: #333;
  cursor: pointer;
  transition: all 0.2s ease;
}

.size-btn__stock {
  font-weight: 400;
  color: #888;
  font-size: 11px;
}

.size-btn:hover:not(:disabled) {
  border-color: #1a1a1a;
  color: #1a1a1a;
}

.size-btn--active,
.size-btn--active:hover:not(:disabled) {
  background: #1a1a1a;
  border-color: #1a1a1a;
  color: white;
}

.size-btn--active .size-btn__stock {
  color: #aaa;
}

.size-btn--disabled {
  opacity: 0.6;
  cursor: not-allowed;
  background: #fffafa;
  border-color: #ffe5e5;
  color: #c53030;
}

.size-btn--disabled .size-btn__stock {
  color: #e53e3e;
}

/* Quantity */
.quantity-selector {
  display: inline-flex;
  align-items: center;
  gap: 12px;
  background: #f5f0e8;
  border-radius: 100px;
  padding: 6px 14px;
}

.qty-btn {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: white;
  border: 1px solid #d0cdc8;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: #1a1a1a;
  transition: all 0.2s ease;
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
  font-weight: 600;
  font-size: 15px;
  min-width: 28px;
  text-align: center;
}

/* ── Actions ── */
.detail__actions {
  margin-bottom: 28px;
}

.add-to-cart-btn {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  padding: 16px 28px;
  background: #1a1a1a;
  color: white;
  border: none;
  border-radius: 100px;
  font-family: 'Cormorant Garamond', serif;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.25s ease;
}

.add-to-cart-btn:hover {
  background: #6b5f4e;
  transform: translateY(-1px);
}

/* ── Shipping info ── */
.detail__shipping-info {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.shipping-item {
  display: flex;
  align-items: center;
  gap: 10px;
  font-family: 'Inter', sans-serif;
  font-size: 12px;
  color: #888;
}

.shipping-item svg {
  color: #7a9e7e;
  flex-shrink: 0;
}

/* ── Responsive ── */
@media (max-width: 900px) {
  .detail__layout {
    grid-template-columns: 1fr;
    gap: 40px;
  }

  .detail__inner {
    padding: 32px 20px 60px;
  }

  .detail__breadcrumb-wrap {
    padding: 14px 20px;
  }

  .gallery__thumbs {
    gap: 8px;
  }

  .gallery__thumb {
    width: 60px;
    height: 76px;
  }
}
</style>
