<template>
	<div class="p-4">
		<div class="flex items-center justify-between mb-6">
			<h1 class="text-2xl font-semibold">Pay Slip History</h1>
			<router-link
				:to="{ name: 'payroll.generate' }"
				class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
			>
				Generate New
			</router-link>
		</div>

		<!-- Filters -->
		<div class="bg-white rounded-lg shadow p-6 mb-6">
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
				<div>
					<label class="block text-sm font-medium mb-2">Employee</label>
					<select v-model="filters.employee_id" class="border rounded px-3 py-2 w-full">
						<option :value="null">All Employees</option>
						<option v-for="emp in employees" :key="emp.id" :value="emp.id">
							{{ emp.first_name }} {{ emp.last_name }}
						</option>
					</select>
				</div>

				<div>
					<label class="block text-sm font-medium mb-2">Department</label>
					<select v-model="filters.department_id" class="border rounded px-3 py-2 w-full">
						<option :value="null">All Departments</option>
						<option v-for="dept in departments" :key="dept.id" :value="dept.id">
							{{ dept.name }}
						</option>
					</select>
				</div>

				<div>
					<label class="block text-sm font-medium mb-2">Period Type</label>
					<select v-model="filters.period_type" class="border rounded px-3 py-2 w-full">
						<option value="">All Types</option>
						<option value="month">Monthly</option>
						<option value="week">Weekly</option>
					</select>
				</div>

				<div>
					<label class="block text-sm font-medium mb-2">Status</label>
					<select v-model="filters.status" class="border rounded px-3 py-2 w-full">
						<option value="">All Status</option>
						<option value="Pending">Pending</option>
						<option value="Paid">Paid</option>
					</select>
				</div>
			</div>

			<div class="mt-4 flex items-center space-x-4">
				<div class="flex-1">
					<input
						v-model="filters.search"
						type="text"
						placeholder="Search by employee name or code..."
						class="border rounded px-3 py-2 w-full"
					/>
				</div>
				<button
					@click="loadPaySlips"
					class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
				>
					Filter
				</button>
				<button
					@click="resetFilters"
					class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-50"
				>
					Reset
				</button>
			</div>
		</div>

		<!-- Pay Slips Table -->
		<div class="bg-white rounded-lg shadow overflow-hidden">
			<div class="overflow-x-auto">
				<table class="min-w-full divide-y divide-gray-200">
					<thead class="bg-gray-50">
						<tr>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
								Employee
							</th>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
								Period
							</th>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
								Basic
							</th>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
								Allowances
							</th>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
								Deductions
							</th>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
								Net Salary
							</th>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
								Status
							</th>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
								Actions
							</th>
						</tr>
					</thead>
					<tbody class="bg-white divide-y divide-gray-200">
						<tr v-for="payslip in paySlips" :key="payslip.id" class="hover:bg-gray-50">
							<td class="px-6 py-4 whitespace-nowrap">
								<div>
									<div class="text-sm font-medium text-gray-900">
										{{ payslip.employee.first_name }} {{ payslip.employee.last_name }}
									</div>
									<div class="text-sm text-gray-500">{{ payslip.employee.employee_code }}</div>
									<div class="text-xs text-gray-400">{{ payslip.employee.department?.name || 'No Department' }}</div>
								</div>
							</td>
							<td class="px-6 py-4 whitespace-nowrap">
								<div class="text-sm text-gray-900">{{ payslip.period_label }}</div>
								<div class="text-xs text-gray-500">{{ payslip.period_type }}</div>
							</td>
							<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
								₹{{ Number(payslip.basic).toLocaleString() }}
							</td>
							<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
								₹{{ Number(payslip.total_allowances).toLocaleString() }}
							</td>
							<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
								₹{{ Number(payslip.total_deductions).toLocaleString() }}
							</td>
							<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
								₹{{ Number(payslip.net_salary).toLocaleString() }}
							</td>
							<td class="px-6 py-4 whitespace-nowrap">
								<span
									:class="{
										'bg-yellow-100 text-yellow-800': payslip.status === 'Pending',
										'bg-green-100 text-green-800': payslip.status === 'Paid'
									}"
									class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
								>
									{{ payslip.status }}
								</span>
							</td>
							<td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
								<div class="flex space-x-2">
									<button
										@click="viewPaySlip(payslip)"
										class="text-blue-600 hover:text-blue-900"
									>
										View
									</button>
									<button
										@click="downloadPaySlip(payslip)"
										class="text-green-600 hover:text-green-900"
									>
										Download
									</button>
									<button
										v-if="payslip.status === 'Pending'"
										@click="markAsPaid(payslip)"
										class="text-purple-600 hover:text-purple-900"
									>
										Mark Paid
									</button>
									<button
										@click="regeneratePaySlip(payslip)"
										class="text-orange-600 hover:text-orange-900"
									>
										Regenerate
									</button>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<!-- Pagination -->
			<div v-if="pagination" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
				<div class="flex-1 flex justify-between sm:hidden">
					<button
						@click="loadPaySlips(pagination.current_page - 1)"
						:disabled="!pagination.prev_page_url"
						class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
					>
						Previous
					</button>
					<button
						@click="loadPaySlips(pagination.current_page + 1)"
						:disabled="!pagination.next_page_url"
						class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
					>
						Next
					</button>
				</div>
				<div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
					<div>
						<p class="text-sm text-gray-700">
							Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} results
						</p>
					</div>
					<div>
						<nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
							<button
								v-for="page in visiblePages"
								:key="page"
								@click="loadPaySlips(page)"
								:class="{
									'bg-blue-50 border-blue-500 text-blue-600': page === pagination.current_page,
									'bg-white border-gray-300 text-gray-500 hover:bg-gray-50': page !== pagination.current_page
								}"
								class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
							>
								{{ page }}
							</button>
						</nav>
					</div>
				</div>
			</div>
		</div>

		<!-- Pay Slip Modal -->
		<div v-if="selectedPaySlip" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
			<div class="bg-white rounded-lg max-w-4xl w-full mx-4 max-h-[90vh] overflow-hidden">
				<div class="flex items-center justify-between p-6 border-b">
					<h3 class="text-lg font-semibold">Pay Slip Details</h3>
					<button @click="selectedPaySlip = null" class="text-gray-400 hover:text-gray-600">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
						</svg>
					</button>
				</div>
				<div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
					<!-- Pay slip details will be shown here -->
					<div class="text-center text-gray-500">
						Pay slip details view will be implemented here
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

