<template>
  <div class="min-h-screen">
    <!-- Header Section -->
    <div class="bg-white shadow-sm border border-gray-200">
      <div class="w-full px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumbs -->
        <nav class="flex py-4" aria-label="Breadcrumb">
          <ol class="flex items-center space-x-2">
            <li>
              <router-link to="/dashboard" class="text-gray-400 hover:text-indigo-600 transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                </svg>
              </router-link>
            </li>
            <li>
              <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
              </svg>
            </li>
            <li>
              <router-link to="/departments" class="text-gray-500 hover:text-indigo-600 transition-colors font-medium">Departments</router-link>
            </li>
            <li>
              <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
              </svg>
            </li>
            <li>
              <span class="text-indigo-600 font-semibold">{{ id ? 'Edit' : 'Create' }} Department</span>
            </li>
          </ol>
        </nav>
        
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6">
          <div class="flex items-center space-x-3">
            <div class="p-2 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
              </svg>
            </div>
            <div>
              <h1 class="text-2xl font-bold text-gray-900">{{ id ? 'Edit' : 'Create' }} Department</h1>
              <p class="text-gray-600 mt-1">{{ id ? 'Update department information and settings' : 'Add a new department to organize your team structure' }}</p>
            </div>
          </div>
          <div class="mt-4 sm:mt-0">
            <router-link to="/departments" 
              class="inline-flex items-center px-4 py-2 bg-white text-gray-700 font-medium rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
              </svg>
              Back to Departments
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="w-full py-2">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Success Alert -->
        <div v-if="successMessage" class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-green-800">{{ successMessage }}</p>
            </div>
          </div>
        </div>

        <!-- Error Alert -->
        <div v-if="errorMessage" class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707 2.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-red-800">{{ errorMessage }}</p>
            </div>
          </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white shadow-sm border border-gray-200">
          <!-- Loading State -->
          <div v-if="loading && !form.name" class="p-8">
            <div class="animate-pulse space-y-6">
              <div class="space-y-2">
                <div class="h-4 bg-gray-200 rounded w-1/4"></div>
                <div class="h-10 bg-gray-200 rounded"></div>
              </div>
              <div class="space-y-2">
                <div class="h-4 bg-gray-200 rounded w-1/3"></div>
                <div class="h-20 bg-gray-200 rounded"></div>
              </div>
              <div class="flex space-x-4">
                <div class="h-10 bg-gray-200 rounded w-24"></div>
                <div class="h-10 bg-gray-200 rounded w-24"></div>
              </div>
            </div>
          </div>

          <!-- Form Content -->
          <form v-else @submit.prevent="submit" class="p-8">
            <div class="space-y-6">
              <!-- Department Name Field -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  <div class="flex items-center space-x-2">
                    <div class="p-1 bg-blue-100 rounded">
                      <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                      </svg>
                    </div>
                    <span>Department Name</span>
                    <span class="text-red-500">*</span>
                  </div>
                </label>
                <div class="relative">
                  <input 
                    v-model="form.name" 
                    type="text" 
                    required 
                    maxlength="191"
                    placeholder="Enter department name (e.g., Human Resources, Engineering)"
                    :class="[
                      'block w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-gray-900 placeholder-gray-500',
                      errors.name ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300'
                    ]"
                  />
                  <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg v-if="form.name && !errors.name" class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <svg v-else-if="errors.name" class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                  </div>
                </div>
                <p v-if="errors.name" class="mt-2 text-sm text-red-600 flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                  </svg>
                  {{ errors.name }}
                </p>
                <p v-else-if="form.name" class="mt-1 text-xs text-gray-500">
                  {{ form.name.length }}/191 characters
                </p>
              </div>

              <!-- Description Field -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  <div class="flex items-center space-x-2">
                    <div class="p-1 bg-purple-100 rounded">
                      <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                      </svg>
                    </div>
                    <span>Description</span>
                    <span class="text-gray-400 text-xs">(Optional)</span>
                  </div>
                </label>
                <div class="relative">
                  <textarea 
                    v-model="form.description" 
                    rows="4"
                    placeholder="Describe the department's purpose, responsibilities, and scope..."
                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-gray-900 placeholder-gray-500 resize-none"
                  ></textarea>
                  <div class="absolute bottom-3 right-3 text-xs text-gray-400">
                    {{ form.description.length }} characters
                  </div>
                </div>
                <p class="mt-1 text-xs text-gray-500">
                  Provide a brief description of what this department does and its main responsibilities.
                </p>
              </div>

              <!-- Form Actions -->
              <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                <button 
                  type="submit" 
                  :disabled="loading || !form.name.trim()"
                  class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-lg hover:from-indigo-700 hover:to-purple-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 disabled:transform-none"
                >
                  <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <svg v-else class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                  </svg>
                  {{ loading ? 'Saving...' : (id ? 'Update Department' : 'Create Department') }}
                </button>
                
                <router-link 
                  to="/departments" 
                  class="inline-flex items-center justify-center px-6 py-3 bg-white text-gray-700 font-medium rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors"
                >
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                  Cancel
                </router-link>
              </div>
            </div>
          </form>
        </div>

        <!-- Additional Info Card -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-6">
          <div class="flex items-start">
            <div class="flex-shrink-0">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-blue-800">Department Management Tips</h3>
              <div class="mt-2 text-sm text-blue-700">
                <ul class="list-disc list-inside space-y-1">
                  <li>Use clear, descriptive names that reflect the department's purpose</li>
                  <li>Provide detailed descriptions to help team members understand roles</li>
                  <li>You can add designations to departments after creation</li>
                  <li>Department names should be unique across your organization</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()
