<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
          <h1 class="text-2xl font-bold text-gray-900">Send Email Notification</h1>
          <p class="mt-1 text-sm text-gray-600">Send notifications to employees via email</p>
        </div>

        <form @submit.prevent="sendNotification" class="p-6 space-y-6">
          <!-- Recipient Selection -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Recipients <span class="text-red-500">*</span>
            </label>
            
            <div class="space-y-4">
              <!-- Recipient Type Selection -->
              <div class="flex space-x-4">
                <label class="flex items-center">
                  <input
                    v-model="form.recipient_type"
                    type="radio"
                    value="all"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                  />
                  <span class="ml-2 text-sm text-gray-700">All Employees</span>
                </label>
                <label class="flex items-center">
                  <input
                    v-model="form.recipient_type"
                    type="radio"
                    value="department"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                  />
                  <span class="ml-2 text-sm text-gray-700">Specific Department</span>
                </label>
                <label class="flex items-center">
                  <input
                    v-model="form.recipient_type"
                    type="radio"
                    value="specific"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                  />
                  <span class="ml-2 text-sm text-gray-700">Selected Employees</span>
                </label>
              </div>

              <!-- Department Selection -->
              <div v-if="form.recipient_type === 'department'" class="mt-4">
                <select
                  v-model="form.department_id"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                  :class="{ 'border-red-300': errors.department_id }"
                >
                  <option value="">Select Department</option>
                  <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                    {{ dept.name }}
                  </option>
                </select>
                <p v-if="errors.department_id" class="mt-1 text-sm text-red-600">{{ errors.department_id }}</p>
              </div>

              <!-- Employee Selection -->
              <div v-if="form.recipient_type === 'specific'" class="mt-4">
                <div class="space-y-2">
                  <div class="flex items-center justify-between">
                    <label class="text-sm font-medium text-gray-700">Select Employees</label>
                    <button
                      type="button"
                      @click="selectAllEmployees"
                      class="text-sm text-blue-600 hover:text-blue-500"
                    >
                      Select All
                    </button>
                  </div>
                  
                  <div class="max-h-60 overflow-y-auto border border-gray-300 rounded-md p-2">
                    <div v-if="loadingEmployees" class="text-center py-4">
                      <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mx-auto"></div>
                    </div>
                    <div v-else class="space-y-2">
                      <label
                        v-for="employee in employees"
                        :key="employee.id"
                        class="flex items-center p-2 hover:bg-gray-50 rounded"
                      >
                        <input
                          v-model="form.employee_ids"
                          type="checkbox"
                          :value="employee.id"
                          class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        />
                        <div class="ml-3">
                          <div class="text-sm font-medium text-gray-900">{{ employee.name }}</div>
                          <div class="text-xs text-gray-500">{{ employee.email }} • {{ employee.department_name }}</div>
                        </div>
                      </label>
                    </div>
                  </div>
                  
                  <p v-if="errors.employee_ids" class="text-sm text-red-600">{{ errors.employee_ids }}</p>
                </div>
              </div>

              <!-- Recipients Preview -->
              <div v-if="recipientsPreview.length > 0" class="mt-4 p-3 bg-blue-50 rounded-md">
                <p class="text-sm font-medium text-blue-900 mb-2">
                  {{ recipientsPreview.length }} recipient(s) selected:
                </p>
                <div class="text-xs text-blue-700">
                  <span v-for="(recipient, index) in recipientsPreview.slice(0, 5)" :key="index">
                    {{ recipient.name }}<span v-if="index < Math.min(recipientsPreview.length, 5) - 1">, </span>
                  </span>
                  <span v-if="recipientsPreview.length > 5"> and {{ recipientsPreview.length - 5 }} more...</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Subject -->
          <div>
            <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
              Subject <span class="text-red-500">*</span>
            </label>
            <input
              id="subject"
              v-model="form.subject"
              type="text"
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              :class="{ 'border-red-300': errors.subject }"
              placeholder="Enter email subject"
            />
            <p v-if="errors.subject" class="mt-1 text-sm text-red-600">{{ errors.subject }}</p>
          </div>

          <!-- Body -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Message Body <span class="text-red-500">*</span>
            </label>
            <!-- Try TipTap first, fallback to textarea if there are issues -->
            <SimpleTipTapEditor
              v-if="!useTextArea"
              v-model="form.body"
              placeholder="Type your message here..."
              :class="{ 'border-red-300': errors.body }"
            />
            <TextAreaEditor
              v-else
              v-model="form.body"
              placeholder="Type your message here..."
              :class="{ 'border-red-300': errors.body }"
            />
            <p v-if="errors.body" class="mt-1 text-sm text-red-600">{{ errors.body }}</p>
          </div>

          <!-- Attachments -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Attachments (Optional)
            </label>
            <FileUpload
              v-model="form.attachments"
              :max-files="5"
              :max-size="10 * 1024 * 1024"
              accepted-types=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif"
            />
          </div>

          <!-- Submit Buttons -->
          <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
            <button
              type="button"
              @click="$router.go(-1)"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="loading"
              class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="loading" class="flex items-center">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Sending...
              </span>
              <span v-else>Send Notification</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import SimpleTipTapEditor from '../components/SimpleTipTapEditor.vue'
