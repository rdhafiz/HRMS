<template>
	<div class="p-4">
		<div class="flex items-center justify-between mb-6">
			<h1 class="text-2xl font-semibold">Generate Pay Slips</h1>
		</div>

		<div class="bg-white rounded-lg shadow p-6">
			<form @submit.prevent="generatePaySlips" class="space-y-6">
				<!-- Period Selection -->
				<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
					<div>
						<label class="block text-sm font-medium mb-2">Period Type</label>
						<select v-model="form.period_type" @change="onPeriodTypeChange" class="border rounded px-3 py-2 w-full">
							<option value="month">Monthly</option>
							<option value="week">Weekly</option>
						</select>
					</div>

					<!-- Monthly Period -->
					<div v-if="form.period_type === 'month'" class="grid grid-cols-2 gap-4">
						<div>
							<label class="block text-sm font-medium mb-2">Month</label>
							<select v-model="form.period_month" class="border rounded px-3 py-2 w-full">
								<option v-for="month in months" :key="month.value" :value="month.value">
									{{ month.label }}
								</option>
							</select>
						</div>
						<div>
							<label class="block text-sm font-medium mb-2">Year</label>
							<select v-model="form.period_year" class="border rounded px-3 py-2 w-full">
								<option v-for="year in years" :key="year" :value="year">{{ year }}</option>
							</select>
						</div>
					</div>

					<!-- Weekly Period -->
					<div v-if="form.period_type === 'week'" class="grid grid-cols-2 gap-4">
						<div>
							<label class="block text-sm font-medium mb-2">Week Start</label>
							<flat-pickr
								v-model="form.period_week_start"
								:config="dateConfig"
								placeholder="Select start date"
								class="border rounded px-3 py-2 w-full"
							/>
						</div>
						<div>
							<label class="block text-sm font-medium mb-2">Week End</label>
							<flat-pickr
								v-model="form.period_week_end"
								:config="{ ...dateConfig, minDate: form.period_week_start }"
								placeholder="Select end date"
								class="border rounded px-3 py-2 w-full"
							/>
						</div>
					</div>
				</div>

				<!-- Department Filter -->
				<div>
					<label class="block text-sm font-medium mb-2">Department (Optional)</label>
					<select v-model="form.department_id" @change="loadEmployees" class="border rounded px-3 py-2 w-full">
						<option :value="null">All Departments</option>
						<option v-for="dept in departments" :key="dept.id" :value="dept.id">
							{{ dept.name }}
						</option>
					</select>
				</div>

				<!-- Employee Selection -->
				<div>
					<label class="block text-sm font-medium mb-2">Select Employees</label>
					<div class="border rounded p-4 max-h-64 overflow-y-auto">
						<div class="mb-3">
							<label class="flex items-center">
								<input
									type="checkbox"
									v-model="selectAll"
									@change="toggleSelectAll"
									class="mr-2"
								/>
								<span class="font-medium">Select All ({{ filteredEmployees.length }} employees)</span>
							</label>
						</div>
						<div class="space-y-2">
							<label
								v-for="employee in filteredEmployees"
								:key="employee.id"
								class="flex items-center p-2 hover:bg-gray-50 rounded"
							>
								<input
									type="checkbox"
									:value="employee.id"
									v-model="form.employee_ids"
									class="mr-3"
								/>
								<div>
									<div class="font-medium">{{ employee.first_name }} {{ employee.last_name }}</div>
									<div class="text-sm text-gray-600">
										{{ employee.employee_code }} - {{ employee.department?.name || 'No Department' }}
									</div>
									<div v-if="employee.current_salary_structure" class="text-xs text-green-600">
										Salary Structure: {{ employee.current_salary_structure.structure?.name }}
									</div>
									<div v-else class="text-xs text-red-600">
										No Salary Structure Assigned
									</div>
								</div>
							</label>
						</div>
					</div>
				</div>

				<!-- Overrides (Optional) -->
				<div class="border rounded p-4">
					<h3 class="font-medium mb-3">Salary Overrides (Optional)</h3>
					<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
						<div>
							<label class="block text-sm font-medium mb-2">Basic Salary Override</label>
							<input
								v-model.number="form.overrides.basic"
								type="number"
								step="0.01"
								placeholder="Override basic salary"
								class="border rounded px-3 py-2 w-full"
							/>
						</div>
						<div>
							<label class="block text-sm font-medium mb-2">Additional Allowances (JSON)</label>
							<textarea
								v-model="overridesText.allowances"
								placeholder='{"Bonus": 5000, "Overtime": 2000}'
								class="border rounded px-3 py-2 w-full"
								rows="2"
							></textarea>
						</div>
						<div>
							<label class="block text-sm font-medium mb-2">Additional Deductions (JSON)</label>
							<textarea
								v-model="overridesText.deductions"
								placeholder='{"Late Fee": 100, "Advance": 1000}'
								class="border rounded px-3 py-2 w-full"
								rows="2"
							></textarea>
						</div>
					</div>
				</div>

				<!-- Options -->
				<div class="flex items-center space-x-4">
					<label class="flex items-center">
						<input
							type="checkbox"
							v-model="form.generate_pdf"
							class="mr-2"
						/>
						<span>Generate PDF</span>
					</label>
					<label class="flex items-center">
						<input
							type="checkbox"
							v-model="form.skip_existing"
							class="mr-2"
						/>
						<span>Skip existing pay slips</span>
					</label>
				</div>

				<!-- Action Buttons -->
				<div class="flex justify-end space-x-4">
					<button
						type="button"
						@click="resetForm"
						class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-50"
					>
						Reset
					</button>
					<button
						type="submit"
						:disabled="!canGenerate || loading"
						class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50"
					>
						{{ loading ? 'Generating...' : 'Generate Pay Slips' }}
					</button>
				</div>
			</form>
		</div>

		<!-- Results Modal -->
		<div v-if="showResults" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
			<div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4">
				<h3 class="text-lg font-semibold mb-4">Generation Results</h3>
				<div class="space-y-2">
					<p><strong>Total Employees:</strong> {{ results.total }}</p>
					<p><strong>Successfully Generated:</strong> {{ results.success }}</p>
					<p v-if="results.errors > 0" class="text-red-600">
						<strong>Errors:</strong> {{ results.errors }}
					</p>
				</div>
				<div class="flex justify-end mt-6">
					<button
						@click="showResults = false; $router.push({ name: 'payroll.payslips' })"
						class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
					>
						View Pay Slips
					</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import flatPickr from 'vue-flatpickr-component'
