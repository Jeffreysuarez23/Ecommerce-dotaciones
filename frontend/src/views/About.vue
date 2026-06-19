<template>
    <div class="about">

        <!-- ─── HERO ─── -->
        <section class="about-hero">
            <div class="about-hero__bg">
                <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1800&q=80" alt="D'gala Historia"
                    class="about-hero__img" />
                <div class="about-hero__overlay"></div>
            </div>
            <div class="about-hero__content">
                <p class="about-hero__eyebrow">/ NUESTRA HISTORIA</p>
                <h1 class="about-hero__title">
                    Creados con<br />
                    <span class="about-hero__title--light">propósito</span>
                </h1>
                <p class="about-hero__subtitle">
                    Más de una década vistiendo empresas, instituciones<br />
                    y equipos con uniformes que representan identidad.
                </p>
            </div>
            <div class="about-hero__scroll">
                <span>DESLIZAR</span>
                <div class="about-hero__scroll-line"></div>
            </div>
        </section>

        <!-- ─── MISIÓN / VISIÓN ─── -->
        <section class="about-mission" v-reveal>
            <div class="section__inner about-mission__layout">
                <div class="about-mission__image-wrap">
                    <img src="https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?w=900&q=80"
                        alt="Taller D'gala" class="about-mission__img" />
                    <div class="about-mission__img-accent"></div>
                </div>
                <div class="about-mission__content">
                    <p class="section__eyebrow">/ NUESTRA FILOSOFÍA</p>
                    <h2 class="about-mission__title">
                        Creado con<br />
                        <em>intención</em>
                    </h2>
                    <p class="about-mission__text">
                        D'gala nace con el propósito de transformar la compra mayorista de vestuario laboral.
                        Pasamos de un modelo tradicional —lento, desorganizado y poco transparente— a una
                        plataforma digital integral que optimiza tiempos, minimiza errores y eleva la experiencia
                        del cliente corporativo.
                    </p>
                    <p class="about-mission__text">
                        Trabajamos con artesanos y fabricantes certificados que comparten nuestro compromiso
                        con la calidad y la sostenibilidad. Cada prenda es seleccionada con rigor para garantizar
                        que cumpla con los estándares de resistencia, confort y normativa laboral vigente.
                    </p>
                    <div class="about-stats">
                        <div class="about-stat" v-for="s in stats" :key="s.value">
                            <span class="about-stat__value">{{ s.value }}</span>
                            <span class="about-stat__label">{{ s.label }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ─── VALORES ─── -->
        <section class="about-values" v-reveal>
            <div class="section__inner">
                <div class="about-values__header">
                    <p class="section__eyebrow">/ LO QUE NOS MUEVE</p>
                    <h2 class="section__title">
                        <strong>Nuestros</strong>
                        <span class="section__title--light">Valores</span>
                    </h2>
                </div>
                <div class="about-values__grid">
                    <div class="value-card" v-for="v in values" :key="v.title">
                        <div class="value-card__icon" v-html="v.icon"></div>
                        <h3 class="value-card__title">{{ v.title }}</h3>
                        <p class="value-card__text">{{ v.text }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ─── HISTORIA / TIMELINE ─── -->
        <section class="about-timeline" v-reveal>
            <div class="section__inner">
                <p class="section__eyebrow">/ CÓMO LLEGAMOS AQUÍ</p>
                <h2 class="section__title" style="margin-bottom: 56px;">
                    <strong>Nuestra</strong>
                    <span class="section__title--light">Trayectoria</span>
                </h2>
                <div class="timeline">
                    <div class="timeline__item" v-for="(item, index) in timeline" :key="item.year"
                        :class="{ 'timeline__item--right': index % 2 !== 0 }">
                        <div class="timeline__dot"></div>
                        <div class="timeline__card">
                            <span class="timeline__year">{{ item.year }}</span>
                            <h3 class="timeline__title">{{ item.title }}</h3>
                            <p class="timeline__text">{{ item.text }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ─── PROPUESTA DE VALOR ─── -->
        <section class="about-proposal" v-reveal>
            <div class="section__inner">
                <div class="about-proposal__layout">
                    <div class="about-proposal__content">
                        <p class="section__eyebrow">/ POR QUÉ D'GALA</p>
                        <h2 class="about-proposal__title">
                            La solución mayorista<br />
                            <em>que tu empresa necesita</em>
                        </h2>
                        <p class="about-proposal__text">
                            Somos la única plataforma en Colombia que unifica catálogo especializado,
                            personalización digital, trazabilidad del pedido y asesoría normativa
                            en un solo ecosistema digital pensado para compras corporativas.
                        </p>
                        <ul class="about-proposal__list">
                            <li v-for="item in proposal" :key="item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                                {{ item }}
                            </li>
                        </ul>
                        <router-link to="/products" class="btn btn--dark">
                            Ver Catálogo
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5">
                                <line x1="5" y1="12" x2="19" y2="12" />
                                <polyline points="12 5 19 12 12 19" />
                            </svg>
                        </router-link>
                    </div>
                    <div class="about-proposal__images">
                        <img src="https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=900&q=80"
                            alt="D'gala plataforma" class="about-proposal__img" />
                    </div>
                </div>
            </div>
        </section>

        <!-- ─── CTA FINAL ─── -->
        <section class="about-cta" v-reveal>
            <div class="section__inner about-cta__inner">
                <h2 class="about-cta__title">
                    ¿Listo para vestir<br />
                    <em>a tu equipo?</em>
                </h2>
                <p class="about-cta__text">
                    Únete a las cientos de empresas que ya confían en D'gala para sus dotaciones corporativas.
                </p>
                <div class="about-cta__btns">
                    <router-link to="/products" class="btn btn--primary-dark">
                        Ver Catálogo
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5">
                            <line x1="5" y1="12" x2="19" y2="12" />
                            <polyline points="12 5 19 12 12 19" />
                        </svg>
                    </router-link>
                    <router-link to="/contact" class="btn btn--outline">Contáctanos</router-link>
                </div>
            </div>
        </section>

    </div>
</template>

<script>
export default {
    name: 'AboutView',
    data() {
        return {
            stats: [
                { value: '12+', label: 'AÑOS DE EXPERIENCIA' },
                { value: '500+', label: 'EMPRESAS ATENDIDAS' },
                { value: '100%', label: 'FIBRAS CERTIFICADAS' },
                { value: '40+', label: 'ARTESANOS ALIADOS' }
            ],
            values: [
                {
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>',
                    title: 'Calidad sobre cantidad',
                    text: 'Seleccionamos cada prenda con rigor técnico. Priorizamos materiales que duren, abriguen y representen bien a cada empresa.'
                },
                {
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>',
                    title: 'Transparencia total',
                    text: 'Desde la cotización hasta la entrega, el cliente sabe exactamente en qué etapa está su pedido y por qué.'
                },
                {
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
                    title: 'Alianzas reales',
                    text: 'No somos solo proveedores. Somos socios estratégicos que acompañan a cada empresa en su imagen corporativa.'
                },
                {
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"/></svg>',
                    title: 'Agilidad operativa',
                    text: 'Procesos digitales que eliminan los cuellos de botella tradicionales en cotizaciones, aprobaciones y despachos.'
                },
                {
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>',
                    title: 'Responsabilidad',
                    text: 'Trabajamos con fábricas certificadas que respetan normativas laborales y prácticas sostenibles.'
                },
                {
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="16" cy="4" r="1"/><path d="m18 19 1-7-6 1"/><path d="m5 8 3-3 5.5 3-2.36 3.5"/><path d="M4.24 14.5a5 5 0 0 0 6.88 6"/><path d="M13.76 17.5a5 5 0 0 0-6.88-6"/></svg>',
                    title: 'Accesibilidad',
                    text: 'Plataforma diseñada para ser usada por cualquier persona, independientemente de su nivel digital.'
                }
            ],
            timeline: [
                {
                    year: '2012',
                    title: 'Los primeros pasos',
                    text: 'D\'gala nace como un pequeño taller familiar en Medellín con la misión de proveer uniformes de calidad a empresas locales.'
                },
                {
                    year: '2015',
                    title: 'Expansión regional',
                    text: 'Ampliamos operaciones a Bogotá y Cali, sumando más de 50 clientes corporativos entre industrias, hospitales y colegios.'
                },
                {
                    year: '2018',
                    title: 'Primer catálogo digital',
                    text: 'Lanzamos nuestro primer sitio web con catálogo en línea, reduciendo el tiempo de cotización en un 60%.'
                },
                {
                    year: '2021',
                    title: 'Plataforma mayorista',
                    text: 'Desarrollamos la primera versión de nuestra plataforma mayorista con carrito de compras, gestión de pedidos y trazabilidad.'
                },
                {
                    year: '2024',
                    title: 'Transformación digital',
                    text: 'Integramos personalización digital, asesoría normativa y sistema de alertas automatizadas para pedidos corporativos.'
                },
                {
                    year: '2026',
                    title: 'D\'gala hoy',
                    text: 'Más de 500 empresas confían en nosotros para sus dotaciones. Seguimos creciendo con tecnología, calidad y propósito.'
                }
            ],
            proposal: [
                'Filtros especializados para compras al por mayor',
                'Personalización digital de bordados y colores corporativos',
                'Trazabilidad completa del pedido en tiempo real',
                'Asesoría normativa en dotación laboral incluida',
                'Toma de tallas a domicilio disponible',
                'Facturación electrónica y documentación técnica'
            ]
        }
    }
}
</script>

<style scoped>
/* ── Shared base (igual que Home.vue) ── */
.about {
    font-family: 'Cormorant Garamond', 'Georgia', serif;
    color: #1a1a1a;
}

.section__inner {
    max-width: 1400px;
    margin: 0 auto;
    padding: 80px 40px;
}

.section__eyebrow {
    font-family: 'Inter', 'Helvetica Neue', sans-serif;
    font-size: 11px;
    font-weight: 500;
    letter-spacing: 0.15em;
    color: #999;
    margin: 0 0 12px 0;
    text-transform: uppercase;
}

.section__title {
    font-size: clamp(36px, 4vw, 52px);
    font-weight: 400;
    line-height: 1.1;
    margin: 0;
    color: #1a1a1a;
}

.section__title strong {
    font-weight: 800;
    display: block;
}

.section__title--light {
    font-weight: 300;
    display: block;
}

/* ── Botones ── */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 28px;
    border-radius: 100px;
    font-family: 'Cormorant Garamond', serif;
    font-size: 16px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.25s ease;
    cursor: pointer;
    border: none;
}

.btn--dark {
    background: #1a1a1a;
    color: white;
}

.btn--dark:hover {
    background: #333;
    transform: translateY(-1px);
}

.btn--primary-dark {
    background: white;
    color: #1a1a1a;
}

.btn--primary-dark:hover {
    background: #f0ebe3;
    transform: translateY(-1px);
}

.btn--outline {
    background: transparent;
    color: white;
    border: 1.5px solid rgba(255, 255, 255, 0.5);
    backdrop-filter: blur(8px);
}

.btn--outline:hover {
    background: rgba(255, 255, 255, 0.15);
}

/* ── HERO ── */
.about-hero {
    position: relative;
    width: 100%;
    height: 100vh;
    min-height: 600px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    margin-top: -70px;
    /* navbar flotante */
}

.about-hero__bg {
    position: absolute;
    inset: 0;
}

.about-hero__img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
}

.about-hero__overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom,
            rgba(0, 0, 0, 0.2) 0%,
            rgba(0, 0, 0, 0.45) 50%,
            rgba(0, 0, 0, 0.2) 100%);
}

