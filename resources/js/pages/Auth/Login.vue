<template>
  <div class="min-h-screen flex items-stretch bg-slate-900">
    <div class="hidden md:flex w-1/2 relative items-center justify-center overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-b from-slate-900 to-slate-800 opacity-95"></div>
      <div class="relative z-10 text-slate-200 px-10">
        <h2 class="text-3xl font-semibold mb-4">Welcome to {{ appName }}</h2>
        <p class="text-slate-300 max-w-md">Streamlined HR portal for attendance, leave and payroll. Secure role-based access powered by Sanctum.</p>
      </div>
      <div class="absolute inset-0 opacity-20">
        <div class="w-96 h-96 rounded-full bg-cyan-400 blur-3xl absolute -top-10 -left-10"></div>
        <div class="w-96 h-96 rounded-full bg-indigo-500 blur-3xl absolute bottom-0 right-0"></div>
      </div>
    </div>

    <div class="w-full md:w-1/2 flex items-center justify-center p-6 md:p-10">
      <div class="w-full max-w-md bg-white shadow-xl rounded-xl p-8">
        <div class="mb-6">
          <h1 class="text-2xl font-semibold tracking-tight"><span class="text-cyan-500">HR</span> PORTAL</h1>
        </div>
        <form @submit.prevent="submit" class="space-y-4">
          <p v-if="notice" class="text-green-700 text-sm">{{ notice }}</p>
          <p v-if="success" class="text-green-700 text-sm">{{ success }}</p>
          <p v-if="successMessage" class="text-green-700 text-sm">{{ successMessage }}</p>
          <div>
            <label class="block text-sm text-gray-600 mb-1">Username</label>
            <input v-model="email" type="email" placeholder="John Doe" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500" required />
          </div>
          <div>
            <label class="block text-sm text-gray-600 mb-1">Password</label>
            <div class="relative">
              <input :type="showPassword ? 'text' : 'password'" v-model="password" placeholder="**********" minlength="8" class="w-full border rounded-lg px-3 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-cyan-500" required />
              <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 px-3 text-gray-500 hover:text-gray-700">
                <span v-if="!showPassword">👁️</span>
                <span v-else>🙈</span>
              </button>
            </div>
            <div class="text-right mt-1">
              <router-link to="/forgot" class="text-xs text-gray-600 hover:text-cyan-600">Change Password</router-link>
            </div>
          </div>

          <p v-if="error" class="text-red-600 text-sm">{{ error }}</p>

          <button :disabled="loading" class="w-full bg-cyan-500 hover:bg-cyan-600 text-white py-2 rounded-lg disabled:opacity-50 transition">Login</button>
        </form>

        <!-- Divider -->
        <div class="relative my-6">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-300"></div>
          </div>
          <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-white text-gray-500">Or continue with</span>
          </div>
        </div>

        <!-- Microsoft Login Button -->
        <div class="space-y-3">
          <button 
            @click="loginWithMicrosoft" 
            :disabled="microsoftLoading"
            class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition"
          >
            <svg v-if="!microsoftLoading" class="w-5 h-5 mr-2" viewBox="0 0 21 21" fill="none">
              <path d="M10.5 1L1 5.5V10.5L10.5 15L20 10.5V5.5L10.5 1Z" fill="#F25022"/>
              <path d="M10.5 1L1 5.5V10.5L10.5 15L20 10.5V5.5L10.5 1Z" fill="#7FBA00"/>
              <path d="M10.5 1L1 5.5V10.5L10.5 15L20 10.5V5.5L10.5 1Z" fill="#00A4EF"/>
              <path d="M10.5 1L1 5.5V10.5L10.5 15L20 10.5V5.5L10.5 1Z" fill="#FFB900"/>
            </svg>
            <svg v-else class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span v-if="!microsoftLoading">Login with Microsoft</span>
            <span v-else>Connecting...</span>
          </button>
        </div>

        <p class="text-xs text-gray-500 mt-6 text-center">Unable to login? Kindly connect with <a href="#" class="underline">IT Support Team</a></p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import { clearUserCache } from '@/router'

const email = ref('admin@example.com')
const password = ref('password123')
const error = ref('')
const success = ref('')
const loading = ref(false)
const microsoftLoading = ref(false)
const showPassword = ref(false)
const appName = computed(() => import.meta.env.VITE_APP_NAME || 'HR Portal')
const route = useRoute()
const notice = computed(() => route.query.notice)
const successMessage = computed(() => route.query.success)

async function submit() {
  error.value = ''
  loading.value = true
  try {
    await axios.get('/sanctum/csrf-cookie')
    const response = await axios.post('/auth/login', { email: email.value, password: password.value })
    
    // Clear user cache to ensure fresh authentication state
    clearUserCache()
    
    // Check user role and redirect appropriately
    const user = response.data
    if (user.admin_type_label === 'EMPLOYEE') {
      window.location.href = '/employee/dashboard'
    } else {
      window.location.href = '/dashboard'
    }
  } catch (e) {
    error.value = e?.response?.data?.message || 'Login failed'
  } finally {
    loading.value = false
  }
}

function loginWithMicrosoft() {
  microsoftLoading.value = true
  error.value = ''
  
  // Redirect to Microsoft OAuth
  window.location.href = '/auth/microsoft'
}
</script>

