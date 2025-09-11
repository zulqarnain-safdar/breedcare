<template>
  <div class="min-h-screen bg-gradient-to-br from-emerald-50 via-teal-50 to-cyan-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <!-- Background Pattern -->
    <div class="absolute inset-0 overflow-hidden">
      <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-green-400/20 to-emerald-600/20 rounded-full blur-3xl"></div>
      <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-tr from-blue-400/20 to-teal-600/20 rounded-full blur-3xl"></div>
    </div>

    <div class="relative max-w-md w-full">
      <!-- Login Card -->
      <div class="bg-white/80 backdrop-blur-md shadow-2xl rounded-3xl border border-white/20 overflow-hidden">
        <!-- Header -->
        <div class="px-8 pt-8 pb-6 text-center">
          <div class="w-16 h-16 bg-gradient-to-r from-green-600 to-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
          </div>
          <h2 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
            Welcome Back
          </h2>
          <p class="mt-2 text-sm text-gray-600">
            Sign in to your BreedCare Platform account
          </p>
        </div>

        <!-- Login Form -->
        <div class="px-8 pb-8 space-y-6">
          <div class="space-y-4">
            <div>
              <label for="mobile" class="block text-sm font-semibold text-gray-700 mb-2">
                Mobile Number
              </label>
              <FlagSelector
                :key="componentKey"
                v-model="form.mobile"
                input-id="mobile"
                input-name="mobile"
                input-type="text"
                :required="true"
                placeholder="3001234567"
                @keydown.enter.prevent="handleLogin"
              />
            </div>

            <div>
              <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                Password
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                  </svg>
                </div>
                <input
                  id="password"
                  v-model="form.password"
                  name="password"
                  :type="showPassword ? 'text' : 'password'"
                  required
                  class="block w-full pl-10 pr-12 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                  placeholder="Enter your password"
                  @keydown.enter.prevent="handleLogin"
                  autocomplete="off"
                  autocapitalize="off"
                  autocorrect="off"
                  spellcheck="false"
                />
                <button
                  type="button"
                  @click="togglePasswordVisibility"
                  class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors duration-200"
                >
                  <svg v-if="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                  <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <!-- Error Message -->
          <div v-if="error" class="bg-gradient-to-r from-red-50 to-rose-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ error }}
          </div>

          <!-- Login Button -->
          <div>
            <button
              type="button"
              :disabled="loading"
              @click="handleLogin"
              @mousedown.prevent
              class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-xl text-white bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
            >
              <span v-if="loading" class="absolute left-0 inset-y-0 flex items-center pl-3">
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
              </span>
              {{ loading ? 'Signing in...' : 'Sign In' }}
            </button>
          </div>

          <!-- Register Link -->
          <div class="text-center">
            <p class="text-sm text-gray-600">
              Don't have an account?
              <router-link to="/register" class="font-semibold text-green-600 hover:text-green-800 transition-colors duration-200">
                Register as farmer / technician
              </router-link>
            </p>
          </div>
        </div>

        
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import FlagSelector from '@/components/FlagSelector.vue'

export default {
  name: 'Login',
  components: {
    FlagSelector
  },
  setup() {
    const router = useRouter()
    const authStore = useAuthStore()
    
    const form = reactive({
      mobile: '',
      password: ''
    })
    
    const loading = ref(false)
    const error = ref('')
    const showPassword = ref(false)
    const componentKey = ref(0)
    
    const handleLogin = async (event) => {
      if (event) {
        event.preventDefault()
        event.stopPropagation()
      }
      
      // Validate form
      if (!form.mobile || !form.password) {
        error.value = 'Please fill in all required fields'
        return
      }
      
      loading.value = true
      error.value = ''
      
      try {
        const result = await authStore.login(form)
        
        if (result.success) {
          // Redirect based on user role
          const role = authStore.userRole
          if (role === 'admin') {
            router.push('/admin/dashboard')
          } else if (role === 'farmer') {
            router.push('/farmer/dashboard')
          } else if (role === 'technician') {
            router.push('/technician/dashboard')
          } else {
            router.push('/dashboard')
          }
        } else {
          error.value = result.message
        }
      } catch (err) {
        console.error('Login error:', err)
        error.value = 'An unexpected error occurred'
      } finally {
        loading.value = false
      }
    }

    const togglePasswordVisibility = () => {
      showPassword.value = !showPassword.value
    }

    // Reset form on component mount
    onMounted(() => {
      console.log('Login component mounted, resetting form')
      form.mobile = ''
      form.password = ''
      error.value = ''
      componentKey.value++
      console.log('Form reset complete, mobile:', form.mobile, 'password:', form.password)
    })
    
    return {
      form,
      loading,
      error,
      showPassword,
      componentKey,
      handleLogin,
      togglePasswordVisibility
    }
  }
}
</script>