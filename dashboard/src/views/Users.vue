<template>
  <div class="users-view">

    <!-- PAGE HEADER -->
    <header class="page-header">
      <div class="page-header__title-wrap">
        <p>/ Seguridad y Control</p>
        <h1 class="title-serif">Gestión de Usuarios</h1>
      </div>
      <div class="page-header__actions">
        <button class="btn btn--primary" @click="openCreateDrawer">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
          </svg>
          Nuevo Usuario
        </button>
      </div>
    </header>

    <!-- ROLES SUMMARY CARDS -->
    <section class="grid-3">
      <div class="card role-summary-card">
        <h3>Administradores</h3>
        <h2>{{ countUsersByRole('admin') + countUsersByRole('super_admin') }}</h2>
        <p class="role-summary-card__desc">Cuentas con acceso a este panel</p>
      </div>
      <div class="card role-summary-card">
        <h3>Clientes Minoristas</h3>
        <h2>{{ countUsersByRole('cliente') }}</h2>
        <p class="role-summary-card__desc">Clientes registrados en la tienda</p>
      </div>
      <div class="card role-summary-card">
        <h3>Total Usuarios</h3>
        <h2>{{ usuarios.length }}</h2>
        <p class="role-summary-card__desc font-semibold">Base de datos unificada</p>
      </div>
    </section>

    <!-- GLOBAL ALERTS -->
    <div v-if="successMsg && !showDrawer" class="alert alert--success" style="margin-top: 0; margin-bottom: 24px;">{{ successMsg }}</div>
    <div v-if="errorMsg && !showDrawer" class="alert alert--error" style="margin-top: 0; margin-bottom: 24px;">{{ errorMsg }}</div>

    <!-- USERS TABLE -->
    <section class="card table-card">
      <div class="table-wrap">
        <table class="table-custom">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Email</th>
              <th>Teléfono</th>
              <th>Rol / Permisos</th>
              <th>Estado de Cuenta</th>
              <th>Fecha de Registro</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in usuarios" :key="user.id">
              <td>#{{ user.id }}</td>
              <td style="font-weight: 600;">{{ user.nombre }}</td>
              <td>{{ user.email }}</td>
              <td>{{ user.telefono || 'No registra' }}</td>
              <td>
                <span :class="['badge', getRoleBadgeClass(user.rol)]">{{ user.rol }}</span>
              </td>
              <td>
                <span :class="['badge', user.email_verificado_en ? 'badge--success' : 'badge--danger']">
                  {{ user.email_verificado_en ? 'Activa' : 'Inactiva' }}
                </span>
              </td>
              <td>{{ formatDate(user.creado_en) }}</td>
              <td>
                <div class="action-buttons">
                  <button class="btn-icon-action" title="Editar Usuario" @click="openEditDrawer(user)">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                      <path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                    </svg>
                  </button>
                  <button class="btn-icon-action btn-icon-action--danger" title="Eliminar Usuario"
                    @click="confirmDelete(user)" :disabled="user.rol === 'super_admin' && countSuperAdmins() <= 1">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <polyline points="3 6 5 6 21 6" />
                      <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="usuarios.length === 0">
              <td colspan="8" class="text-center text-muted" style="text-align: center; padding: 40px 20px;">
                No hay usuarios registrados
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <!-- ─── DRAWER BACKDROP ─── -->
    <div class="drawer-backdrop" :class="{ active: showDrawer }" @click="closeDrawer"></div>

    <!-- ─── DRAWER ─── -->
    <div class="drawer" :class="{ active: showDrawer }">

      <!-- Drawer Header -->
          <div class="drawer__header">
            <div class="drawer__header-left">
              <div class="drawer-avatar" v-if="isEditMode">
                {{ form.nombre?.charAt(0)?.toUpperCase() || '?' }}
              </div>
              <div class="drawer-avatar drawer-avatar--new" v-else>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                  <line x1="12" y1="5" x2="12" y2="19" />
                  <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
              </div>
              <div>
                <p class="drawer__subtitle">{{ isEditMode ? 'Editando perfil' : 'Nuevo registro' }}</p>
                <h2 class="drawer__title title-serif">{{ isEditMode ? form.nombre : 'Crear Usuario' }}</h2>
              </div>
            </div>
            <button class="drawer__close" @click="closeDrawer">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="18" y1="6" x2="6" y2="18" />
                <line x1="6" y1="6" x2="18" y2="18" />
              </svg>
            </button>
          </div>

          <!-- Drawer Body -->
          <div class="drawer__body">
            <form @submit.prevent="submitForm">

              <!-- Nombre + Teléfono -->
              <div class="grid-2">
                <div class="form-group">
                  <label class="form-label">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                      <circle cx="12" cy="7" r="4" />
                    </svg>
                    Nombre completo *
                  </label>
                  <input type="text" class="input-text" v-model="form.nombre" @input="sanitizeName('nombre', form)" placeholder="Ej: Juan Pérez" required />
                </div>
                <div class="form-group">
                  <label class="form-label">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path
                        d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 3.07 9.81a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 2 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L6.09 8.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" />
                    </svg>
                    Teléfono
                  </label>
                  <input type="text" class="input-text" v-model="form.telefono" @input="sanitizePhone('telefono', form)" placeholder="+57 300 000 0000" />
                </div>
              </div>

              <!-- Email -->
              <div class="form-group">
                <label class="form-label">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                    <polyline points="22,6 12,13 2,6" />
                  </svg>
                  Correo electrónico *
                </label>
                <input type="email" class="input-text" v-model="form.email" @input="sanitizeNoSpaces('email', form)" placeholder="correo@empresa.com" :readonly="isEditMode" required />
              </div>

              <!-- Password (solo en crear) -->
              <div class="form-group" v-if="!isEditMode">
                <label class="form-label">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                  </svg>
                  Contraseña *
                </label>
                <input type="password" class="input-text" v-model="form.password" @input="sanitizeNoSpaces('password', form)" placeholder="Mínimo 8 caracteres"
                  required />
              </div>

              <!-- Rol -->
              <div class="form-group">
                <label class="form-label">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                  </svg>
                  Rol y permisos
                </label>
                <div class="role-grid">
                  <label v-for="role in roles" :key="role.value" class="role-option"
                    :class="{ 'role-option--active': form.rol === role.value }">
                    <input type="radio" :value="role.value" v-model="form.rol" class="role-radio" />
                    <span class="role-icon">{{ role.icon }}</span>
                    <div>
                      <p class="role-name">{{ role.label }}</p>
                      <p class="role-desc">{{ role.desc }}</p>
                    </div>
                  </label>
                </div>
              </div>

              <!-- Estado -->
              <div class="form-group" v-if="isEditMode">
                <label class="form-label">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                    <polyline points="22 4 12 14.01 9 11.01" />
                  </svg>
                  Estado de la Cuenta
                </label>
                <div class="role-grid">
                  <label class="role-option" :class="{ 'role-option--active': form.estado === true }">
                    <input type="radio" :value="true" v-model="form.estado" class="role-radio" />
                    <span class="role-icon">✅</span>
                    <div>
                      <p class="role-name">Activa</p>
                      <p class="role-desc">Acceso permitido</p>
                    </div>
                  </label>
                  <label class="role-option" :class="{ 'role-option--active': form.estado === false }">
                    <input type="radio" :value="false" v-model="form.estado" class="role-radio" />
                    <span class="role-icon">⛔</span>
                    <div>
                      <p class="role-name">Inactiva</p>
                      <p class="role-desc">Acceso bloqueado</p>
                    </div>
                  </label>
                </div>
              </div>

              <div v-if="successMsg" class="alert alert--success">{{ successMsg }}</div>
              <div v-if="errorMsg" class="alert alert--error">{{ errorMsg }}</div>

              <div class="drawer__divider"></div>

              <!-- Actions -->
              <div class="form-actions">
                <button type="button" class="btn btn--secondary" @click="closeDrawer">Cancelar</button>
                <button type="submit" class="btn btn--primary" :disabled="saving">
                  <span class="spinner" v-if="saving"></span>
                  <svg v-else width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.5">
                    <polyline points="20 6 9 17 4 12" />
                  </svg>
                  {{ saving ? 'Guardando...' : (isEditMode ? 'Guardar Cambios' : 'Crear Usuario') }}
                </button>
              </div>

            </form>
          </div>
        </div>

    <!-- Delete Confirmation Modal -->
    <Transition name="modal">
      <div v-if="showDeleteModal" class="modal-overlay" @click.self="showDeleteModal = false">
        <div class="modal-card">
          <div class="modal-icon">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#b91c1c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10" class="modal-circle" />
              <line x1="12" y1="8" x2="12" y2="12" class="modal-line" />
              <line x1="12" y1="16" x2="12.01" y2="16" />
            </svg>
          </div>
          <h2 class="modal-title">¿Eliminar este usuario?</h2>
          <p class="modal-text">Se eliminará permanentemente a <strong>{{ userToDelete?.nombre }}</strong>. Esta acción no se puede deshacer.</p>
          
          <div class="modal-actions">
            <button class="modal-btn modal-btn--cancel" @click="showDeleteModal = false" :disabled="deleting">Cancelar</button>
            <button class="modal-btn modal-btn--danger" @click="executeDelete" :disabled="deleting">
              <span v-if="deleting">Eliminando...</span>
              <span v-else>Sí, eliminar</span>
            </button>
          </div>
        </div>
      </div>
    </Transition>

  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import axios from 'axios'

