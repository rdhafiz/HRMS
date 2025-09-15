<template>
	<div class="p-4 max-w-2xl">
		<h1 class="text-xl font-semibold mb-4">{{ isEdit ? 'Edit Leave Request' : 'New Leave Request' }}</h1>
		<form @submit.prevent="submit">
			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<div>
					<label class="block text-sm font-medium">Employee</label>
					<select v-model="form.employee_id" class="border rounded px-3 py-2 w-full">
						<option :value="null">Select Employee</option>
						<option v-for="e in employees" :key="e.id" :value="e.id">{{ e.first_name }} {{ e.last_name }} ({{ e.employee_code }})</option>
					</select>
					<p v-if="errors.employee_id" class="text-red-600 text-sm mt-1">{{ errors.employee_id[0] }}</p>
				</div>
				<div>
					<label class="block text-sm font-medium">Type</label>
					<select v-model="form.leave_type" class="border rounded px-3 py-2 w-full">
						<option value="sick">Sick</option>
						<option value="casual">Casual</option>
						<option value="paid">Paid</option>
						<option value="unpaid">Unpaid</option>
					</select>
					<p v-if="errors.leave_type" class="text-red-600 text-sm mt-1">{{ errors.leave_type[0] }}</p>
				</div>
				<div>
					<label class="block text-sm font-medium">Start Date</label>
					<flat-pickr
						v-model="form.start_date"
						:config="{ ...dateConfig, minDate: new Date() }"
						placeholder="Select start date"
						class="border rounded px-3 py-2 w-full"
					/>
					<p v-if="errors.start_date" class="text-red-600 text-sm mt-1">{{ errors.start_date[0] }}</p>
				</div>
				<div>
					<label class="block text-sm font-medium">End Date</label>
					<flat-pickr
						v-model="form.end_date"
						:config="{ ...dateConfig, minDate: form.start_date || new Date() }"
						placeholder="Select end date"
						class="border rounded px-3 py-2 w-full"
					/>
					<p v-if="errors.end_date" class="text-red-600 text-sm mt-1">{{ errors.end_date[0] }}</p>
				</div>
				<div class="md:col-span-2">
					<label class="block text-sm font-medium">Reason</label>
					<textarea v-model="form.reason" class="border rounded px-3 py-2 w-full" rows="3"></textarea>
					<p v-if="errors.reason" class="text-red-600 text-sm mt-1">{{ errors.reason[0] }}</p>
				</div>
			</div>
			<div class="flex justify-end gap-2 mt-6">
				<router-link :to="{ name: 'attendance.leaves' }" class="px-4 py-2 border rounded">Cancel</router-link>
				<button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">{{ isEdit ? 'Update' : 'Create' }}</button>
			</div>
		</form>
	</div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
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

const route = useRoute()
const router = useRouter()
const isEdit = computed(() => !!route.params.id)

const form = ref({ employee_id: null, leave_type: 'sick', start_date: null, end_date: null, reason: '' })
const errors = ref({})
const employees = ref([])

const loadEmployees = async () => {
	const { data } = await axios.get('/employment/employees')
	employees.value = data.data || data
}


const load = async () => {
	if (!isEdit.value) return
	const { data } = await axios.get(`/employment/leave-requests`, { params: { id: route.params.id } })
	const item = (data.data || []).find(x => x.id == route.params.id) || data
	form.value = { employee_id: item.employee_id, leave_type: item.leave_type, start_date: item.start_date || null, end_date: item.end_date || null, reason: item.reason }
}

const submit = async () => {
	errors.value = {}
	try {
		if (isEdit.value) {
			await axios.put(`/employment/leave-requests/${route.params.id}`, form.value)
		} else {
			await axios.post('/employment/leave-requests', form.value)
		}
		router.push({ name: 'attendance.leaves' })
	} catch (e) {
		if (e.response && e.response.status === 422) {
			errors.value = e.response.data.errors || {}
		}
	}
}

onMounted(async () => {
	await loadEmployees()
	await load()
})
</script>