import 'flatpickr/dist/flatpickr.css'

// Date configuration
const dateConfig = {
	dateFormat: 'Y-m-d',
	altFormat: 'M j, Y',
	altInput: true,
	allowInput: true,
	clickOpens: true,
	defaultDate: null
}

const router = useRouter()

// Form data
const form = ref({
	period_type: 'month',
	period_month: new Date().getMonth() + 1,
	period_year: new Date().getFullYear(),
	period_week_start: null,
	period_week_end: null,
	department_id: null,
	employee_ids: [],
	overrides: {
		basic: null,
		allowances: {},
		deductions: {}
	},
	generate_pdf: true,
	skip_existing: true
})

// Override text inputs for JSON
const overridesText = ref({
	allowances: '',
	deductions: ''
})

// Data
const departments = ref([])
const employees = ref([])
const loading = ref(false)
const selectAll = ref(false)
const showResults = ref(false)
const results = ref({})

// Computed
const filteredEmployees = computed(() => {
	if (form.value.department_id) {
		return employees.value.filter(emp => emp.department_id === form.value.department_id)
	}
	return employees.value
})

const canGenerate = computed(() => {
	return form.value.employee_ids.length > 0 && 
		   (form.value.period_type === 'month' || 
		    (form.value.period_week_start && form.value.period_week_end))
})

// Months and years
const months = [
	{ value: 1, label: 'January' },
	{ value: 2, label: 'February' },
	{ value: 3, label: 'March' },
	{ value: 4, label: 'April' },
	{ value: 5, label: 'May' },
	{ value: 6, label: 'June' },
	{ value: 7, label: 'July' },
	{ value: 8, label: 'August' },
	{ value: 9, label: 'September' },
	{ value: 10, label: 'October' },
	{ value: 11, label: 'November' },
	{ value: 12, label: 'December' }
]

const years = computed(() => {
	const currentYear = new Date().getFullYear()
	return Array.from({ length: 5 }, (_, i) => currentYear - 2 + i)
})

// Methods
const loadDepartments = async () => {
	try {
		const { data } = await axios.get('/employment/departments')
		departments.value = data.data || data
	} catch (error) {
		console.error('Failed to load departments:', error)
	}
}

const loadEmployees = async () => {
	try {
		const params = {}
		if (form.value.department_id) {
			params.department_id = form.value.department_id
		}
		const { data } = await axios.get('/employment/pay-slips/employees', { params })
		employees.value = data
	} catch (error) {
		console.error('Failed to load employees:', error)
	}
}

const onPeriodTypeChange = () => {
	form.value.employee_ids = []
	selectAll.value = false
}

const toggleSelectAll = () => {
	if (selectAll.value) {
		form.value.employee_ids = filteredEmployees.value
			.filter(emp => emp.current_salary_structure)
			.map(emp => emp.id)
	} else {
		form.value.employee_ids = []
	}
}

const generatePaySlips = async () => {
	if (!canGenerate.value) return

	loading.value = true

	try {
		// Parse JSON overrides
		const overrides = { ...form.value.overrides }
		
		if (overridesText.value.allowances) {
			try {
				overrides.allowances = JSON.parse(overridesText.value.allowances)
			} catch (e) {
				alert('Invalid allowances JSON format')
				loading.value = false
				return
			}
		}

		if (overridesText.value.deductions) {
			try {
				overrides.deductions = JSON.parse(overridesText.value.deductions)
			} catch (e) {
				alert('Invalid deductions JSON format')
				loading.value = false
				return
			}
		}

		const payload = {
			...form.value,
			overrides
		}

		const { data } = await axios.post('/employment/pay-slips/generate-batch', payload)

		results.value = {
			total: form.value.employee_ids.length,
			success: data.employee_count,
			errors: 0
		}

		showResults.value = true

	} catch (error) {
		console.error('Failed to generate pay slips:', error)
		alert('Failed to generate pay slips: ' + (error.response?.data?.message || error.message))
	} finally {
		loading.value = false
	}
}

const resetForm = () => {
	form.value = {
		period_type: 'month',
		period_month: new Date().getMonth() + 1,
		period_year: new Date().getFullYear(),
		period_week_start: null,
		period_week_end: null,
		department_id: null,
		employee_ids: [],
		overrides: {
			basic: null,
			allowances: {},
			deductions: {}
		},
		generate_pdf: true,
		skip_existing: true
	}
	overridesText.value = {
		allowances: '',
		deductions: ''
	}
	selectAll.value = false
}

// Watch for employee selection changes
watch(() => form.value.employee_ids, (newIds) => {
	selectAll.value = newIds.length === filteredEmployees.value.filter(emp => emp.current_salary_structure).length
})

// Lifecycle
onMounted(async () => {
	await loadDepartments()
	await loadEmployees()
})
</script>
