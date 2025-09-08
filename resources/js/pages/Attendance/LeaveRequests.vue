<template>
	<div class="p-4">
		<div class="flex items-center justify-between mb-4">
			<h1 class="text-xl font-semibold">Leave Requests</h1>
			<router-link v-if="canManage" :to="{ name: 'attendance.leaves.create' }" class="bg-green-600 text-white px-4 py-2 rounded">New Leave</router-link>
		</div>

		<div class="flex flex-wrap gap-3 mb-4">
			<select v-model="filters.leave_type" class="border rounded px-3 py-2">
				<option value="">All Types</option>
				<option value="sick">Sick</option>
				<option value="casual">Casual</option>
				<option value="paid">Paid</option>
				<option value="unpaid">Unpaid</option>
			</select>
			<select v-model="filters.status" class="border rounded px-3 py-2">
				<option value="">All Status</option>
				<option value="pending">Pending</option>
				<option value="approved">Approved</option>
				<option value="rejected">Rejected</option>
			</select>
			<button @click="load" class="border px-3 py-2 rounded">Filter</button>
		</div>

		<div class="overflow-auto border rounded">
			<table class="min-w-full text-sm">
				<thead class="bg-gray-50 text-left">
					<tr>
						<th class="p-3">Employee</th>
						<th class="p-3">Type</th>
						<th class="p-3">Dates</th>
						<th class="p-3">Status</th>
						<th class="p-3 text-right">Actions</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="r in items" :key="r.id" class="border-t">
						<td class="p-3">{{ r.employee?.first_name }} {{ r.employee?.last_name }}</td>
						<td class="p-3">{{ r.leave_type }}</td>
						<td class="p-3">{{ r.start_date }} → {{ r.end_date }}</td>
						<td class="p-3">{{ r.status }}</td>
						<td class="p-3 text-right">
							<div v-if="canManage" class="inline-flex gap-2">
								<router-link :to="{ name: 'attendance.leaves.edit', params: { id: r.id } }" class="text-amber-600 hover:underline">Edit</router-link>
								<button @click="decision(r, 'approve')" class="text-green-700 hover:underline">Approve</button>
								<button @click="decision(r, 'reject')" class="text-red-600 hover:underline">Reject</button>
							</div>
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
const filters = ref({ leave_type: '', status: '' })
const me = ref(null)
const canManage = computed(() => me.value?.admin_type === 1 || me.value?.admin_type === 2)

const load = async () => {
	const { data } = await axios.get('/employment/leave-requests', { params: filters.value })
	items.value = data.data || []
}

const decision = async (row, action) => {
	if (!canManage.value) return
	await axios.post(`/employment/leave-requests/${row.id}/decision`, { action })
	await load()
}

onMounted(async () => {
	const { data } = await axios.get('/auth/user')
	me.value = data
	await load()
})
</script>

