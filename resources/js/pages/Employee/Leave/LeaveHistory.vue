<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Leave History</h1>
        <p class="mt-2 text-gray-600">View your leave applications and their status</p>
      </div>

      <!-- Filters -->
      <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-6 py-4">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Filters</h3>
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Status Filter -->
            <div>
              <label for="status_filter" class="block text-sm font-medium text-gray-700 mb-1">
                Status
              </label>
              <select
                id="status_filter"
                v-model="filters.status"
                @change="applyFilters"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
              </select>
            </div>

            <!-- Leave Type Filter -->
            <div>
              <label for="leave_type_filter" class="block text-sm font-medium text-gray-700 mb-1">
                Leave Type
              </label>
              <select
                id="leave_type_filter"
                v-model="filters.leave_type"
                @change="applyFilters"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">All Types</option>
                <option value="sick">Sick Leave</option>
                <option value="casual">Casual Leave</option>
                <option value="paid">Paid Leave</option>
                <option value="unpaid">Unpaid Leave</option>
              </select>
            </div>

            <!-- Start Date Filter -->
            <div>
              <label for="start_date_filter" class="block text-sm font-medium text-gray-700 mb-1">
                From Date
              </label>
              <flatpickr
                v-model="filters.start_date"
                :config="datePickerConfig"
                @change="applyFilters"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                placeholder="Select start date"
              />
            </div>

            <!-- End Date Filter -->
            <div>
              <label for="end_date_filter" class="block text-sm font-medium text-gray-700 mb-1">
                To Date
              </label>
              <flatpickr
                v-model="filters.end_date"
                :config="datePickerConfig"
                @change="applyFilters"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                placeholder="Select end date"
              />
            </div>
          </div>

          <!-- Clear Filters Button -->
          <div class="mt-4">
            <button
              @click="clearFilters"
              class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              Clear Filters
            </button>
          </div>
        </div>
      </div>

      <!-- Leave Applications Table -->
      <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">Leave Applications</h3>
            <router-link
              to="/employee/leaves/apply"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
              </svg>
              Apply for Leave
            </router-link>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="isLoading" class="px-6 py-8 text-center">
          <div class="inline-flex items-center">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Loading leave applications...
          </div>
        </div>

        <!-- Empty State -->
        <div v-else-if="leaveRequests.length === 0" class="px-6 py-8 text-center">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">No leave applications</h3>
          <p class="mt-1 text-sm text-gray-500">Get started by applying for a new leave.</p>
          <div class="mt-6">
            <router-link
              to="/employee/leaves/apply"
              class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
              </svg>
              Apply for Leave
            </router-link>
          </div>
        </div>

        <!-- Table -->
        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Subject
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Leave Type
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Dates
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Status
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Submitted At
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="leaveRequest in leaveRequests" :key="leaveRequest.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">{{ leaveRequest.subject }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                    {{ leaveRequest.leave_type_label }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">{{ leaveRequest.formatted_dates }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getStatusBadgeClass(leaveRequest.status_color)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                    {{ leaveRequest.status_label }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(leaveRequest.created_at) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <button
                    @click="viewDetails(leaveRequest)"
                    class="text-blue-600 hover:text-blue-900 mr-3"
                  >
                    View Details
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="pagination && pagination.total > pagination.per_page" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
          <div class="flex items-center justify-between">
            <div class="flex-1 flex justify-between sm:hidden">
              <button
                @click="goToPage(pagination.current_page - 1)"
                :disabled="pagination.current_page <= 1"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Previous
              </button>
              <button
                @click="goToPage(pagination.current_page + 1)"
                :disabled="pagination.current_page >= pagination.last_page"
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
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                  <button
                    @click="goToPage(pagination.current_page - 1)"
                    :disabled="pagination.current_page <= 1"
                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    Previous
                  </button>
                  <button
                    v-for="page in visiblePages"
                    :key="page"
                    @click="goToPage(page)"
                    :class="[
                      page === pagination.current_page
                        ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                        : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                      'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
                    ]"
                  >
                    {{ page }}
                  </button>
                  <button
                    @click="goToPage(pagination.current_page + 1)"
                    :disabled="pagination.current_page >= pagination.last_page"
                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    Next
                  </button>
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Leave Details Modal -->
      <div v-if="selectedLeaveRequest" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
          <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-medium text-gray-900">Leave Application Details</h3>
              <button @click="selectedLeaveRequest = null" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>
            
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Subject</label>
                <p class="mt-1 text-sm text-gray-900">{{ selectedLeaveRequest.subject }}</p>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700">Leave Type</label>
                <p class="mt-1 text-sm text-gray-900">{{ selectedLeaveRequest.leave_type_label }}</p>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700">Dates</label>
                <p class="mt-1 text-sm text-gray-900">{{ selectedLeaveRequest.formatted_dates }}</p>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <span :class="getStatusBadgeClass(selectedLeaveRequest.status_color)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                  {{ selectedLeaveRequest.status_label }}
                </span>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700">Application Details</label>
                <div class="mt-1 p-3 bg-gray-50 rounded-md">
                  <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ selectedLeaveRequest.application_body }}</p>
                </div>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700">Submitted At</label>
                <p class="mt-1 text-sm text-gray-900">{{ formatDate(selectedLeaveRequest.created_at) }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import flatpickr from 'vue-flatpickr-component'
import 'flatpickr/dist/flatpickr.css'

const leaveRequests = ref([])
const pagination = ref(null)
const isLoading = ref(false)
const selectedLeaveRequest = ref(null)

const filters = ref({
  status: '',
  leave_type: '',
  start_date: '',
  end_date: ''
})

const datePickerConfig = {
  dateFormat: 'Y-m-d',
  allowInput: true,
  clickOpens: true,
}

const visiblePages = computed(() => {
  if (!pagination.value) return []
  
  const current = pagination.value.current_page
  const last = pagination.value.last_page
  const pages = []
  
  // Show up to 5 pages around current page
  const start = Math.max(1, current - 2)
  const end = Math.min(last, current + 2)
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  
  return pages
})

const getStatusBadgeClass = (color) => {
  const classes = {
    green: 'bg-green-100 text-green-800',
    yellow: 'bg-yellow-100 text-yellow-800',
    red: 'bg-red-100 text-red-800',
    gray: 'bg-gray-100 text-gray-800'
  }
  return classes[color] || classes.gray
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const fetchLeaveRequests = async (page = 1) => {
  isLoading.value = true
  
  try {
    const params = new URLSearchParams({
      page: page,
      ...filters.value
    })
    
    // Remove empty filters
    Object.keys(params).forEach(key => {
      if (!params.get(key)) {
        params.delete(key)
      }
    })
    
    const response = await axios.get(`/leaves?${params}`)
    leaveRequests.value = response.data.data
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
      per_page: response.data.per_page,
      total: response.data.total,
      from: response.data.from,
      to: response.data.to
    }
  } catch (error) {
    console.error('Error fetching leave requests:', error)
    alert('Failed to load leave requests. Please try again.')
  } finally {
    isLoading.value = false
  }
}

const applyFilters = () => {
  fetchLeaveRequests(1)
}

const clearFilters = () => {
  filters.value = {
    status: '',
    leave_type: '',
    start_date: '',
    end_date: ''
  }
  fetchLeaveRequests(1)
}

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    fetchLeaveRequests(page)
  }
}

const viewDetails = (leaveRequest) => {
  selectedLeaveRequest.value = leaveRequest
}

onMounted(() => {
  fetchLeaveRequests()
})
</script>
