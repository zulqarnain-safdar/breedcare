<template>
  <div class="min-h-screen bg-gradient-to-br from-emerald-50 via-teal-50 to-cyan-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <!-- Background Pattern -->
    <div class="absolute inset-0 overflow-hidden">
      <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-green-400/20 to-emerald-600/20 rounded-full blur-3xl"></div>
      <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-tr from-blue-400/20 to-teal-600/20 rounded-full blur-3xl"></div>
    </div>

    <div class="relative max-w-md w-full">
      <!-- Register Card -->
      <div class="bg-white/80 backdrop-blur-md shadow-2xl rounded-3xl border border-white/20 overflow-hidden">
        <!-- Header -->
        <div class="px-8 pt-8 pb-6 text-center">
          <div class="w-16 h-16 bg-gradient-to-r from-green-600 to-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
            </svg>
          </div>
          <h2 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
            Join BreedCare
          </h2>
          <p class="mt-2 text-sm text-gray-600">
            Create your account and start managing your farm
          </p>
        </div>

        <!-- Registration Form -->
        <form v-if="authStore.otpStep === 'register'" class="px-8 pb-8 space-y-6" @submit.prevent="handleRegister">
          <div class="space-y-4">
            <!-- Account Type -->
            <div>
              <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">
                Account Type
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                </div>
                <select
                  id="role"
                  v-model="form.role"
                  name="role"
                  required
                  class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 appearance-none bg-white"
                >
                  <option value="">Select account type</option>
                  <option value="farmer">Farmer</option>
                  <option value="technician">Technician</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                  </svg>
                </div>
              </div>
            </div>
            
            <!-- Full Name -->
            <div>
              <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                Full Name
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                </div>
                <input
                  id="name"
                  v-model="form.name"
                  name="name"
                  type="text"
                  required
                  class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                  placeholder="Enter your full name"
                  autocomplete="off"
                  autocapitalize="off"
                  autocorrect="off"
                  spellcheck="false"
                />
              </div>
            </div>
            
            <!-- Mobile Number -->
            <div>
              <label for="mobile" class="block text-sm font-semibold text-gray-700 mb-2">
                Mobile Number
              </label>
              <FlagSelector
                :key="componentKey"
                v-model="form.mobile"
                input-id="mobile"
                input-name="mobile"
                input-type="tel"
                :required="true"
                placeholder="3001234567"
              />
              <p class="mt-1 text-xs text-gray-500">
                Enter your mobile number (Pakistan +92)
              </p>
            </div>
            
            <!-- Password -->
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
                  placeholder="Enter password"
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
            
            <!-- Confirm Password -->
            <div>
              <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                Confirm Password
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
                <input
                  id="password_confirmation"
                  v-model="form.password_confirmation"
                  name="password_confirmation"
                  :type="showConfirmPassword ? 'text' : 'password'"
                  required
                  class="block w-full pl-10 pr-12 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                  placeholder="Confirm password"
                  autocomplete="off"
                  autocapitalize="off"
                  autocorrect="off"
                  spellcheck="false"
                />
                <button
                  type="button"
                  @click="toggleConfirmPasswordVisibility"
                  class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors duration-200"
                >
                  <svg v-if="!showConfirmPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

          <!-- Error Messages -->
          <div v-if="error" class="bg-gradient-to-r from-red-50 to-rose-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ error }}
          </div>

          <div v-if="errors && Object.keys(errors).length > 0" class="bg-gradient-to-r from-red-50 to-rose-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
            <div class="flex items-start">
              <svg class="w-5 h-5 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <ul class="list-disc list-inside text-sm">
                <li v-for="(errorList, field) in errors" :key="field">
                  <span class="font-semibold">{{ field }}:</span> {{ errorList.join(', ') }}
                </li>
              </ul>
            </div>
          </div>

          <!-- Register Button -->
          <div>
            <button
              type="submit"
              :disabled="loading"
              class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-xl text-white bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
            >
              <span v-if="loading" class="absolute left-0 inset-y-0 flex items-center pl-3">
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
              </span>
              {{ loading ? 'Creating account...' : 'Create Account' }}
            </button>
          </div>

          <!-- Login Link -->
          <div class="text-center">
            <p class="text-sm text-gray-600">
              Already have an account?
              <router-link to="/login" class="font-semibold text-green-600 hover:text-green-800 transition-colors duration-200">
                Sign in here
              </router-link>
            </p>
          </div>
        </form>

        <!-- OTP Verification Form -->
        <div v-if="authStore.otpStep === 'verify'" class="px-8 pb-8 space-y-6">
          <!-- Success Message -->
          <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ successMessage }}
          </div>

          <!-- OTP Instructions -->
          <div class="text-center">
            <div class="w-16 h-16 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Verify Your Mobile Number</h3>
            <p class="text-sm text-gray-600 mb-4">
              We've sent a 6-digit verification code to
              <span class="font-semibold text-gray-900">{{ authStore.pendingUser?.mobile }}</span>
            </p>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
              <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="text-sm text-blue-800">
                  <p class="font-medium">For testing purposes:</p>
                  <p class="mt-1">Check your browser console or server logs for the OTP code.</p>
                </div>
              </div>
            </div>
          </div>

          <!-- OTP Form -->
          <form @submit.prevent="handleVerifyOtp" class="space-y-6">
            <div>
              <label for="otp" class="block text-sm font-semibold text-gray-700 mb-2">
                Enter Verification Code
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                  </svg>
                </div>
                <input
                  id="otp"
                  v-model="otpForm.otp"
                  name="otp"
                  type="text"
                  maxlength="6"
                  required
                  class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-center text-2xl tracking-widest"
                  placeholder="000000"
                  @input="formatOtp"
                />
              </div>
              <p class="mt-2 text-xs text-gray-500 text-center">
                Enter the 6-digit code sent to your mobile number
              </p>
            </div>

            <!-- Error Messages -->
            <div v-if="error" class="bg-gradient-to-r from-red-50 to-rose-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              {{ error }}
            </div>

            <!-- Verify Button -->
            <div>
              <button
                type="submit"
                :disabled="loading || otpForm.otp.length !== 6"
                class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-xl text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
              >
                <span v-if="loading" class="absolute left-0 inset-y-0 flex items-center pl-3">
                  <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                </span>
                {{ loading ? 'Verifying...' : 'Verify & Continue' }}
              </button>
            </div>

            <!-- Resend OTP -->
            <div class="text-center">
              <p class="text-sm text-gray-600">
                Didn't receive the code?
                <button 
                  type="button"
                  @click="handleResendOtp"
                  :disabled="resendCooldown > 0"
                  class="font-semibold text-blue-600 hover:text-blue-800 transition-colors duration-200 disabled:opacity-50"
                >
                  {{ resendCooldown > 0 ? `Resend in ${resendCooldown}s` : 'Resend OTP' }}
                </button>
              </p>
            </div>

            <!-- Back to Registration -->
            <div class="text-center">
              <button 
                type="button"
                @click="goBackToRegistration"
                class="text-sm text-gray-600 hover:text-gray-800 transition-colors duration-200"
              >
                ← Back to registration
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import FlagSelector from '@/components/FlagSelector.vue'