import TextAreaEditor from '../components/TextAreaEditor.vue'
import FileUpload from '../components/FileUpload.vue'

const router = useRouter()

const loading = ref(false)
const loadingEmployees = ref(false)
const departments = ref([])
const employees = ref([])
const useTextArea = ref(false) // Set to true if TipTap has issues

const form = reactive({
  recipient_type: 'all',
  department_id: '',
  employee_ids: [],
  subject: '',
  body: '<p></p>', // Initialize with empty paragraph to avoid null
  attachments: []
})

const errors = ref({})

const recipientsPreview = computed(() => {
  if (form.recipient_type === 'all') {
    return employees.value
  } else if (form.recipient_type === 'department') {
    return employees.value.filter(emp => emp.department_id == form.department_id)
  } else if (form.recipient_type === 'specific') {
    return employees.value.filter(emp => form.employee_ids.includes(emp.id))
  }
  return []
})

// Watch for department changes to load employees
watch(() => form.department_id, (newDeptId) => {
  if (form.recipient_type === 'department' && newDeptId) {
    loadEmployees(newDeptId)
  }
})

// Watch for recipient type changes
watch(() => form.recipient_type, (newType) => {
  if (newType === 'all') {
    loadEmployees()
  } else if (newType === 'specific') {
    loadEmployees()
  }
  // Reset selections
  form.department_id = ''
  form.employee_ids = []
})

const loadDepartments = async () => {
  try {
    const response = await axios.get('/email-notifications/departments')
    departments.value = response.data
  } catch (error) {
    console.error('Error loading departments:', error)
  }
}

const loadEmployees = async (departmentId = null) => {
  loadingEmployees.value = true
  try {
    const params = departmentId ? { department_id: departmentId } : {}
    const response = await axios.get('/email-notifications/employees', { params })
    employees.value = response.data
  } catch (error) {
    console.error('Error loading employees:', error)
  } finally {
    loadingEmployees.value = false
  }
}

const selectAllEmployees = () => {
  if (form.recipient_type === 'specific') {
    form.employee_ids = employees.value.map(emp => emp.id)
  }
}

const sendNotification = async () => {
  errors.value = {}
  loading.value = true

  try {
    // Ensure body is not null or empty
    const emailBody = form.body && form.body.trim() !== '' ? form.body : '<p>No content</p>'
    
    const formData = new FormData()
    
    // Add form fields
    formData.append('subject', form.subject)
    formData.append('body', emailBody)
    formData.append('recipient_type', form.recipient_type)
  
    
    if (form.recipient_type === 'department') {
      formData.append('department_id', form.department_id)
    } else if (form.recipient_type === 'specific') {
      form.employee_ids.forEach(id => {
        formData.append('employee_ids[]', id)
      })
    }

    // Add attachments
    form.attachments.forEach(file => {
      formData.append('attachments[]', file)
    })

    const response = await axios.post('/email-notifications', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    // Show success message
    alert('Email notification sent successfully!')
    
    // Redirect to history page
    router.push('/email-notifications/history')
    
  } catch (error) {
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors
    } else {
      alert('Error sending notification. Please try again.')
      console.error('Error sending notification:', error)
    }
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadDepartments()
  loadEmployees()
})
</script>
