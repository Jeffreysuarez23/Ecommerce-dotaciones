<template>
  <div class="my-account">
    <div class="my-account__container">
      <header class="my-account__header">
        <h1 class="my-account__title">Mi Cuenta</h1>
        <p class="my-account__subtitle">Administra tu información personal y opciones de seguridad.</p>
      </header>

      <div class="my-account__content">
        <div class="card my-account__card">
          <form @submit.prevent="updateProfile" class="form">
            <h2 class="form__title">Información Personal</h2>
            
            <div class="form__row">
              <div class="form__group">
                <label for="nombre" class="form__label">Nombre completo</label>
                <input type="text" id="nombre" v-model="form.nombre" @input="sanitizeName('nombre', form)" class="form__input" />
              </div>

              <div class="form__group">
                <label for="telefono" class="form__label">Teléfono</label>
                <input type="text" id="telefono" v-model="form.telefono" @input="sanitizePhone('telefono', form)" class="form__input" />
              </div>
            </div>

            <div class="form__group">
              <label for="email" class="form__label">Correo electrónico</label>
              <input type="email" id="email" v-model="form.email" class="form__input" />
            </div>

            <h2 class="form__title form__title--margin">Cambiar Contraseña</h2>
            <p class="form__hint">Deja estos campos en blanco si no deseas cambiar tu contraseña.</p>

            <div class="form__row">
              <div class="form__group">
                <label for="password" class="form__label">Nueva contraseña</label>
                <input type="password" id="password" v-model="form.password" @input="sanitizeNoSpaces('password', form)" class="form__input" placeholder="Mínimo 6 caracteres" />
              </div>

              <div class="form__group">
                <label for="password_confirmation" class="form__label">Confirmar contraseña</label>
                <input type="password" id="password_confirmation" v-model="form.password_confirmation" @input="sanitizeNoSpaces('password_confirmation', form)" class="form__input" />
              </div>
            </div>

            <div v-if="message" class="alert alert--success">{{ message }}</div>
            <div v-if="error" class="alert alert--error">{{ error }}</div>

            <div class="form__actions">
              <button type="submit" class="btn btn--primary" :disabled="loading">
                <span v-if="loading">Guardando...</span>
                <span v-else>Guardar Cambios</span>
              </button>
            </div>
          </form>
        </div>

        <div class="card card--danger my-account__card my-account__card--danger">
          <h2 class="form__title">Zona de Peligro</h2>
          <p class="danger-text">Una vez que elimines tu cuenta, no hay vuelta atrás. Por favor, asegúrate de tu decisión.</p>
          <button @click="showDeleteModal = true" type="button" class="btn btn--danger" :disabled="deleting">
            <span v-if="deleting">Eliminando...</span>
            <span v-else>Eliminar Cuenta</span>
          </button>
        </div>
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
          <h2 class="modal-title">¿Eliminar tu cuenta?</h2>
          <p class="modal-text">Esta acción es permanente y no se puede deshacer. Se borrarán todos tus datos personales.</p>
          
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

