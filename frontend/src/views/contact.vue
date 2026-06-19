<template>
  <div class="contact-page">

    <!-- ─── HERO BANNER ── -->
    <section class="contact-hero">
      <div class="contact-hero__inner">
        <p class="eyebrow">/ CONTÁCTANOS</p>
        <h1 class="contact-hero__title">
          Nos encantaría<br />
          <em>saber de ti</em>
        </h1>
      </div>
    </section>

    <!-- ─── CONTACT BODY ── -->
    <section class="contact-body">
      <div class="contact-body__inner">

        <!-- LEFT: Info -->
        <div class="contact-info">
          <h2 class="contact-info__title">Información de Contacto</h2>
          <p class="contact-info__desc">
            ¿Tienes una pregunta sobre un pedido, un producto o simplemente quieres saludar?
            Estamos aquí para ayudar. Nuestro equipo normalmente responde en 24 horas.
          </p>

          <ul class="contact-details">
            <li class="contact-detail">
              <div class="contact-detail__icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
                </svg>
              </div>
              <div>
                <p class="contact-detail__label">CORREO</p>
                <p class="contact-detail__value">hello@store.com</p>
              </div>
            </li>

            <li class="contact-detail">
              <div class="contact-detail__icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81a19.79 19.79 0 01-3.07-8.67A2 2 0 012 1h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L6.09 8.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/>
                </svg>
              </div>
              <div>
                <p class="contact-detail__label">TELÉFONO</p>
                <p class="contact-detail__value">+57 300 000 000</p>
              </div>
            </li>

            <li class="contact-detail">
              <div class="contact-detail__icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/>
                </svg>
              </div>
              <div>
                <p class="contact-detail__label">DIRECCIÓN</p>
                <p class="contact-detail__value">Calle 8 #4-56<br />Bogota Colombia</p>
              </div>
            </li>

            <li class="contact-detail">
              <div class="contact-detail__icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
              </div>
              <div>
                <p class="contact-detail__label">HORARIO</p>
                <p class="contact-detail__value">Lun–Vie: 8am – 6pm</p>
              </div>
            </li>
          </ul>

          <div class="contact-social">
            <p class="contact-social__label">SÍGUENOS</p>
            <div class="contact-social__icons">
              <a href="#" class="social-icon" aria-label="Instagram">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
                </svg>
              </a>
              <a href="#" class="social-icon" aria-label="Pinterest">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10"/><path d="M8.56 2.75c4.37 6.03 6.02 9.42 8.03 17.72m2.54-15.38c-3.72 4.35-8.94 5.66-16.88 5.85m19.5 1.9c-3.5-.93-6.63-.82-8.94 0-2.58.92-5.01 2.86-7.44 6.32"/>
                </svg>
              </a>
              <a href="#" class="social-icon" aria-label="YouTube">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M22.54 6.42a2.78 2.78 0 00-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 00-1.95 1.96A29 29 0 001 12a29 29 0 00.46 5.58A2.78 2.78 0 003.41 19.6C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 001.95-1.95A29 29 0 0023 12a29 29 0 00-.46-5.58z"/><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02"/>
                </svg>
              </a>
            </div>
          </div>
        </div>

        <!-- RIGHT: Form -->
        <div class="contact-form">
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Nombre <span class="required">*</span></label>
              <input type="text" class="form-input" v-model="form.firstName" placeholder="" />
            </div>
            <div class="form-group">
              <label class="form-label">Apellido <span class="required">*</span></label>
              <input type="text" class="form-input" v-model="form.lastName" placeholder="" />
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Correo <span class="required">*</span></label>
            <input type="email" class="form-input" v-model="form.email" placeholder="" />
          </div>

          <div class="form-group">
            <label class="form-label">Asunto <span class="required">*</span></label>
            <div class="form-select-wrap">
              <select class="form-select" v-model="form.subject">
                <option value="" disabled>Selecciona un tema...</option>
                <option value="order">Problema con el pedido</option>
                <option value="product">Pregunta sobre un producto</option>
                <option value="return">Devoluciones y Cambios</option>
                <option value="shipping">Envío</option>
                <option value="other">Otro</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">
              Mensaje <span class="required">*</span>
              <span class="char-count">({{ form.message.length }}/500)</span>
            </label>
            <textarea
              class="form-textarea"
              v-model="form.message"
              maxlength="500"
              placeholder="Cuéntanos cómo podemos ayudarte..."
              rows="6"
            ></textarea>
          </div>

          <div v-if="message" class="alert alert--success">{{ message }}</div>
          <div v-if="error" class="alert alert--error">{{ error }}</div>

          <button class="submit-btn" @click="handleSubmit" :disabled="submitted">
            <span v-if="!submitted">Enviar Mensaje</span>
            <span v-else>Mensaje Enviado ✓</span>
            <svg v-if="!submitted" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
            </svg>
          </button>
        </div>

      </div>
    </section>

  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'ContactView',
  data() {
    return {
      submitted: false,
      form: {
        firstName: '',
        lastName: '',
        email: '',
        subject: '',
        message: ''
      },
      message: '',
      error: ''
    }
  },
  methods: {
    async handleSubmit() {
      if (!this.form.firstName || !this.form.email || !this.form.message) return
      
      this.message = ''
      this.error = ''

      try {
        await axios.post('http://localhost:8000/api/contactos', this.form)
        this.submitted = true
        
        // Alerta de confirmación para el usuario con estilo
        this.message = 'Ya su mensaje fue enviado y nos pondremos en contacto. ¡Muchas gracias!'
        
        // Reset form after submission
        setTimeout(() => { 
          this.submitted = false
          this.message = ''
          this.form = {
            firstName: '',
            lastName: '',
            email: '',
            subject: '',
            message: ''
          }
        }, 3000)
      } catch (err) {
        console.error('Error enviando el mensaje:', err)
        this.error = 'Hubo un error al enviar tu mensaje. Por favor intenta de nuevo.'
      }
    }
  }
}
</script>