const usuarios = ref([])
const showDrawer = ref(false)
const isEditMode = ref(false)
const saving = ref(false)
const errorMsg = ref('')
const successMsg = ref('')
const showDeleteModal = ref(false)
const userToDelete = ref(null)
const deleting = ref(false)

const roles = [
  { value: 'cliente', icon: '👤', label: 'Cliente', desc: 'Solo acceso a tienda' },
  { value: 'admin', icon: '🛠️', label: 'Admin', desc: 'Acceso al panel' },
  { value: 'super_admin', icon: '👑', label: 'Super Admin', desc: 'Control total' },
]

const emptyForm = () => ({
  id: null,
  nombre: '',
  email: '',
  telefono: '',
  password: '',
  rol: 'cliente',
  estado: false
})

const form = reactive(emptyForm())

const fetchUsuarios = async () => {
  try {
    const { data } = await axios.get((import.meta.env.VITE_API_URL || 'http://localhost:8000/api') + '/usuarios')
    usuarios.value = data
  } catch (error) {
    console.error('Error fetching users:', error)
  }
}

onMounted(() => { fetchUsuarios() })

const countUsersByRole = (role) => usuarios.value.filter(u => u.rol === role).length
const countSuperAdmins = () => usuarios.value.filter(u => u.rol === 'super_admin').length

