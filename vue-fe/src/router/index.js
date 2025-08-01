import { createRouter, createWebHistory } from 'vue-router'

import HomePage from '@/pages/HomePage.vue'
import BookingPage from '@/pages/BookingPage.vue'
import SessionPage from '@/pages/SessionPage.vue'

const routes = [
  { path: '/', name: 'Home', component: HomePage },
  { path: '/booking', name: 'Booking', component: BookingPage },
  { path: '/session', name: 'Session', component: SessionPage },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
