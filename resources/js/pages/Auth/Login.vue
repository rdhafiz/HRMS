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
        <form @submit.prevent="handleLogin" class="space-y-4">
          <p v-if="notice" class="text-green-700 text-sm">{{ notice }}</p>
          <p v-if="success" class="text-green-700 text-sm">{{ success }}</p>
          <p v-if="successMessage" class="text-green-700 text-sm">{{ successMessage }}</p>
          <div>
            <label class="block text-sm text-gray-600 mb-1">Email</label>
            <input 
              v-model="email" 
              type="email" 
              placeholder="john@company.com" 
              class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500" 
              required 
            />
          </div>
          <div>
            <label class="block text-sm text-gray-600 mb-1">Password</label>
            <div class="relative">
              <input 
                :type="showPassword ? 'text' : 'password'" 
                v-model="password" 
                placeholder="**********" 
                minlength="8" 
                class="w-full border rounded-lg px-3 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-cyan-500" 
                required 
              />
              <button 
                type="button" 
                @click="showPassword = !showPassword" 
                class="absolute inset-y-0 right-0 px-3 text-gray-500 hover:text-gray-700"
              >
                <span v-if="!showPassword">👁️</span>
                <span v-else>🙈</span>
              </button>
            </div>
            <div class="text-right mt-1">
              <router-link to="/forgot" class="text-xs text-gray-600 hover:text-cyan-600">Forgot Password?</router-link>
            </div>
          </div>

          <p v-if="error" class="text-red-600 text-sm">{{ error }}</p>

          <button 
            :disabled="authStore.isLoading" 
            class="w-full bg-cyan-500 hover:bg-cyan-600 text-white py-2 rounded-lg disabled:opacity-50 transition"
          >
            <span v-if="authStore.isLoading">Logging in...</span>
            <span v-else>Login</span>
          </button>
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
            class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 hover:shadow-md"
          >
            <!-- Microsoft Logo -->
            <div v-if="!microsoftLoading" class="w-5 h-5 mr-3 flex-shrink-0">
              <svg viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="1" y="1" width="10" height="10" fill="#F25022"/>
                <rect x="12" y="1" width="10" height="10" fill="#7FBA00"/>
                <rect x="1" y="12" width="10" height="10" fill="#00A4EF"/>
                <rect x="12" y="12" width="10" height="10" fill="#FFB900"/>
              </svg>
            </div>
            
            <!-- Microsoft Loading Animation -->
            <div v-else class="w-5 h-5 mr-3 flex-shrink-0 relative">
              <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-4 h-4 border-2 border-gray-300 border-t-blue-500 rounded-full animate-spin"></div>
              </div>
            </div>
            
            <span v-if="!microsoftLoading" class="text-gray-700 font-medium">Continue with Microsoft</span>
            <span v-else class="text-gray-500">Connecting to Microsoft...</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const router = useRouter()
const route = useRoute()

const email = ref('admin@example.com')
const password = ref('password123')
const error = ref('')
const success = ref('')
const microsoftLoading = ref(false)

const appName = computed(() => import.meta.env.VITE_APP_NAME || 'HR Portal')
const notice = computed(() => route.query.notice)
const successMessage = computed(() => route.query.success)

async function handleLogin() {
  error.value = ''
  
  const result = await authStore.login({
    email: email.value,
    password: password.value
  })
  
  if (result.success) {
    // Redirect based on user role
    if (result.user.admin_type_label === 'EMPLOYEE') {
      router.push({ name: 'employee.dashboard' })
    } else {
      router.push({ name: 'dashboard' })
    }
  } else {
    error.value = result.message
  }
}

function loginWithMicrosoft() {
  microsoftLoading.value = true
  error.value = ''
  
  // Redirect to Microsoft OAuth
  window.location.href = '/auth/microsoft'
}
</script>