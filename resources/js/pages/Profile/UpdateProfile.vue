<template>
  <div class="max-w-xl p-6 space-y-6">
    <div class="text-xl font-semibold">Update Profile</div>

    <form @submit.prevent="submit" class="space-y-4">
      <div>
        <label class="block text-sm text-gray-600 mb-1">Name</label>
        <input v-model="form.name" type="text" class="w-full border rounded px-3 py-2" required />
        <div v-if="errors.name" class="text-sm text-red-600 mt-1">{{ errors.name[0] }}</div>
      </div>

      <div>
        <label class="block text-sm text-gray-600 mb-1">Avatar</label>
        <input @change="onFile" type="file" accept="image/*" class="w-full border rounded px-3 py-2" />
        <div v-if="errors.avatar" class="text-sm text-red-600 mt-1">{{ errors.avatar[0] }}</div>
      </div>

      <div class="flex items-center gap-3">
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded" :disabled="loading">
          <span v-if="!loading">Save Changes</span>
          <span v-else>Saving...</span>
        </button>
        <router-link class="px-4 py-2 border rounded" :to="{ name: 'profile' }">Cancel</router-link>
      </div>
    </form>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import axios from 'axios'

const form = reactive({ name: '', avatar: null })
const errors = reactive({})
const loading = ref(false)

function onFile(e) {
  form.avatar = e.target.files[0] || null
}

async function load() {
  const { data } = await axios.get('/auth/profile')
  form.name = data?.user?.name || ''
}

async function submit() {
  loading.value = true
  errors.name = null
  errors.avatar = null
  try {
    const payload = new FormData()
    payload.append('name', form.name)
    if (form.avatar) payload.append('avatar', form.avatar)
    const { data } = await axios.post('/auth/profile', payload, { headers: { 'Content-Type': 'multipart/form-data' } })
    // navigate back
    window.toast && window.toast.success('Profile updated')
    history.back()
  } catch (e) {
    if (e.response?.status === 422) {
      Object.assign(errors, e.response.data.errors || {})
    }
  } finally {
    loading.value = false
  }
}

onMounted(load)
</script>

<style scoped>
</style>


