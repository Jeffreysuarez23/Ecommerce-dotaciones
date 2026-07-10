import { createApp } from 'vue'
import './style.css'
import App from './App.vue'
import router from './router'
import axios from 'axios'

// Global Axios Interceptor - Auto-attach auth token
axios.interceptors.request.use(config => {
  const token = localStorage.getItem('auth_token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

// Global Axios Interceptor for 401 Unauthorized
axios.interceptors.response.use(
  response => response,
  error => {
    if (error.response && error.response.status === 401) {
      // Token is invalid or expired (e.g., logged out from frontend)
      localStorage.removeItem('auth_token')
      localStorage.removeItem('auth_user')
      window.location.href = 'http://localhost:5173/login'
    }
    return Promise.reject(error)
  }
)

// Sync auth from URL parameters if redirected from frontend
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.has('auth_user') && urlParams.has('auth_token')) {
  localStorage.setItem('auth_user', urlParams.get('auth_user'));
  localStorage.setItem('auth_token', urlParams.get('auth_token'));
  // Clean URL without reloading
  window.history.replaceState({}, document.title, window.location.pathname);
}

// Initialize Theme
const storedState = localStorage.getItem('ecommerce_dashboard_state')
if (storedState) {
  try {
    const state = JSON.parse(storedState)
    if (state.theme) {
      document.documentElement.className = state.theme
    }
  } catch (e) {
    console.error('Failed to parse initial state theme', e)
  }
} else {
  document.documentElement.className = 'light'
}

const app = createApp(App)
app.use(router)
app.mount('#app')
