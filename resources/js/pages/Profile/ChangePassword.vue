<template>
  <div class="max-w-xl p-6 space-y-6">
    <div class="text-xl font-semibold">Change Password</div>

    <form @submit.prevent="submit" class="space-y-4">
      <div>
        <label class="block text-sm text-gray-600 mb-1">Current Password</label>
        <input v-model="form.current_password" type="password" class="w-full border rounded px-3 py-2" required />
        <div v-if="errors.current_password" class="text-sm text-red-600 mt-1">{{ errors.current_password[0] }}</div>
      </div>
      <div>
        <label class="block text-sm text-gray-600 mb-1">New Password</label>
        <input v-model="form.new_password" type="password" class="w-full border rounded px-3 py-2" required />
        <div class="text-xs text-gray-500 mt-1">Min 8 chars, include letters, numbers, and symbols.</div>
        <div v-if="errors.new_password" class="text-sm text-red-600 mt-1">{{ errors.new_password[0] }}</div>
      </div>
      <div>
        <label class="block text-sm text-gray-600 mb-1">Confirm New Password</label>
        <input v-model="form.new_password_confirmation" type="password" class="w-full border rounded px-3 py-2" required />
      </div>

      <div class="flex items-center gap-3">
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded" :disabled="loading">
          <span v-if="!loading">Update Password</span>
          <span v-else>Updating...</span>
        </button>
        <router-link class="px-4 py-2 border rounded" :to="{ name: 'profile' }">Cancel</router-link>
      </div>
    </form>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import axios from 'axios'

const form = reactive({ current_password: '', new_password: '', new_password_confirmation: '' })
const errors = reactive({})
const loading = ref(false)

async function submit() {
  loading.value = true
  errors.current_password = null
  errors.new_password = null
  try {
    await axios.post('/auth/profile/change-password', form)
    window.toast && window.toast.success('Password updated')
    history.back()
  } catch (e) {
    if (e.response?.status === 422) {
      Object.assign(errors, e.response.data.errors || {})
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
</style>


