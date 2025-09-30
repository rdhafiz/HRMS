<template>
	<div class="min-h-screen bg-gray-50 py-8">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
			<!-- Header -->
			<div class="mb-8">
				<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
					<div>
						<h1 class="text-3xl font-bold text-gray-900">My Profile</h1>
						<p class="mt-2 text-gray-600">View your personal information and activity logs</p>
					</div>
					<div class="mt-4 sm:mt-0 flex flex-col sm:flex-row gap-2">
						<router-link
							to="/employee/profile/update"
							class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-green-600 bg-green-100 hover:bg-green-200 transition-colors"
						>
							Update Profile
						</router-link>
						<router-link
							to="/employee/profile/change-password"
							class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-purple-600 bg-purple-100 hover:bg-purple-200 transition-colors"
						>
							Change Password
						</router-link>
					</div>
				</div>
			</div>

			<!-- Loading State -->
			<div v-if="loading" class="flex justify-center items-center py-12">
				<div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
			</div>

			<!-- Profile Content -->
			<div v-else-if="employee" class="space-y-8">
				<!-- Basic Information Card -->
				<div class="bg-white shadow rounded-lg overflow-hidden">
					<div class="px-6 py-4 border-b border-gray-200">
						<h2 class="text-lg font-semibold text-gray-900 flex items-center">
							<UserIcon class="h-5 w-5 mr-2 text-blue-600" />
							Basic Information
						</h2>
					</div>
					<div class="p-6">
						<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
							<!-- Left Side - Avatar and Basic Info -->
							<div class="space-y-6">
								<!-- Avatar and Name -->
								<div class="flex items-center space-x-4">
									<div class="h-20 w-20 bg-blue-100 rounded-full flex items-center justify-center">
										<UserIcon class="h-10 w-10 text-blue-600" />
									</div>
									<div>
										<h3 class="text-xl font-semibold text-gray-900">{{ employee.name }}</h3>
										<p class="text-gray-600">{{ employee.email }}</p>
										<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
											{{ employee.status }}
										</span>
									</div>
								</div>

								<!-- Personal Details -->
								<div class="space-y-4">
									<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
										<div>
											<label class="block text-sm font-medium text-gray-500">Employee Code</label>
											<p class="mt-1 text-sm text-gray-900">{{ employee.employee_code }}</p>
										</div>
										<div>
											<label class="block text-sm font-medium text-gray-500">Phone</label>
											<p class="mt-1 text-sm text-gray-900">{{ employee.phone || 'Not provided' }}</p>
										</div>
									</div>

									<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
										<div>
											<label class="block text-sm font-medium text-gray-500">Date of Birth</label>
											<p class="mt-1 text-sm text-gray-900">{{ formatDate(employee.date_of_birth) || 'Not provided' }}</p>
										</div>
										<div>
											<label class="block text-sm font-medium text-gray-500">Gender</label>
											<p class="mt-1 text-sm text-gray-900 capitalize">{{ employee.gender || 'Not specified' }}</p>
										</div>
									</div>

									<div>
										<label class="block text-sm font-medium text-gray-500">Address</label>
										<p class="mt-1 text-sm text-gray-900">{{ employee.address || 'Not provided' }}</p>
									</div>
								</div>
							</div>

							<!-- Right Side - Work Information -->
							<div class="space-y-6">
								<!-- Work Details -->
								<div class="space-y-4">
									<div>
										<label class="block text-sm font-medium text-gray-500">Department</label>
										<p class="mt-1 text-sm text-gray-900">{{ employee.department?.name || 'Not assigned' }}</p>
									</div>

									<div>
										<label class="block text-sm font-medium text-gray-500">Designation</label>
										<p class="mt-1 text-sm text-gray-900">{{ employee.designation?.name || 'Not assigned' }}</p>
									</div>

									<div>
										<label class="block text-sm font-medium text-gray-500">Join Date</label>
										<p class="mt-1 text-sm text-gray-900">{{ formatDate(employee.join_date) }}</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Salary Structure Card -->
				<div class="bg-white shadow rounded-lg overflow-hidden">
					<div class="px-6 py-4 border-b border-gray-200">
						<h2 class="text-lg font-semibold text-gray-900 flex items-center">
							<CurrencyDollarIcon class="h-5 w-5 mr-2 text-green-600" />
							Salary Structure
						</h2>
					</div>
					<div class="p-6">
						<div v-if="employee.current_salary_structure" class="space-y-6">
							<!-- Salary Structure Info -->
							<div class="bg-gray-50 rounded-lg p-4">
								<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
									<div>
										<label class="block text-sm font-medium text-gray-500">Structure Name</label>
										<p class="mt-1 text-sm font-semibold text-gray-900">{{ employee.current_salary_structure.structure?.name }}</p>
									</div>
									<div>
										<label class="block text-sm font-medium text-gray-500">Effective Date</label>
										<p class="mt-1 text-sm text-gray-900">{{ formatDate(employee.current_salary_structure.effective_date) }}</p>
									</div>
								</div>
							</div>

							<!-- Salary Components -->
							<div v-if="employee.current_salary_structure.structure?.components?.length">
								<h4 class="text-sm font-medium text-gray-900 mb-3">Salary Components</h4>
								<div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
									<table class="min-w-full divide-y divide-gray-300">
										<thead class="bg-gray-50">
											<tr>
												<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Component</th>
												<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
												<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
											</tr>
										</thead>
										<tbody class="bg-white divide-y divide-gray-200">
											<tr v-for="component in employee.current_salary_structure.structure.components" :key="component.id">
												<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
													{{ component.name }}
												</td>
												<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
													<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
														:class="component.type === 'earning' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
														{{ component.type }}
													</span>
												</td>
												<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
													{{ formatCurrency(component.amount) }}
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div v-else class="text-center py-8">
							<CurrencyDollarIcon class="mx-auto h-12 w-12 text-gray-400" />
							<h3 class="mt-2 text-sm font-medium text-gray-900">No salary structure</h3>
							<p class="mt-1 text-sm text-gray-500">No salary structure has been assigned to you yet.</p>
						</div>
					</div>
				</div>

				<!-- Activity Logs Card -->
				<div class="bg-white shadow rounded-lg overflow-hidden">
					<div class="px-6 py-4 border-b border-gray-200">
						<h2 class="text-lg font-semibold text-gray-900 flex items-center">
							<ClockIcon class="h-5 w-5 mr-2 text-purple-600" />
							Activity Logs
						</h2>
					</div>
					<div class="p-6">
						<div v-if="activityLogs.length > 0">
							<div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
								<table class="min-w-full divide-y divide-gray-300">
									<thead class="bg-gray-50">
										<tr>
											<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activity</th>
											<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
											<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
										</tr>
									</thead>
									<tbody class="bg-white divide-y divide-gray-200">
										<tr v-for="log in activityLogs" :key="log.id">
											<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
												{{ formatActivityType(log.type) }}
											</td>
											<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
												{{ log.ip }}
											</td>
											<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
												{{ formatDateTime(log.created_at) }}
											</td>
										</tr>
									</tbody>
								</table>
							</div>

							<!-- Pagination -->
							<div v-if="pagination" class="mt-6 flex items-center justify-between">
								<div class="flex-1 flex justify-between sm:hidden">
									<button
										@click="loadActivityLogs(pagination.current_page - 1)"
										:disabled="!pagination.prev_page_url"
										class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
									>
										Previous
									</button>
									<button
										@click="loadActivityLogs(pagination.current_page + 1)"
										:disabled="!pagination.next_page_url"
										class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
									>
										Next
									</button>
								</div>
								<div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
									<div>
										<p class="text-sm text-gray-700">
											Showing
											<span class="font-medium">{{ pagination.from }}</span>
											to
											<span class="font-medium">{{ pagination.to }}</span>
											of
											<span class="font-medium">{{ pagination.total }}</span>
											results
										</p>
									</div>
									<div>
										<nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
											<button
												@click="loadActivityLogs(pagination.current_page - 1)"
												:disabled="!pagination.prev_page_url"
												class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
											>
												<ChevronLeftIcon class="h-5 w-5" />
											</button>
											<button
												@click="loadActivityLogs(pagination.current_page + 1)"
												:disabled="!pagination.next_page_url"
												class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
											>
												<ChevronRightIcon class="h-5 w-5" />
											</button>
										</nav>
									</div>
								</div>
							</div>
						</div>
						<div v-else class="text-center py-8">
							<ClockIcon class="mx-auto h-12 w-12 text-gray-400" />
							<h3 class="mt-2 text-sm font-medium text-gray-900">No activity logs</h3>
							<p class="mt-1 text-sm text-gray-500">No activity logs found for your account.</p>
						</div>
					</div>
				</div>
			</div>

			<!-- Error State -->
			<div v-else-if="error" class="text-center py-12">
				<ExclamationTriangleIcon class="mx-auto h-12 w-12 text-red-400" />
				<h3 class="mt-2 text-sm font-medium text-gray-900">Error loading profile</h3>
				<p class="mt-1 text-sm text-gray-500">{{ error }}</p>
				<div class="mt-6">
					<button
						@click="loadProfile"
						class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
					>
						Try Again
					</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import {
	UserIcon,
	CurrencyDollarIcon,
	ClockIcon,
	ExclamationTriangleIcon,
	ChevronLeftIcon,
	ChevronRightIcon
} from '@heroicons/vue/24/outline'

