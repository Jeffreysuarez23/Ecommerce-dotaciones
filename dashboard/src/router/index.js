import { createRouter, createWebHistory } from 'vue-router'
import Overview from '../views/Overview.vue'
import Products from '../views/Products.vue'
import Orders from '../views/Orders.vue'
import Dotaciones from '../views/Dotaciones.vue'
import Users from '../views/Users.vue'
import AdminAccount from '../views/AdminAccount.vue'
import Categorias from '../views/Categorias.vue'

const routes = [
  {
    path: '/',
    name: 'Overview',
    component: Overview
  },
  {
    path: '/products',
    name: 'Products',
    component: Products
  },
  {
    path: '/orders',
    name: 'Orders',
    component: Orders
  },
  {
    path: '/dotaciones',
    name: 'Dotaciones',
    component: Dotaciones
  },
  {
    path: '/users',
    name: 'Users',
    component: Users
  },
  {
    path: '/account',
    name: 'AdminAccount',
    component: AdminAccount
  },
  {
    path: '/categorias',
    name: 'Categorias',
    component: Categorias
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

// Super Validación: Proteger el acceso solo a administradores
router.beforeEach((to, from, next) => {
  const authUser = localStorage.getItem('auth_user')
  const frontendUrl = window.location.port === '5174' ? 'http://localhost:5173' : window.location.origin
  
  if (!authUser) {
    // No ha iniciado sesión, devolver al login
    window.location.href = frontendUrl + '/login'
    return
  }

  try {
    const user = JSON.parse(authUser)
    const validRoles = ['admin', 'superadmin', 'super admin', 'super_admin']
    
    if (!validRoles.includes(user.rol)) {
      // No tiene permisos de administrador, devolver al home
      window.location.href = frontendUrl + '/'
      return
    }
  } catch (e) {
    // Token mal formado
    window.location.href = frontendUrl + '/login'
    return
  }
  
  next()
})

export default router
