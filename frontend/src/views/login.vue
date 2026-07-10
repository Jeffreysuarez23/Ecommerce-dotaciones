<template>
  <div class="login-page">

    <!-- ─── HERO BANNER ── -->
    <section class="login-hero">
      <div class="login-hero__inner">
        <p class="eyebrow" v-if="isLogin">/ BIENVENIDO DE NUEVO</p>
        <p class="eyebrow" v-else>/ ÚNETE</p>
        <h1 class="login-hero__title" v-if="isLogin">
          Inicia sesión en tu<br />
          <em>cuenta</em>
        </h1>
        <h1 class="login-hero__title" v-else>
          Crea una<br />
          <em>cuenta</em>
        </h1>
      </div>
    </section>

    <!-- ─── LOGIN BODY ── -->
    <section class="login-body">
      <div class="login-body__inner">

        <!-- Form Container -->
        <div class="login-form-container">
          
          <div class="tabs">
            <button class="tab-btn" :class="{ active: isLogin }" @click="isLogin = true">Iniciar Sesión</button>
            <button class="tab-btn" :class="{ active: !isLogin }" @click="isLogin = false">Registrarse</button>
          </div>

          <!-- LOGIN FORM -->
          <div v-if="isLogin" class="login-form">
            <div class="form-group">
              <label class="form-label">Email <span class="required">*</span></label>
              <input type="email" class="form-input" v-model="form.login" @input="sanitizeTrim('login', form)" placeholder="hello@store.com" />
            </div>

            <div class="form-group">
              <label class="form-label">Contraseña <span class="required">*</span></label>
              <input type="password" class="form-input" v-model="form.password" @input="sanitizeNoSpaces('password', form)" placeholder="••••••••" />
              <div class="form-forgot">
                <router-link to="/forgot-password" class="forgot-link">¿Olvidaste tu contraseña?</router-link>
              </div>
            </div>

            <button class="submit-btn" @click="handleSubmit" :disabled="loading">
              <span v-if="!loading">Iniciar Sesión</span>
              <span v-else>Iniciando...</span>
              <svg v-if="!loading" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
              </svg>
            </button>
          </div>

          <!-- REGISTER FORM -->
          <div v-else class="login-form">
            <div class="form-group">
              <label class="form-label">Nombre completo <span class="required">*</span></label>
              <input type="text" class="form-input" v-model="form.nombre" @input="sanitizeName('nombre', form)" placeholder="Tu nombre completo" />
            </div>

            <div class="form-group">
              <label class="form-label">Email <span class="required">*</span></label>
              <input type="email" class="form-input" v-model="form.email" @input="sanitizeNoSpaces('email', form)" placeholder="hello@store.com" />
            </div>

            <div class="form-group">
              <label class="form-label">Teléfono</label>
              <input type="tel" class="form-input" v-model="form.telefono" @input="sanitizePhone('telefono', form)" placeholder="3000000000" maxlength="10" />
            </div>

            <div class="form-group">
              <label class="form-label">Contraseña <span class="required">*</span></label>
              <input type="password" class="form-input" v-model="form.password" @input="sanitizeNoSpaces('password', form)" placeholder="••••••••" />
            </div>

            <div class="form-group">
              <label class="form-label">Confirmar Contraseña <span class="required">*</span></label>
              <input type="password" class="form-input" v-model="form.password_confirmation" @input="sanitizeNoSpaces('password_confirmation', form)" placeholder="••••••••" />
            </div>

            <button class="submit-btn" @click="handleSubmit" :disabled="loading">
              <span v-if="!loading">Crear Cuenta</span>
              <span v-else>Creando...</span>
              <svg v-if="!loading" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
              </svg>
            </button>
          </div>

          <!-- Messages -->
          <div v-if="errorMsg" class="form-message form-message--error">{{ errorMsg }}</div>
          <div v-if="successMsg" class="form-message form-message--success">{{ successMsg }}</div>

        </div>

      </div>
    </section>

    <!-- Register Success Modal -->
    <Transition name="modal">
      <div v-if="showSuccessModal" class="modal-overlay" @click.self="closeModal">
        <div class="modal-card">
          <div class="modal-icon">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10" class="modal-circle" />
              <polyline points="9 12 11.5 14.5 16 9.5" class="modal-check" />
            </svg>
          </div>
          <h2 class="modal-title">¡Cuenta creada!</h2>
          <p class="modal-text">Mandamos un correo de activación.<br>Actívalo antes de iniciar sesión.</p>
          <button class="modal-btn" @click="closeModal">Ir a inicio de sesión</button>
        </div>
      </div>
    </Transition>

    <!-- Login Success Modal -->
    <Transition name="modal">
      <div v-if="showLoginModal" class="modal-overlay">
        <div class="modal-card">
          <div class="modal-icon">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" class="modal-circle" />
              <circle cx="12" cy="7" r="4" class="modal-check" />
            </svg>
          </div>
          <h2 class="modal-title">¡Bienvenido, {{ loggedUserName }}!</h2>
          <p class="modal-text">
            Has iniciado sesión correctamente.<br>
            <template v-if="isAdmin">
              ¿Deseas ir al panel administrativo o explorar la tienda?
            </template>
            <template v-else>
              Explora nuestra tienda.
            </template>
          </p>
          <div style="display: flex; flex-direction: column; gap: 12px;">
            <button class="modal-btn" @click="goToHome">Ir a la página principal</button>
            <button v-if="isAdmin" class="modal-btn admin-btn" @click="goToAdmin">Ir a panel administrativo</button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Verification Error Modal -->
    <Transition name="modal">
      <div v-if="showVerificationErrorModal" class="modal-overlay" @click.self="showVerificationErrorModal = false">
        <div class="modal-card">
          <div class="modal-icon">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#b91c1c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10" />
              <line x1="12" y1="8" x2="12" y2="12" />
              <line x1="12" y1="16" x2="12.01" y2="16" />
            </svg>
          </div>
          <h2 class="modal-title" style="color: #b91c1c;">Acceso Bloqueado</h2>
          <p class="modal-text">Tu correo electrónico no ha sido verificado.<br>Revisa tu bandeja de entrada.</p>
          <button class="modal-btn" style="background: linear-gradient(135deg, #b91c1c 0%, #7f1d1d 100%);" @click="showVerificationErrorModal = false">Cerrar Alerta</button>
        </div>
      </div>
    </Transition>

  </div>
