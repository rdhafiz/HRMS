<template>
  <div class="flex items-center space-x-4">
    <span class="text-sm text-gray-700">{{ authStore.user?.name }}</span>
    <button
      @click="handleLogout"
      class="text-sm text-gray-600 hover:text-gray-800 transition-colors"
    >
      Logout
    </button>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const router = useRouter()

async function handleLogout() {
  await authStore.logout()
  router.push({ name: 'login' })
}
</script>
