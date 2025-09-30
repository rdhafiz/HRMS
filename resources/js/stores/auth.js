import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref(null)
  const token = ref(localStorage.getItem('auth_token'))
  const isLoading = ref(false)

  // Getters
  const isAuthenticated = computed(() => !!token.value && !!user.value)
  const userRole = computed(() => user.value?.admin_type_label || null)

  // Actions
  async function login(credentials) {
    isLoading.value = true
    try {
      const response = await axios.post('/auth/login', credentials)
      const { user: userData, token: authToken } = response.data
      
      // Store token and user data
      token.value = authToken
      user.value = userData
      localStorage.setItem('auth_token', authToken)
      
      // Set default authorization header
      axios.defaults.headers.common['Authorization'] = `Bearer ${authToken}`
      
      return { success: true, user: userData }
    } catch (error) {
      const message = error.response?.data?.message || 'Login failed'
      return { success: false, message }
    } finally {
      isLoading.value = false
    }
  }

  async function logout() {
    try {
      if (token.value) {
        await axios.post('/auth/logout', {}, {
          headers: { Authorization: `Bearer ${token.value}` }
        })
      }
    } catch (error) {
      console.error('Logout error:', error)
    } finally {
      // Clear state regardless of API call success
      token.value = null
      user.value = null
      localStorage.removeItem('auth_token')
      delete axios.defaults.headers.common['Authorization']
    }
  }

  async function fetchUser() {
    if (!token.value) return null
    
    try {
      axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
      const response = await axios.get('/auth/user')
      user.value = response.data
      return response.data
    } catch (error) {
      // Token is invalid, clear auth state
      await logout()
      throw error
    }
  }

  async function refreshToken() {
    if (!token.value) return null
    
    try {
      const response = await axios.post('/auth/refresh', {}, {
        headers: { Authorization: `Bearer ${token.value}` }
      })
      const { user: userData, token: authToken } = response.data
      
      token.value = authToken
      user.value = userData
      localStorage.setItem('auth_token', authToken)
      axios.defaults.headers.common['Authorization'] = `Bearer ${authToken}`
      
      return { success: true, user: userData }
    } catch (error) {
      await logout()
      throw error
    }
  }

  async function forgotPassword(email) {
    try {
      const response = await axios.post('/auth/forgot', { email })
      return { success: true, message: response.data.message }
    } catch (error) {
      const message = error.response?.data?.message || 'Request failed'
      return { success: false, message }
    }
  }

  async function verifyResetCode(email, code) {
    try {
      const response = await axios.post('/auth/verify-reset-code', { email, code })
      return { success: true, message: response.data.message }
    } catch (error) {
      const message = error.response?.data?.message || 'Verification failed'
      return { success: false, message }
    }
  }

  async function resetPassword(email, code, password, passwordConfirmation) {
    try {
      const response = await axios.post('/auth/reset', {
        email,
        code,
        password,
        password_confirmation: passwordConfirmation
      })
      return { success: true, message: response.data.message }
    } catch (error) {
      const message = error.response?.data?.message || 'Reset failed'
      return { success: false, message }
    }
  }

  // Initialize auth state from localStorage
  function initializeAuth() {
    if (token.value) {
      axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
    }
  }

  return {
    // State
    user,
    token,
    isLoading,
    
    // Getters
    isAuthenticated,
    userRole,
    
    // Actions
    login,
    logout,
    fetchUser,
    refreshToken,
    forgotPassword,
    verifyResetCode,
    resetPassword,
    initializeAuth
  }
})