const getRoleBadgeClass = (role) => {
  const map = { super_admin: 'badge--danger', admin: 'badge--pending', cliente: 'badge--info' }
  return map[role] || ''
}

const formatDate = (dateStr) => {
  if (!dateStr) return 'No disponible'
  return new Date(dateStr).toLocaleDateString('es-ES', { year: 'numeric', month: 'short', day: 'numeric' })
}

const openCreateDrawer = () => {
  try {
    errorMsg.value = ''
    successMsg.value = ''
    isEditMode.value = false
    Object.assign(form, emptyForm())
    showDrawer.value = true
    document.body.style.overflow = 'hidden'
  } catch (err) {
    alert('Error abriendo drawer: ' + err.message)
  }
}

const openEditDrawer = (user) => {
  try {
    errorMsg.value = ''
    successMsg.value = ''
    isEditMode.value = true
    Object.assign(form, { ...user, password: '', estado: !!user.email_verificado_en })
    showDrawer.value = true
    document.body.style.overflow = 'hidden'
  } catch (err) {
    alert('Error abriendo edición: ' + err.message)
  }
}

const closeDrawer = () => {
  showDrawer.value = false
  document.body.style.overflow = ''
}

const sanitizeNoSpaces = (field, obj) => {
  if (obj[field] == null) return
  obj[field] = obj[field].replace(/\s/g, '') // Sin espacios
}

const submitForm = async () => {
  errorMsg.value = ''
  successMsg.value = ''

  // Validaciones del frontend
  if (!form.nombre || !form.email) {
    errorMsg.value = 'Por favor completa todos los campos obligatorios.'
    return
  }
  if (!isEditMode.value && (!form.password || form.password.length < 8)) {
    errorMsg.value = 'La contraseña debe tener al menos 8 caracteres.'
    return
  }
  if (isEditMode.value && form.password && form.password.length < 8) {
    errorMsg.value = 'La contraseña debe tener al menos 8 caracteres.'
    return
  }

  saving.value = true
  try {
    if (isEditMode.value) {
      if (form.rol === 'cliente' &&
        usuarios.value.find(u => u.id === form.id)?.rol === 'super_admin' &&
        countSuperAdmins() <= 1) {
        errorMsg.value = 'No puedes quitar los permisos del único Super Administrador.'
        saving.value = false
        return
      }
      const { data } = await axios.put(`${import.meta.env.VITE_API_URL || 'http://localhost:8000/api'}/usuarios/${form.id}`, form)
      const idx = usuarios.value.findIndex(u => u.id === form.id)
      if (idx !== -1) usuarios.value[idx] = data.usuario
      successMsg.value = 'Usuario actualizado correctamente.'
    } else {
      const { data } = await axios.post((import.meta.env.VITE_API_URL || 'http://localhost:8000/api') + '/usuarios', form)
      if (!data.usuario.creado_en) data.usuario.creado_en = new Date().toISOString()
      usuarios.value.push(data.usuario)
      successMsg.value = 'Usuario creado correctamente.'
    }
    closeDrawer()
  } catch (error) {
    if (error.response?.status === 422) {
      const firstError = Object.values(error.response.data.errors)[0][0]
      errorMsg.value = `Error de validación: ${firstError}`
    } else {
      errorMsg.value = error.response?.data?.message || 'Error al guardar el usuario.'
    }
  } finally {
    saving.value = false
  }
}