// Data
const paySlips = ref([])
const employees = ref([])
const departments = ref([])
const pagination = ref(null)
const selectedPaySlip = ref(null)
const loading = ref(false)

// Filters
const filters = ref({
	employee_id: null,
	department_id: null,
	period_type: '',
	status: '',
	search: ''
})

// Computed
const visiblePages = computed(() => {
	if (!pagination.value) return []
	const current = pagination.value.current_page
	const last = pagination.value.last_page
	const pages = []
	
	for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
		pages.push(i)
	}
	
	return pages
})

// Methods
const loadPaySlips = async (page = 1) => {
	loading.value = true
	try {
		const params = {
			page,
			...filters.value
		}
		
		// Remove null values
		Object.keys(params).forEach(key => {
			if (params[key] === null || params[key] === '') {
				delete params[key]
			}
		})

		const { data } = await axios.get('/employment/pay-slips', { params })
		
		if (data.data) {
			paySlips.value = data.data
			pagination.value = {
				current_page: data.current_page,
				last_page: data.last_page,
				from: data.from,
				to: data.to,
				total: data.total,
				prev_page_url: data.prev_page_url,
				next_page_url: data.next_page_url
			}
		} else {
			paySlips.value = data
			pagination.value = null
		}
	} catch (error) {
		console.error('Failed to load pay slips:', error)
		alert('Failed to load pay slips')
	} finally {
		loading.value = false
	}
}

const loadEmployees = async () => {
	try {
		const { data } = await axios.get('/employment/pay-slips/employees')
		employees.value = data
	} catch (error) {
		console.error('Failed to load employees:', error)
	}
}

const loadDepartments = async () => {
	try {
		const { data } = await axios.get('/employment/departments')
		departments.value = data.data || data
	} catch (error) {
		console.error('Failed to load departments:', error)
	}
}

const resetFilters = () => {
	filters.value = {
		employee_id: null,
		department_id: null,
		period_type: '',
		status: '',
		search: ''
	}
	loadPaySlips()
}

const viewPaySlip = (payslip) => {
	selectedPaySlip.value = payslip
}

const downloadPaySlip = async (payslip) => {
	try {
		const response = await axios.get(`/employment/pay-slips/${payslip.id}/download`, {
			responseType: 'json'
		})
		
		// const url = window.URL.createObjectURL(new Blob([response.data]))
		const link = document.createElement('a')
		link.href = response.data.pdf
		link.setAttribute('download', response.data.name)
		document.body.appendChild(link)
		link.click()
		link.remove()
	} catch (error) {
		console.error('Failed to download pay slip:', error)
		alert('Failed to download pay slip')
	}
}

const markAsPaid = async (payslip) => {
	if (!confirm('Mark this pay slip as paid?')) return

	try {
		await axios.put(`/employment/pay-slips/${payslip.id}/status`, {
			status: 'Paid'
		})
		
		payslip.status = 'Paid'
		payslip.paid_at = new Date().toISOString()
		
		alert('Pay slip marked as paid')
	} catch (error) {
		console.error('Failed to mark pay slip as paid:', error)
		alert('Failed to mark pay slip as paid')
	}
}

const regeneratePaySlip = async (payslip) => {
	if (!confirm('Regenerate this pay slip? This will update the existing record.')) return

	try {
		await axios.post(`/employment/pay-slips/${payslip.id}/regenerate`, {
			reason: 'Manual regeneration'
		})
		
		alert('Pay slip regenerated successfully')
		loadPaySlips()
	} catch (error) {
		console.error('Failed to regenerate pay slip:', error)
		alert('Failed to regenerate pay slip')
	}
}

// Lifecycle
onMounted(async () => {
	await Promise.all([
		loadPaySlips(),
		loadEmployees(),
		loadDepartments()
	])
})
</script>
