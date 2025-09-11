import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
  state: () => {
    let user = null
    try {
      const userData = localStorage.getItem('user')
      if (userData && userData !== 'null') {
        user = JSON.parse(userData)
      }
    } catch (error) {
      console.error('Error parsing user data from localStorage:', error)
      localStorage.removeItem('user')
    }
    
    return {
      token: localStorage.getItem('token'),
      user: user,
      isAuthenticated: !!localStorage.getItem('token'),
      otpStep: 'register', // 'register', 'verify', 'complete'
      pendingUser: null
    }
  },

  getters: {
    isLoggedIn: (state) => !!state.token,
    userRole: (state) => state.user?.role,
    isAdmin: (state) => state.user?.role === 'admin',
    isFarmer: (state) => state.user?.role === 'farmer',
    isTechnician: (state) => state.user?.role === 'technician'
  },

  actions: {
    async login(credentials) {
      try {
        const response = await window.axios.post('/auth/login', credentials)
        
        if (response.data.success) {
          this.token = response.data.token
          this.user = response.data.user
          this.isAuthenticated = true
          
          // Store in localStorage
          localStorage.setItem('token', this.token)
          localStorage.setItem('user', JSON.stringify(this.user))
          
          // Set default axios header
          window.axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
          
          return { success: true, data: response.data }
        } else {
          return { 
            success: false, 
            message: response.data.message || 'Login failed' 
          }
        }
      } catch (error) {
        console.error('Login error:', error)
        return { 
          success: false, 
          message: error.response?.data?.message || 'Login failed' 
        }
      }
    },

    async register(userData) {
      try {
        const response = await window.axios.post('/auth/register', userData)
        
        if (response.data.success) {
          // Store pending user data for OTP verification
          this.pendingUser = {
            mobile: userData.mobile,
            role: userData.role,
            name: userData.name
          }
          this.otpStep = 'verify'
          
          return { 
            success: true, 
            message: 'Registration successful. Please verify your mobile number with the OTP sent.',
            data: response.data 
          }
        }
      } catch (error) {
        console.error('Registration error:', error)
        return { 
          success: false, 
          message: error.response?.data?.message || 'Registration failed',
          errors: error.response?.data?.errors
        }
      }
    },

    async verifyOtp(otpData) {
      try {
        const response = await window.axios.post('/auth/verify-otp', {
          mobile: this.pendingUser.mobile,
          otp: otpData.otp
        })
        
        if (response.data.success) {
          this.token = response.data.token
          this.user = response.data.user
          this.isAuthenticated = true
          this.otpStep = 'complete'
          this.pendingUser = null
          
          // Store in localStorage
          localStorage.setItem('token', this.token)
          localStorage.setItem('user', JSON.stringify(this.user))
          
          // Set default axios header
          window.axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
          
          // Set profile completion flag for new users
          localStorage.setItem('showProfileCompletionAlert', 'true')
          
          return { 
            success: true, 
            data: response.data,
            redirectTo: this.getProfileRedirectPath()
          }
        }
      } catch (error) {
        console.error('OTP verification error:', error)
        return { 
          success: false, 
          message: error.response?.data?.message || 'OTP verification failed'
        }
      }
    },

    getProfileRedirectPath() {
      if (this.user?.role === 'farmer') {
        return '/farmer/dashboard?tab=profile'
      } else if (this.user?.role === 'technician') {
        return '/technician/dashboard?tab=profile'
      } else if (this.user?.role === 'admin') {
        return '/admin/dashboard'
      }
      return '/dashboard'
    },

    async resendOtp() {
      try {
        const response = await window.axios.post('/auth/resend-otp', {
          mobile: this.pendingUser.mobile
        })
        
        if (response.data.success) {
          return { 
            success: true, 
            message: 'OTP sent successfully' 
          }
        }
      } catch (error) {
        console.error('Resend OTP error:', error)
        return { 
          success: false, 
          message: error.response?.data?.message || 'Failed to resend OTP'
        }
      }
    },

    resetOtpFlow() {
      this.otpStep = 'register'
      this.pendingUser = null
    },

    async logout() {
      try {
        if (this.token) {
          await window.axios.post('/auth/logout', {}, {
            headers: { Authorization: `Bearer ${this.token}` }
          })
        }
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        // Clear state and localStorage
        this.token = null
        this.user = null
        this.isAuthenticated = false
        
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        
        // Remove axios header
        delete window.axios.defaults.headers.common['Authorization']
      }
    },

    async fetchUser() {
      try {
        if (!this.token) return false
        
        const response = await window.axios.get('/auth/me', {
          headers: { Authorization: `Bearer ${this.token}` }
        })
        
        if (response.data.success) {
          this.user = response.data.user
          localStorage.setItem('user', JSON.stringify(this.user))
          return true
        }
      } catch (error) {
        console.error('Fetch user error:', error)
        // If token is invalid, logout
        this.logout()
        return false
      }
    },

    initializeAuth() {
      if (this.token) {
        // Set axios header
        window.axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
        
        // Fetch fresh user data
        this.fetchUser()
      }
    }
  }
})