const confirmDelete = (user) => {
  errorMsg.value = ''
  successMsg.value = ''

  if (user.rol === 'super_admin' && countSuperAdmins() <= 1) {
    errorMsg.value = 'No puedes eliminar al único Super Administrador del sistema.'
    return
  }
  userToDelete.value = user
  showDeleteModal.value = true
}

const executeDelete = async () => {
  if (!userToDelete.value) return
  deleting.value = true
  errorMsg.value = ''
  successMsg.value = ''

  try {
    await axios.delete(`${import.meta.env.VITE_API_URL || 'http://localhost:8000/api'}/usuarios/${userToDelete.value.id}`)
    usuarios.value = usuarios.value.filter(u => u.id !== userToDelete.value.id)
    successMsg.value = 'Usuario eliminado correctamente.'
    showDeleteModal.value = false
    userToDelete.value = null
  } catch (error) {
    errorMsg.value = error.response?.data?.message || 'Error al eliminar el usuario.'
    showDeleteModal.value = false
    userToDelete.value = null
  } finally {
    deleting.value = false
  }
}
</script>

<style scoped>
/* Table cell padding specific to Users to increase breathing room */
.table-custom th {
  padding: 16px 24px;
}

.table-custom td {
  padding: 22px 24px;
  vertical-align: middle;
}

.users-view {
  display: flex;
  flex-direction: column;
  gap: 24px;
  padding: 24px;
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
  align-self: center;
}

.grid-3 {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
}

@media (max-width: 1024px) {
  .grid-3 {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .grid-3 {
    grid-template-columns: 1fr;
  }
}

.role-summary-card {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.role-summary-card h3 {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--text-secondary);
}

.role-summary-card h2 {
  font-size: 32px;
  font-weight: 700;
  color: var(--text-primary);
  line-height: 1;
}

.role-summary-card__desc {
  font-size: 12px;
  color: var(--text-muted);
}

.font-semibold {
  font-weight: 600;
}

.action-buttons {
  display: flex;
  gap: 8px;
}

.btn-icon-action {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border-radius: var(--radius-md);
  border: 1px solid var(--color-border);
  background-color: var(--bg-card);
  color: var(--text-secondary);
  cursor: pointer;
  transition: var(--transition-smooth);
}

.btn-icon-action:hover {
  border-color: var(--color-accent);
  color: var(--color-accent);
  background-color: var(--color-accent-light);
}

.btn-icon-action--danger:hover {
  border-color: var(--color-danger);
  color: var(--color-danger);
  background-color: var(--color-danger-light);
}

.btn-icon-action:disabled {
  opacity: 0.35;
  cursor: not-allowed;
  pointer-events: none;
}

/* ─── Drawer Header ─── */
.drawer__header-left {
  display: flex;
  align-items: center;
  gap: 14px;
}

.drawer-avatar {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: var(--color-accent);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  font-weight: 700;
  flex-shrink: 0;
}

.drawer-avatar--new {
  background: var(--color-accent-light);
  color: var(--color-accent);
  border: 1.5px dashed var(--color-accent);
}

.drawer__subtitle {
  font-size: 10px;
  font-weight: 600;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: var(--text-muted);
  margin: 0 0 3px;
}

.drawer__title {
  font-size: 20px;
  font-weight: 700;
  color: var(--text-primary);
  margin: 0;
  line-height: 1.2;
}

/* ─── Form elements ─── */
.form-label {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 11px;
  font-weight: 600;
  color: var(--text-secondary);
  text-transform: uppercase;
  letter-spacing: 0.06em;
  margin-bottom: 7px;
}

.grid-2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  margin-bottom: 0;
}

