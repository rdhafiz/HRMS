<template>
  <div>
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-xl font-semibold">Departments</h2>
      <router-link v-if="canManage" to="/departments/create" class="px-3 py-2 rounded bg-indigo-600 text-white text-sm">Create</router-link>
    </div>
    <div class="flex items-center gap-2 mb-3">
      <input v-model="q" @input="fetch" placeholder="Search..." class="border rounded px-3 py-2 w-64" />
    </div>
    <div class="bg-white rounded shadow divide-y">
      <div class="grid grid-cols-3 px-4 py-2 text-xs text-gray-500">
        <div>Name</div>
        <div>Description</div>
        <div class="text-right">Designations</div>
      </div>
      <div v-for="d in items" :key="d.id" class="grid grid-cols-3 px-4 py-3 items-center">
        <div class="font-medium">{{ d.name }}</div>
        <div class="text-sm text-gray-600">{{ d.description }}</div>
        <div class="text-right">
          <span class="text-sm mr-3">{{ d.designations_count }}</span>
          <router-link v-if="canManage" :to="`/departments/${d.id}/edit`" class="text-indigo-600 text-sm mr-2">Edit</router-link>
          <button v-if="canManage" @click="remove(d)" class="text-rose-600 text-sm">Delete</button>
        </div>
      </div>
    </div>
    <div class="flex justify-between items-center text-sm mt-3">
      <button @click="prev" :disabled="!links.prev" class="px-2 py-1 border rounded disabled:opacity-50">Prev</button>
      <div>Page {{ meta.current_page }} / {{ meta.last_page }}</div>
      <button @click="next" :disabled="!links.next" class="px-2 py-1 border rounded disabled:opacity-50">Next</button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const items = ref([])
const meta = ref({ current_page: 1, last_page: 1 })
const links = ref({})
const q = ref('')
const me = ref(null)
const canManage = computed(() => me.value?.admin_type === 1 || me.value?.admin_type === 2)

async function fetch(page = 1) {
  const { data } = await axios.get('/employment/departments', { params: { page, q: q.value } })
  items.value = data.data
  meta.value = { current_page: data.current_page, last_page: data.last_page }
  links.value = { next: data.next_page_url, prev: data.prev_page_url }
}

function next() { if (links.value.next) fetch(meta.value.current_page + 1) }
function prev() { if (links.value.prev) fetch(meta.value.current_page - 1) }

async function remove(row) {
  if (!confirm('Delete this department?')) return
  await axios.delete(`/employment/departments/${row.id}`)
  fetch(meta.value.current_page)
}

async function loadMe() { const { data } = await axios.get('/auth/user'); me.value = data }

onMounted(async () => { await loadMe(); fetch() })
</script>