.about-hero__content {
    position: relative;
    z-index: 2;
    text-align: center;
    color: white;
    max-width: 680px;
    padding: 0 24px;
}

.about-hero__eyebrow {
    font-family: 'Inter', sans-serif;
    font-size: 11px;
    font-weight: 500;
    letter-spacing: 0.2em;
    color: rgba(255, 255, 255, 0.75);
    margin: 0 0 16px 0;
}

.about-hero__title {
    font-size: clamp(52px, 8vw, 100px);
    font-weight: 800;
    line-height: 1;
    margin: 0 0 20px 0;
    letter-spacing: -0.02em;
}

.about-hero__title--light {
    font-weight: 300;
    font-style: italic;
    display: block;
}

.about-hero__subtitle {
    font-size: 17px;
    font-weight: 300;
    line-height: 1.65;
    color: rgba(255, 255, 255, 0.82);
    margin: 0;
}

.about-hero__scroll {
    position: absolute;
    bottom: 36px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    z-index: 2;
    color: rgba(255, 255, 255, 0.6);
    font-family: 'Inter', sans-serif;
    font-size: 10px;
    font-weight: 500;
    letter-spacing: 0.15em;
}

.about-hero__scroll-line {
    width: 1px;
    height: 40px;
    background: rgba(255, 255, 255, 0.4);
    animation: scrollPulse 2s ease-in-out infinite;
}

