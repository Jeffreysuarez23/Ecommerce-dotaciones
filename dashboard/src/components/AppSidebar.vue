<template>
  <aside class="app-sidebar" :class="{ 'app-sidebar--open': isOpen }">
    
    <!-- Brand Logo -->
    <div class="sidebar__brand" style="display: flex; flex-direction: column; align-items: center; gap: 12px; margin-bottom: 16px; padding-top: 10px;">
      <img src="/logo.png" alt="Logo" style="max-width: 120px; height: auto; filter: brightness(0) invert(1);" />
    </div>

    <!-- Navigation Links -->
    <nav class="sidebar__nav">

      <router-link to="/" class="nav-item" @click="$emit('close')">
        <span class="nav-item__icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="3" width="7" height="9"></rect>
            <rect x="14" y="3" width="7" height="5"></rect>
            <rect x="14" y="12" width="7" height="9"></rect>
            <rect x="3" y="16" width="7" height="5"></rect>
          </svg>
        </span>
        <span class="nav-item__label">Overview</span>
      </router-link>

      <router-link to="/users" class="nav-item" @click="$emit('close')">
        <span class="nav-item__icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="9" cy="7" r="4"></circle>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
          </svg>
        </span>
        <span class="nav-item__label">Usuarios</span>
      </router-link>

      <router-link to="/trabajadores" class="nav-item" @click="$emit('close')">
        <span class="nav-item__icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="8.5" cy="7" r="4"></circle>
            <polyline points="17 11 19 13 23 9"></polyline>
          </svg>
        </span>
        <span class="nav-item__label">Trabajadores</span>
      </router-link>

      <router-link to="/products" class="nav-item" @click="$emit('close')">
        <span class="nav-item__icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
            <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
            <line x1="12" y1="22.08" x2="12" y2="12"></line>
          </svg>
        </span>
        <span class="nav-item__label">Productos</span>
      </router-link>

      <router-link to="/categorias" class="nav-item" @click="$emit('close')">
        <span class="nav-item__icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
            <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
            <line x1="12" y1="22.08" x2="12" y2="12"></line>
          </svg>
        </span>
        <span class="nav-item__label">Categorias</span>
      </router-link>

      <router-link to="/orders" class="nav-item" @click="$emit('close')">
        <span class="nav-item__icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <path d="M16 10a4 4 0 0 1-8 0"></path>
          </svg>
        </span>
        <span class="nav-item__label">Pedidos</span>
        <span v-if="totalOrdersCount > 0" class="nav-item__badge">{{ totalOrdersCount }}</span>
      </router-link>

      <router-link to="/dotaciones" class="nav-item" @click="$emit('close')">
        <span class="nav-item__icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
            <polyline points="2 17 12 22 22 17"></polyline>
            <polyline points="2 12 12 17 22 12"></polyline>
          </svg>
        </span>
        <span class="nav-item__label">Dotaciones / Lonas</span>
      </router-link>
    </nav>

    <!-- Sidebar Footer (Theme Toggle) -->
    <div class="sidebar__footer">
      <button class="theme-toggle" @click="toggleTheme">
        <span class="theme-toggle__icon">
          <svg v-if="state.theme === 'light'" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
          </svg>
          <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="5"></circle>
            <line x1="12" y1="1" x2="12" y2="3"></line>
            <line x1="12" y1="21" x2="12" y2="23"></line>
            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
            <line x1="1" y1="12" x2="3" y2="12"></line>
            <line x1="21" y1="12" x2="23" y2="12"></line>
            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
          </svg>
        </span>
        <span class="theme-toggle__label">{{ state.theme === 'light' ? 'Modo Oscuro' : 'Modo Claro' }}</span>
      </button>
    </div>

  </aside>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { state, actions } from '../store/state.js'
import axios from 'axios'

defineProps({
  isOpen: {
    type: Boolean,
    default: false
  }
})

defineEmits(['close'])

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

const totalOrdersCount = ref(0)

const fetchTotalOrdersCount = async () => {
  try {
    const response = await api.get('/ordenes')
    const allOrders = response.data.data || []
    totalOrdersCount.value = allOrders.length
  } catch (error) {
    console.error('Error fetching orders count:', error)
  }
}

onMounted(() => {
  fetchTotalOrdersCount()
  // Refrescar automáticamente cada 15 segundos para mantenerlo al día
  setInterval(fetchTotalOrdersCount, 15000)

  window.addEventListener('orders-updated', fetchTotalOrdersCount)
})

onUnmounted(() => {
  window.removeEventListener('orders-updated', fetchTotalOrdersCount)
})

const toggleTheme = () => {
  actions.toggleTheme()
}
</script>

<style scoped>
.app-sidebar {
  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
  width: var(--sidebar-width);
  background-color: var(--bg-sidebar);
  border-right: 1px solid var(--color-border);
  display: flex;
  flex-direction: column;
  padding: 40px 24px;
  z-index: 95;
  transition: transform 0.3s ease;
}

.sidebar__brand {
  margin-bottom: 48px;
  padding-left: 8px;
}

.sidebar__brand h2 {
  font-size: 26px;
  line-height: 1.1;
}

.sidebar__nav {
  display: flex;
  flex-direction: column;
  gap: 8px;
  flex-grow: 1;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  border-radius: var(--radius-md);
  color: var(--text-secondary);
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  transition: var(--transition-smooth);
}

.nav-item:hover {
  background-color: var(--color-accent-light);
  color: var(--text-primary);
}

.nav-item.router-link-active {
  background-color: var(--color-accent);
  color: #FFFBF7 !important;
}

.nav-item.router-link-active .nav-item__badge {
  background-color: #FFFBF7;
  color: var(--color-accent);
}

.nav-item__badge {
  margin-left: auto;
  background-color: var(--color-accent);
  color: white;
  font-size: 10px;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: var(--radius-full);
}

.sidebar__footer {
  display: flex;
  flex-direction: column;
  gap: 20px;
  border-top: 1px solid var(--color-border);
  padding-top: 24px;
}

.theme-toggle {
  display: flex;
  align-items: center;
  gap: 12px;
  width: 100%;
  padding: 10px 16px;
  border-radius: var(--radius-md);
  background: transparent;
  border: 1px solid var(--color-border);
  color: var(--text-secondary);
  font-family: var(--font-sans);
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  transition: var(--transition-smooth);
}

.theme-toggle:hover {
  background-color: var(--color-accent-light);
  border-color: var(--color-border-hover);
  color: var(--text-primary);
}

@media (max-width: 992px) {
  .app-sidebar {
    transform: translateX(-100%);
    top: 60px;
  }
  .app-sidebar--open {
    transform: translateX(0);
  }
}
</style>
