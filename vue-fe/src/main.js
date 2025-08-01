import { registerPlugins } from '@/plugins'
import App from './App.vue'
import router from './router'
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import 'unfonts.css'

const app = createApp(App)
app.use(router)
app.use(createPinia())
registerPlugins(app)

app.mount('#app')
