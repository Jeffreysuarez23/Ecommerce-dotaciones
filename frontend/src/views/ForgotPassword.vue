<template>
  <div class="login-page">
    
    <div class="login-hero">
      <div class="login-hero__bg"></div>
      <div class="login-hero__inner">
        <h1 class="login-title">Recuperar<span class="login-title--light">Cuenta</span></h1>
        <p class="login-subtitle">Recupera el acceso a tu cuenta.</p>
      </div>
    </div>

    <section class="login-body">
      <div class="login-body__inner">
        <div class="login-card">
          
          <div class="login-header">
            <h2 class="login-heading">¿Olvidaste tu contraseña?</h2>
            <p class="login-desc">Ingresa tu correo electrónico y te enviaremos un enlace para restablecerla.</p>
          </div>

          <div class="login-form">
            <div class="form-group">
              <label class="form-label">Correo <span class="required">*</span></label>
              <input type="email" class="form-input" v-model="email" placeholder="hello@store.com" />
            </div>

            <button class="submit-btn" @click="handleSubmit" :disabled="loading">
              <span v-if="!loading">Enviar enlace de recuperación</span>
              <span v-else>Enviando...</span>
              <svg v-if="!loading" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="22" y1="2" x2="11" y2="13"/>
                <polygon points="22 2 15 22 11 13 2 9 22 2"/>
              </svg>
            </button>
            
            <div class="form-forgot" style="text-align: center; margin-top: 24px;">
              <router-link to="/login" class="forgot-link">Volver al inicio de sesión</router-link>
            </div>
          </div>

          <!-- Messages -->
          <div v-if="errorMsg" class="form-message form-message--error">{{ errorMsg }}</div>
          
        </div>
      </div>
    </section>

    <!-- Success Modal -->
    <Transition name="modal">
      <div v-if="showSuccessModal" class="modal-overlay" @click.self="goBackToLogin">
        <div class="modal-card">
          <div class="modal-icon">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10" class="modal-circle" />
              <polyline points="9 12 11.5 14.5 16 9.5" class="modal-check" />
            </svg>
          </div>
          <h2 class="modal-title">¡Correo enviado!</h2>
          <p class="modal-text">Si existe una cuenta con ese correo, recibirás un enlace de recuperación.<br>Revisa tu bandeja de entrada o spam.</p>
          <button class="modal-btn" @click="goBackToLogin">Volver a Iniciar Sesión</button>
        </div>
      </div>
    </Transition>

  </div>
</template>

<script>
import axios from 'axios'

const API_URL = 'http://localhost:8000/api'

export default {
  name: 'ForgotPassword',
  data() {
    return {
      email: '',
      loading: false,
      errorMsg: '',
      showSuccessModal: false
    }
  },
  methods: {
    async handleSubmit() {
      this.errorMsg = ''
      
      if (!this.email) {
        this.errorMsg = 'Por favor ingresa tu correo electrónico.'
        return
      }

      this.loading = true

      try {
        await axios.post(`${API_URL}/forgot-password`, {
          email: this.email
        })
        this.showSuccessModal = true
      } catch (error) {
        if (error.response) {
          if (error.response.status === 422 && error.response.data.errors) {
            const errors = error.response.data.errors
            this.errorMsg = Object.values(errors).flat().join(' ')
          } else {
            this.errorMsg = error.response.data.message || 'Error al procesar la solicitud.'
          }
        } else {
          this.errorMsg = 'No se pudo conectar con el servidor.'
        }
      } finally {
        this.loading = false
      }
    },
    goBackToLogin() {
      this.$router.push('/login')
    }
  }
}
</script>

<style scoped>
/* ── Estilos base copiados de login.vue para consistencia visual ── */
.login-page {
  font-family: 'Cormorant Garamond', 'Georgia', serif;
  background: #faf8f4;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.login-hero {
  position: relative;
  background: #1a1a1a;
  padding: 100px 40px 140px;
  text-align: center;
}

.login-hero__bg {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
  opacity: 0.95;
}

.login-hero__inner {
  position: relative;
  z-index: 2;
  max-width: 800px;
  margin: 0 auto;
}

