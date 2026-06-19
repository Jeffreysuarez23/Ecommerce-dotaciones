<template>
  <header class="app-header">
    
    <!-- LEFT SIDE: MOBILE TOGGLE & SEARCH BAR -->
    <div class="header__left">
      <button class="mobile-toggle-btn" @click="$emit('toggle-sidebar')">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="3" y1="12" x2="21" y2="12"></line>
          <line x1="3" y1="6" x2="21" y2="6"></line>
          <line x1="3" y1="18" x2="21" y2="18"></line>
        </svg>
      </button>

      <!-- Global Search Bar -->
      <div class="header__search">
        <span class="search-icon">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          </svg>
        </span>
        <input 
          type="text" 
          placeholder="Buscar pedidos, productos o lotes..." 
          class="search-input"
          v-model="searchQuery"
          @input="onSearch"
        />
        <span class="search-shortcut">⌘K</span>
      </div>
    </div>

    <!-- RIGHT SIDE: NOTIFICATIONS & PROFILE -->
    <div class="header__right">
      
      <!-- Notifications dropdown activator -->
      <div class="header-action-wrap">
        <button class="header-action-btn" @click="showNotifDropdown = !showNotifDropdown">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
          </svg>
          <span v-if="unreadNotificationsCount > 0" class="badge-counter">{{ unreadNotificationsCount > 99 ? '99+' : unreadNotificationsCount }}</span>
        </button>
        
        <!-- Notifications Quick Dropdown -->
        <div v-if="showNotifDropdown" class="header-dropdown notif-dropdown" v-click-outside="closeNotifDropdown">
          <div class="dropdown-header">
            <h4>Notificaciones</h4>
            <button class="btn-clear" v-if="unreadNotificationsCount > 0" @click="clearAllNotifs">Limpiar todo</button>
          </div>
          <div class="dropdown-body">
            <div v-if="unreadNotificationsCount === 0" class="dropdown-empty">
              No hay alertas pendientes
            </div>
            <div 
              v-else 
              v-for="notif in unreadNotifications" 
              :key="notif.id" 
              class="dropdown-item"
              @click="markAsRead(notif.id)"
            >
              <!-- Icono dinámico según el tipo -->
              <div class="notif-icon" :class="`notif-icon--${notif.tipo}`">
                <!-- Icono para Órdenes -->
                <svg v-if="notif.tipo === 'orden'" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
                <!-- Icono para Stock Bajo -->
                <svg v-else-if="notif.tipo === 'stock_bajo'" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
                <!-- Icono por defecto (Sistema) -->
                <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
              </div>
              <div class="notif-text">
                <p class="notif-title">{{ notif.titulo }}</p>
                <p class="notif-message">{{ notif.mensaje }}</p>
                <span class="notif-time">{{ new Date(notif.creado_en).toLocaleString('es-CO', { hour: '2-digit', minute: '2-digit', day: '2-digit', month: 'short' }) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- User Profile Dropdown -->
      <div class="header-action-wrap">
        <div class="header-profile" @click="showProfileDropdown = !showProfileDropdown">
          <div class="profile-avatar">{{ authUser.nombre ? authUser.nombre.charAt(0).toUpperCase() : 'A' }}</div>
          <div class="profile-meta">
            <span class="profile-name">{{ authUser.nombre }}</span>
            <span class="profile-role" style="text-transform: capitalize;">{{ authUser.rol?.replace('_', ' ') || 'Admin' }}</span>
          </div>
          <span class="arrow-icon">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </span>
        </div>

        <!-- Profile Dropdown Menu -->
        <div v-if="showProfileDropdown" class="header-dropdown profile-dropdown" v-click-outside="closeProfileDropdown">
          <div class="dropdown-profile-header">
            <p class="p-name">{{ authUser.nombre }}</p>
            <p class="p-email">{{ authUser.email }}</p>
          </div>
          <div class="dropdown-divider"></div>
          <router-link to="/account" class="dropdown-menu-item" @click="showProfileDropdown = false">
            Configuración Cuenta
          </router-link>
          <a href="#" class="dropdown-menu-item dropdown-menu-item--danger" @click.prevent="logoutAlert">
            Cerrar Sesión
          </a>
        </div>
      </div>

    </div>

  </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import { state, actions } from '../store/state.js'

defineEmits(['toggle-sidebar'])

const api = axios.create({
  baseURL: 'http://localhost:8000/api'
})

api.interceptors.request.use(config => {
  const token = localStorage.getItem('auth_token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

const searchQuery = ref('')
const showNotifDropdown = ref(false)
const showProfileDropdown = ref(false)

const authUser = ref({
  nombre: 'Cargando...',
  email: '',
  rol: 'Admin'
})

const unreadNotifications = ref([])

const fetchNotifications = async () => {
  try {
    const { data } = await api.get('/notificaciones')
    unreadNotifications.value = data.filter(n => !n.leido_en)
  } catch (err) {
    console.error('Error fetching notifications:', err)
  }
}

onMounted(() => {
  const storedUser = localStorage.getItem('auth_user')
  if (storedUser) {
    try {
      authUser.value = JSON.parse(storedUser)
    } catch (e) {
      console.error('Error parsing auth_user', e)
    }
  }

  fetchNotifications()
  setInterval(fetchNotifications, 15000)
  window.addEventListener('notifications-updated', fetchNotifications)
})

onUnmounted(() => {
  window.removeEventListener('notifications-updated', fetchNotifications)
})

const unreadNotificationsCount = computed(() => unreadNotifications.value.length)

const onSearch = () => {
  state.globalSearch = searchQuery.value
}

const markAsRead = async (id) => {
  try {
    await api.put(`/notificaciones/${id}/leer`)
    await fetchNotifications()
  } catch (err) {
    console.error('Error marking as read:', err)
  }
}

const clearAllNotifs = async () => {
  try {
    const promises = unreadNotifications.value.map(n => api.put(`/notificaciones/${n.id}/leer`))
    await Promise.all(promises)
    await fetchNotifications()
    showNotifDropdown.value = false
  } catch (err) {
    console.error('Error clearing notifs:', err)
  }
}

const closeNotifDropdown = () => {
  showNotifDropdown.value = false
}

const closeProfileDropdown = () => {
  showProfileDropdown.value = false
}

const logoutAlert = async () => {
  try {
    const token = localStorage.getItem('auth_token')
    if (token) {
      await axios.post('http://localhost:8000/api/logout', {}, {
        headers: {
          Authorization: `Bearer ${token}`
        }
      })
    }
  } catch (error) {
    console.error('Error logging out from backend', error)
  } finally {
    localStorage.removeItem('auth_token')
    localStorage.removeItem('auth_user')
    showProfileDropdown.value = false
    window.location.href = 'http://localhost:5173/login'
  }
}

// Simple directive helper to click outside dropdowns
const vClickOutside = {
  mounted(el, binding) {
    el.clickOutsideEvent = function(event) {
      if (!(el === event.target || el.contains(event.target) || event.target.closest('.header-action-btn') || event.target.closest('.header-profile'))) {
        binding.value(event)
      }
    }
    document.body.addEventListener('click', el.clickOutsideEvent)
  },
  unmounted(el) {
    document.body.removeEventListener('click', el.clickOutsideEvent)
  }
}
</script>

<style scoped>
.app-header {
  height: 70px;
  background-color: var(--bg-card);
  border-bottom: 1px solid var(--color-border);
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 40px;
  position: sticky;
  top: 0;
  z-index: 90;
  transition: var(--transition-smooth);
}

.header__left, .header__right {
  display: flex;
  align-items: center;
  gap: 20px;
}

/* Mobile Sidebar toggle button */
.mobile-toggle-btn {
  display: none;
  background: none;
  border: none;
  color: var(--text-primary);
  cursor: pointer;
}

/* Search bar elements */
.header__search {
  position: relative;
  display: flex;
  align-items: center;
  width: 320px;
}

.search-icon {
  position: absolute;
  left: 14px;
  color: var(--text-muted);
  display: flex;
  align-items: center;
}

.search-input {
  width: 100%;
  padding: 8px 14px 8px 38px;
  border-radius: var(--radius-full);
  border: 1px solid var(--color-border);
  background-color: var(--bg-app);
  color: var(--text-primary);
  font-family: var(--font-sans);
  font-size: 13px;
  outline: none;
  transition: var(--transition-smooth);
}

.search-input:focus {
  border-color: var(--color-accent);
  background-color: var(--bg-card);
  box-shadow: 0 0 0 3px rgba(122, 106, 83, 0.1);
  width: 360px;
}

.search-shortcut {
  position: absolute;
  right: 14px;
  font-size: 10px;
  font-weight: 600;
  color: var(--text-muted);
  background-color: var(--bg-card);
  border: 1px solid var(--color-border);
  padding: 2px 6px;
  border-radius: var(--radius-sm);
  pointer-events: none;
}

/* Header buttons and action containers */
.header-action-wrap {
  position: relative;
}

.header-action-btn {
  background: none;
  border: none;
  width: 38px;
  height: 38px;
  border-radius: var(--radius-full);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--text-secondary);
  cursor: pointer;
  transition: var(--transition-smooth);
  position: relative;
  border: 1px solid transparent;
}

.header-action-btn:hover {
  background-color: var(--color-accent-light);
  color: var(--text-primary);
  border-color: var(--color-border);
}

.badge-counter {
  position: absolute;
  top: 0;
  right: -4px;
  background-color: var(--color-danger);
  color: white;
  font-size: 10px;
  font-weight: 700;
  padding: 2px 5px;
  border-radius: 10px;
  border: 1.5px solid var(--bg-card);
  line-height: 1;
}

/* Header User Profile Badge */
.header-profile {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 6px 12px;
  border-radius: var(--radius-full);
  border: 1px solid var(--color-border);
  cursor: pointer;
  background-color: var(--bg-app);
  transition: var(--transition-smooth);
}

.header-profile:hover {
  border-color: var(--color-accent);
  background-color: var(--bg-card);
}

.profile-avatar {
  width: 28px;
  height: 28px;
  border-radius: var(--radius-full);
  background-color: var(--color-accent);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 12px;
}

.profile-meta {
  display: flex;
  flex-direction: column;
  text-align: left;
}

.profile-name {
  font-size: 12px;
  font-weight: 600;
  color: var(--text-primary);
  line-height: 1.2;
}

.profile-role {
  font-size: 10px;
  color: var(--text-muted);
}

.arrow-icon {
  color: var(--text-secondary);
  display: flex;
  align-items: center;
}

/* Header Dropdowns styling */
.header-dropdown {
  position: absolute;
  top: 48px;
  right: 0;
  background-color: var(--bg-card);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  box-shadow: var(--shadow-lg);
  width: 280px;
  display: flex;
  flex-direction: column;
  z-index: 100;
  overflow: hidden;
  animation: slideDown 0.2s ease;
}

@keyframes slideDown {
  from { opacity: 0; transform: translateY(-8px); }
  to { opacity: 1; transform: translateY(0); }
}

.dropdown-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 16px;
  border-bottom: 1px solid var(--color-border);
}

.dropdown-header h4 {
  font-size: 13px;
  font-weight: 700;
}

.btn-clear {
  background: none;
  border: none;
  font-size: 11px;
  color: var(--color-accent);
  cursor: pointer;
  font-weight: 600;
}

.btn-clear:hover {
  text-decoration: underline;
}

.dropdown-body {
  max-height: 240px;
  overflow-y: auto;
}

.dropdown-empty {
  padding: 24px;
  text-align: center;
  color: var(--text-muted);
  font-size: 12px;
}

.dropdown-item {
  display: flex;
  gap: 12px;
  padding: 12px 16px;
  border-bottom: 1px solid var(--color-border);
  cursor: pointer;
  transition: var(--transition-smooth);
}

.dropdown-item:last-child {
  border-bottom: none;
}

.dropdown-item:hover {
  background-color: var(--bg-sidebar);
}

.notif-icon {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  margin-top: 2px;
}

.notif-icon--stock_bajo { 
  background-color: var(--color-danger-light, #fef2f2); 
  color: var(--color-danger, #ef4444); 
}
.notif-icon--orden { 
  background-color: #ecfdf5; 
  color: #10b981; 
}
.notif-icon--sistema { 
  background-color: var(--color-accent-light, #f0f4ff); 
  color: var(--color-accent, #3b82f6); 
}

.notif-text {
  display: flex;
  flex-direction: column;
  gap: 2px;
  flex: 1;
}

.notif-title {
  font-size: 13px;
  font-weight: 700;
  color: var(--text-primary);
  margin: 0;
}

.notif-message {
  font-size: 11.5px;
  color: var(--text-secondary);
  line-height: 1.4;
  margin: 0 0 4px 0;
}

.notif-time {
  font-size: 10px;
  color: var(--text-muted);
}

/* Profile Dropdown specific */
.profile-dropdown {
  width: 200px;
  padding: 8px 0;
}

.dropdown-profile-header {
  padding: 8px 16px;
}

.p-name {
  font-size: 13px;
  font-weight: 700;
  color: var(--text-primary);
}

.p-email {
  font-size: 11px;
  color: var(--text-muted);
}

.dropdown-divider {
  height: 1px;
  background-color: var(--color-border);
  margin: 6px 0;
}

.dropdown-menu-item {
  padding: 8px 16px;
  font-size: 12px;
  color: var(--text-secondary);
  text-decoration: none;
  display: block;
  transition: var(--transition-smooth);
}

.dropdown-menu-item:hover {
  background-color: var(--color-accent-light);
  color: var(--text-primary);
}

.dropdown-menu-item--danger {
  color: var(--color-danger);
}

.dropdown-menu-item--danger:hover {
  background-color: var(--color-danger-light);
  color: var(--color-danger);
}

@media (max-width: 992px) {
  .app-header {
    padding: 0 20px;
    height: 60px;
  }
  .mobile-toggle-btn {
    display: flex;
  }
  .header__search {
    width: 200px;
  }
  .search-shortcut {
    display: none;
  }
  .search-input:focus {
    width: 220px;
  }
  .profile-meta {
    display: none;
  }
}
</style>
