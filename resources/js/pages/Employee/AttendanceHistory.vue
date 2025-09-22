<template>
	<div class="min-h-screen bg-gray-50 py-8">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
			<!-- Header -->
			<div class="mb-8">
				<h1 class="text-3xl font-bold text-gray-900">Attendance History</h1>
				<p class="mt-2 text-gray-600">View your attendance records and working hours</p>
			</div>

			<!-- Filter Controls -->
			<div class="bg-white shadow rounded-lg p-6 mb-6">
				<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
					<div>
						<label class="block text-sm font-medium text-gray-700 mb-2">Status Filter</label>
						<select
							v-model="statusFilter"
							@change="filterAttendance"
							class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
						>
							<option value="">All Status</option>
							<option value="present">Present</option>
							<option value="absent">Absent</option>
							<option value="late">Late</option>
							<option value="half_day">Half Day</option>
							<option value="leave">Leave</option>
							<option value="holiday">Holiday</option>
						</select>
					</div>
					<div>
						<label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
						<flat-pickr
							v-model="startDateFilter"
							:config="dateConfig"
							placeholder="Select start date"
							@change="filterAttendance"
							class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
						/>
					</div>
					<div>
						<label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
						<flat-pickr
							v-model="endDateFilter"
							:config="dateConfig"
							placeholder="Select end date"
							@change="filterAttendance"
							class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
						/>
					</div>
					<div class="flex items-end">
						<button
							@click="clearFilters"
							class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
						>
							Clear Filters
						</button>
					</div>
				</div>
			</div>

			<!-- Summary Cards -->
			<div v-if="!loading && attendanceRecords.length > 0" class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
				<div class="bg-white overflow-hidden shadow rounded-lg">
					<div class="p-5">
						<div class="flex items-center">
							<div class="flex-shrink-0">
								<div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
									<CheckCircleIcon class="h-5 w-5 text-white" />
								</div>
							</div>
							<div class="ml-5 w-0 flex-1">
								<dl>
									<dt class="text-sm font-medium text-gray-500 truncate">Present Days</dt>
									<dd class="text-lg font-medium text-gray-900">{{ getStatusCount('present') }}</dd>
								</dl>
							</div>
						</div>
					</div>
				</div>

				<div class="bg-white overflow-hidden shadow rounded-lg">
					<div class="p-5">
						<div class="flex items-center">
							<div class="flex-shrink-0">
								<div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
									<XCircleIcon class="h-5 w-5 text-white" />
								</div>
							</div>
							<div class="ml-5 w-0 flex-1">
								<dl>
									<dt class="text-sm font-medium text-gray-500 truncate">Absent Days</dt>
									<dd class="text-lg font-medium text-gray-900">{{ getStatusCount('absent') }}</dd>
								</dl>
							</div>
						</div>
					</div>
				</div>

				<div class="bg-white overflow-hidden shadow rounded-lg">
					<div class="p-5">
						<div class="flex items-center">
							<div class="flex-shrink-0">
								<div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
									<ClockIcon class="h-5 w-5 text-white" />
								</div>
							</div>
							<div class="ml-5 w-0 flex-1">
								<dl>
									<dt class="text-sm font-medium text-gray-500 truncate">Late Days</dt>
									<dd class="text-lg font-medium text-gray-900">{{ getStatusCount('late') }}</dd>
								</dl>
							</div>
						</div>
					</div>
				</div>

				<div class="bg-white overflow-hidden shadow rounded-lg">
					<div class="p-5">
						<div class="flex items-center">
							<div class="flex-shrink-0">
								<div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
									<ClockIcon class="h-5 w-5 text-white" />
								</div>
							</div>
							<div class="ml-5 w-0 flex-1">
								<dl>
									<dt class="text-sm font-medium text-gray-500 truncate">Total Hours</dt>
									<dd class="text-lg font-medium text-gray-900">{{ getTotalHours() }}</dd>
								</dl>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Loading State -->
			<div v-if="loading" class="flex justify-center items-center py-12">
				<div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
			</div>

			<!-- Attendance Table -->
			<div v-else-if="filteredAttendance.length > 0" class="bg-white shadow rounded-lg overflow-hidden">
				<div class="px-6 py-4 border-b border-gray-200">
					<h2 class="text-lg font-semibold text-gray-900 flex items-center">
						<ClockIcon class="h-5 w-5 mr-2 text-blue-600" />
						Attendance Records
						<span class="ml-2 text-sm font-normal text-gray-500">({{ filteredAttendance.length }} records)</span>
					</h2>
				</div>

				<div class="overflow-x-auto">
					<table class="min-w-full divide-y divide-gray-200">
						<thead class="bg-gray-50">
							<tr>
								<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
								<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-in</th>
								<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-out</th>
								<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Hours</th>
								<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
								<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
							</tr>
						</thead>
						<tbody class="bg-white divide-y divide-gray-200">
							<tr
								v-for="attendance in filteredAttendance"
								:key="attendance.id"
								class="hover:bg-gray-50"
							>
								<!-- Date Column -->
								<td class="px-6 py-4 whitespace-nowrap">
									<div class="text-sm font-medium text-gray-900">
										{{ formatDate(attendance.date) }}
									</div>
									<div class="text-sm text-gray-500">
										{{ formatDayOfWeek(attendance.date) }}
									</div>
								</td>

								<!-- Check-in Column -->
								<td class="px-6 py-4 whitespace-nowrap">
									<div v-if="attendance.check_in_time" class="text-sm text-gray-900">
										{{ attendance.check_in_time }}
									</div>
									<div v-else class="text-sm text-gray-400">
										--
									</div>
								</td>

								<!-- Check-out Column -->
								<td class="px-6 py-4 whitespace-nowrap">
									<div v-if="attendance.check_out_time" class="text-sm text-gray-900">
										{{ attendance.check_out_time }}
									</div>
									<div v-else class="text-sm text-gray-400">
										--
									</div>
								</td>

								<!-- Total Hours Column -->
								<td class="px-6 py-4 whitespace-nowrap">
									<div class="text-sm text-gray-900">
										{{ formatHours(attendance.working_hours) }}
									</div>
								</td>

								<!-- Status Column -->
								<td class="px-6 py-4 whitespace-nowrap">
									<span
										:class="[
											'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
											getStatusBadgeClass(attendance.status_color)
										]"
									>
										<div
											:class="[
												'w-2 h-2 rounded-full mr-2',
												getStatusDotClass(attendance.status_color)
											]"
										></div>
										{{ attendance.status_label }}
									</span>
								</td>

								<!-- Remarks Column -->
								<td class="px-6 py-4">
									<div class="text-sm text-gray-900 max-w-xs truncate">
										{{ attendance.remarks || '--' }}
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>

			<!-- Empty State -->
			<div v-else class="text-center py-12">
				<ClockIcon class="mx-auto h-12 w-12 text-gray-400" />
				<h3 class="mt-2 text-sm font-medium text-gray-900">No attendance records found</h3>
				<p class="mt-1 text-sm text-gray-500">
					{{ statusFilter ? `No ${statusFilter} records found.` : 'No attendance records available for the selected period.' }}
				</p>
			</div>

			<!-- Error State -->
			<div v-if="error" class="text-center py-12">
				<ExclamationTriangleIcon class="mx-auto h-12 w-12 text-red-400" />
				<h3 class="mt-2 text-sm font-medium text-gray-900">Error loading attendance</h3>
				<p class="mt-1 text-sm text-gray-500">{{ error }}</p>
				<div class="mt-6">
					<button
						@click="loadAttendance"
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
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import flatPickr from 'vue-flatpickr-component'
import 'flatpickr/dist/flatpickr.css'
import {
	ClockIcon,
	CheckCircleIcon,
	XCircleIcon,
	ExclamationTriangleIcon
} from '@heroicons/vue/24/outline'

