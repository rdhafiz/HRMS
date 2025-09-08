<template>
  <div class="max-w-xl">
    <h2 class="text-xl font-semibold mb-4">{{ id ? 'Edit' : 'Create' }} Designation</h2>
    <form @submit.prevent="submit" class="space-y-3">
      <div>
        <label class="block text-sm mb-1">Department</label>
        <select v-model="form.department_id" class="w-full border rounded px-3 py-2" required>
          <option value="" disabled>Select department</option>
          <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
        </select>
        <p v-if="errors.department_id" class="text-sm text-rose-600 mt-1">{{ errors.department_id }}</p>
      </div>
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
        <router-link to="/designations" class="px-3 py-2 border rounded">Cancel</router-link>
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
const form = ref({ department_id: '', name: '', description: '' })
const errors = ref({})
const loading = ref(false)
const departments = ref([])

async function load()
{
  const depts = await axios.get('/employment/departments')
  departments.value = depts.data.data
  if (!id) return
  const { data } = await axios.get(`/employment/designations/${id}`)
  form.value = { department_id: data.department_id, name: data.name, description: data.description }
}

async function submit() {
  loading.value = true
  errors.value = {}
  try {
    if (id) {
      await axios.put(`/employment/designations/${id}`, form.value)
    } else {
      await axios.post('/employment/designations', form.value)
    }
    router.push('/designations')
  } catch (e) {
    if (e.response?.data?.errors) {
      errors.value = Object.fromEntries(Object.entries(e.response.data.errors).map(([k,v])=>[k,v[0]]))
    }
  } finally {
    loading.value = false
  }
}

onMounted(load)
</script>

