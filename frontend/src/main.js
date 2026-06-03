import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router'
import './style.css'
import './assets/theme.css'
import './assets/css/animations.css'
import App from './App.vue'
import vReveal from './directives/v-reveal'

const app = createApp(App)
app.use(createPinia())
app.use(router)
app.directive('reveal', vReveal)
app.mount('#app')