// Reusable date configuration
const dateConfig = {
	dateFormat: 'Y-m-d',
	altFormat: 'M j, Y',
	altInput: true,
	allowInput: true,
	clickOpens: true,
	defaultDate: null
}

const loading = ref(true)
const error = ref(null)
const attendanceRecords = ref([])
const statusFilter = ref('')
const startDateFilter = ref('')
const endDateFilter = ref('')

// Computed property for filtered attendance
const filteredAttendance = computed(() => {
	let filtered = [...attendanceRecords.value]

	// Filter by status
	if (statusFilter.value) {
		filtered = filtered.filter(attendance => attendance.status === statusFilter.value)
	}

	// Filter by date range
	if (startDateFilter.value) {
		filtered = filtered.filter(attendance => attendance.date >= startDateFilter.value)
	}
	if (endDateFilter.value) {
		filtered = filtered.filter(attendance => attendance.date <= endDateFilter.value)
	}

	return filtered
})

const loadAttendance = async () => {
	try {
		loading.value = true
		error.value = null
		
		const params = {}
		if (startDateFilter.value) params.start_date = startDateFilter.value
		if (endDateFilter.value) params.end_date = endDateFilter.value
		if (statusFilter.value) params.status = statusFilter.value

		const { data } = await axios.get('/attendances/employee', { params })
		attendanceRecords.value = data
	} catch (err) {
		error.value = err.response?.data?.message || 'Failed to load attendance records'
		console.error('Error loading attendance:', err)
	} finally {
		loading.value = false
	}
}

const filterAttendance = () => {
	// The computed property will automatically update the filtered results
}

const clearFilters = () => {
	statusFilter.value = ''
	startDateFilter.value = ''
	endDateFilter.value = ''
}

const getStatusCount = (status) => {
	return attendanceRecords.value.filter(attendance => attendance.status === status).length
}

const getTotalHours = () => {
	const total = attendanceRecords.value.reduce((sum, attendance) => {
		return parseFloat(sum) + parseFloat(attendance.working_hours || 0)
	}, 0)
    console.log(total);
	return formatHours(total)
}

const formatDate = (date) => {
	if (!date) return null
	return new Date(date).toLocaleDateString('en-US', {
		year: 'numeric',
		month: 'short',
		day: 'numeric'
	})
}

const formatDayOfWeek = (date) => {
	if (!date) return null
	return new Date(date).toLocaleDateString('en-US', {
		weekday: 'long'
	})
}

const formatHours = (hours) => {
	if (!hours || hours === 0) return '0h'
	const h = Math.floor(hours)
	const m = Math.round((hours - h) * 60)
	return m > 0 ? `${h}h ${m}m` : `${h}h`
}

const getStatusBadgeClass = (color) => {
	const classes = {
		green: 'bg-green-100 text-green-800',
		red: 'bg-red-100 text-red-800',
		yellow: 'bg-yellow-100 text-yellow-800',
		blue: 'bg-blue-100 text-blue-800',
		purple: 'bg-purple-100 text-purple-800',
		gray: 'bg-gray-100 text-gray-800'
	}
	return classes[color] || classes.gray
}

const getStatusDotClass = (color) => {
	const classes = {
		green: 'bg-green-400',
		red: 'bg-red-400',
		yellow: 'bg-yellow-400',
		blue: 'bg-blue-400',
		purple: 'bg-purple-400',
		gray: 'bg-gray-400'
	}
	return classes[color] || classes.gray
}

onMounted(() => {
	loadAttendance()
})
</script>
