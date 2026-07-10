<template>
  <nav class="navbar" :class="{ 'navbar--scrolled': scrolled, 'navbar--open': menuOpen, 'navbar--solid': (!isHome && !scrolled) || (isHome && scrolled) }">
    <div class="navbar__inner">

      <!-- Logo -->
      <router-link to="/" class="navbar__logo" @click="menuOpen = false">
        <img src="/logo.png" alt="Logo" class="navbar__logo-img" />
      </router-link>

      <!-- Nav Links -->
      <ul class="navbar__links" :class="{ 'navbar__links--open': menuOpen }">
        <li>
          <router-link to="/" class="navbar__link" active-class="navbar__link--active" exact @click="menuOpen = false">Inicio</router-link>
        </li>
        <li>
          <router-link to="/products" class="navbar__link" active-class="navbar__link--active" @click="menuOpen = false">Productos</router-link>
        </li>
        <li>
          <router-link to="/contact" class="navbar__link" active-class="navbar__link--active" @click="menuOpen = false">Contacto</router-link>
        </li>
        <li class="navbar__links-cart-mobile">
          <router-link to="/cart" class="navbar__link" @click="menuOpen = false">
            Carrito
            <span class="badge-mobile" v-if="cartCount > 0">{{ cartCount }}</span>
          </router-link>
        </li>
      </ul>

      <!-- Right: Cart + Hamburger -->
      <div class="navbar__right">
        <router-link to="/cart" class="navbar__cart">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
            <line x1="3" y1="6" x2="21" y2="6"/>
            <path d="M16 10a4 4 0 01-8 0"/>
          </svg>
          <span class="navbar__cart-label">Carrito</span>
          <span class="navbar__cart-badge" v-if="cartCount > 0">{{ cartCount }}</span>
        </router-link>

        <!-- User: Not logged in -->
        <router-link v-if="!isLoggedIn" to="/login" class="navbar__login" @click="menuOpen = false">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
          <span class="navbar__login-label">Iniciar Sesión</span>
        </router-link>

        <!-- User: Logged in -->
        <div v-else class="navbar__user" @mouseenter="userMenuOpen = true" @mouseleave="userMenuOpen = false">
          <button class="navbar__user-btn">
            <span class="navbar__user-avatar">{{ userInitial }}</span>
          </button>
          <Transition name="dropdown">
            <div v-show="userMenuOpen" class="navbar__dropdown">
              <div class="navbar__dropdown-header">
                <span class="navbar__dropdown-name">{{ userName }}</span>
                <span class="navbar__dropdown-email">{{ userEmail }}</span>
              </div>
              <div class="navbar__dropdown-divider"></div>
              <!-- Admin Dashboard Link -->
              <a v-if="isAdmin" :href="dashboardUrl" target="_blank" class="navbar__dropdown-item navbar__dropdown-item--admin" @click="userMenuOpen = false">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                Panel Admin
              </a>
              <div v-if="isAdmin" class="navbar__dropdown-divider"></div>
              <router-link to="/my-account" class="navbar__dropdown-item" @click="userMenuOpen = false">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Mi Cuenta
              </router-link>
              <router-link to="/mis-pedidos" class="navbar__dropdown-item" @click="userMenuOpen = false">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
                Mis Pedidos
              </router-link>
              <div class="navbar__dropdown-divider"></div>
              <button class="navbar__dropdown-item navbar__dropdown-item--logout" @click="handleLogout">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Cerrar Sesión
              </button>
            </div>
          </Transition>
        </div>

        <button class="navbar__toggle" @click="toggleMenu" :aria-expanded="menuOpen" aria-label="Toggle menu">
          <span class="navbar__toggle-bar" :class="{ 'bar--open': menuOpen }"></span>
          <span class="navbar__toggle-bar" :class="{ 'bar--open': menuOpen }"></span>
          <span class="navbar__toggle-bar" :class="{ 'bar--open': menuOpen }"></span>
        </button>
      </div>
    </div>

    <div class="navbar__overlay" :class="{ 'navbar__overlay--visible': menuOpen }" @click="menuOpen = false"></div>
  </nav>
</template>

<script>
import axios from 'axios'