<style scoped>
.contact-page {
  padding-top: 100px;
  min-height: 100vh;
  font-family: 'Cormorant Garamond', 'Georgia', serif;
  color: #1a1a1a;
}

/* ── Hero ── */
.contact-hero {
  background: #faf8f2;
  border-bottom: 1px solid #ede8de;
  padding: 80px 0 90px;
}

.contact-hero__inner {
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
}

.contact-hero__title {
  font-size: clamp(40px, 5vw, 68px);
  font-weight: 800;
  line-height: 1.1;
  margin: 0;
  color: #1a1a1a;
}

.contact-hero__title em {
  font-weight: 300;
  font-style: italic;
  display: block;
}

/* ── Body ── */
.contact-body {
  background: #fff;
}

.contact-body__inner {
  max-width: 1400px;
  margin: 0 auto;
  padding: 72px 40px 100px;
  display: grid;
  grid-template-columns: 1fr 1.6fr;
  gap: 80px;
  align-items: start;
}

/* ── Left: Info ── */
.contact-info__title {
  font-size: 22px;
  font-weight: 800;
  margin: 0 0 16px 0;
  color: #1a1a1a;
}

.contact-info__desc {
  font-size: 15px;
  line-height: 1.7;
  color: #666;
  margin: 0 0 40px 0;
  max-width: 400px;
}

.contact-details {
  list-style: none;
  margin: 0 0 48px 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.contact-detail {
  display: flex;
  align-items: flex-start;
  gap: 16px;
}

.contact-detail__icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #f5f3ef;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #666;
  flex-shrink: 0;
}

.contact-detail__label {
  font-family: 'Inter', sans-serif;
  font-size: 10px;
  font-weight: 600;
  letter-spacing: 0.12em;
  color: #aaa;
  margin: 0 0 4px 0;
}

.contact-detail__value {
  font-size: 15px;
  color: #1a1a1a;
  margin: 0;
  line-height: 1.5;
}

.contact-social__label {
  font-family: 'Inter', sans-serif;
  font-size: 10px;
  font-weight: 600;
  letter-spacing: 0.12em;
  color: #aaa;
  margin: 0 0 14px 0;
}

.contact-social__icons {
  display: flex;
  gap: 10px;
}

.social-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: 1px solid #e8e2d9;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #666;
  text-decoration: none;
  transition: all 0.2s ease;
}

.social-icon:hover {
  border-color: #1a1a1a;
  color: #1a1a1a;
  background: #f5f3ef;
}

/* ── Right: Form ── */
.contact-form {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
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

.char-count {
  font-weight: 400;
  color: #aaa;
  margin-left: 4px;
}

.form-input,
.form-select,
.form-textarea {
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

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
  border-color: #1a1a1a;
  box-shadow: 0 0 0 3px rgba(26, 26, 26, 0.06);
}

.form-input::placeholder,
.form-textarea::placeholder {
  color: #bbb;
}

.form-select-wrap {
  position: relative;
}

.form-select-wrap::after {
  content: '';
  position: absolute;
  right: 16px;
  top: 50%;
  transform: translateY(-50%);
  width: 0;
  height: 0;
  border-left: 5px solid transparent;
  border-right: 5px solid transparent;
  border-top: 5px solid #999;
  pointer-events: none;
}

.form-select {
  appearance: none;
  cursor: pointer;
  color: #888;
}

.form-select:has(option:not([value=""]):checked) {
  color: #1a1a1a;
}

.form-textarea {
  resize: vertical;
  min-height: 140px;
  line-height: 1.6;
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
  background: #4a9e6d;
  cursor: default;
}

/* ── Responsive ── */
@media (max-width: 900px) {
  .contact-body__inner {
    grid-template-columns: 1fr;
    gap: 48px;
    padding: 48px 20px 60px;
  }

  .contact-hero__inner {
    padding: 0 20px;
  }

  .form-row {
    grid-template-columns: 1fr;
  }
}

/* ── Alerts ── */
.alert {
  padding: 14px 16px;
  border-radius: 10px;
  font-family: 'Inter', sans-serif;
  font-size: 14px;
  font-weight: 500;
  line-height: 1.5;
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
</style>