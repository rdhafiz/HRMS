<template>
	<div class="p-4">
		<div class="flex items-center justify-between mb-4">
			<h1 class="text-xl font-semibold">Holidays</h1>
			<router-link v-if="canManage" :to="{ name: 'attendance.holidays.create' }" class="bg-green-600 text-white px-4 py-2 rounded">New Holiday</router-link>
		</div>

		<div class="flex flex-wrap items-end gap-3 mb-4">
			<div>
				<label class="block text-sm font-medium">Department</label>
				<select v-model="filters.department_id" class="border rounded px-3 py-2">
					<option :value="null">All</option>
					<option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
				</select>
			</div>
			<div>
				<label class="block text-sm font-medium">Start</label>
				<input v-model="filters.start_date" type="date" class="border rounded px-3 py-2" />
			</div>
			<div>
				<label class="block text-sm font-medium">End</label>
				<input v-model="filters.end_date" type="date" class="border rounded px-3 py-2" />
			</div>
			<button @click="load" class="border px-3 py-2 rounded">Filter</button>
		</div>

		<div class="overflow-auto border rounded">
			<table class="min-w-full text-sm">
				<thead class="bg-gray-50 text-left">
					<tr>
						<th class="p-3">Name</th>
						<th class="p-3">Department</th>
						<th class="p-3">From</th>
						<th class="p-3">To</th>
						<th class="p-3 text-right">Actions</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="h in items" :key="h.id" class="border-t">
						<td class="p-3">{{ h.name }}</td>
						<td class="p-3">{{ h.department?.name || 'All' }}</td>
						<td class="p-3">{{ h.start_date }}</td>
						<td class="p-3">{{ h.end_date }}</td>
						<td class="p-3 text-right">
							<router-link v-if="canManage" :to="{ name: 'attendance.holidays.edit', params: { id: h.id } }" class="text-amber-600 hover:underline">Edit</router-link>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

const items = ref([])
const departments = ref([])
const filters = ref({ department_id: null, start_date: '', end_date: '' })
const me = ref(null)
const canManage = computed(() => me.value?.admin_type === 1 || me.value?.admin_type === 2)

const load = async () => {
	const { data } = await axios.get('/employment/holidays', { params: filters.value })
	items.value = data.data || []
}

const loadDepartments = async () => {
	const { data } = await axios.get('/employment/departments')
	departments.value = data.data || data
}

onMounted(async () => {
	const { data } = await axios.get('/auth/user')
	me.value = data
	await loadDepartments()
	await load()
})
</script>