.login-title {
  font-size: clamp(48px, 6vw, 72px);
  font-weight: 800;
  line-height: 1;
  margin: 0 0 16px;
  color: white;
}

.login-title--light {
  font-weight: 300;
  font-style: italic;
}

.login-subtitle {
  font-family: 'Inter', sans-serif;
  font-size: 16px;
  color: #aaa;
  margin: 0;
  font-weight: 400;
}

.login-body {
  flex: 1;
  margin-top: -80px;
  position: relative;
  z-index: 10;
}

.login-body__inner {
  max-width: 480px;
  margin: 0 auto;
  padding: 0 20px 80px;
}

.login-card {
  background: rgba(255, 255, 255, 0.85);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border-radius: 20px;
  padding: 48px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.05), 0 1px 3px rgba(0,0,0,0.02);
  border: 1px solid rgba(255, 255, 255, 0.5);
}

.login-header {
  margin-bottom: 32px;
  text-align: center;
}

.login-heading {
  font-size: 28px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 8px 0;
}

.login-desc {
  font-family: 'Inter', sans-serif;
  font-size: 14px;
  color: #666;
  margin: 0;
  line-height: 1.5;
}

.form-group {
  margin-bottom: 24px;
}

.form-label {
  display: block;
  font-family: 'Inter', sans-serif;
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  color: #1a1a1a;
  margin-bottom: 8px;
}

.required {
  color: #c44a2c;
}

.form-input {
  width: 100%;
  padding: 14px 16px;
  background: white;
  border: 1px solid #e5e5e5;
  border-radius: 8px;
  font-family: 'Inter', sans-serif;
  font-size: 15px;
  color: #1a1a1a;
  transition: all 0.2s ease;
  box-sizing: border-box;
}

.form-input::placeholder {
  color: #bbb;
}

.form-input:focus {
  outline: none;
  border-color: #1a1a1a;
  box-shadow: 0 0 0 3px rgba(26,26,26,0.05);
}

.forgot-link {
  font-family: 'Inter', sans-serif;
  font-size: 14px;
  color: #6b5f4e;
  text-decoration: none;
  font-weight: 600;
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
  gap: 10px;
  width: 100%;
  padding: 16px;
  background: #1a1a1a;
  color: white;
  border: none;
  border-radius: 100px;
  font-family: 'Cormorant Garamond', serif;
  font-size: 18px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.25s ease;
}

.submit-btn:hover:not(:disabled) {
  background: #333;
  transform: translateY(-1px);
}

.submit-btn:disabled {
  background: #aaa;
  cursor: default;
}

.form-message {
  margin-top: 24px;
  padding: 12px 16px;
  border-radius: 8px;
  font-family: 'Inter', sans-serif;
  font-size: 13px;
  font-weight: 500;
  text-align: center;
}

.form-message--error {
  background: #fdf2f2;
  color: #c44a2c;
  border: 1px solid #fadcd9;
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

.modal-icon { margin-bottom: 20px; }
.modal-circle { stroke-dasharray: 63; stroke-dashoffset: 63; animation: drawCircle 0.6s ease forwards 0.1s; }
.modal-check { stroke-dasharray: 20; stroke-dashoffset: 20; animation: drawCheck 0.4s ease forwards 0.5s; }
.modal-title { font-family: 'Cormorant Garamond', serif; font-size: 26px; font-weight: 700; color: #1a1a1a; margin: 0 0 10px 0; }
.modal-text { font-family: 'Inter', sans-serif; font-size: 14px; color: #666; line-height: 1.6; margin: 0 0 28px 0; }

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

@keyframes modalPop {
  from { opacity: 0; transform: scale(0.85) translateY(20px); }
  to { opacity: 1; transform: scale(1) translateY(0); }
}
@keyframes drawCircle { to { stroke-dashoffset: 0; } }
@keyframes drawCheck { to { stroke-dashoffset: 0; } }

.modal-enter-active { transition: opacity 0.3s ease; }
.modal-leave-active { transition: opacity 0.2s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; }

@media (max-width: 900px) {
  .login-body__inner { padding: 30px 20px 60px; }
  .login-hero__inner { padding: 0 20px; }
}
</style>
