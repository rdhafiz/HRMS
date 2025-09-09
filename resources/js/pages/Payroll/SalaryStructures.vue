<template>
	<div class="p-4">
		<div class="flex items-center justify-between mb-4">
			<h1 class="text-xl font-semibold">Salary Structures</h1>
			<router-link v-if="canManage" :to="{ name: 'payroll.structures.create' }" class="bg-green-600 text-white px-4 py-2 rounded">New Structure</router-link>
		</div>

		<div class="flex flex-wrap items-end gap-3 mb-4">
			<input v-model="q" @keyup.enter="load" type="text" placeholder="Search name" class="border rounded px-3 py-2" />
			<select v-model="is_template" class="border rounded px-3 py-2">
				<option value="">All</option>
				<option :value="true">Templates</option>
				<option :value="false">Employee-specific</option>
			</select>
			<button @click="load" class="border px-3 py-2 rounded">Filter</button>
		</div>

		<div class="overflow-auto border rounded">
			<table class="min-w-full text-sm">
				<thead class="bg-gray-50 text-left">
					<tr>
						<th class="p-3">Name</th>
						<th class="p-3">Type</th>
						<th class="p-3">Components</th>
						<th class="p-3 text-right">Actions</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="s in items" :key="s.id" class="border-t">
						<td class="p-3">{{ s.name }}</td>
						<td class="p-3">{{ s.is_template ? 'Template' : 'Employee Specific' }}</td>
						<td class="p-3">
							<span class="inline-flex gap-1 flex-wrap">
								<span v-for="c in s.components" :key="c.id" class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-xs">{{ c.type }}: {{ c.name }} ({{ c.amount }})</span>
							</span>
						</td>
						<td class="p-3 text-right">
							<div class="inline-flex gap-2">
								<router-link :to="{ name: 'payroll.structures.edit', params: { id: s.id } }" class="text-amber-600 hover:underline" v-if="canManage">Edit</router-link>
								<button v-if="canManage" @click="remove(s)" class="text-red-600 hover:underline">Delete</button>
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
const q = ref('')
const is_template = ref('')
const me = ref(null)
const canManage = computed(() => me.value?.admin_type === 1 || me.value?.admin_type === 2)

const load = async () => {
	const params = { q: q.value }
	if (is_template.value !== '') params.is_template = is_template.value
	const { data } = await axios.get('/employment/salary-structures', { params })
	items.value = data.data || data
}

const remove = async (row) => {
	if (!canManage.value) return
	if (!confirm('Delete this structure?')) return
	await axios.delete(`/employment/salary-structures/${row.id}`)
	await load()
}

onMounted(async () => { const { data } = await axios.get('/auth/user'); me.value = data; await load() })
</script>


