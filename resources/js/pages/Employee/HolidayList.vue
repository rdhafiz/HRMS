<template>
	<div class="min-h-screen bg-gray-50 py-8">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
			<!-- Header -->
			<div class="mb-8">
				<h1 class="text-3xl font-bold text-gray-900">Holidays</h1>
				<p class="mt-2 text-gray-600">View upcoming and past holidays</p>
			</div>

			<!-- Filter Controls -->
			<div class="bg-white shadow rounded-lg p-6 mb-6">
				<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
					<div>
						<label class="block text-sm font-medium text-gray-700 mb-2">Filter by Status</label>
						<select
							v-model="statusFilter"
							@change="filterHolidays"
							class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
						>
							<option value="all">All Holidays</option>
							<option value="upcoming">Upcoming</option>
							<option value="current">Current</option>
							<option value="past">Past</option>
						</select>
					</div>
					<div>
						<label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
						<flat-pickr
							v-model="startDateFilter"
							:config="dateConfig"
							placeholder="Select start date"
							@change="filterHolidays"
							class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
						/>
					</div>
					<div>
						<label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
						<flat-pickr
							v-model="endDateFilter"
							:config="dateConfig"
							placeholder="Select end date"
							@change="filterHolidays"
							class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
						/>
					</div>
				</div>
				<div class="mt-4 flex justify-end">
					<button
						@click="clearFilters"
						class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
					>
						Clear Filters
					</button>
				</div>
			</div>

			<!-- Loading State -->
			<div v-if="loading" class="flex justify-center items-center py-12">
				<div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
			</div>

			<!-- Holidays Table -->
			<div v-else-if="filteredHolidays.length > 0" class="bg-white shadow rounded-lg overflow-hidden">
				<div class="px-6 py-4 border-b border-gray-200">
					<h2 class="text-lg font-semibold text-gray-900 flex items-center">
						<CalendarDaysIcon class="h-5 w-5 mr-2 text-blue-600" />
						Holiday List
						<span class="ml-2 text-sm font-normal text-gray-500">({{ filteredHolidays.length }} holidays)</span>
					</h2>
				</div>

				<div class="overflow-x-auto">
					<table class="min-w-full divide-y divide-gray-200">
						<thead class="bg-gray-50">
							<tr>
								<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
								<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Holiday Name</th>
								<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Range</th>
								<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
								<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
							</tr>
						</thead>
						<tbody class="bg-white divide-y divide-gray-200">
							<tr
								v-for="holiday in filteredHolidays"
								:key="holiday.id"
								:class="[
									'cursor-pointer hover:bg-gray-50',
									holiday.is_past ? 'opacity-60' : '',
									holiday.is_current ? 'bg-green-50' : '',
									holiday.is_upcoming ? 'bg-blue-50' : ''
								]"
							>
								<!-- Status Column -->
								<td class="px-6 py-4 whitespace-nowrap">
									<div class="flex items-center">
										<div
											:class="[
												'w-3 h-3 rounded-full mr-2',
												holiday.is_past ? 'bg-gray-400' : '',
												holiday.is_current ? 'bg-green-500 animate-pulse' : '',
												holiday.is_upcoming ? 'bg-blue-500' : ''
											]"
										></div>
										<span
											:class="[
												'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
												holiday.is_past ? 'bg-gray-100 text-gray-800' : '',
												holiday.is_current ? 'bg-green-100 text-green-800' : '',
												holiday.is_upcoming ? 'bg-blue-100 text-blue-800' : ''
											]"
										>
											{{ getStatusText(holiday.status) }}
										</span>
									</div>
								</td>

								<!-- Holiday Name Column -->
								<td class="px-6 py-4 whitespace-nowrap">
									<div class="flex items-center">
										<div class="flex-shrink-0 h-10 w-10">
											<div
												:class="[
													'h-10 w-10 rounded-full flex items-center justify-center',
													holiday.is_past ? 'bg-gray-200' : '',
													holiday.is_current ? 'bg-green-200' : '',
													holiday.is_upcoming ? 'bg-blue-200' : ''
												]"
											>
												<CalendarDaysIcon
													:class="[
														'h-5 w-5',
														holiday.is_past ? 'text-gray-500' : '',
														holiday.is_current ? 'text-green-600' : '',
														holiday.is_upcoming ? 'text-blue-600' : ''
													]"
												/>
											</div>
										</div>
										<div class="ml-4">
											<div
												:class="[
													'text-sm font-medium',
													holiday.is_past ? 'text-gray-500' : 'text-gray-900'
												]"
											>
												{{ holiday.name }}
											</div>
										</div>
									</div>
								</td>

								<!-- Date Range Column -->
								<td class="px-6 py-4 whitespace-nowrap">
									<div class="text-sm text-gray-900">
										<div class="font-medium">{{ formatDate(holiday.start_date) }}</div>
										<div v-if="holiday.start_date !== holiday.end_date" class="text-gray-500">
											to {{ formatDate(holiday.end_date) }}
										</div>
									</div>
								</td>

								<!-- Duration Column -->
								<td class="px-6 py-4 whitespace-nowrap">
									<span class="text-sm text-gray-900">
										{{ getDuration(holiday.start_date, holiday.end_date) }}
									</span>
								</td>

								<!-- Description Column -->
								<td class="px-6 py-4">
									<div
										:class="[
											'text-sm',
											holiday.is_past ? 'text-gray-500' : 'text-gray-900'
										]"
									>
										{{ holiday.description || 'No description available' }}
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>

			<!-- Empty State -->
			<div v-else class="text-center py-12">
				<CalendarDaysIcon class="mx-auto h-12 w-12 text-gray-400" />
				<h3 class="mt-2 text-sm font-medium text-gray-900">No holidays found</h3>
				<p class="mt-1 text-sm text-gray-500">
					{{ statusFilter !== 'all' ? `No ${statusFilter} holidays found.` : 'No holidays available for your department.' }}
				</p>
			</div>

			<!-- Error State -->
			<div v-if="error" class="text-center py-12">
				<ExclamationTriangleIcon class="mx-auto h-12 w-12 text-red-400" />
				<h3 class="mt-2 text-sm font-medium text-gray-900">Error loading holidays</h3>
				<p class="mt-1 text-sm text-gray-500">{{ error }}</p>
				<div class="mt-6">
					<button
						@click="loadHolidays"
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
	CalendarDaysIcon,
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
const holidays = ref([])
const statusFilter = ref('all')
const startDateFilter = ref('')
const endDateFilter = ref('')

