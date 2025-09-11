import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

// Import views
import Login from '@/views/auth/Login.vue'
import Register from '@/views/auth/Register.vue'
import Dashboard from '@/views/Dashboard.vue'
import FarmerDashboard from '@/views/farmer/Dashboard.vue'
import TechnicianDashboard from '@/views/technician/Dashboard.vue'
import AdminDashboard from '@/views/admin/Dashboard.vue'
import SemenCatalog from '@/views/farmer/SemenCatalog.vue'
import OrderCart from '@/views/farmer/OrderCart.vue'
import ServiceConfirmation from '@/views/farmer/ServiceConfirmation.vue'
import PregnancyTracking from '@/views/farmer/PregnancyTracking.vue'

const routes = [
  {
    path: '/',
    redirect: '/login'
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { requiresGuest: true }
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { requiresGuest: true }
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  {
    path: '/farmer/dashboard',
    name: 'FarmerDashboard',
    component: FarmerDashboard,
    meta: { requiresAuth: true, role: 'farmer' }
  },
  {
    path: '/technician/dashboard',
    name: 'TechnicianDashboard',
    component: TechnicianDashboard,
    meta: { requiresAuth: true, role: 'technician' }
  },
  {
    path: '/admin/dashboard',
    name: 'AdminDashboard',
    component: AdminDashboard,
    meta: { requiresAuth: true, role: 'admin' }
  },
  {
    path: '/farmer/semen-catalog',
    name: 'SemenCatalog',
    component: SemenCatalog,
    meta: { requiresAuth: true, role: 'farmer' }
  },
  {
    path: '/farmer/cart',
    name: 'OrderCart',
    component: OrderCart,
    meta: { requiresAuth: true, role: 'farmer' }
  },
  {
    path: '/farmer/service-confirmation/:jobId',
    name: 'ServiceConfirmation',
    component: ServiceConfirmation,
    meta: { requiresAuth: true, role: 'farmer' }
  },
  {
    path: '/farmer/pregnancy-tracking',
    name: 'PregnancyTracking',
    component: PregnancyTracking,
    meta: { requiresAuth: true, role: 'farmer' }
  }
]

const router = createRouter({
  history: createWebHistory('/'),
  routes
})

// Navigation guards
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  
  // Check if route requires authentication
  if (to.meta.requiresAuth) {
    if (!authStore.isLoggedIn) {
      // Redirect to login if not authenticated
      next('/login')
      return
    }
    
    // Check role-based access
    if (to.meta.role && authStore.userRole !== to.meta.role) {
      // Redirect to appropriate dashboard based on user role
      if (authStore.isAdmin) {
        next('/admin/dashboard')
      } else if (authStore.isFarmer) {
        next('/farmer/dashboard')
      } else if (authStore.isTechnician) {
        next('/technician/dashboard')
      } else {
        next('/dashboard')
      }
      return
    }
  }
  
  // Check if route requires guest (not logged in)
  if (to.meta.requiresGuest && authStore.isLoggedIn) {
    // Redirect to appropriate dashboard based on user role
    if (authStore.isAdmin) {
      next('/admin/dashboard')
    } else if (authStore.isFarmer) {
      next('/farmer/dashboard')
    } else if (authStore.isTechnician) {
      next('/technician/dashboard')
    } else {
      next('/dashboard')
    }
    return
  }
  
  next()
})

export default router