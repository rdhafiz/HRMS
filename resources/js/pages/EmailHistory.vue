<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">Email History</h1>
              <p class="mt-1 text-sm text-gray-600">View all sent email notifications</p>
            </div>
            <button
              @click="$router.push('/email-notifications/send')"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
              </svg>
              Send New
            </button>
          </div>
        </div>

        <!-- Filters -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
              <input
                v-model="filters.search"
                type="text"
                placeholder="Search by subject..."
                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                @input="debouncedSearch"
              />
            </div>

            <!-- Date From -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
              <input
                v-model="filters.date_from"
                type="date"
                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                @change="loadNotifications"
              />
            </div>

            <!-- Date To -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
              <input
                v-model="filters.date_to"
                type="date"
                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                @change="loadNotifications"
              />
            </div>

            <!-- Department Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
              <select
                v-model="filters.department_id"
                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                @change="loadNotifications"
              >
                <option value="">All Departments</option>
                <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                  {{ dept.name }}
                </option>
              </select>
            </div>
          </div>

          <div class="mt-4 flex justify-end">
            <button
              @click="clearFilters"
              class="text-sm text-gray-600 hover:text-gray-800"
            >
              Clear Filters
            </button>
          </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Subject
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Sender
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Recipients
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Sent Date
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-if="loading" class="animate-pulse">
                <td colspan="5" class="px-6 py-4 text-center">
                  <div class="flex justify-center">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                  </div>
                </td>
              </tr>
              <tr v-else-if="notifications.length === 0">
                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                  No notifications found
                </td>
              </tr>
              <tr v-else v-for="notification in notifications" :key="notification.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">{{ notification.subject }}</div>
                  <div v-if="notification.attachments && notification.attachments.length > 0" class="text-xs text-gray-500">
                    {{ notification.attachments.length }} attachment(s)
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">{{ notification.sender.name }}</div>
                  <div class="text-xs text-gray-500">{{ notification.sender.email }}</div>
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm text-gray-900">
                    {{ notification.recipients.length }} recipient(s)
                  </div>
                  <div class="text-xs text-gray-500">
                    {{ getRecipientsPreview(notification.recipients) }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(notification.sent_at) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <button
                    @click="viewNotification(notification)"
                    class="text-blue-600 hover:text-blue-900 mr-3"
                  >
                    View
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="pagination" class="px-6 py-4 border-t border-gray-200">
          <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
              Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} results
            </div>
            <div class="flex space-x-2">
              <button
                @click="loadNotifications(pagination.current_page - 1)"
                :disabled="!pagination.prev_page_url"
                class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Previous
              </button>
              <button
                @click="loadNotifications(pagination.current_page + 1)"
                :disabled="!pagination.next_page_url"
                class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Next
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- View Modal -->
    <div v-if="selectedNotification" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" @click="closeModal">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
          <div class="bg-white px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-medium text-gray-900">{{ selectedNotification.subject }}</h3>
              <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>
            <div class="mt-2 text-sm text-gray-500">
              From: {{ selectedNotification.sender.name }} ({{ selectedNotification.sender.email }})<br>
              Sent: {{ formatDate(selectedNotification.sent_at) }}<br>
              Recipients: {{ selectedNotification.recipients.length }} people
            </div>
          </div>

          <div class="bg-white px-6 py-4 max-h-96 overflow-y-auto">
            <!-- Recipients List -->
            <div class="mb-6">
              <h4 class="text-sm font-medium text-gray-900 mb-2">Recipients:</h4>
              <div class="max-h-32 overflow-y-auto border border-gray-200 rounded-md p-2">
                <div v-for="recipient in selectedNotification.recipients" :key="recipient.employee_id" class="text-sm text-gray-700 py-1">
                  {{ recipient.name }} ({{ recipient.email }}) - {{ recipient.department_name }}
                </div>
              </div>
            </div>

            <!-- Attachments -->
            <div v-if="selectedNotification.attachments && selectedNotification.attachments.length > 0" class="mb-6">
              <h4 class="text-sm font-medium text-gray-900 mb-2">Attachments:</h4>
              <div class="space-y-2">
                <div v-for="attachment in selectedNotification.attachments" :key="attachment.path" class="flex items-center text-sm text-gray-700">
                  <svg class="w-4 h-4 mr-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                  </svg>
                  {{ attachment.original_name }} ({{ formatFileSize(attachment.size) }})
                </div>
              </div>
            </div>

            <!-- Message Body -->
            <div>
              <h4 class="text-sm font-medium text-gray-900 mb-2">Message:</h4>
              <div class="prose prose-sm max-w-none" v-html="selectedNotification.body"></div>
            </div>
          </div>

          <div class="bg-gray-50 px-6 py-3 flex justify-end">
            <button
              @click="closeModal"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import axios from 'axios'

const loading = ref(false)
const notifications = ref([])
const departments = ref([])
const selectedNotification = ref(null)
const pagination = ref(null)

const filters = reactive({
  search: '',
  date_from: '',
  date_to: '',
  department_id: ''
})

let searchTimeout = null

const debouncedSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    loadNotifications()
  }, 500)
}

const loadNotifications = async (page = 1) => {
  loading.value = true
  try {
    const params = {
      page,
      ...filters
    }
    
    // Remove empty filters
    Object.keys(params).forEach(key => {
      if (params[key] === '' || params[key] === null || params[key] === undefined) {
        delete params[key]
      }
    })

    const response = await axios.get('/email-notifications', { params })
    notifications.value = response.data.data
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
      per_page: response.data.per_page,
      total: response.data.total,
      from: response.data.from,
      to: response.data.to,
      prev_page_url: response.data.prev_page_url,
      next_page_url: response.data.next_page_url
    }
  } catch (error) {
    console.error('Error loading notifications:', error)
  } finally {
    loading.value = false
  }
}

const loadDepartments = async () => {
  try {
    const response = await axios.get('/email-notifications/departments')
    departments.value = response.data
  } catch (error) {
    console.error('Error loading departments:', error)
  }
}

const viewNotification = async (notification) => {
  try {
    const response = await axios.get(`/email-notifications/${notification.id}`)
    selectedNotification.value = response.data
  } catch (error) {
    console.error('Error loading notification details:', error)
  }
}

const closeModal = () => {
  selectedNotification.value = null
}

const clearFilters = () => {
  filters.search = ''
  filters.date_from = ''
  filters.date_to = ''
  filters.department_id = ''
  loadNotifications()
}

const getRecipientsPreview = (recipients) => {
  if (recipients.length <= 3) {
    return recipients.map(r => r.name).join(', ')
  }
  return `${recipients.slice(0, 3).map(r => r.name).join(', ')} and ${recipients.length - 3} more`
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

onMounted(() => {
  loadNotifications()
  loadDepartments()
})
</script>