</template>

<script>
import axios from 'axios'

const API_URL = (import.meta.env.VITE_API_URL || 'http://localhost:8000/api') + ''

export default {
  name: 'LoginView',
  data() {
    return {
      isLogin: true,
      loading: false,
      errorMsg: '',
      successMsg: '',
      showSuccessModal: false,
      showLoginModal: false,
      showVerificationErrorModal: false,
      loggedUserName: '',
      isAdmin: false,
      form: {
        login: '',
        nombre: '',
        email: '',
        telefono: '',
        password: '',
        password_confirmation: ''
      }
    }
  },
  watch: {
    isLogin() {
      this.errorMsg = ''
      this.successMsg = ''
      this.form = { login: '', nombre: '', email: '', telefono: '', password: '', password_confirmation: '' }
    }
  },
  methods: {
    sanitizeName(field, obj) {
      let val = obj[field].replace(/^\s+/, '') // Sin espacios al inicio
      val = val.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '') // Solo letras y espacios
      val = val.replace(/\s{2,}/g, ' ') // Sin espacios dobles
      obj[field] = val
    },
    sanitizePhone(field, obj) {
      let val = obj[field].replace(/\D/g, '') // Solo números
      if (val.length > 10) {
        val = val.slice(0, 10)
      }
      obj[field] = val
    },
    sanitizeNoSpaces(field, obj) {
      obj[field] = obj[field].replace(/\s/g, '') // Sin espacios
    },
    sanitizeTrim(field, obj) {
      if (obj[field].startsWith(' ')) {
        obj[field] = obj[field].replace(/^\s+/, '')
      }
    },
    closeModal() {
      this.showSuccessModal = false
      this.isLogin = true
      this.form = { login: '', nombre: '', email: '', telefono: '', password: '', password_confirmation: '' }
    },
    goToHome() {
      this.showLoginModal = false
      this.$router.push('/')
    },
    goToAdmin() {
      this.showLoginModal = false
      const user = localStorage.getItem('auth_user') || ''
      const token = localStorage.getItem('auth_token') || ''
      const qs = '?auth_user=' + encodeURIComponent(user) + '&auth_token=' + encodeURIComponent(token)
      window.location.href = window.location.protocol + '//' + window.location.hostname + ':5174/' + qs
    },
    async handleSubmit() {
      this.errorMsg = ''
      this.successMsg = ''

      // Validación básica en frontend
      if (this.isLogin) {
        if (!this.form.login || !this.form.password) {
          this.errorMsg = 'Por favor completa email/nombre y contraseña.'
          return
        }
      } else {
        if (!this.form.nombre || !this.form.email || !this.form.password) {
          this.errorMsg = 'Por favor completa todos los campos obligatorios.'
          return
        }
        if (this.form.telefono) {
          const digits = this.form.telefono.replace(/\D/g, '')
          if (digits.length !== 10) {
            this.errorMsg = 'El teléfono debe tener exactamente 10 números.'
            return
          }
        }
        if (this.form.password.length < 6) {
          this.errorMsg = 'La contraseña debe tener al menos 6 caracteres.'
          return
        }
        if (this.form.password !== this.form.password_confirmation) {
          this.errorMsg = 'Las contraseñas no coinciden.'
          return
        }
      }

      this.loading = true

      try {
        if (this.isLogin) {
          // --- LOGIN (email o nombre) ---
          const response = await axios.post(`${API_URL}/login`, {
            login: this.form.login,
            password: this.form.password
          })

          localStorage.setItem('auth_token', response.data.token)
          localStorage.setItem('auth_user', JSON.stringify(response.data.user))

          this.loggedUserName = response.data.user.nombre || 'Usuario'
          this.isAdmin = response.data.user.rol === 'admin' || response.data.user.rol === 'superadmin' || response.data.user.rol === 'super admin' || response.data.user.rol === 'super_admin'
          this.showLoginModal = true

        } else {
          // --- REGISTER ---
          const payload = {
            nombre: this.form.nombre,
            email: this.form.email,
            password: this.form.password
          }
          if (this.form.telefono) {
            payload.telefono = this.form.telefono
          }
          const response = await axios.post(`${API_URL}/register`, payload)

          // Mostrar modal de éxito y luego ir al login
          this.showSuccessModal = true
        }
      } catch (error) {
        if (error.response) {
          // Si el correo no está verificado (403)
          if (error.response.status === 403 && error.response.data.email_not_verified) {
            this.showVerificationErrorModal = true
          }
          // Errores de validación de Laravel (422)
          else if (error.response.status === 422 && error.response.data.errors) {
            const errors = error.response.data.errors
            let rawMsg = Object.values(errors).flat().join(' ')
            
            // Traducciones comunes de Laravel
            const tr = {
              'The email has already been taken.': 'Este correo ya está registrado.',
              'The email must be a valid email address.': 'El correo debe ser válido.',
              'The password field confirmation does not match.': 'Las contraseñas no coinciden.',
              'The password confirmation does not match.': 'Las contraseñas no coinciden.',
              'The password must be at least 6 characters.': 'La contraseña debe tener al menos 6 caracteres.',
              'The login field is required.': 'El campo de email/nombre es obligatorio.'
            }
            Object.keys(tr).forEach(en => {
              rawMsg = rawMsg.replace(new RegExp(en, 'gi'), tr[en])
            })
            
            this.errorMsg = rawMsg
          } else {
            let msg = error.response.data.message || 'Error en el servidor.'
            if (msg === 'Too Many Attempts.') msg = 'Demasiados intentos. Por favor espera.'
            this.errorMsg = msg
          }
        } else {
          this.errorMsg = 'No se pudo conectar con el servidor. Verifica tu conexión.'
        }
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
.login-page {
  /* Reduced top padding */
  padding-top: 20px;
  min-height: 100vh;
  font-family: 'Cormorant Garamond', 'Georgia', serif;
  color: #1a1a1a;
  background: #faf8f2;
}

/* ── Hero ── */
.login-hero {
  background: #faf8f2;
  border-bottom: 1px solid #ede8de;
  /* Reduced padding */
  padding: 40px 0 50px;
  text-align: center;
}

.login-hero__inner {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 40px;
}

.eyebrow {
  font-family: 'Inter', sans-serif;
  font-size: 11px;
  font-weight: 500;
  letter-spacing: 0.15em;
  color: #aaa;
  margin: 0 0 16px 0;
  text-transform: uppercase;
}

.login-hero__title {
  font-size: clamp(40px, 5vw, 68px);
  font-weight: 800;
  line-height: 1.1;
  margin: 0;
  color: #1a1a1a;
}

.login-hero__title em {
  font-weight: 300;
  font-style: italic;
  display: block;
}

/* ── Body ── */
.login-body {
  background: #fff;
}

.login-body__inner {
  max-width: 500px;
  margin: 0 auto;
  /* Reduced padding */
  padding: 40px 40px 100px;
}

/* ── Tabs ── */
.tabs {
  display: flex;
  gap: 20px;
  justify-content: center;
  margin-bottom: 30px;
  border-bottom: 1px solid #e0ddd8;
}

.tab-btn {
  background: none;
  border: none;
  font-family: 'Inter', sans-serif;
  font-size: 15px;
  font-weight: 600;
  color: #aaa;
  padding: 10px 20px;
  cursor: pointer;
  position: relative;
  transition: color 0.3s ease;
}

.tab-btn.active {
  color: #1a1a1a;
}

.tab-btn.active::after {
  content: '';
  position: absolute;
  bottom: -1px;
  left: 0;
  width: 100%;
  height: 2px;
  background: #1a1a1a;
}

/* ── Form ── */
.login-form {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

/* ── Messages ── */
.form-message {
  margin-top: 16px;
  padding: 14px 18px;
  border-radius: 8px;
  font-family: 'Inter', sans-serif;
  font-size: 13px;
  font-weight: 500;
  line-height: 1.5;
  animation: fadeIn 0.3s ease;
}

.form-message--error {
  background: #fef2f2;
  color: #b91c1c;
  border: 1px solid #fecaca;
}

.form-message--success {
  background: #f0fdf4;
  color: #15803d;
  border: 1px solid #bbf7d0;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-6px); }
  to { opacity: 1; transform: translateY(0); }
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-label {
  font-family: 'Inter', sans-serif;
  font-size: 13px;
  font-weight: 500;
  color: #444;
  display: flex;
  align-items: center;
  gap: 4px;
}

.required {
  color: #c0392b;
}

.form-input {
  width: 100%;
  padding: 13px 16px;
  border: 1px solid #e0ddd8;
  border-radius: 8px;
  font-family: 'Cormorant Garamond', serif;
  font-size: 15px;
  color: #1a1a1a;
  background: #fff;
  outline: none;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
  box-sizing: border-box;
}

.form-input:focus {
  border-color: #1a1a1a;
  box-shadow: 0 0 0 3px rgba(26, 26, 26, 0.06);
}

.form-input::placeholder {
  color: #bbb;
}

.form-forgot {
  text-align: right;
  margin-top: 8px;
}

.forgot-link {
  font-family: 'Inter', sans-serif;
  font-size: 13px;
  color: #6b5f4e;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.2s ease;
}

.forgot-link:hover {
  color: #1a1a1a;
  text-decoration: underline;
}

.submit-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  width: 100%;
  padding: 18px 32px;
  background: #1a1a1a;
  color: white;
  border: none;
  border-radius: 100px;
  font-family: 'Cormorant Garamond', serif;
  font-size: 17px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s ease, transform 0.2s ease;
  margin-top: 8px;
}

.submit-btn:hover:not(:disabled) {
  background: #333;
  transform: translateY(-1px);
}

.submit-btn:disabled {
  background: #aaa;
  cursor: default;
}

/* ── Responsive ── */
@media (max-width: 900px) {
  .login-body__inner {
    padding: 30px 20px 60px;
  }

  .login-hero__inner {
    padding: 0 20px;
  }
}

/* ── Success Modal ── */
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
  max-width: 380px;
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

.modal-check {
  stroke-dasharray: 20;
  stroke-dashoffset: 20;
  animation: drawCheck 0.4s ease forwards 0.5s;
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
  margin: 0 0 28px 0;
}

.modal-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  padding: 16px 32px;
  background: linear-gradient(135deg, #1a1a1a 0%, #333 100%);
  color: white;
  border: none;
  border-radius: 100px;
  font-family: 'Cormorant Garamond', serif;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.modal-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
}

.admin-btn {
  background: white;
  color: #1a1a1a;
  border: 1px solid #1a1a1a;
}

.admin-btn:hover {
  background: #f4f4f4;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
}

@keyframes modalPop {
  from { opacity: 0; transform: scale(0.85) translateY(20px); }
  to { opacity: 1; transform: scale(1) translateY(0); }
}

@keyframes drawCircle {
  to { stroke-dashoffset: 0; }
}

@keyframes drawCheck {
  to { stroke-dashoffset: 0; }
}

/* Vue Transition */
.modal-enter-active { transition: opacity 0.3s ease; }
.modal-leave-active { transition: opacity 0.2s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
</style>
