<template>
  <footer class="footer">
    <div class="footer__inner">
      <div class="footer__grid">

        <!-- Brand -->
        <div class="footer__brand">
          <div class="footer__logo">
            <img src="/logo.png" alt="Logo" class="footer__logo-img" />
          </div>
          <p class="footer__tagline">
            Piezas atemporales creadas con intención.<br />
            Calidad sobre cantidad, siempre.
          </p>
        </div>

        <!-- Navigation -->
        <div class="footer__col">
          <h4 class="footer__heading">NAVEGACIÓN</h4>
          <ul class="footer__nav">
            <li><router-link to="/" class="footer__link">Inicio</router-link></li>
            <li><router-link to="/products" class="footer__link">Productos</router-link></li>
            <li><router-link to="/cart" class="footer__link">Carrito</router-link></li>
            <li><router-link to="/contact" class="footer__link">Contacto</router-link></li>
          </ul>
        </div>

        <!-- Follow Us -->
        <div class="footer__col">
          <h4 class="footer__heading">SÍGUENOS</h4>
          <ul class="footer__nav">
            <li>
              <a href="#" class="footer__link footer__social-link">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                Instagram
              </a>
            </li>
            <li>
              <a href="#" class="footer__link footer__social-link">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M8.56 2.75c4.37 6.03 6.02 9.42 8.03 17.72m2.54-15.38c-3.72 4.35-8.94 5.66-16.88 5.85m19.5 1.9c-3.5-.93-6.63-.82-8.94 0-2.58.92-5.01 2.86-7.44 6.32"/></svg>
                Pinterest
              </a>
            </li>
            <li>
              <a href="#" class="footer__link footer__social-link">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22.54 6.42a2.78 2.78 0 00-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 00-1.95 1.96A29 29 0 001 12a29 29 0 00.46 5.58A2.78 2.78 0 003.41 19.6C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 001.95-1.95A29 29 0 0023 12a29 29 0 00-.46-5.58z"/><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02"/></svg>
                YouTube
              </a>
            </li>
          </ul>
        </div>

        <!-- Newsletter -->
        <div class="footer__col">
          <h4 class="footer__heading">BOLETÍN</h4>
          <p class="footer__newsletter-text">Recibe novedades y ofertas exclusivas.</p>
          <div class="footer__newsletter-form">
            <input
              type="email"
              placeholder="tu@correo.com"
              class="footer__newsletter-input"
              v-model="email"
            />
            <button class="footer__newsletter-btn" @click="subscribe">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </button>
          </div>
        </div>

      </div>

      <!-- Bottom bar -->
      <div class="footer__bottom">
        <span class="footer__copyright">© 2026 Todos los derechos reservados.</span>
        <div class="footer__legal">
          <a href="#" class="footer__legal-link">Política de Privacidad</a>
          <a href="#" class="footer__legal-link">Términos de Servicio</a>
          <a href="#" class="footer__legal-link">Devoluciones</a>
        </div>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  name: 'Footer',
  data() {
    return {
      email: ''
    }
  },
  methods: {
    subscribe() {
      if (this.email) {
        alert(`Suscrito: ${this.email}`)
        this.email = ''
      }
    }
  }
}
</script>

<style scoped>
.footer {
  background: #111111;
  color: #ffffff;
  padding-top: 72px;
  padding-bottom: 0;
  font-family: 'Cormorant Garamond', 'Georgia', serif;
}

.footer__inner {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 40px;
}

.footer__grid {
  display: grid;
  grid-template-columns: 1.5fr 1fr 1fr 1.5fr;
  gap: 60px;
  padding-bottom: 64px;
}

.footer__logo {
  margin-bottom: 20px;
}

.footer__logo-img {
  width: auto;
  height: 120px;
  object-fit: contain;
  filter: brightness(0) invert(1); /* If the logo is dark, this makes it white for the dark footer */
}

.footer__tagline {
  font-size: 14px;
  line-height: 1.7;
  color: rgba(255, 255, 255, 0.5);
  margin: 0;
  font-style: italic;
}

.footer__heading {
  font-family: 'Inter', 'Helvetica Neue', sans-serif;
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.15em;
  color: rgba(255, 255, 255, 0.9);
  margin: 0 0 24px 0;
}

.footer__nav {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.footer__link {
  text-decoration: none;
  color: rgba(255, 255, 255, 0.5);
  font-size: 15px;
  font-weight: 400;
  transition: color 0.2s ease;
  display: inline-block;
}

.footer__link:hover {
  color: rgba(255, 255, 255, 0.95);
}

.footer__social-link {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
}

.footer__newsletter-text {
  font-size: 14px;
  color: rgba(255, 255, 255, 0.5);
  margin: 0 0 20px 0;
  line-height: 1.5;
}

.footer__newsletter-form {
  display: flex;
  align-items: center;
  gap: 8px;
}

.footer__newsletter-input {
  flex: 1;
  background: transparent;
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 100px;
  padding: 12px 20px;
  color: white;
  font-family: 'Cormorant Garamond', serif;
  font-size: 14px;
  outline: none;
  transition: border-color 0.2s ease;
}

.footer__newsletter-input::placeholder {
  color: rgba(255, 255, 255, 0.3);
}

.footer__newsletter-input:focus {
  border-color: rgba(255, 255, 255, 0.5);
}

.footer__newsletter-btn {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background: white;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #111;
  flex-shrink: 0;
  transition: background 0.2s ease, transform 0.2s ease;
}

.footer__newsletter-btn:hover {
  background: #e8e2d9;
  transform: scale(1.05);
}

.footer__bottom {
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  padding: 24px 0;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.footer__copyright {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.3);
  font-family: 'Inter', sans-serif;
}

.footer__legal {
  display: flex;
  gap: 28px;
}

.footer__legal-link {
  text-decoration: none;
  font-family: 'Inter', sans-serif;
  font-size: 13px;
  color: rgba(255, 255, 255, 0.3);
  transition: color 0.2s ease;
}

.footer__legal-link:hover {
  color: rgba(255, 255, 255, 0.7);
}

@media (max-width: 900px) {
  .footer__grid {
    grid-template-columns: 1fr 1fr;
    gap: 40px;
  }
}

@media (max-width: 560px) {
  .footer__grid {
    grid-template-columns: 1fr;
  }
  .footer__bottom {
    flex-direction: column;
    gap: 16px;
    text-align: center;
  }
}
</style>