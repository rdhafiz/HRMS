<template>
	<div class="p-4 max-w-4xl">
		<h1 class="text-xl font-semibold mb-4">{{ isEdit ? 'Edit Employee' : 'Create Employee' }}</h1>
		<form @submit.prevent="submit">
			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<div>
					<label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
					<input v-model="form.first_name" type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" :class="{ 'border-red-300': errors.first_name }" />
					<p v-if="errors.first_name" class="mt-1 text-sm text-red-600">{{ errors.first_name[0] }}</p>
				</div>
				<div>
					<label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
					<input v-model="form.last_name" type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" :class="{ 'border-red-300': errors.last_name }" />
					<p v-if="errors.last_name" class="mt-1 text-sm text-red-600">{{ errors.last_name[0] }}</p>
				</div>
				<div>
					<label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
					<input v-model="form.email" type="email" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" :class="{ 'border-red-300': errors.email }" />
					<p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email[0] }}</p>
				</div>
				<div>
					<label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
					<input v-model="form.phone" type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" :class="{ 'border-red-300': errors.phone }" />
					<p v-if="errors.phone" class="mt-1 text-sm text-red-600">{{ errors.phone[0] }}</p>
				</div>
				<div>
					<label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
					<flat-pickr
						v-model="form.date_of_birth"
						:config="{ ...dateConfig, maxDate: new Date() }"
						placeholder="Select date of birth"
						class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
						:class="{ 'border-red-300': errors.date_of_birth }"
					/>
					<p v-if="errors.date_of_birth" class="mt-1 text-sm text-red-600">{{ errors.date_of_birth[0] }}</p>
				</div>
				<div>
					<label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
					<select v-model="form.gender" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" :class="{ 'border-red-300': errors.gender }">
						<option :value="null">Select</option>
						<option value="male">Male</option>
						<option value="female">Female</option>
						<option value="other">Other</option>
					</select>
					<p v-if="errors.gender" class="mt-1 text-sm text-red-600">{{ errors.gender[0] }}</p>
				</div>
				<div class="md:col-span-2">
					<label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
					<textarea v-model="form.address" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" :class="{ 'border-red-300': errors.address }" rows="3"></textarea>
					<p v-if="errors.address" class="mt-1 text-sm text-red-600">{{ errors.address[0] }}</p>
				</div>

				<div>
					<label class="block text-sm font-medium text-gray-700 mb-2">Employee Code</label>
					<input v-model="form.employee_code" type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" :class="{ 'border-red-300': errors.employee_code }" />
					<p v-if="errors.employee_code" class="mt-1 text-sm text-red-600">{{ errors.employee_code[0] }}</p>
				</div>
				<div>
					<label class="block text-sm font-medium text-gray-700 mb-2">Join Date</label>
					<flat-pickr
						v-model="form.join_date"
						:config="dateConfig"
						placeholder="Select join date"
						class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
						:class="{ 'border-red-300': errors.join_date }"
					/>
					<p v-if="errors.join_date" class="mt-1 text-sm text-red-600">{{ errors.join_date[0] }}</p>
				</div>
				<div>
					<label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
					<select v-model="form.department_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" :class="{ 'border-red-300': errors.department_id }">
						<option :value="null">Select Department</option>
						<option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
					</select>
					<p v-if="errors.department_id" class="mt-1 text-sm text-red-600">{{ errors.department_id[0] }}</p>
				</div>
				<div>
					<label class="block text-sm font-medium text-gray-700 mb-2">Designation</label>
					<select v-model="form.designation_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" :class="{ 'border-red-300': errors.designation_id }">
						<option :value="null">Select Designation</option>
						<option v-for="d in designations" :key="d.id" :value="d.id">{{ d.name }}</option>
					</select>
					<p v-if="errors.designation_id" class="mt-1 text-sm text-red-600">{{ errors.designation_id[0] }}</p>
				</div>
				<div>
					<label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
					<select v-model="form.status" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" :class="{ 'border-red-300': errors.status }">
						<option value="active">Active</option>
						<option value="inactive">Inactive</option>
						<option value="terminated">Terminated</option>
					</select>
					<p v-if="errors.status" class="mt-1 text-sm text-red-600">{{ errors.status[0] }}</p>
				</div>
				<div>
					<label class="block text-sm font-medium text-gray-700 mb-2">Salary Structure</label>
					<select v-model="form.salary_structure_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" :class="{ 'border-red-300': errors.salary_structure_id }">
						<option :value="null">Select Salary Structure</option>
						<option v-for="structure in salaryStructures" :key="structure.id" :value="structure.id">
							{{ structure.name }}
						</option>
					</select>
					<p v-if="errors.salary_structure_id" class="mt-1 text-sm text-red-600">{{ errors.salary_structure_id[0] }}</p>
				</div>
				<div>
					<label class="block text-sm font-medium text-gray-700 mb-2">Effective Date - {{ form.effective_date }}</label>
					<flat-pickr
						v-model="form.effective_date"
						:config="{ ...dateConfig }"
						placeholder="Select effective date"
						class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
						:class="{ 'border-red-300': errors.effective_date }"
					/>
					<p v-if="errors.effective_date" class="mt-1 text-sm text-red-600">{{ errors.effective_date[0] }}</p>
				</div>
				
				<div>
					<label class="block text-sm font-medium text-gray-700 mb-2">
						Password <span v-if="!isEdit" class="text-red-500">*</span>
						<span v-else class="text-gray-500 text-sm">(Leave blank to keep current password)</span>
					</label>
					<input
						v-model="form.password"
						type="password"
						class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
						:class="{ 'border-red-300': errors.password }"
						:placeholder="isEdit ? 'Enter new password (min 8 characters)' : 'Enter password (min 8 characters)'"
					/>
					<p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password[0] }}</p>
				</div>
				<div>
					<label class="block text-sm font-medium text-gray-700 mb-2">
						Confirm Password <span v-if="!isEdit" class="text-red-500">*</span>
						<span v-else class="text-gray-500 text-sm">(Required if changing password)</span>
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
				
			</div>

			<div class="flex justify-end gap-3 mt-6">
				<router-link :to="{ name: 'employees' }" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Cancel</router-link>
				<button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md shadow-sm text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">{{ isEdit ? 'Update' : 'Create' }}</button>
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
	effective_date: null, // Default to today's date
	
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
		effective_date: data.current_salary_structure?.effective_date || null,
	}
	console.log(form.value.effective_date, data.current_salary_structure.effective_date);
	
}

const submit = async () => {
	errors.value = {}
	try {
		let submitData = { ...form.value }
		
		// For edit mode, only include password fields if they have values
		if (isEdit.value) {
			if (!submitData.password) {
				delete submitData.password
				delete submitData.password_confirmation
			}
		}
		
		if (isEdit.value) {
			await axios.put(`/employment/employees/${route.params.id}`, submitData)
		} else {
			await axios.post('/employment/employees', submitData)
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