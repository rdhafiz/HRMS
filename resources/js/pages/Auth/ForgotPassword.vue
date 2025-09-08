<template>
  <div class="min-h-screen flex items-stretch bg-slate-900">
    <div class="hidden md:flex w-1/2 relative items-center justify-center overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-b from-slate-900 to-slate-800 opacity-95"></div>
      <div class="relative z-10 text-slate-200 px-10">
        <h2 class="text-3xl font-semibold mb-4">Forgot your password?</h2>
        <p class="text-slate-300 max-w-md">Enter your email and we'll send a 6-digit reset code to help you regain access.</p>
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
          <p v-if="message" class="text-green-700 text-sm">{{ message }}</p>
          <p v-if="error" class="text-red-600 text-sm">{{ error }}</p>
          <div>
            <label class="block text-sm text-gray-600 mb-1">Email</label>
            <input v-model="email" type="email" placeholder="you@company.com" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500" required />
          </div>
          <button :disabled="loading" class="w-full bg-cyan-500 hover:bg-cyan-600 text-white py-2 rounded-lg disabled:opacity-50 transition">Send reset code</button>
        </form>

        <div class="flex items-center justify-between mt-4 text-sm">
          <router-link to="/" class="text-gray-600 hover:text-cyan-600">Back to Login</router-link>
          <router-link to="/reset" class="text-cyan-600 hover:text-cyan-700">Have a code? Reset now</router-link>
        </div>

        <p class="text-xs text-gray-500 mt-6 text-center">Need help? Contact <a href="#" class="underline">IT Support Team</a></p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const email = ref('admin@example.com')
const message = ref('')
const error = ref('')
const loading = ref(false)
const router = useRouter()

async function submit() {
  message.value = ''
  error.value = ''
  loading.value = true
  try {
    await axios.get('/sanctum/csrf-cookie')
    const { data } = await axios.post('/auth/forgot', { email: email.value })
    const notice = 'A 6-digit reset code has been sent to your email. Use it to reset your password.'
    message.value = notice
    router.push({ name: 'reset', query: { notice } })
  } catch (e) {
    error.value = e?.response?.data?.message || 'Request failed'
  } finally {
    loading.value = false
  }
}
</script>