@keyframes scrollPulse {

    0%,
    100% {
        opacity: 0.4;
        transform: scaleY(1);
    }

    50% {
        opacity: 1;
        transform: scaleY(1.2);
    }
}

/* ── MISIÓN ── */
.about-mission {
    background: #faf8f4;
}

.about-mission__layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 80px;
    align-items: center;
}

.about-mission__image-wrap {
    position: relative;
}

.about-mission__img {
    width: 100%;
    height: auto;
    border-radius: 12px;
    display: block;
}

.about-mission__img-accent {
    position: absolute;
    bottom: -20px;
    right: -20px;
    width: 45%;
    aspect-ratio: 1;
    background: #e8e2d6;
    border-radius: 8px;
    z-index: -1;
}

.about-mission__title {
    font-size: clamp(36px, 4vw, 54px);
    font-weight: 700;
    line-height: 1.15;
    color: #1a1a1a;
    margin: 0 0 28px 0;
}

.about-mission__title em {
    font-weight: 300;
    font-style: italic;
    color: #6b5f4e;
    display: block;
}

.about-mission__text {
    font-size: 16px;
    line-height: 1.75;
    color: #555;
    margin: 0 0 16px 0;
}

.about-stats {
    display: flex;
    gap: 32px;
    margin-top: 40px;
    padding-top: 40px;
    border-top: 1px solid #e0d8cc;
    flex-wrap: wrap;
}

