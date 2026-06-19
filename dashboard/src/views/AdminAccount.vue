<template>
  <div class="admin-account">

    <!-- Page Header -->
    <div class="page-header">
      <div class="page-header__title-wrap">
        <p>/ Configuración</p>
        <h1 class="title-serif">Mi Cuenta</h1>
      </div>
    </div>

    <!-- Content -->
    <div class="admin-account__content">

      <!-- Profile Info Card -->
      <div class="card admin-account__card">
        <h2 class="section-title">Información Personal</h2>

        <form @submit.prevent="updateProfile" class="admin-form">
          <div class="admin-form__row">
            <div class="form-group">
              <label>Nombre completo</label>
              <input type="text" class="input-text" v-model="form.nombre" @input="sanitizeName('nombre', form)" />
            </div>
            <div class="form-group">
              <label>Teléfono</label>
              <input type="text" class="input-text" v-model="form.telefono" @input="sanitizePhone('telefono', form)" />
            </div>
          </div>

          <div class="form-group">
            <label>Correo electrónico</label>
            <input type="email" class="input-text" v-model="form.email" readonly/>
          </div>

          <div class="form-group">
            <label>Rol</label>
            <input type="text" class="input-text" :value="displayRol" disabled style="opacity: 0.6; cursor: not-allowed;" />
          </div>

          <div class="section-divider"></div>

          <h2 class="section-title">Cambiar Contraseña</h2>
          <p class="section-hint">Deja estos campos en blanco si no deseas cambiar tu contraseña.</p>

          <div class="admin-form__row">
            <div class="form-group">
              <label>Nueva contraseña</label>
              <input type="password" class="input-text" v-model="form.password" @input="sanitizeNoSpaces('password', form)" placeholder="Mínimo 6 caracteres" />
            </div>
            <div class="form-group">
              <label>Confirmar contraseña</label>
              <input type="password" class="input-text" v-model="form.password_confirmation" @input="sanitizeNoSpaces('password_confirmation', form)" />
            </div>
          </div>

          <div v-if="message" class="admin-alert admin-alert--success">{{ message }}</div>
          <div v-if="error" class="admin-alert admin-alert--error">{{ error }}</div>

          <div class="admin-form__actions">
            <button type="submit" class="btn btn--primary" :disabled="loading">
              <svg v-if="!loading" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
              <span v-if="loading">Guardando...</span>
              <span v-else>Guardar Cambios</span>
            </button>
          </div>
        </form>
      </div>

      <!-- Danger Zone -->
      <div class="card admin-account__card admin-account__card--danger">
        <h2 class="section-title section-title--danger">Zona de Peligro</h2>
        <p class="danger-description">Una vez que elimines tu cuenta de administrador, no hay vuelta atrás. Perderás el acceso al panel administrativo de forma permanente.</p>
        <button @click="showDeleteModal = true" type="button" class="btn btn--danger" :disabled="deleting">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
          <span v-if="deleting">Eliminando...</span>
          <span v-else>Eliminar Cuenta</span>
        </button>
      </div>

    </div>

    <!-- Delete Confirmation Modal -->
    <Transition name="modal-fade">
      <div v-if="showDeleteModal" class="modal-overlay" @click.self="showDeleteModal = false">
        <div class="modal-card">
          <div class="modal-icon">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--color-danger)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10" class="modal-circle" />
              <line x1="12" y1="8" x2="12" y2="12" class="modal-line" />
              <line x1="12" y1="16" x2="12.01" y2="16" />
            </svg>
          </div>
          <h2 class="modal-title">¿Eliminar tu cuenta?</h2>
          <p class="modal-text">Esta acción es permanente y no se puede deshacer. Perderás el acceso al panel administrativo.</p>
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

<script>
import axios from 'axios'

const API_URL = 'http://localhost:8000/api'

