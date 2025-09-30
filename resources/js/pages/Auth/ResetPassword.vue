<template>
  <div class="min-h-screen flex items-stretch bg-slate-900">
    <div class="hidden md:flex w-1/2 relative items-center justify-center overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-b from-slate-900 to-slate-800 opacity-95"></div>
      <div class="relative z-10 text-slate-200 px-10">
        <h2 class="text-3xl font-semibold mb-4">Reset your password</h2>
        <p class="text-slate-300 max-w-md">Enter the 6-digit code we sent to your email and set a new password.</p>
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
        <p v-if="route.query.notice" class="text-green-700 text-sm mb-3">{{ route.query.notice }}</p>
        <form @submit.prevent="handleResetPassword" class="space-y-4">
          <div>
            <label class="block text-sm text-gray-600 mb-1">Email</label>
            <input 
              v-model="email" 
              type="email" 
              placeholder="you@company.com" 
              class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500" 
              required 
            />
          </div>
          <div>
            <label class="block text-sm text-gray-600 mb-1">6-digit code</label>
            <input 
              v-model="code" 
              type="text" 
              pattern="\\d{6}" 
              placeholder="123456" 
              maxlength="6"
              class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500" 
              required 
            />
          </div>
          <div>
            <label class="block text-sm text-gray-600 mb-1">New password</label>
            <div class="relative">
              <input 
                :type="showPassword ? 'text' : 'password'" 
                v-model="password" 
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
          </div>
          <div>
            <label class="block text-sm text-gray-600 mb-1">Confirm password</label>
            <input 
              v-model="passwordConfirmation" 
              type="password" 
              minlength="8" 
              class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500" 
              required 
            />
          </div>
          <p v-if="message" class="text-green-700 text-sm">{{ message }}</p>
          <p v-if="error" class="text-red-600 text-sm">{{ error }}</p>
          <button 
            :disabled="authStore.isLoading" 
            class="w-full bg-cyan-500 hover:bg-cyan-600 text-white py-2 rounded-lg disabled:opacity-50 transition"
          >
            <span v-if="authStore.isLoading">Resetting...</span>
            <span v-else>Reset password</span>
          </button>
        </form>

        <div class="flex items-center justify-between mt-4 text-sm">
          <router-link to="/" class="text-gray-600 hover:text-cyan-600">Back to Login</router-link>
          <router-link to="/forgot" class="text-cyan-600 hover:text-cyan-700">Didn't get a code?</router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const router = useRouter()
const route = useRoute()

const email = ref('admin@example.com')
const code = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const message = ref('')
const error = ref('')
const showPassword = ref(false)

async function handleResetPassword() {
  message.value = ''
  error.value = ''
  
  if (password.value !== passwordConfirmation.value) {
    error.value = 'Passwords do not match'
    return
  }
  
  const result = await authStore.resetPassword(
    email.value,
    code.value,
    password.value,
    passwordConfirmation.value
  )
  
  if (result.success) {
    const notice = 'Your password has been reset successfully. Please log in with your new credentials.'
    message.value = notice
    router.push({ name: 'login', query: { notice } })
  } else {
    error.value = result.message
  }
}
</script>