export default {
  name: 'Navbar',
  props: {
    cartCount: {
      type: Number,
      default: 0
    },
    transparent: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      scrolled: false,
      menuOpen: false,
      userMenuOpen: false
    }
  },
  computed: {
    isHome() {
      return this.$route.path === '/';
    },
    isLoggedIn() {
      return !!localStorage.getItem('auth_token')
    },
    currentUser() {
      try {
        return JSON.parse(localStorage.getItem('auth_user') || 'null')
      } catch { return null }
    },
    userName() {
      return this.currentUser?.nombre || 'Usuario'
    },
    userEmail() {
      return this.currentUser?.email || ''
    },
    userInitial() {
      return this.userName.charAt(0).toUpperCase()
    },
    isAdmin() {
      return ['admin', 'super_admin'].includes(this.currentUser?.rol)
    },
    dashboardUrl() {
      return import.meta.env.VITE_DASHBOARD_URL || 'http://localhost:5174';
    }
  },
  mounted() {
    window.addEventListener('scroll', this.handleScroll)
  },
  unmounted() {
    window.removeEventListener('scroll', this.handleScroll)
  },
  methods: {
    handleScroll() {
      this.scrolled = window.scrollY > 20
    },
    toggleMenu() {
      this.menuOpen = !this.menuOpen
    },
    async handleLogout() {
      this.userMenuOpen = false
      try {
        const token = localStorage.getItem('auth_token')
        await axios.post((import.meta.env.VITE_API_URL || (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1' ? 'http://localhost:8000/api' : 'https://ecommerce-backend-qqda.onrender.com/api')) + '/logout', {}, {
          headers: { Authorization: `Bearer ${token}` }
        })
      } catch (e) {
        // ignore - token may already be invalid
      }
      localStorage.removeItem('auth_token')
      localStorage.removeItem('auth_user')
      this.$router.push('/login')
    }
  }
}
</script>

<style scoped>
/* ── Base ── */
.navbar {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 200;
  background: transparent;
  border-bottom: 1px solid transparent;
  transition: background 0.4s ease, border-color 0.3s ease, box-shadow 0.3s ease;
}

/* Solid: páginas sin hero (Products, Contact, Cart...) */
.navbar--solid {
  background: rgba(255, 255, 255, 0.97) !important;
  border-color: #e8e2d9 !important;
  box-shadow: 0 1px 12px rgba(0, 0, 0, 0.06) !important;
}

/* Scrolled: en Home al bajar */
.navbar--scrolled {
  background: rgba(255, 255, 255, 0.97);
  border-color: #e8e2d9;
  box-shadow: 0 1px 12px rgba(0, 0, 0, 0.06);
}

/* Mobile menu abierto */
.navbar--open {
  background: rgba(255, 255, 255, 0.97);
}

.navbar__inner {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 40px;
  height: 100px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: relative;
  transition: height 0.4s ease;
}

.navbar--scrolled .navbar__inner {
  height: 70px;
}

/* ── Logo ── */
.navbar__logo {
  text-decoration: none;
  display: flex;
  align-items: center;
  z-index: 2;
}

.navbar__logo-img {
  width: auto;
  height: 80px;
  object-fit: contain;
  transition: height 0.4s ease;
}

.navbar--scrolled .navbar__logo-img {
  height: 54px;
}

/* ── Nav Links ── */
.navbar__links {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  align-items: center;
  gap: 48px;
  position: absolute;
  left: 50%;
  transform: translateX(-50%);
}

/* Link color: blanco sobre hero transparente */
.navbar__link {
  text-decoration: none;
  color: rgba(255, 255, 255, 0.85);
  font-family: 'Cormorant Garamond', 'Georgia', serif;
  font-size: 15px;
  font-weight: 500;
  letter-spacing: 0.02em;
  transition: color 0.2s ease;
  position: relative;
}

.navbar__link::after {
  content: '';
  position: absolute;
  bottom: -4px;
  left: 0;
  width: 0;
  height: 1px;
  background: white;
  transition: width 0.3s ease;
}

.navbar__link:hover { color: white; }
.navbar__link:hover::after,
.navbar__link--active::after { width: 100%; }
.navbar__link--active { color: white; font-weight: 700; }