const id = route.params.id
const form = ref({ name: '', description: '' })
const errors = ref({})
const loading = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

async function load() {
  if (!id) return
  
  loading.value = true
  try {
    const { data } = await axios.get(`/employment/departments/${id}`)
    form.value = { name: data.name, description: data.description || '' }
  } catch (error) {
    console.error('Error loading department:', error)
    errorMessage.value = 'Failed to load department data. Please try again.'
  } finally {
    loading.value = false
  }
}

async function submit() {
  loading.value = true
  errors.value = {}
  successMessage.value = ''
  errorMessage.value = ''
  
  try {
    if (id) {
      await axios.put(`/employment/departments/${id}`, form.value)
      successMessage.value = 'Department updated successfully!'
    } else {
      await axios.post('/employment/departments', form.value)
      successMessage.value = 'Department created successfully!'
    }
    
    // Clear form for new departments
    if (!id) {
      form.value = { name: '', description: '' }
    }
    
    // Redirect after a short delay to show success message
    setTimeout(() => {
      router.push('/departments')
    }, 1500)
    
  } catch (error) {
    console.error('Error saving department:', error)
    
    if (error.response?.data?.errors) {
      // Handle validation errors
      errors.value = Object.fromEntries(
        Object.entries(error.response.data.errors).map(([k, v]) => [k, v[0]])
      )
    } else if (error.response?.data?.message) {
      // Handle general error messages
      errorMessage.value = error.response.data.message
    } else {
      // Handle network or other errors
      errorMessage.value = 'An unexpected error occurred. Please try again.'
    }
  } finally {
    loading.value = false
  }
}

// Clear messages when form changes
function clearMessages() {
  successMessage.value = ''
  errorMessage.value = ''
  errors.value = {}
}

// Watch for form changes to clear messages
import { watch } from 'vue'
watch(form, clearMessages, { deep: true })

onMounted(load)
</script>

<style scoped>
/* Custom animations */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fadeInUp {
  animation: fadeInUp 0.5s ease-out;
}

/* Form field focus effects */
input:focus, textarea:focus {
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

/* Loading spinner animation */
@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}

/* Button hover effects */
button:not(:disabled):hover {
  transform: translateY(-1px);
}

button:not(:disabled):active {
  transform: translateY(0);
}

/* Character counter styling */
.textarea-counter {
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(4px);
}
</style>

