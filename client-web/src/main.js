import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import vue3GoogleLogin from "vue3-google-login";
import router from './router'
// Bootstrap CSS & JS
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'

// Bootstrap Icons
import 'bootstrap-icons/font/bootstrap-icons.css'

const app = createApp(App)

app.use(createPinia())
app.use(vue3GoogleLogin, {
  clientId: import.meta.env.VITE_CLIENT_ID,
});
app.use(router)

app.mount('#app')