// Computed property for filtered holidays
const filteredHolidays = computed(() => {
	let filtered = [...holidays.value]

	// Filter by status
	if (statusFilter.value !== 'all') {
		filtered = filtered.filter(holiday => holiday.status === statusFilter.value)
	}

	// Filter by date range
	if (startDateFilter.value) {
		filtered = filtered.filter(holiday => holiday.start_date >= startDateFilter.value)
	}
	if (endDateFilter.value) {
		filtered = filtered.filter(holiday => holiday.end_date <= endDateFilter.value)
	}

	return filtered
})

const loadHolidays = async () => {
	try {
		loading.value = true
		error.value = null
		
		const params = {}
		if (startDateFilter.value) params.start_date = startDateFilter.value
		if (endDateFilter.value) params.end_date = endDateFilter.value

		const { data } = await axios.get('/holidays/employee', { params })
		holidays.value = data
	} catch (err) {
		error.value = err.response?.data?.message || 'Failed to load holidays'
		console.error('Error loading holidays:', err)
	} finally {
		loading.value = false
	}
}

const filterHolidays = () => {
	// The computed property will automatically update the filtered results
}

const clearFilters = () => {
	statusFilter.value = 'all'
	startDateFilter.value = ''
	endDateFilter.value = ''
}

const formatDate = (date) => {
	if (!date) return null
	return new Date(date).toLocaleDateString('en-US', {
		year: 'numeric',
		month: 'long',
		day: 'numeric'
	})
}

const getDuration = (startDate, endDate) => {
	if (!startDate || !endDate) return 'N/A'
	
	const start = new Date(startDate)
	const end = new Date(endDate)
	const diffTime = Math.abs(end - start)
	const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1
	
	if (diffDays === 1) {
		return '1 day'
	} else {
		return `${diffDays} days`
	}
}

const getStatusText = (status) => {
	const statusMap = {
		'past': 'Past',
		'current': 'Current',
		'upcoming': 'Upcoming'
	}
	return statusMap[status] || status
}

onMounted(() => {
	loadHolidays()
})
</script>