@media (max-width: 520px) {
  .grid-2 {
    grid-template-columns: 1fr;
  }
}

/* ─── Role Selector ─── */
.role-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 10px;
}

@media (max-width: 520px) {
  .role-grid {
    grid-template-columns: 1fr;
  }
}

.role-option {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 6px;
  padding: 12px 14px;
  border: 1.5px solid var(--color-border);
  border-radius: 12px;
  background: var(--bg-app);
  cursor: pointer;
  transition: all 0.2s ease;
}

.role-option:hover {
  border-color: var(--color-accent);
  background: var(--color-accent-light);
}

.role-option--active {
  border-color: var(--color-accent);
  background: var(--color-accent-light);
  box-shadow: 0 0 0 3px rgba(122, 106, 83, 0.12);
}

.role-radio {
  display: none;
}

.role-icon {
  font-size: 20px;
  line-height: 1;
}

.role-name {
  font-size: 12px;
  font-weight: 700;
  color: var(--text-primary);
  margin: 0;
  line-height: 1.2;
}

.role-desc {
  font-size: 10px;
  color: var(--text-muted);
  margin: 0;
  line-height: 1.3;
}

/* ─── Form actions ─── */
.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding-top: 20px;
}

/* ─── Alerts ─── */
.alert {
  padding: 14px 16px;
  border-radius: 10px;
  margin-bottom: 24px;
  font-size: 14px;
  font-weight: 500;
  animation: fadeIn 0.3s ease;
}

.alert--success {
  background-color: #ecfdf5;
  color: #065f46;
  border: 1px solid #a7f3d0;
}

.alert--error {
  background-color: #fef2f2;
  color: #991b1b;
  border: 1px solid #fecaca;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-6px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Spinner */
.spinner {
  width: 13px;
  height: 13px;
  border: 2px solid rgba(255, 255, 255, 0.35);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
  flex-shrink: 0;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* ── Modal Styles ── */
.modal-overlay {
  position: fixed;
  inset: 0;
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 0.45);
  backdrop-filter: blur(6px);
  -webkit-backdrop-filter: blur(6px);
}

.modal-card {
  background: var(--bg-card, #fff);
  border-radius: 20px;
  padding: 48px 40px 36px;
  max-width: 400px;
  width: 90%;
  text-align: center;
  box-shadow: 0 25px 60px rgba(0, 0, 0, 0.2), 0 0 0 1px var(--color-border, rgba(255,255,255,0.1));
  animation: modalPop 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.modal-icon {
  margin-bottom: 20px;
}

.modal-circle {
  stroke-dasharray: 63;
  stroke-dashoffset: 63;
  animation: drawCircle 0.6s ease forwards 0.1s;
}

.modal-line {
  stroke-dasharray: 10;
  stroke-dashoffset: 10;
  animation: drawLine 0.4s ease forwards 0.5s;
}

.modal-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: 26px;
  font-weight: 700;
  color: var(--text-primary);
  margin: 0 0 10px 0;
}

.modal-text {
  font-family: 'Inter', sans-serif;
  font-size: 14px;
  color: var(--text-muted);
  line-height: 1.6;
  margin: 0 0 32px 0;
}

.modal-actions {
  display: flex;
  gap: 12px;
  justify-content: center;
}

.modal-btn {
  flex: 1;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 14px 24px;
  font-family: 'Inter', sans-serif;
  font-size: 14px;
  font-weight: 600;
  border-radius: 100px;
  cursor: pointer;
  transition: all 0.2s ease;
  border: none;
}

.modal-btn--cancel {
  background: var(--bg-sidebar);
  color: var(--text-secondary);
}

.modal-btn--cancel:hover {
  background: var(--color-border);
}

.modal-btn--danger {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  color: white;
}

.modal-btn--danger:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(239, 68, 68, 0.3);
}

.modal-btn--danger:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

@keyframes modalPop {
  from { opacity: 0; transform: scale(0.85) translateY(20px); }
  to { opacity: 1; transform: scale(1) translateY(0); }
}

@keyframes drawCircle {
  to { stroke-dashoffset: 0; }
}

@keyframes drawLine {
  to { stroke-dashoffset: 0; }
}

/* Vue Transition */
.modal-enter-active { transition: opacity 0.3s ease; }
.modal-leave-active { transition: opacity 0.2s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
</style>