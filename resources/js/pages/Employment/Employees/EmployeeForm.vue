<template>
	<div class="p-4 max-w-4xl">
		<h1 class="text-xl font-semibold mb-4">{{ isEdit ? 'Edit Employee' : 'Create Employee' }}</h1>
		<form @submit.prevent="submit">
			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<div>
					<label class="block text-sm font-medium">First Name</label>
					<input v-model="form.first_name" type="text" class="border rounded px-3 py-2 w-full" />
					<p v-if="errors.first_name" class="text-red-600 text-sm mt-1">{{ errors.first_name[0] }}</p>
				</div>
				<div>
					<label class="block text-sm font-medium">Last Name</label>
					<input v-model="form.last_name" type="text" class="border rounded px-3 py-2 w-full" />
					<p v-if="errors.last_name" class="text-red-600 text-sm mt-1">{{ errors.last_name[0] }}</p>
				</div>
				<div>
					<label class="block text-sm font-medium">Email</label>
					<input v-model="form.email" type="email" class="border rounded px-3 py-2 w-full" />
					<p v-if="errors.email" class="text-red-600 text-sm mt-1">{{ errors.email[0] }}</p>
				</div>
				<div>
					<label class="block text-sm font-medium">Phone</label>
					<input v-model="form.phone" type="text" class="border rounded px-3 py-2 w-full" />
					<p v-if="errors.phone" class="text-red-600 text-sm mt-1">{{ errors.phone[0] }}</p>
				</div>
				<div>
					<label class="block text-sm font-medium">Date of Birth</label>
					<flat-pickr
						v-model="form.date_of_birth"
						:config="{ ...dateConfig, maxDate: new Date() }"
						placeholder="Select date of birth"
						class="border rounded px-3 py-2 w-full"
					/>
					<p v-if="errors.date_of_birth" class="text-red-600 text-sm mt-1">{{ errors.date_of_birth[0] }}</p>
				</div>
				<div>
					<label class="block text-sm font-medium">Gender</label>
					<select v-model="form.gender" class="border rounded px-3 py-2 w-full">
						<option :value="null">Select</option>
						<option value="male">Male</option>
						<option value="female">Female</option>
						<option value="other">Other</option>
					</select>
					<p v-if="errors.gender" class="text-red-600 text-sm mt-1">{{ errors.gender[0] }}</p>
				</div>
				<div class="md:col-span-2">
					<label class="block text-sm font-medium">Address</label>
					<textarea v-model="form.address" class="border rounded px-3 py-2 w-full" rows="3"></textarea>
					<p v-if="errors.address" class="text-red-600 text-sm mt-1">{{ errors.address[0] }}</p>
				</div>

				<div>
					<label class="block text-sm font-medium">Employee Code</label>
					<input v-model="form.employee_code" type="text" class="border rounded px-3 py-2 w-full" />
					<p v-if="errors.employee_code" class="text-red-600 text-sm mt-1">{{ errors.employee_code[0] }}</p>
				</div>
				<div>
					<label class="block text-sm font-medium">Join Date</label>
					<flat-pickr
						v-model="form.join_date"
						:config="dateConfig"
						placeholder="Select join date"
						class="border rounded px-3 py-2 w-full"
					/>
					<p v-if="errors.join_date" class="text-red-600 text-sm mt-1">{{ errors.join_date[0] }}</p>
				</div>
				<div>
					<label class="block text-sm font-medium">Department</label>
					<select v-model="form.department_id" class="border rounded px-3 py-2 w-full">
						<option :value="null">Select Department</option>
						<option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
					</select>
					<p v-if="errors.department_id" class="text-red-600 text-sm mt-1">{{ errors.department_id[0] }}</p>
				</div>
				<div>
					<label class="block text-sm font-medium">Designation</label>
					<select v-model="form.designation_id" class="border rounded px-3 py-2 w-full">
						<option :value="null">Select Designation</option>
						<option v-for="d in designations" :key="d.id" :value="d.id">{{ d.name }}</option>
					</select>
					<p v-if="errors.designation_id" class="text-red-600 text-sm mt-1">{{ errors.designation_id[0] }}</p>
				</div>
				<div>
					<label class="block text-sm font-medium">Status</label>
					<select v-model="form.status" class="border rounded px-3 py-2 w-full">
						<option value="active">Active</option>
						<option value="inactive">Inactive</option>
						<option value="terminated">Terminated</option>
					</select>
					<p v-if="errors.status" class="text-red-600 text-sm mt-1">{{ errors.status[0] }}</p>
				</div>
				<div>
					<label class="block text-sm font-medium">Salary Structure</label>
					<select v-model="form.salary_structure_id" class="border rounded px-3 py-2 w-full">
						<option :value="null">Select Salary Structure</option>
						<option v-for="structure in salaryStructures" :key="structure.id" :value="structure.id">
							{{ structure.name }}
						</option>
					</select>
					<p v-if="errors.salary_structure_id" class="text-red-600 text-sm mt-1">{{ errors.salary_structure_id[0] }}</p>
				</div>
				<div>
					<label class="block text-sm font-medium">Effective Date</label>
					<flat-pickr
						v-model="form.effective_date"
						:config="{ ...dateConfig, minDate: form.join_date || new Date() }"
						placeholder="Select effective date"
						class="border rounded px-3 py-2 w-full"
					/>
					<p v-if="errors.effective_date" class="text-red-600 text-sm mt-1">{{ errors.effective_date[0] }}</p>
				</div>
				
				<!-- Password fields - only show when creating new employee -->
				<template v-if="!isEdit">
					<div>
						<label class="block text-sm font-medium text-gray-700 mb-2">
							Password <span class="text-red-500">*</span>
						</label>
						<input
							v-model="form.password"
							type="password"
							class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
							:class="{ 'border-red-300': errors.password }"
							placeholder="Enter password (min 8 characters)"
						/>
						<p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password[0] }}</p>
					</div>
					<div>
						<label class="block text-sm font-medium text-gray-700 mb-2">
							Confirm Password <span class="text-red-500">*</span>
						</label>
						<input
							v-model="form.password_confirmation"
							type="password"
							class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
							:class="{ 'border-red-300': errors.password_confirmation }"
							placeholder="Confirm password"
						/>
						<p v-if="errors.password_confirmation" class="mt-1 text-sm text-red-600">{{ errors.password_confirmation[0] }}</p>
					</div>
				</template>
			</div>

			<div class="flex justify-end gap-2 mt-6">
				<router-link :to="{ name: 'employees' }" class="px-4 py-2 border rounded">Cancel</router-link>
				<button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">{{ isEdit ? 'Update' : 'Create' }}</button>
			</div>
		</form>
	</div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import axios from 'axios'