export default {
  name: 'MyAccount',
  data() {
    return {
      form: {
        nombre: '',
        email: '',
        telefono: '',
        password: '',
        password_confirmation: ''
      },
      loading: false,
      deleting: false,
      showDeleteModal: false,
      message: '',
      error: ''
    }
  },
  mounted() {
    this.fetchProfile()
  },
  methods: {
    sanitizeName(field, obj) {
      let val = obj[field].replace(/^\s+/, '') // Sin espacios al inicio
      val = val.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '') // Solo letras y espacios
      val = val.replace(/\s{2,}/g, ' ') // Sin espacios dobles
      obj[field] = val
    },
    sanitizePhone(field, obj) {
      let val = obj[field].replace(/^\s+/, '')
      val = val.replace(/[^\+0-9\s]/g, '') // Solo números, + y espacios
      val = val.replace(/(?!^\+)\+/g, '') // + solo al inicio
      val = val.replace(/\s{2,}/g, ' ')
      obj[field] = val
    },
    sanitizeNoSpaces(field, obj) {
      obj[field] = obj[field].replace(/\s/g, '') // Sin espacios
    },
    async fetchProfile() {
      try {
        const token = localStorage.getItem('auth_token')
        if (!token) {
          this.$router.push('/login')
          return
        }

        const { data } = await axios.get((import.meta.env.VITE_API_URL || (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1' ? 'http://localhost:8000/api' : 'https://ecommerce-backend-qqda.onrender.com/api')) + '/profile', {
          headers: { Authorization: `Bearer ${token}` }
        })
        
        this.form.nombre = data.nombre
        this.form.email = data.email
        this.form.telefono = data.telefono || ''
      } catch (err) {
        console.error(err)
        if (err.response?.status === 401) {
          localStorage.removeItem('auth_token')
          localStorage.removeItem('auth_user')
          this.$router.push('/login')
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

        const { data } = await axios.put((import.meta.env.VITE_API_URL || (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1' ? 'http://localhost:8000/api' : 'https://ecommerce-backend-qqda.onrender.com/api')) + '/profile', payload, {
          headers: { Authorization: `Bearer ${token}` }
        })

        this.message = data.message
        this.form.password = ''
        this.form.password_confirmation = ''
        
        // Update local storage user info
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
        await axios.delete((import.meta.env.VITE_API_URL || (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1' ? 'http://localhost:8000/api' : 'https://ecommerce-backend-qqda.onrender.com/api')) + '/profile', {
          headers: { Authorization: `Bearer ${token}` }
        })

        localStorage.removeItem('auth_token')
        localStorage.removeItem('auth_user')
        this.showDeleteModal = false
        this.$router.push('/login')
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
.my-account {
  min-height: 100vh;
  padding: 120px 20px 80px;
  background-color: #faf8f4;
  font-family: 'Inter', sans-serif;
}

.my-account__container {
  max-width: 800px;
  margin: 0 auto;
}

.my-account__header {
  text-align: center;
  margin-bottom: 48px;
}

.my-account__title {
  font-family: 'Cormorant Garamond', serif;
  font-size: 42px;
  font-weight: 700;
  color: #1a1a1a;
  margin-bottom: 12px;
}

.my-account__subtitle {
  font-size: 16px;
  color: #666;
}

.my-account__content {
  display: flex;
  flex-direction: column;
  gap: 32px;
}

.card {
  background: #ffffff;
  border-radius: 20px;
  padding: 40px;
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.04);
}

.card--danger {
  border: 1px solid #fee2e2;
}

.form__title {
  font-family: 'Cormorant Garamond', serif;
  font-size: 24px;
  font-weight: 600;
  color: #1a1a1a;
  margin-bottom: 24px;
}

.form__title--margin {
  margin-top: 48px;
  margin-bottom: 8px;
}

.form__hint {
  font-size: 14px;
  color: #888;
  margin-bottom: 24px;
}

.form__group {
  margin-bottom: 24px;
}

.form__row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
  margin-bottom: 24px;
}

.form__row .form__group {
  margin-bottom: 0;
}

.form__label {
  display: block;
  font-size: 14px;
  font-weight: 500;
  color: #333;
  margin-bottom: 8px;
}

.form__input {
  width: 100%;
  padding: 14px 16px;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  font-family: 'Inter', sans-serif;
  font-size: 15px;
  color: #1a1a1a;
  transition: all 0.3s ease;
  background-color: #fafafa;
}

.form__input:focus {
  outline: none;
  border-color: #1a1a1a;
  background-color: #ffffff;
  box-shadow: 0 0 0 4px rgba(26, 26, 26, 0.05);
}

.form__actions {
  margin-top: 32px;
}

.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 14px 28px;
  font-family: 'Inter', sans-serif;
  font-size: 15px;
  font-weight: 600;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.3s ease;
  border: none;
}

.btn--primary {
  background: #1a1a1a;
  color: #ffffff;
  width: 100%;
}

.btn--primary:hover:not(:disabled) {
  background: #333333;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(26, 26, 26, 0.2);
}

.btn--danger {
  background: #ef4444;
  color: #ffffff;
}

.btn--danger:hover:not(:disabled) {
  background: #dc2626;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.danger-text {
  font-size: 15px;
  color: #666;
  margin-bottom: 24px;
  line-height: 1.6;
}

.alert {
  padding: 14px 16px;
  border-radius: 10px;
  margin-top: 24px;
  font-size: 14px;
  font-weight: 500;
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

@media (max-width: 640px) {
  .form__row {
    grid-template-columns: 1fr;
  }

  .my-account {
    padding-top: 100px;
  }
  
  .card {
    padding: 24px;
  }
  
  .my-account__title {
    font-size: 32px;
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
  background: #fff;
  border-radius: 20px;
  padding: 48px 40px 36px;
  max-width: 400px;
  width: 90%;
  text-align: center;
  box-shadow: 0 25px 60px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(255,255,255,0.1);
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
  color: #1a1a1a;
  margin: 0 0 10px 0;
}

.modal-text {
  font-family: 'Inter', sans-serif;
  font-size: 14px;
  color: #666;
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
  background: #f3f4f6;
  color: #4b5563;
}

.modal-btn--cancel:hover {
  background: #e5e7eb;
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
