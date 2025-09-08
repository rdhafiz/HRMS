<template>
	<div class="p-4">
		<div class="flex items-center justify-between mb-4">
			<h1 class="text-xl font-semibold">Admins</h1>
			<button v-if="canManage" @click="goCreate" class="bg-green-600 text-white px-4 py-2 rounded">New Admin</button>
		</div>

		<div class="flex flex-wrap items-end gap-3 mb-3">
			<input v-model="q" @keyup.enter="load" type="text" placeholder="Search name/email" class="border rounded px-3 py-2" />
			<select v-model="admin_type" class="border rounded px-3 py-2">
				<option value="">All Types</option>
				<option :value="1">System Admin</option>
				<option :value="2">HR Manager</option>
				<option :value="3">Supervisor</option>
			</select>
			<button @click="load" class="border px-3 py-2 rounded">Filter</button>
		</div>

		<div class="overflow-auto border rounded">
			<table class="min-w-full text-sm">
				<thead class="bg-gray-50 text-left">
					<tr>
						<th class="p-3">Avatar</th>
						<th class="p-3">Name</th>
						<th class="p-3">Email</th>
						<th class="p-3">Admin Type</th>
						<th class="p-3 text-right" v-if="canManage">Actions</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="u in items" :key="u.id" class="border-t">
						<td class="p-3"><img :src="u.avatar" alt="avatar" class="w-8 h-8 rounded-full object-cover" /></td>
						<td class="p-3">{{ u.name }}</td>
						<td class="p-3">{{ u.email }}</td>
						<td class="p-3 capitalize">{{ u.admin_type_label }}</td>
						<td class="p-3 text-right" v-if="canManage">
							<div class="inline-flex gap-2">
								<button @click="goEdit(u)" class="text-amber-600 hover:underline">Edit</button>
								<button @click="confirmDelete(u)" class="text-red-600 hover:underline">Delete</button>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="flex items-center justify-between mt-4" v-if="meta">
			<div class="text-sm text-gray-600">Showing {{ meta.from || 0 }}-{{ meta.to || 0 }} of {{ meta.total || 0 }}</div>
			<div class="flex gap-2">
				<button class="px-3 py-1 border rounded" :disabled="!links.prev" @click="go(meta.current_page - 1)">Prev</button>
				<button class="px-3 py-1 border rounded" :disabled="!links.next" @click="go(meta.current_page + 1)">Next</button>
			</div>
		</div>

		<div v-if="toDelete" class="fixed inset-0 bg-black/30 flex items-center justify-center z-50">
			<div class="bg-white rounded p-6 w-full max-w-md">
				<h3 class="text-lg font-semibold mb-2">Delete Admin</h3>
				<p class="mb-4">Are you sure you want to delete {{ toDelete.name }}?</p>
				<div class="flex justify-end gap-2">
					<button class="px-4 py-2 border rounded" @click="toDelete = null">Cancel</button>
					<button class="px-4 py-2 bg-red-600 text-white rounded" @click="doDelete">Delete</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()
const q = ref('')
const admin_type = ref('')
const items = ref([])
const meta = ref(null)
const links = ref({ prev: null, next: null })
const page = ref(1)
const toDelete = ref(null)
const me = ref(null)

const canManage = computed(() => me.value?.admin_type === 1)

const loadMe = async () => {
  const { data } = await axios.get('/auth/user')
  me.value = data
}

const load = async () => {
  const { data } = await axios.get('/admins', { params: { page: page.value, q: q.value, admin_type: admin_type.value } })
  items.value = data.data || []
  meta.value = { current_page: data.current_page, from: data.from, to: data.to, total: data.total }
  links.value = { prev: data.prev_page_url, next: data.next_page_url }
}

const go = (p) => { page.value = p; load() }
const goCreate = () => router.push({ name: 'admins.create' })
const goEdit = (u) => router.push({ name: 'admins.edit', params: { id: u.id } })
const confirmDelete = (u) => { toDelete.value = u }
const doDelete = async () => { await axios.delete(`/admins/${toDelete.value.id}`); toDelete.value = null; load() }

onMounted(async () => { await loadMe(); await load() })
</script>