.about-stat__value {
    display: block;
    font-size: 28px;
    font-weight: 800;
    color: #1a1a1a;
    line-height: 1;
    margin-bottom: 5px;
}

.about-stat__label {
    font-family: 'Inter', sans-serif;
    font-size: 9px;
    font-weight: 500;
    letter-spacing: 0.12em;
    color: #999;
    text-transform: uppercase;
}

/* ── VALORES ── */
.about-values {
    background: #fff;
}

.about-values__header {
    margin-bottom: 48px;
}

.about-values__grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 28px;
}

.value-card {
    padding: 32px 28px;
    border: 1px solid #ede8e0;
    border-radius: 16px;
    background: #faf8f4;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.value-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.07);
}

.value-card__icon {
    margin-bottom: 16px;
    display: block;
    color: #1a1a1a;
}
.value-card__icon svg {
    display: block;
}

.value-card__title {
    font-size: 20px;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0 0 12px 0;
}

.value-card__text {
    font-size: 15px;
    line-height: 1.7;
    color: #666;
    margin: 0;
}

/* ── TIMELINE ── */
.about-timeline {
    background: #f7f4ef;
}

.timeline {
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 0;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 50%;
    top: 0;
    bottom: 0;
    width: 1px;
    background: #e0d8cc;
    transform: translateX(-50%);
}

