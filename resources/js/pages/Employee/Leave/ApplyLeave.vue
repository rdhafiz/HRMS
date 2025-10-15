<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Apply for Leave</h1>
        <p class="mt-2 text-gray-600">Submit your leave application for review</p>
      </div>

      <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-8">
          <form @submit.prevent="submitLeaveApplication" class="space-y-6">
            <!-- Subject Field -->
            <div>
              <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                Application Subject <span class="text-red-500">*</span>
              </label>
              <input
                type="text"
                id="subject"
                v-model="form.subject"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                placeholder="Enter leave application subject"
              />
              <p v-if="errors.subject" class="mt-1 text-sm text-red-600">{{ errors.subject }}</p>
            </div>

            <!-- Application Body -->
            <div>
              <label for="application_body" class="block text-sm font-medium text-gray-700 mb-2">
                Application Details / Reason <span class="text-red-500">*</span>
              </label>
              <textarea
                id="application_body"
                v-model="form.application_body"
                required
                rows="4"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                placeholder="Please provide detailed reason for your leave application..."
              ></textarea>
              <p v-if="errors.application_body" class="mt-1 text-sm text-red-600">{{ errors.application_body }}</p>
            </div>

            <!-- Leave Type -->
            <div>
              <label for="leave_type" class="block text-sm font-medium text-gray-700 mb-2">
                Leave Type <span class="text-red-500">*</span>
              </label>
              <select
                id="leave_type"
                v-model="form.leave_type"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">Select leave type</option>
                <option value="sick">Sick Leave</option>
                <option value="casual">Casual Leave</option>
                <option value="paid">Paid Leave</option>
                <option value="unpaid">Unpaid Leave</option>
              </select>
              <p v-if="errors.leave_type" class="mt-1 text-sm text-red-600">{{ errors.leave_type }}</p>
            </div>

            <!-- Date Range Picker -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Select Leave Date Range <span class="text-red-500">*</span>
                {{ form.specific_dates }}
              </label>
              <flatpickr
                v-model="form.selectedDates"
                :config="datePickerConfig"
                @change="changeDates"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                placeholder="Select date range"
              />
              <p v-if="errors.start_date" class="mt-1 text-sm text-red-600">{{ errors.start_date }}</p>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-3 pt-6">
              <button
                type="button"
                @click="resetForm"
                class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                Reset
              </button>
              <button
                type="submit"
                :disabled="isSubmitting"
                class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <span v-if="isSubmitting">Submitting...</span>
                <span v-else>Submit Application</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import flatpickr from 'vue-flatpickr-component'
import 'flatpickr/dist/flatpickr.css'

const router = useRouter()

const form = ref({
  subject: '',
  application_body: '',
  selectedDates: '',
  start_date: '',
  end_date: '',
  leave_type: ''
})

const errors = ref({})
const isSubmitting = ref(false)

const datePickerConfig = computed(() => {
  return {
    altInput: true,
    altFormat: "F j, Y",
    dateFormat: "Y-m-d",
    mode: 'range'
  }
})

const changeDates = () => {
  const selectedDates = form.value.selectedDates.split(' to ');
  form.value.start_date = selectedDates[0] ?? ''
  form.value.end_date = selectedDates[1] ?? ''
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const resetForm = () => {
  form.value = {
    subject: '',
    application_body: '',
    specific_dates: [],
    leave_type: ''
  }
  errors.value = {}
}

const submitLeaveApplication = async () => {
  isSubmitting.value = true
  errors.value = {}

  // Validate form before submission
  if (!validateForm()) {
    isSubmitting.value = false
    return
  }

  try {
    // Prepare form data with proper date array
    const formData = {
      ...form.value,
      date_type: 'range',
      specific_dates: Array.isArray(form.value.specific_dates) ? form.value.specific_dates : []
    }

    const response = await axios.post('/leaves', formData)
    
    // Show success message
    alert('Leave application submitted successfully!')
    
    // Reset form
    resetForm()
    
    // Redirect to leave history
    router.push('/employee/leaves/history')
  } catch (error) {
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors || {}
    } else {
      alert('An error occurred while submitting your application. Please try again.')
    }
  } finally {
    isSubmitting.value = false
  }
}

const validateForm = () => {
  const newErrors = {}

  // Validate required fields
  if (!form.value.subject.trim()) {
    newErrors.subject = 'Application subject is required'
  }

  if (!form.value.application_body.trim()) {
    newErrors.application_body = 'Application details are required'
  }

  if (!form.value.leave_type) {
    newErrors.leave_type = 'Leave type is required'
  }

  if (!form.value.start_date) {
    newErrors.start_date = 'Start date is required'
  }

  errors.value = newErrors
  return Object.keys(newErrors).length === 0
}

</script>
