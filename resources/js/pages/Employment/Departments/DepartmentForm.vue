<template>
  <div class="max-w-xl">
    <h2 class="text-xl font-semibold mb-4">{{ id ? 'Edit' : 'Create' }} Department</h2>
    <form @submit.prevent="submit" class="space-y-3">
      <div>
        <label class="block text-sm mb-1">Name</label>
        <input v-model="form.name" class="w-full border rounded px-3 py-2" required maxlength="191" />
        <p v-if="errors.name" class="text-sm text-rose-600 mt-1">{{ errors.name }}</p>
      </div>
      <div>
        <label class="block text-sm mb-1">Description</label>
        <textarea v-model="form.description" class="w-full border rounded px-3 py-2" rows="3"></textarea>
      </div>
      <div class="flex gap-2">
        <button class="px-3 py-2 bg-indigo-600 text-white rounded" :disabled="loading">Save</button>
        <router-link to="/departments" class="px-3 py-2 border rounded">Cancel</router-link>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()
const id = route.params.id
const form = ref({ name: '', description: '' })
const errors = ref({})
const loading = ref(false)

async function load() {
  if (!id) return
  const { data } = await axios.get(`/employment/departments/${id}`)
  form.value = { name: data.name, description: data.description }
}

async function submit() {
  loading.value = true
  errors.value = {}
  try {
    if (id) {
      await axios.put(`/employment/departments/${id}`, form.value)
    } else {
      await axios.post('/employment/departments', form.value)
    }
    router.push('/departments')
  } catch (e) {
    if (e.response?.data?.errors) {
      const first = Object.entries(e.response.data.errors)[0]
      errors.value = Object.fromEntries(Object.entries(e.response.data.errors).map(([k,v])=>[k,v[0]]))
    }
  } finally {
    loading.value = false
  }
}

onMounted(load)
</script>