.timeline__item {
    display: flex;
    justify-content: flex-end;
    padding-right: calc(50% + 40px);
    padding-bottom: 48px;
    position: relative;
}

.timeline__item--right {
    justify-content: flex-start;
    padding-right: 0;
    padding-left: calc(50% + 40px);
}

.timeline__dot {
    position: absolute;
    left: 50%;
    top: 20px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: #1a1a1a;
    border: 3px solid #f7f4ef;
    transform: translateX(-50%);
    z-index: 1;
}

.timeline__card {
    background: white;
    border: 1px solid #ede8e0;
    border-radius: 14px;
    padding: 24px 28px;
    max-width: 380px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
}

.timeline__year {
    font-family: 'Inter', sans-serif;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.1em;
    color: #b5a898;
    text-transform: uppercase;
    display: block;
    margin-bottom: 8px;
}

.timeline__title {
    font-size: 20px;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0 0 10px 0;
}

.timeline__text {
    font-size: 14px;
    line-height: 1.7;
    color: #666;
    margin: 0;
}

/* ── PROPUESTA ── */
.about-proposal {
    background: #fff;
}

.about-proposal__layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 80px;
    align-items: center;
}

.about-proposal__title {
    font-size: clamp(32px, 3.5vw, 48px);
    font-weight: 700;
    line-height: 1.15;
    color: #1a1a1a;
    margin: 0 0 20px 0;
}

.about-proposal__title em {
    font-weight: 300;
    font-style: italic;
    color: #6b5f4e;
}

.about-proposal__text {
    font-size: 16px;
    line-height: 1.75;
    color: #555;
    margin: 0 0 24px 0;
}

.about-proposal__list {
    list-style: none;
    margin: 0 0 36px 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.about-proposal__list li {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    font-size: 15px;
    color: #444;
    line-height: 1.5;
}

.about-proposal__list li svg {
    color: #6b5f4e;
    flex-shrink: 0;
    margin-top: 2px;
}

.about-proposal__img {
    width: 100%;
    height: auto;
    border-radius: 16px;
    display: block;
}

/* ── CTA ── */
.about-cta {
    background: #1a1a1a;
    color: white;
}

.about-cta__inner {
    text-align: center;
    padding-top: 100px;
    padding-bottom: 100px;
}

.about-cta__title {
    font-size: clamp(40px, 5vw, 72px);
    font-weight: 800;
    line-height: 1.1;
    color: white;
    margin: 0 0 20px 0;
}

.about-cta__title em {
    font-weight: 300;
    font-style: italic;
    color: rgba(255, 255, 255, 0.65);
}

.about-cta__text {
    font-size: 17px;
    line-height: 1.6;
    color: rgba(255, 255, 255, 0.65);
    margin: 0 0 40px 0;
    max-width: 520px;
    margin-left: auto;
    margin-right: auto;
}

.about-cta__btns {
    display: flex;
    align-items: center;
    gap: 16px;
    justify-content: center;
}

/* ── Responsive ── */
@media (max-width: 1024px) {

    .about-mission__layout,
    .about-proposal__layout {
        grid-template-columns: 1fr;
        gap: 48px;
    }

    .about-values__grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .timeline::before {
        left: 20px;
    }

    .timeline__item,
    .timeline__item--right {
        justify-content: flex-start;
        padding-left: 56px;
        padding-right: 0;
    }

    .timeline__dot {
        left: 20px;
    }

    .timeline__card {
        max-width: 100%;
    }
}

@media (max-width: 640px) {
    .section__inner {
        padding: 60px 20px;
    }

    .about-values__grid {
        grid-template-columns: 1fr;
    }

    .about-stats {
        gap: 20px;
    }

    .about-cta__btns {
        flex-direction: column;
    }
}
</style>