/* Links oscuros cuando hay fondo blanco */
.navbar--scrolled .navbar__link,
.navbar--solid .navbar__link,
.navbar--open .navbar__link {
  color: #888;
}
.navbar--scrolled .navbar__link::after,
.navbar--solid .navbar__link::after,
.navbar--open .navbar__link::after { background: #1a1a1a; }

.navbar--scrolled .navbar__link:hover,
.navbar--solid .navbar__link:hover,
.navbar--open .navbar__link:hover { color: #1a1a1a; }

.navbar--scrolled .navbar__link--active,
.navbar--solid .navbar__link--active,
.navbar--open .navbar__link--active { color: #1a1a1a; }

/* ── Right ── */
.navbar__right {
  display: flex;
  align-items: center;
  gap: 20px;
  z-index: 2;
}

/* ── Login (desktop) ── */
.navbar__login {
  display: flex;
  align-items: center;
  gap: 8px;
  text-decoration: none;
  color: white;
  font-family: 'Cormorant Garamond', 'Georgia', serif;
  font-size: 15px;
  font-weight: 500;
  position: relative;
  transition: opacity 0.2s ease;
}

.navbar--scrolled .navbar__login,
.navbar--solid .navbar__login,
.navbar--open .navbar__login { color: #1a1a1a; }

.navbar__login:hover { opacity: 0.7; }

/* ── Cart (desktop) ── */
.navbar__cart {
  display: flex;
  align-items: center;
  gap: 8px;
  text-decoration: none;
  color: white;
  font-family: 'Cormorant Garamond', 'Georgia', serif;
  font-size: 15px;
  font-weight: 500;
  position: relative;
  transition: opacity 0.2s ease;
}

.navbar--scrolled .navbar__cart,
.navbar--solid .navbar__cart,
.navbar--open .navbar__cart { color: #1a1a1a; }

.navbar__cart:hover { opacity: 0.7; }

.navbar__cart-badge {
  position: absolute;
  top: -8px;
  right: -10px;
  background: white;
  color: #1a1a1a;
  font-size: 10px;
  font-family: 'Inter', sans-serif;
  font-weight: 700;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.navbar--scrolled .navbar__cart-badge,
.navbar--solid .navbar__cart-badge,
.navbar--open .navbar__cart-badge {
  background: #1a1a1a;
  color: white;
}

/* ── Hamburger ── */
.navbar__toggle {
  display: none;
  flex-direction: column;
  justify-content: center;
  gap: 5px;
  width: 36px;
  height: 36px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 4px;
  z-index: 2;
}

.navbar__toggle-bar {
  display: block;
  width: 24px;
  height: 2px;
  background: white;
  border-radius: 2px;
  transition: transform 0.3s ease, opacity 0.3s ease, background 0.3s ease;
  transform-origin: center;
}

.navbar--scrolled .navbar__toggle-bar,
.navbar--solid .navbar__toggle-bar,
.navbar--open .navbar__toggle-bar { background: #1a1a1a; }

.navbar__toggle .bar--open:nth-child(1) { transform: translateY(7px) rotate(45deg); }
.navbar__toggle .bar--open:nth-child(2) { opacity: 0; transform: scaleX(0); }
.navbar__toggle .bar--open:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

/* Cart mobile hidden in nav on mobile */
.navbar__links-cart-mobile { display: none; }

/* ── Overlay ── */
.navbar__overlay {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.3);
  backdrop-filter: blur(2px);
  z-index: -1;
  opacity: 0;
  transition: opacity 0.3s ease;
}

/* ── RESPONSIVE ── */
@media (max-width: 768px) {
  .navbar__inner { padding: 0 20px; }

  .navbar__toggle { display: flex; }

  .navbar__cart-label,
  .navbar__login-label { display: none; }

  .navbar__links {
    position: fixed;
    top: 70px;
    left: 0;
    right: 0;
    flex-direction: column;
    align-items: stretch;
    gap: 0;
    background: white;
    padding: 16px 0 24px;
    transform: none;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    opacity: 0;
    pointer-events: none;
    translate: 0 -8px;
    transition: opacity 0.3s ease, translate 0.3s ease;
  }

  .navbar__links--open {
    opacity: 1;
    pointer-events: all;
    translate: 0 0;
  }

  .navbar__links li { border-bottom: 1px solid #f0ede8; }
  .navbar__links li:last-child { border-bottom: none; }

  .navbar__link {
    display: block;
    padding: 16px 28px;
    font-size: 18px;
    color: #1a1a1a !important;
  }

  .navbar__link::after { display: none; }

  .navbar__link--active {
    color: #1a1a1a !important;
    font-weight: 700;
    background: #faf8f4;
  }

  .navbar__links-cart-mobile { display: block; }

  .badge-mobile {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #1a1a1a;
    color: white;
    font-size: 10px;
    font-family: 'Inter', sans-serif;
    font-weight: 700;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    margin-left: 8px;
    vertical-align: middle;
  }

  .navbar__overlay { display: block; }
  .navbar__overlay--visible { opacity: 1; }
}

@media (max-width: 480px) {
  .navbar__logo-img { height: 50px; }
  .navbar--scrolled .navbar__logo-img { height: 40px; }
}

/* ── User Menu (logged in) ── */
.navbar__user {
  position: relative;
  z-index: 10;
}

.navbar__user-btn {
  background: none;
  border: none;
  cursor: pointer;
  padding: 0;
  display: flex;
  align-items: center;
}

.navbar__user-avatar {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: #1a1a1a;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: 'Inter', sans-serif;
  font-size: 14px;
  font-weight: 600;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.navbar__user:hover .navbar__user-avatar {
  transform: scale(1.08);
  box-shadow: 0 2px 12px rgba(0,0,0,0.15);
}

.navbar--scrolled .navbar__user-avatar,
.navbar--solid .navbar__user-avatar,
.navbar--open .navbar__user-avatar {
  background: #1a1a1a;
  color: white;
}

/* ── Dropdown ── */
.navbar__dropdown {
  position: absolute;
  top: calc(100% + 10px);
  right: 0;
  width: 240px;
  background: white;
  border-radius: 14px;
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12), 0 0 0 1px rgba(0,0,0,0.04);
  padding: 8px 0;
  z-index: 999;
}

.navbar__dropdown::before {
  content: '';
  position: absolute;
  top: -8px;
  right: 12px;
  width: 16px;
  height: 16px;
  background: white;
  transform: rotate(45deg);
  border-radius: 3px;
  box-shadow: -2px -2px 4px rgba(0,0,0,0.03);
}

.navbar__dropdown-header {
  padding: 14px 18px 10px;
}

.navbar__dropdown-name {
  display: block;
  font-family: 'Cormorant Garamond', serif;
  font-size: 17px;
  font-weight: 700;
  color: #1a1a1a;
}

.navbar__dropdown-email {
  display: block;
  font-family: 'Inter', sans-serif;
  font-size: 12px;
  color: #999;
  margin-top: 2px;
}

.navbar__dropdown-divider {
  height: 1px;
  background: #f0ede8;
  margin: 4px 14px;
}

.navbar__dropdown-item {
  display: flex;
  align-items: center;
  gap: 10px;
  width: 100%;
  padding: 11px 18px;
  background: none;
  border: none;
  text-decoration: none;
  color: #555;
  font-family: 'Inter', sans-serif;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.15s ease, color 0.15s ease;
  text-align: left;
}

.navbar__dropdown-item:hover {
  background: #faf8f4;
  color: #1a1a1a;
}

.navbar__dropdown-item--logout {
  color: #c0392b;
}

.navbar__dropdown-item--logout:hover {
  background: #fef2f2;
  color: #b91c1c;
}

/* Admin button */
.navbar__dropdown-item--admin {
  color: #1a1a1a;
  font-weight: 600;
  background: linear-gradient(135deg, #f8f6f3 0%, #ede8de 100%);
  border-radius: 8px;
  margin: 2px 8px;
  padding: 11px 14px !important;
  width: auto;
  transition: background 0.2s ease, transform 0.15s ease, box-shadow 0.2s ease;
}

.navbar__dropdown-item--admin:hover {
  background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
  color: #ffffff;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.navbar__dropdown-item--admin:hover svg {
  stroke: #ffffff;
}

/* Dropdown transition */
.dropdown-enter-active { transition: opacity 0.2s ease, transform 0.2s ease; }
.dropdown-leave-active { transition: opacity 0.15s ease, transform 0.15s ease; }
.dropdown-enter-from, .dropdown-leave-to {
  opacity: 0;
  transform: translateY(-6px) scale(0.97);
}
</style>