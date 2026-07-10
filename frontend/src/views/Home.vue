<template>
  <div class="home">

    <!-- ─── HERO ──────────────────────────────────────── -->
    <section class="hero">
      <div class="hero__bg">
        <transition-group name="fade" tag="div" class="hero__bg-wrapper">
          <img
            v-for="(slide, index) in slides"
            :key="slide.id"
            v-show="currentSlide === index"
            :src="slide.image"
            alt="Hero"
            class="hero__img"
          />
        </transition-group>
        <div class="hero__overlay"></div>
      </div>

      <div class="hero__content">
        <transition name="fade-content" mode="out-in">
          <div :key="currentSlide" class="hero__content-inner">
            <p class="hero__eyebrow">{{ slides[currentSlide].eyebrow }}</p>
            <h1 class="hero__title">
              {{ slides[currentSlide].title1 }}<br />
              <span class="hero__title--light">{{ slides[currentSlide].title2 }}</span>
            </h1>
            <p class="hero__subtitle" v-html="slides[currentSlide].subtitle"></p>
            <div class="hero__ctas">
              <router-link :to="slides[currentSlide].link" class="btn btn--primary">
                Comprar Ahora
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
              </router-link>
              <router-link to="/about" class="btn btn--ghost">Nuestra Historia</router-link>
            </div>
          </div>
        </transition>
      </div>

      <div class="hero__scroll">
        <span>DESLIZAR</span>
        <div class="hero__scroll-line"></div>
      </div>

      <!-- Slide dots -->
      <div class="hero__dots">
        <button
          v-for="(slide, index) in slides"
          :key="slide.id"
          class="hero__dot"
          :class="{ 'hero__dot--active': currentSlide === index }"
          @click="goToSlide(index)"
        ></button>
      </div>
    </section>

    <!-- ─── FEATURED PIECES ───────────────────────────── -->
    <section class="featured" v-reveal>
      <div class="section__inner">
        <div class="section__header">
          <div>
            <p class="section__eyebrow">/ SELECCIÓN CURADA</p>
            <h2 class="section__title">
              <strong>Piezas</strong><br />
              <span class="section__title--light">Destacadas</span>
            </h2>
          </div>
          <router-link to="/products" class="view-all">
            Ver todos los productos
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </router-link>
        </div>

        <div class="featured__grid">
          <router-link
            v-for="product in featuredProducts"
            :key="product.id"
            :to="`/product/${product.id}`"
            class="product-card"
            style="text-decoration: none;"
          >
            <div class="product-card__image-wrap">
              <img :src="product.image" :alt="product.name" class="product-card__image" />
              <span v-if="product.badge" class="product-card__badge" :class="`product-card__badge--${product.badgeType}`">
                {{ product.badge }}
              </span>
            </div>
            <div class="product-card__info">
              <p class="product-card__category">{{ product.category }}</p>
              <p class="product-card__name">{{ product.name }}</p>
              <p class="product-card__price">{{ product.price }}</p>
            </div>
          </router-link>
        </div>
      </div>
    </section>

    <!-- ─── EXPLORE CATEGORIES ──────────────────────── -->
    <section class="collections" v-reveal>
      <div class="section__inner">
        <div class="section__header section__header--simple">
          <p class="section__eyebrow">/ COMPRA POR CATEGORÍA</p>
          <h2 class="section__title">
            <strong>Explora</strong><br />
            <span class="section__title--light">Categorías Destacadas</span>
          </h2>
        </div>

        <div class="collections__grid">
          <div
            v-for="cat in categoriasDestacadas"
            :key="cat.id"
            class="collection-card"
          >
            <div class="collection-card__image-wrap">
              <img :src="cat.image" :alt="cat.nombre" class="collection-card__image" />
              <div class="collection-card__overlay">
                <span class="collection-card__label">{{ cat.nombre }}</span>
                <router-link :to="cat.link" class="collection-card__btn">
                  Comprar
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ─── OUR PHILOSOPHY ───────────────────────────── -->
    <section class="philosophy" v-reveal>
      <div class="section__inner philosophy__layout">
        <div class="philosophy__images">
          <img
            src="https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?w=900&q=80"
            alt="Crafted with intention"
            class="philosophy__main-img"
          />
          <div class="philosophy__img-accent"></div>
        </div>

        <div class="philosophy__content">
          <p class="section__eyebrow">/ NUESTRA FILOSOFÍA</p>
          <h2 class="philosophy__title">
            Creado con<br />
            <em>intención</em>
          </h2>
          <p class="philosophy__text">
            Creemos en el poder de tener menos cosas, pero mejores. Cada pieza en nuestra colección está
            diseñada para durar, no solo en calidad, sino en estilo. Trabajamos con artesanos que
            comparten nuestro compromiso con prácticas sostenibles y mano de obra excepcional.
          </p>
          <p class="philosophy__text">
            Desde la selección de materias primas hasta la última puntada, cada decisión se toma con cuidado.
            Nuestras telas provienen de fábricas certificadas y nuestra producción es transparente de
            principio a fin.
          </p>

          <div class="philosophy__stats">
            <div class="stat" v-for="s in stats" :key="s.value">
              <span class="stat__value">{{ s.value }}</span>
              <span class="stat__label">{{ s.label }}</span>
            </div>
          </div>
        </div>
      </div>
    </section>

  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'HomeView',
  data() {
    return {
      currentSlide: 0,
      slideInterval: null,
      slides: [
        {
          id: 1,
          eyebrow: 'NUEVA COLECCIÓN 2026',
          title1: 'Usa lo que',
          title2: 'Importa',
          subtitle: 'Piezas atemporales creadas con intención. Calidad sobre<br />cantidad, siempre.',
          image: 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=1800&q=80',
          link: '/products'
        },
        {
          id: 2,
          eyebrow: 'ESTILO EXCLUSIVO',
          title1: 'Diseños que',
          title2: 'Inspiran',
          subtitle: 'Expresa tu personalidad a través de cortes modernos y<br />detalles únicos.',
          image: 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1800&q=80',
          link: '/products'
        },
        {
          id: 3,
          eyebrow: 'MODA AUTÉNTICA',
          title1: 'Viste tu',
          title2: 'Esencia',
          subtitle: 'Combinaciones perfectas para destacar en cualquier<br />ocasión.',
          image: 'https://images.unsplash.com/photo-1509062522246-3755977927d7?w=1800&q=80',
          link: '/products'
        }
      ],
      featuredProducts: [],
      categoriasDestacadas: [],
      stats: [
        { value: '12+', label: 'AÑOS DE EXPERIENCIA' },
        { value: '100%', label: 'FIBRAS NATURALES' },
        { value: '40+', label: 'ARTESANOS ASOCIADOS' }
      ]
    }
  },
  mounted() {
    this.startSlider()
    this.fetchFeaturedProducts()
    this.fetchCategoriasDestacadas()
  },
  unmounted() {
    this.stopSlider()
  },
  methods: {
    startSlider() {
      this.slideInterval = setInterval(() => {
        this.nextSlide()
      }, 5000)
    },
    stopSlider() {
      clearInterval(this.slideInterval)
    },
    nextSlide() {
      this.currentSlide = (this.currentSlide + 1) % this.slides.length
    },
    goToSlide(index) {
      this.currentSlide = index
      this.stopSlider()
      this.startSlider()
    },
    async fetchFeaturedProducts() {
      try {
        const { data } = await axios.get((import.meta.env.VITE_API_URL || (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1' ? 'http://localhost:8000/api' : 'https://ecommerce-backend-qqda.onrender.com/api')) + '/productos')
        const destacados = data.filter(p => p.destacado === 1 && p.publicado === 1).slice(0, 4)
        
        this.featuredProducts = destacados.map(p => {
          let image = 'https://via.placeholder.com/600'
          if (p.imagenes && p.imagenes.length > 0) {
            const cover = p.imagenes.find(img => img.es_portada === 1)
            image = cover ? cover.url : p.imagenes[0].url
          }

          return {
            id: p.id,
            name: p.nombre,
            category: p.categoria ? p.categoria.nombre : 'General',
            price: new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 }).format(p.precio_minorista),
            badge: null,
            badgeType: null,
            image: image
          }
        })
      } catch (error) {
        console.error('Error fetching featured products:', error)
      }
    },
    async fetchCategoriasDestacadas() {
      try {
        const { data } = await axios.get((import.meta.env.VITE_API_URL || (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1' ? 'http://localhost:8000/api' : 'https://ecommerce-backend-qqda.onrender.com/api')) + '/categorias')
        
        // Flatten the categories to include subcategories easily
        const flatCategories = []
        const flatten = (cats) => {
          cats.forEach(c => {
            flatCategories.push(c)
            if (c.hijos && c.hijos.length > 0) flatten(c.hijos)
          })
        }
        flatten(data)

        const destacadas = flatCategories.filter(c => c.destacada === 1 || c.destacada === true)
        
        const placeholderImages = [
          'https://images.unsplash.com/photo-1544717305-2782549b5136?w=900&q=80',
          'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=900&q=80',
          'https://images.unsplash.com/photo-1525921429624-479b6a26d84d?w=900&q=80',
          'https://images.unsplash.com/photo-1519330377309-951c841407bf?w=900&q=80',
          'https://images.unsplash.com/photo-1608681285094-1a3b934b5722?w=900&q=80'
        ]

        this.categoriasDestacadas = destacadas.map((c, index) => {
          return {
            id: c.id,
            nombre: c.nombre,
            link: '/products?category=' + encodeURIComponent(c.nombre),
            image: c.imagen_url ? c.imagen_url : placeholderImages[index % placeholderImages.length]
          }
        })
      } catch (error) {
        console.error('Error fetching featured categories:', error)
      }
    }
  }
}
</script>

<style scoped>
/* ── Global / Shared ── */
.home {
  font-family: 'Cormorant Garamond', 'Georgia', serif;
  color: #1a1a1a;
  /* Sin padding-top: el hero empieza desde arriba y el navbar flota encima */
}

.section__inner {
  max-width: 1400px;
  margin: 0 auto;
  padding: 80px 40px;
}

.section__eyebrow {
  font-family: 'Inter', 'Helvetica Neue', sans-serif;
  font-size: 11px;
  font-weight: 500;
  letter-spacing: 0.15em;
  color: #999;
  margin: 0 0 12px 0;
  text-transform: uppercase;
}

.section__title {
  font-size: clamp(36px, 4vw, 52px);
  font-weight: 400;
  line-height: 1.1;
  margin: 0 0 8px 0;
  color: #1a1a1a;
}

.section__title strong {
  font-weight: 800;
  display: block;
}

.section__title--light {
  font-weight: 300;
  display: block;
}

.section__header {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  margin-bottom: 48px;
}

.section__header--simple {
  margin-bottom: 48px;
  display: block;
}

.view-all {
  display: flex;
  align-items: center;
  gap: 8px;
  text-decoration: none;
  font-size: 14px;
  font-family: 'Inter', sans-serif;
  color: #1a1a1a;
  font-weight: 500;
  transition: gap 0.2s ease;
  padding-bottom: 4px;
}

.view-all:hover {
  gap: 14px;
}

/* ── Hero ── */
.hero {
  position: relative;
  width: 100%;
  height: 100vh;
  min-height: 600px;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  /* Empieza desde arriba cubriendo el navbar (que flota encima con z-index) */
  margin-top: 0;
}

.hero__bg {
  position: absolute;
  inset: 0;
}

.hero__bg-wrapper {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
}

.hero__img {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 1s ease-in-out;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.fade-content-enter-active, .fade-content-leave-active {
  transition: opacity 0.5s ease, transform 0.5s ease;
}
.fade-content-enter-from, .fade-content-leave-to {
  opacity: 0;
  transform: translateY(20px);
}

.hero__overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    to bottom,
    rgba(0,0,0,0.1) 0%,
    rgba(0,0,0,0.3) 50%,
    rgba(0,0,0,0.15) 100%
  );
}

.hero__content {
  position: relative;
  z-index: 2;
  text-align: center;
  color: white;
  max-width: 680px;
  padding: 0 24px;
}

.hero__eyebrow {
  font-family: 'Inter', sans-serif;
  font-size: 11px;
  font-weight: 500;
  letter-spacing: 0.2em;
  color: rgba(255,255,255,0.8);
  margin: 0 0 16px 0;
}

.hero__title {
  font-size: clamp(52px, 8vw, 100px);
  font-weight: 800;
  line-height: 1;
  margin: 0 0 16px 0;
  letter-spacing: -0.02em;
}

.hero__title--light {
  font-weight: 300;
  font-style: italic;
}

.hero__subtitle {
  font-size: 16px;
  font-weight: 300;
  line-height: 1.6;
  color: rgba(255,255,255,0.85);
  margin: 0 0 36px 0;
}

.hero__ctas {
  display: flex;
  align-items: center;
  gap: 16px;
  justify-content: center;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 14px 28px;
  border-radius: 100px;
  font-family: 'Cormorant Garamond', serif;
  font-size: 16px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.25s ease;
  cursor: pointer;
  border: none;
}

.btn--primary {
  background: white;
  color: #1a1a1a;
}

.btn--primary:hover {
  background: #f0ebe3;
  transform: translateY(-1px);
}

.btn--ghost {
  background: rgba(255,255,255,0.15);
  color: white;
  border: 1px solid rgba(255,255,255,0.4);
  backdrop-filter: blur(8px);
}

.btn--ghost:hover {
  background: rgba(255,255,255,0.25);
}

.hero__scroll {
  position: absolute;
  bottom: 36px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
  z-index: 2;
  color: rgba(255,255,255,0.6);
  font-family: 'Inter', sans-serif;
  font-size: 10px;
  font-weight: 500;
  letter-spacing: 0.15em;
}

.hero__scroll-line {
  width: 1px;
  height: 40px;
  background: rgba(255,255,255,0.4);
  animation: scrollPulse 2s ease-in-out infinite;
}

@keyframes scrollPulse {
  0%, 100% { opacity: 0.4; transform: scaleY(1); }
  50% { opacity: 1; transform: scaleY(1.2); }
}

.hero__dots {
  position: absolute;
  bottom: 36px;
  right: 40px;
  display: flex;
  gap: 8px;
  z-index: 2;
}

.hero__dot {
  width: 24px;
  height: 4px;
  border-radius: 2px;
  background: rgba(255,255,255,0.35);
  border: none;
  cursor: pointer;
  transition: background 0.2s ease, width 0.2s ease;
}

.hero__dot--active {
  background: white;
  width: 36px;
}

/* ── Featured Products ── */
.featured {
  background: #fafafa;
}

.featured__grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 24px;
}

.product-card {
  cursor: pointer;
}

.product-card__image-wrap {
  position: relative;
  border-radius: 12px;
  overflow: hidden;
  background: #f0ebe3;
  aspect-ratio: 3 / 4;
}

.product-card__image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.product-card:hover .product-card__image {
  transform: scale(1.04);
}

.product-card__badge {
  position: absolute;
  top: 14px;
  left: 14px;
  padding: 6px 14px;
  border-radius: 100px;
  font-family: 'Inter', sans-serif;
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.03em;
}

.product-card__badge--dark {
  background: #1a1a1a;
  color: white;
}

.product-card__badge--light {
  background: #f5f0e8;
  color: #7a6a50;
  border: 1px solid #e0d5c0;
}

.product-card__badge--outline {
  background: white;
  color: #1a1a1a;
  border: 1px solid #d0cdc8;
}

.product-card__info {
  padding: 16px 4px 0;
}

.product-card__category {
  font-family: 'Inter', sans-serif;
  font-size: 10px;
  font-weight: 500;
  letter-spacing: 0.15em;
  color: #aaa;
  margin: 0 0 6px 0;
}

.product-card__name {
  font-size: 16px;
  font-weight: 500;
  color: #1a1a1a;
  margin: 0 0 4px 0;
}

.product-card__price {
  font-size: 15px;
  color: #555;
  margin: 0;
}

/* ── Collections ── */
.collections {
  background: #f7f4ef;
}

.collections__grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
}