import { useRoute, useRouter } from 'vue-router'
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

const form = ref({
	first_name: '',
	last_name: '',
	email: '',
	phone: '',
	date_of_birth: null,
	gender: null,
	address: '',

	employee_code: '',
	join_date: null,
	department_id: null,
	designation_id: null,
	status: 'active',
	salary_structure_id: null,
	effective_date: new Date().toISOString().slice(0, 10), // Default to today's date
	
	// Password fields for new employees
	password: '',
	password_confirmation: '',
})
const errors = ref({})
const departments = ref([])
const designations = ref([])
const salaryStructures = ref([])

const loadDepartments = async () => {
	const { data } = await axios.get('/employment/departments')
	departments.value = data.data || data // supports both paginate and array
}
const loadDesignations = async () => {
	const params = form.value.department_id ? { department_id: form.value.department_id } : {}
	const { data } = await axios.get('/employment/designations', { params })
	designations.value = data.data || data
}

const loadSalaryStructures = async () => {
	const { data } = await axios.get('/employment/salary-structures?all=true')
	salaryStructures.value = data
}

const loadEmployee = async () => {
	if (!isEdit.value) return
	const { data } = await axios.get(`/employment/employees/${route.params.id}`)
	form.value = {
		first_name: data.first_name,
		last_name: data.last_name,
		email: data.email,
		phone: data.phone,
		date_of_birth: data.date_of_birth || null,
		gender: data.gender,
		address: data.address,
		employee_code: data.employee_code,
		join_date: data.join_date || null,
		department_id: data.department_id,
		designation_id: data.designation_id,
		status: data.status,
		salary_structure_id: data.current_salary_structure?.structure?.id || null,
		effective_date: data.current_salary_structure?.effective_date || new Date().toISOString().slice(0, 10),
	}
}

const submit = async () => {
	errors.value = {}
	try {
		if (isEdit.value) {
			await axios.put(`/employment/employees/${route.params.id}`, form.value)
		} else {
			await axios.post('/employment/employees', form.value)
		}
		router.push({ name: 'employees' })
	} catch (e) {
		if (e.response && e.response.status === 422) {
			errors.value = e.response.data.errors || {}
		}
	}
}

watch(() => form.value.department_id, async () => {
	await loadDesignations()
})

onMounted(async () => {
	await Promise.all([loadDepartments(), loadDesignations(), loadSalaryStructures(), loadEmployee()])
})
</script>