export default {
  name: 'Register',
  components: {
    FlagSelector
  },
  setup() {
    const router = useRouter()
    const authStore = useAuthStore()
    
    const form = reactive({
      role: '',
      name: '',
      mobile: '',
      password: '',
      password_confirmation: ''
    })
    
    const otpForm = reactive({
      otp: ''
    })
    
    const loading = ref(false)
    const error = ref('')
    const errors = ref({})
    const successMessage = ref('')
    const resendCooldown = ref(0)
    const showPassword = ref(false)
    const showConfirmPassword = ref(false)
    const componentKey = ref(0)
    let resendTimer = null
    
    // Computed properties
    const isOtpStep = computed(() => authStore.otpStep === 'verify')
    
    const handleRegister = async () => {
      loading.value = true
      error.value = ''
      errors.value = ''
      successMessage.value = ''
      
      try {
        const result = await authStore.register(form)
        
        if (result.success) {
          successMessage.value = result.message
          // Start resend cooldown
          startResendCooldown()
        } else {
          error.value = result.message
          if (result.errors) {
            errors.value = result.errors
          }
        }
      } catch (err) {
        error.value = 'An unexpected error occurred'
      } finally {
        loading.value = false
      }
    }
    
    const handleVerifyOtp = async () => {
      loading.value = true
      error.value = ''
      
      try {
        const result = await authStore.verifyOtp(otpForm)
        
        if (result.success) {
          // Use the redirect path from auth store
          if (result.redirectTo) {
            router.push(result.redirectTo)
          } else {
            // Fallback redirect based on user role
            const role = authStore.userRole
            if (role === 'farmer') {
              router.push('/farmer/dashboard')
            } else if (role === 'technician') {
              router.push('/technician/dashboard')
            } else {
              router.push('/dashboard')
            }
          }
        } else {
          error.value = result.message
        }
      } catch (err) {
        error.value = 'An unexpected error occurred'
      } finally {
        loading.value = false
      }
    }
    
    const handleResendOtp = async () => {
      if (resendCooldown.value > 0) return
      
      loading.value = true
      error.value = ''
      
      try {
        const result = await authStore.resendOtp()
        
        if (result.success) {
          successMessage.value = result.message
          startResendCooldown()
        } else {
          error.value = result.message
        }
      } catch (err) {
        error.value = 'Failed to resend OTP'
      } finally {
        loading.value = false
      }
    }
    
    const goBackToRegistration = () => {
      authStore.resetOtpFlow()
      error.value = ''
      successMessage.value = ''
      otpForm.otp = ''
    }
    
    const formatOtp = (event) => {
      // Only allow numbers and limit to 6 digits
      let value = event.target.value.replace(/\D/g, '').slice(0, 6)
      otpForm.otp = value
      
      // Auto-submit when 6 digits are entered
      if (value.length === 6) {
        handleVerifyOtp()
      }
    }
    
    
    const startResendCooldown = () => {
      resendCooldown.value = 60 // 60 seconds cooldown
      resendTimer = setInterval(() => {
        resendCooldown.value--
        if (resendCooldown.value <= 0) {
          clearInterval(resendTimer)
          resendTimer = null
        }
      }, 1000)
    }
    
    // Cleanup timer on component unmount
    onUnmounted(() => {
      if (resendTimer) {
        clearInterval(resendTimer)
      }
    })
    
    // Reset OTP flow when component mounts
    onMounted(() => {
      authStore.resetOtpFlow()
      form.mobile = ''
      form.password = ''
      form.password_confirmation = ''
      form.name = ''
      form.role = ''
      error.value = ''
      errors.value = {}
      successMessage.value = ''
      componentKey.value++
    })

    const togglePasswordVisibility = () => {
      showPassword.value = !showPassword.value
    }

    const toggleConfirmPasswordVisibility = () => {
      showConfirmPassword.value = !showConfirmPassword.value
    }
    
    return {
      authStore,
      form,
      otpForm,
      loading,
      error,
      errors,
      successMessage,
      resendCooldown,
      showPassword,
      showConfirmPassword,
      componentKey,
      isOtpStep,
      handleRegister,
      handleVerifyOtp,
      handleResendOtp,
      goBackToRegistration,
      formatOtp,
      togglePasswordVisibility,
      toggleConfirmPasswordVisibility
    }
  }
}
</script>