.collection-card {
  cursor: pointer;
}

.collection-card__image-wrap {
  position: relative;
  border-radius: 12px;
  overflow: hidden;
  aspect-ratio: 1 / 1;
}

.collection-card__image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.6s ease;
}

.collection-card:hover .collection-card__image {
  transform: scale(1.05);
}

.collection-card__overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 32px 24px 24px;
  background: linear-gradient(to top, rgba(0,0,0,0.5) 0%, transparent 100%);
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.collection-card:hover .collection-card__overlay {
  opacity: 1;
}

.collection-card__label {
  font-family: 'Cormorant Garamond', serif;
  font-size: 22px;
  font-weight: 600;
  color: white;
}

.collection-card__btn {
  display: flex;
  align-items: center;
  gap: 6px;
  text-decoration: none;
  font-family: 'Inter', sans-serif;
  font-size: 12px;
  font-weight: 500;
  color: white;
  background: rgba(255,255,255,0.2);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255,255,255,0.4);
  padding: 8px 16px;
  border-radius: 100px;
  transition: background 0.2s ease;
}

.collection-card__btn:hover {
  background: rgba(255,255,255,0.35);
}

/* ── Philosophy ── */
.philosophy {
  background: #faf8f4;
}

.philosophy__layout {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 80px;
  align-items: center;
}

