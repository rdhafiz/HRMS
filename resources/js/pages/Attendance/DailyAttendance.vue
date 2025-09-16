<template>
	<div class="p-4">
		<h1 class="text-xl font-semibold mb-4">Daily Attendance</h1>

		<div class="flex flex-wrap items-end gap-3 mb-4">
			<div>
				<label class="block text-sm font-medium">Date</label>
				<flat-pickr
					v-model="filters.date"
					:config="dateConfig"
					placeholder="Select date"
					class="border rounded px-3 py-2"
				/>
			</div>
			<div>
				<label class="block text-sm font-medium">Department</label>
				<select v-model="filters.department_id" class="border rounded px-3 py-2">
					<option :value="null">All</option>
					<option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
				</select>
			</div>
			<button @click="load" class="bg-blue-600 text-white px-4 py-2 rounded">Load</button>
		</div>

		<div class="overflow-auto border rounded">
			<table class="min-w-full text-sm">
				<thead class="bg-gray-50 text-left">
					<tr>
						<th class="p-3">Employee</th>
						<th class="p-3">Status</th>
						<th class="p-3">Check-in</th>
						<th class="p-3">Check-out</th>
						<th class="p-3">Hours</th>
						<th class="p-3">Remarks</th>
						<th class="p-3 text-right">Action</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="row in rows" :key="row.employee.id" class="border-t">
						<td class="p-3">{{ row.employee.first_name }} {{ row.employee.last_name }}<div class="text-xs text-gray-500">{{ row.employee.employee_code }}</div></td>
						<td class="p-3">
							<select v-model="row.form.status" class="border rounded px-2 py-1">
								<option value="present">Present</option>
								<option value="absent">Absent</option>
								<option value="late">Late</option>
								<option value="half_day">Half-Day</option>
								<option value="leave">Leave</option>
								<option value="holiday">Holiday</option>
							</select>
						</td>
						<td class="p-3"><input v-model="row.form.check_in" type="time" class="border rounded px-2 py-1" /></td>
						<td class="p-3"><input v-model="row.form.check_out" type="time" class="border rounded px-2 py-1" /></td>
						<td class="p-3 w-24">{{ row.form.working_hours }}</td>
						<td class="p-3"><input v-model="row.form.remarks" type="text" class="border rounded px-2 py-1 w-full" /></td>
						<td class="p-3 text-right">
							<button v-if="canManage" @click="save(row)" class="bg-green-600 text-white px-3 py-1 rounded">Save</button>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="mt-6">
			<h2 class="font-semibold mb-2">Past Records</h2>
			<div class="flex items-center gap-2 mb-3">
				<input v-model="history.q" @keyup.enter="loadHistory" type="text" placeholder="Search name/code" class="border rounded px-3 py-2" />
				<button @click="loadHistory" class="border px-3 py-2 rounded">Search</button>
			</div>
			<div class="overflow-auto border rounded">
				<table class="min-w-full text-sm">
					<thead class="bg-gray-50 text-left">
						<tr>
							<th class="p-3">Date</th>
							<th class="p-3">Employee</th>
							<th class="p-3">Status</th>
							<th class="p-3">Hours</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="a in history.items" :key="a.id" class="border-t">
							<td class="p-3">{{ a.date }}</td>
							<td class="p-3">{{ a.employee?.first_name }} {{ a.employee?.last_name }}</td>
							<td class="p-3">{{ a.status }}</td>
							<td class="p-3">{{ a.working_hours }}</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import flatPickr from 'vue-flatpickr-component'
import 'flatpickr/dist/flatpickr.css'

// Reusable date configuration
const dateConfig = {
	dateFormat: 'Y-m-d',
	altFormat: 'M j, Y',
	altInput: true,
	allowInput: true,
	clickOpens: true,
	defaultDate: null
}

const filters = ref({ date: new Date().toISOString().slice(0,10), department_id: null })
const departments = ref([])
const rows = ref([])
const history = ref({ q: '', items: [] })
const me = ref(null)
const canManage = computed(() => me.value?.admin_type === 1 || me.value?.admin_type === 2)


const loadDepartments = async () => {
	const { data } = await axios.get('/employment/departments')
	departments.value = data.data || data
}

const load = async () => {
	// Load employees by department
	const params = {}
	if (filters.value.department_id) params.department_id = filters.value.department_id
	const { data } = await axios.get('/employment/employees', { params })
	const list = data.data || data
	rows.value = list.map(e => ({
		employee: e,
		form: { employee_id: e.id, date: filters.value.date, status: 'present', check_in: null, check_out: null, working_hours: 0, remarks: '' },
	}))
}

const save = async (row) => {
	if (!canManage.value) return
	await axios.post('/employment/attendance', row.form)
}

const loadHistory = async () => {
	const { data } = await axios.get('/employment/attendance', { params: { date: filters.value.date, department_id: filters.value.department_id } })
	history.value.items = data.data || []
}

onMounted(async () => {
	const { data } = await axios.get('/auth/user')
	me.value = data
	await loadDepartments()
	await load()
	await loadHistory()
})
</script>