export default {
  name: 'AdminAccount',
  data() {
    return {
      form: {
        nombre: '',
        email: '',
        telefono: '',
        password: '',
        password_confirmation: ''
      },
      userRol: '',
      loading: false,
      deleting: false,
      showDeleteModal: false,
      message: '',
      error: ''
    }
  },
  computed: {
    displayRol() {
      return (this.userRol || 'admin').replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())
    }
  },
  mounted() {
    this.fetchProfile()
  },
  methods: {
    sanitizeName(field, obj) {
      let val = obj[field].replace(/^\s+/, '')
      val = val.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')
      val = val.replace(/\s{2,}/g, ' ')
      obj[field] = val
    },
    sanitizePhone(field, obj) {
      let val = obj[field].replace(/^\s+/, '')
      val = val.replace(/[^\+0-9\s]/g, '')
      val = val.replace(/(?!^\+)\+/g, '')
      val = val.replace(/\s{2,}/g, ' ')
      obj[field] = val
    },
    sanitizeNoSpaces(field, obj) {
      obj[field] = obj[field].replace(/\s/g, '')
    },
    async fetchProfile() {
      try {
        const token = localStorage.getItem('auth_token')
        if (!token) {
          window.location.href = 'http://localhost:5173/login'
          return
        }

        const { data } = await axios.get(`${API_URL}/profile`, {
          headers: { Authorization: `Bearer ${token}` }
        })

        this.form.nombre = data.nombre
        this.form.email = data.email
        this.form.telefono = data.telefono || ''
        this.userRol = data.rol || 'admin'
      } catch (err) {
        console.error(err)
        if (err.response?.status === 401) {
          localStorage.removeItem('auth_token')
          localStorage.removeItem('auth_user')
          window.location.href = 'http://localhost:5173/login'
        }
      }
    },
    async updateProfile() {
      this.loading = true
      this.message = ''
      this.error = ''

      try {
        const token = localStorage.getItem('auth_token')
        const payload = {
          nombre: this.form.nombre,
          email: this.form.email,
          telefono: this.form.telefono
        }

        if (this.form.password) {
          payload.password = this.form.password
          payload.password_confirmation = this.form.password_confirmation
        }

        const { data } = await axios.put(`${API_URL}/profile`, payload, {
          headers: { Authorization: `Bearer ${token}` }
        })

        this.message = data.message
        this.form.password = ''
        this.form.password_confirmation = ''

        // Sync localStorage so AppHeader updates on next load
        if (data.user) {
          localStorage.setItem('auth_user', JSON.stringify(data.user))
        }
      } catch (err) {
        if (err.response?.data?.errors) {
          const errors = err.response.data.errors
          this.error = Object.values(errors).flat().join(' ')
        } else {
          this.error = err.response?.data?.message || 'Error al actualizar el perfil'
        }
      } finally {
        this.loading = false
      }
    },
    async executeDelete() {
      this.deleting = true
      this.message = ''
      this.error = ''

      try {
        const token = localStorage.getItem('auth_token')
        await axios.delete(`${API_URL}/profile`, {
          headers: { Authorization: `Bearer ${token}` }
        })

        localStorage.removeItem('auth_token')
        localStorage.removeItem('auth_user')
        this.showDeleteModal = false
        window.location.href = 'http://localhost:5173/login'
      } catch (err) {
        this.error = 'No se pudo eliminar la cuenta.'
        this.showDeleteModal = false
      } finally {
        this.deleting = false
      }
    }
  }
}
</script>

<style scoped>
.admin-account {
  display: flex;
  flex-direction: column;
  gap: 32px;
}

.admin-account__content {
  display: flex;
  flex-direction: column;
  gap: 24px;
  width: 100%;
}

.admin-account__card {
  padding: 32px;
}

.admin-account__card--danger {
  border-color: var(--color-danger-light);
}

.section-title {
  font-family: var(--font-serif);
  font-size: 22px;
  font-weight: 500;
  color: var(--text-primary);
  margin-bottom: 24px;
}

.section-title--danger {
  color: var(--color-danger);
}

.section-divider {
  height: 1px;
  background: var(--color-border);
  margin: 32px 0;
}

.section-hint {
  font-size: 13px;
  color: var(--text-muted);
  margin-top: -16px;
  margin-bottom: 24px;
}

.admin-form__row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.admin-form__actions {
  margin-top: 28px;
}