.philosophy__images {
  position: relative;
}

.philosophy__main-img {
  width: 100%;
  height: auto;
  border-radius: 12px;
  display: block;
}

.philosophy__img-accent {
  position: absolute;
  bottom: -20px;
  right: -20px;
  width: 45%;
  aspect-ratio: 1;
  background: #e8e2d6;
  border-radius: 8px;
  z-index: -1;
}

.philosophy__title {
  font-size: clamp(36px, 4vw, 54px);
  font-weight: 700;
  line-height: 1.15;
  color: #1a1a1a;
  margin: 0 0 28px 0;
}

.philosophy__title em {
  font-weight: 300;
  font-style: italic;
  color: #6b5f4e;
  display: block;
}

.philosophy__text {
  font-size: 16px;
  line-height: 1.75;
  color: #555;
  margin: 0 0 16px 0;
}

.philosophy__stats {
  display: flex;
  gap: 40px;
  margin-top: 40px;
  padding-top: 40px;
  border-top: 1px solid #e0d8cc;
}

.stat__value {
  display: block;
  font-size: 32px;
  font-weight: 800;
  color: #1a1a1a;
  line-height: 1;
  margin-bottom: 6px;
}

.stat__label {
  font-family: 'Inter', sans-serif;
  font-size: 10px;
  font-weight: 500;
  letter-spacing: 0.12em;
  color: #999;
  text-transform: uppercase;
}

/* ── Responsive ── */
@media (max-width: 1024px) {
  .featured__grid {
    grid-template-columns: repeat(2, 1fr);
  }
  .collections__grid {
    grid-template-columns: repeat(2, 1fr);
  }
  .philosophy__layout {
    grid-template-columns: 1fr;
    gap: 48px;
  }
}

@media (max-width: 640px) {
  .section__inner {
    padding: 60px 20px;
  }
  .featured__grid {
    grid-template-columns: 1fr;
  }
  .collections__grid {
    grid-template-columns: 1fr;
  }
  .section__header {
    flex-direction: column;
    align-items: flex-start;
    gap: 16px;
  }
  .philosophy__stats {
    gap: 24px;
  }
}
</style>