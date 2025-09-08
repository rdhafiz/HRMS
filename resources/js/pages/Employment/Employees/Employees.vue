<template>
	<div class="p-4">
		<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
			<h1 class="text-xl font-semibold">Employees</h1>
			<div class="flex items-center gap-2">
				<input v-model="q" @keyup.enter="fetchData" type="text" placeholder="Search by name, email, code" class="border rounded px-3 py-2 w-64" />
				<button @click="fetchData" class="bg-blue-600 text-white px-4 py-2 rounded">Search</button>
				<router-link v-if="canManage" :to="{ name: 'employees.create' }" class="bg-green-600 text-white px-4 py-2 rounded">Add Employee</router-link>
			</div>
		</div>

		<div class="overflow-auto border rounded">
			<table class="min-w-full text-sm">
				<thead class="bg-gray-50 text-left">
					<tr>
						<th class="p-3">Employee Code</th>
						<th class="p-3">Name</th>
						<th class="p-3">Email</th>
						<th class="p-3">Department</th>
						<th class="p-3">Designation</th>
						<th class="p-3">Status</th>
						<th class="p-3">Join Date</th>
						<th class="p-3 text-right">Actions</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="emp in items" :key="emp.id" class="border-t">
						<td class="p-3 font-mono">{{ emp.employee_code }}</td>
						<td class="p-3">{{ emp.first_name }} {{ emp.last_name }}</td>
						<td class="p-3">{{ emp.email }}</td>
						<td class="p-3">{{ emp.department?.name }}</td>
						<td class="p-3">{{ emp.designation?.name }}</td>
						<td class="p-3">
							<span class="px-2 py-1 rounded text-xs" :class="badgeClass(emp.status)">{{ emp.status }}</span>
						</td>
						<td class="p-3">{{ emp.join_date }}</td>
						<td class="p-3 text-right">
							<div class="inline-flex gap-2">
								<router-link :to="{ name: 'employees.view', params: { id: emp.id } }" class="text-blue-600 hover:underline">View</router-link>
								<router-link v-if="canManage" :to="{ name: 'employees.edit', params: { id: emp.id } }" class="text-amber-600 hover:underline">Edit</router-link>
								<button v-if="canManage" @click="confirmDelete(emp)" class="text-red-600 hover:underline">Delete</button>
							</div>
						</td>
					</tr>
					<tr v-if="!loading && items.length === 0">
						<td colspan="8" class="p-4 text-center text-gray-500">No employees found.</td>
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

		<!-- Confirm -->
		<div v-if="toDelete" class="fixed inset-0 bg-black/30 flex items-center justify-center z-50">
			<div class="bg-white rounded p-6 w-full max-w-md">
				<h3 class="text-lg font-semibold mb-2">Delete Employee</h3>
				<p class="mb-4">Are you sure you want to delete {{ toDelete.first_name }} {{ toDelete.last_name }}?</p>
				<div class="flex justify-end gap-2">
					<button class="px-4 py-2 border rounded" @click="toDelete = null">Cancel</button>
					<button class="px-4 py-2 bg-red-600 text-white rounded" @click="doDelete">Delete</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

const q = ref('')
const items = ref([])
const loading = ref(false)
const meta = ref(null)
const links = ref({ prev: null, next: null })
const page = ref(1)
const toDelete = ref(null)
const me = ref(null)
const canManage = computed(() => me.value?.admin_type === 1 || me.value?.admin_type === 2)

const fetchData = async () => {
	loading.value = true
	try {
		const { data } = await axios.get('/employment/employees', { params: { page: page.value, q: q.value } })
		items.value = data.data || []
		meta.value = {
			current_page: data.current_page,
			from: data.from,
			to: data.to,
			total: data.total,
		}
		links.value = { prev: data.prev_page_url, next: data.next_page_url }
	} finally {
		loading.value = false
	}
}

const go = (p) => {
	page.value = p
	fetchData()
}

const confirmDelete = (emp) => {
	toDelete.value = emp
}

const doDelete = async () => {
	if (!toDelete.value) return
	await axios.delete(`/employment/employees/${toDelete.value.id}`)
	toDelete.value = null
	fetchData()
}

const badgeClass = (status) => {
	if (status === 'active') return 'bg-green-100 text-green-700'
	if (status === 'inactive') return 'bg-amber-100 text-amber-700'
	return 'bg-red-100 text-red-700'
}

onMounted(async () => { const { data } = await axios.get('/auth/user'); me.value = data; fetchData() })
</script>