const loading = ref(true)
const error = ref(null)
const employee = ref(null)
const activityLogs = ref([])
const pagination = ref(null)

const loadProfile = async () => {
	try {
		loading.value = true
		error.value = null
		
		const { data } = await axios.get('/employee/profile')
		employee.value = data
		
		// Load activity logs after profile is loaded
		await loadActivityLogs()
	} catch (err) {
		error.value = err.response?.data?.message || 'Failed to load profile'
		console.error('Error loading profile:', err)
	} finally {
		loading.value = false
	}
}

const loadActivityLogs = async (page = 1) => {
	try {
		const { data } = await axios.get('/employee/activity-logs', {
			params: { page, per_page: 10 }
		})
		
		activityLogs.value = data.data
		pagination.value = {
			current_page: data.current_page,
			last_page: data.last_page,
			per_page: data.per_page,
			total: data.total,
			from: data.from,
			to: data.to,
			prev_page_url: data.prev_page_url,
			next_page_url: data.next_page_url
		}
	} catch (err) {
		console.error('Error loading activity logs:', err)
	}
}

const formatDate = (date) => {
	if (!date) return null
	return new Date(date).toLocaleDateString('en-US', {
		year: 'numeric',
		month: 'long',
		day: 'numeric'
	})
}

const formatDateTime = (date) => {
	if (!date) return null
	return new Date(date).toLocaleString('en-US', {
		year: 'numeric',
		month: 'short',
		day: 'numeric',
		hour: '2-digit',
		minute: '2-digit'
	})
}

const formatCurrency = (amount) => {
	if (!amount) return 'N/A'
	return new Intl.NumberFormat('en-US', {
		style: 'currency',
		currency: 'USD'
	}).format(amount)
}

const formatActivityType = (type) => {
	const types = {
		'login': 'User Login',
		'logout': 'User Logout',
		'profile_update': 'Profile Updated',
		'password_change': 'Password Changed',
		'leave_request': 'Leave Request',
		'attendance_checkin': 'Attendance Check-in',
		'attendance_checkout': 'Attendance Check-out'
	}
	return types[type] || type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())
}

onMounted(() => {
	loadProfile()
})
</script>