/* Primary Save button */
.admin-form__actions .btn--primary {
  width: 100%;
  padding: 14px 28px;
  font-size: 14px;
  border-radius: var(--radius-md);
  transition: all 0.3s ease;
}

.admin-form__actions .btn--primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(122, 106, 83, 0.25);
}

.admin-form__actions .btn--primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Danger delete button */
.admin-account__card--danger .btn--danger {
  padding: 14px 28px;
  font-size: 14px;
  border-radius: var(--radius-md);
  transition: all 0.3s ease;
}

.admin-account__card--danger .btn--danger:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(143, 77, 77, 0.3);
}

.admin-account__card--danger .btn--danger:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.danger-description {
  font-size: 14px;
  color: var(--text-secondary);
  line-height: 1.6;
  margin-bottom: 20px;
}

/* Alerts */
.admin-alert {
  padding: 12px 16px;
  border-radius: var(--radius-md);
  font-size: 13px;
  font-weight: 500;
  margin-top: 16px;
  animation: alertSlide 0.3s ease;
}

.admin-alert--success {
  background-color: var(--color-success-light);
  color: var(--color-success);
  border: 1px solid rgba(78, 106, 83, 0.2);
}

.admin-alert--error {
  background-color: var(--color-danger-light);
  color: var(--color-danger);
  border: 1px solid rgba(143, 77, 77, 0.2);
}

@keyframes alertSlide {
  from { opacity: 0; transform: translateY(-6px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Modal */
.modal-overlay {
  position: fixed;
  inset: 0;
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(6px);
  -webkit-backdrop-filter: blur(6px);
}

.modal-card {
  background: var(--bg-card);
  border-radius: var(--radius-lg);
  padding: 40px 36px 32px;
  max-width: 400px;
  width: 90%;
  text-align: center;
  box-shadow: var(--shadow-lg);
  animation: modalPop 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
  border: 1px solid var(--color-border);
}

.modal-icon {
  margin-bottom: 20px;
}

.modal-circle {
  stroke-dasharray: 63;
  stroke-dashoffset: 63;
  animation: drawStroke 0.6s ease forwards 0.1s;
}

.modal-line {
  stroke-dasharray: 10;
  stroke-dashoffset: 10;
  animation: drawStroke 0.4s ease forwards 0.5s;
}

.modal-title {
  font-family: var(--font-serif);
  font-size: 24px;
  font-weight: 500;
  color: var(--text-primary);
  margin: 0 0 10px 0;
}

.modal-text {
  font-size: 13px;
  color: var(--text-secondary);
  line-height: 1.6;
  margin: 0 0 28px 0;
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
  padding: 12px 20px;
  font-family: var(--font-sans);
  font-size: 13px;
  font-weight: 600;
  border-radius: var(--radius-full);
  cursor: pointer;
  transition: var(--transition-smooth);
  border: none;
}

.modal-btn--cancel {
  background: var(--color-accent-light);
  color: var(--text-secondary);
  border: 1px solid var(--color-border);
}

.modal-btn--cancel:hover {
  background: var(--bg-sidebar);
  border-color: var(--color-border-hover);
}

.modal-btn--danger {
  background: var(--color-danger);
  color: white;
}

.modal-btn--danger:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(143, 77, 77, 0.3);
}

.modal-btn--danger:disabled,
.modal-btn--cancel:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

@keyframes modalPop {
  from { opacity: 0; transform: scale(0.85) translateY(20px); }
  to { opacity: 1; transform: scale(1) translateY(0); }
}

@keyframes drawStroke {
  to { stroke-dashoffset: 0; }
}

/* Vue Transition */
.modal-fade-enter-active { transition: opacity 0.3s ease; }
.modal-fade-leave-active { transition: opacity 0.2s ease; }
.modal-fade-enter-from, .modal-fade-leave-to { opacity: 0; }

/* Responsive */
@media (max-width: 640px) {
  .admin-form__row {
    grid-template-columns: 1fr;
  }
  .admin-account__card {
    padding: 20px;
  }
}
</style>
