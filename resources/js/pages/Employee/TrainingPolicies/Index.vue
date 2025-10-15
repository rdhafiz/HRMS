<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Training & Policies</h1>
            <p class="mt-2 text-gray-600">Complete your training modules and review company policies</p>
          </div>
          <div class="mt-4 sm:mt-0">
            <button
              @click="saveProgress"
              :disabled="isSaving || !hasChanges"
              class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              <svg v-if="isSaving" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <svg v-else class="-ml-1 mr-3 h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
              {{ isSaving ? 'Saving...' : 'Save Progress' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Error loading training policies</h3>
        <p class="mt-1 text-sm text-gray-500">{{ error }}</p>
        <div class="mt-6">
          <button
            @click="loadTrainingPolicies"
            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            Try Again
          </button>
        </div>
      </div>

      <!-- Training Policies Content -->
      <div v-else-if="categories.length > 0" class="space-y-8">
        <div v-for="category in categories" :key="category.id" class="bg-white shadow rounded-lg overflow-hidden">
          <!-- Category Header -->
          <div class="px-6 py-4 border-b border-gray-200" :class="category.type === 'training' ? 'bg-blue-50' : 'bg-purple-50'">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
              <!-- Training Category Icon -->
              <svg v-if="category.type === 'training'" class="h-5 w-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
              </svg>
              <!-- Policy Category Icon -->
              <svg v-else class="h-5 w-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              {{ category.title }}
              <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                    :class="category.type === 'training' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'">
                {{ category.type === 'training' ? 'Training Category' : 'Policy Category' }}
              </span>
            </h2>
          </div>

          <!-- Category Content -->
          <div class="p-6">
            <!-- Parent Category Training Policies -->
            <div v-if="category.trainings_and_policies && category.trainings_and_policies.length > 0" class="mb-8">
              <div class="mb-4">
                <h3 class="text-md font-medium text-gray-800 flex items-center">
                  <svg class="h-4 w-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                  </svg>
                  {{ category.title }} - Direct Items
                </h3>
              </div>

              <!-- Parent Category Training Policies List -->
              <div class="space-y-4">
                <!-- Training Items Section -->
                <div v-if="getGroupedTrainingPolicies(category.trainings_and_policies).trainings.length > 0" class="space-y-3">
                  <div class="flex items-center space-x-2 mb-3">
                    <div class="h-px bg-blue-200 flex-1"></div>
                    <span class="text-xs font-medium text-blue-600 uppercase tracking-wider">Training Items</span>
                    <div class="h-px bg-blue-200 flex-1"></div>
                  </div>
                  <div
                    v-for="trainingPolicy in getGroupedTrainingPolicies(category.trainings_and_policies).trainings"
                    :key="trainingPolicy.id"
                    class="flex items-start space-x-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                  >
                    <!-- Checkbox -->
                    <div class="flex-shrink-0 mt-1">
                      <input
                        :id="`training-policy-${trainingPolicy.id}`"
                        v-model="selectedTrainingPolicies"
                        :value="trainingPolicy.id"
                        type="checkbox"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded transition-colors"
                      />
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                      <label
                        :for="`training-policy-${trainingPolicy.id}`"
                        class="block text-sm font-medium text-gray-900 cursor-pointer"
                      >
                        {{ trainingPolicy.title }}
                      </label>
                      <p v-if="trainingPolicy.description" class="mt-1 text-sm text-gray-600">
                        {{ trainingPolicy.description }}
                      </p>
                      <div class="mt-2 flex items-center space-x-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                          Training
                        </span>
                        <span v-if="trainingPolicy.completed" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                          <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                          </svg>
                          Completed
                        </span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Policy Items Section -->
                <div v-if="getGroupedTrainingPolicies(category.trainings_and_policies).policies.length > 0" class="space-y-3">
                  <div class="flex items-center space-x-2 mb-3">
                    <div class="h-px bg-purple-200 flex-1"></div>
                    <span class="text-xs font-medium text-purple-600 uppercase tracking-wider">Policy Items</span>
                    <div class="h-px bg-purple-200 flex-1"></div>
                  </div>
                  <div
                    v-for="trainingPolicy in getGroupedTrainingPolicies(category.trainings_and_policies).policies"
                    :key="trainingPolicy.id"
                    class="flex items-start space-x-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                  >
                    <!-- Checkbox -->
                    <div class="flex-shrink-0 mt-1">
                      <input
                        :id="`training-policy-${trainingPolicy.id}`"
                        v-model="selectedTrainingPolicies"
                        :value="trainingPolicy.id"
                        type="checkbox"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded transition-colors"
                      />
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                      <label
                        :for="`training-policy-${trainingPolicy.id}`"
                        class="block text-sm font-medium text-gray-900 cursor-pointer"
                      >
                        {{ trainingPolicy.title }}
                      </label>
                      <p v-if="trainingPolicy.description" class="mt-1 text-sm text-gray-600">
                        {{ trainingPolicy.description }}
                      </p>
                      <div class="mt-2 flex items-center space-x-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                          Policy
                        </span>
                        <span v-if="trainingPolicy.completed" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                          <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                          </svg>
                          Completed
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Subcategories -->
            <div v-if="category.children && category.children.length > 0">
              <div v-for="subcategory in category.children" :key="subcategory.id" class="mb-8 last:mb-0">
                <!-- Subcategory Header -->
                <div class="mb-4">
                  <h3 class="text-md font-medium text-gray-800 flex items-center">
                    <svg class="h-4 w-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ subcategory.title }}
                  </h3>
                </div>

                <!-- Subcategory Training Policies List -->
                <div v-if="subcategory.trainings_and_policies && subcategory.trainings_and_policies.length > 0" class="ml-4">
                  <div class="space-y-4">
                    <!-- Training Items Section -->
                    <div v-if="getGroupedTrainingPolicies(subcategory.trainings_and_policies).trainings.length > 0" class="space-y-3">
                      <div class="flex items-center space-x-2 mb-3">
                        <div class="h-px bg-blue-200 flex-1"></div>
                        <span class="text-xs font-medium text-blue-600 uppercase tracking-wider">Training Items</span>
                        <div class="h-px bg-blue-200 flex-1"></div>
                      </div>
                      <div
                        v-for="trainingPolicy in getGroupedTrainingPolicies(subcategory.trainings_and_policies).trainings"
                        :key="trainingPolicy.id"
                        class="flex items-start space-x-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                      >
                        <!-- Checkbox -->
                        <div class="flex-shrink-0 mt-1">
                          <input
                            :id="`training-policy-${trainingPolicy.id}`"
                            v-model="selectedTrainingPolicies"
                            :value="trainingPolicy.id"
                            type="checkbox"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded transition-colors"
                          />
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                          <label
                            :for="`training-policy-${trainingPolicy.id}`"
                            class="block text-sm font-medium text-gray-900 cursor-pointer"
                          >
                            {{ trainingPolicy.title }}
                          </label>
                          <p v-if="trainingPolicy.description" class="mt-1 text-sm text-gray-600">
                            {{ trainingPolicy.description }}
                          </p>
                          <div class="mt-2 flex items-center space-x-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                              Training
                            </span>
                            <span v-if="trainingPolicy.completed" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                              <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                              </svg>
                              Completed
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Policy Items Section -->
                    <div v-if="getGroupedTrainingPolicies(subcategory.trainings_and_policies).policies.length > 0" class="space-y-3">
                      <div class="flex items-center space-x-2 mb-3">
                        <div class="h-px bg-purple-200 flex-1"></div>
                        <span class="text-xs font-medium text-purple-600 uppercase tracking-wider">Policy Items</span>
                        <div class="h-px bg-purple-200 flex-1"></div>
                      </div>
                      <div
                        v-for="trainingPolicy in getGroupedTrainingPolicies(subcategory.trainings_and_policies).policies"
                        :key="trainingPolicy.id"
                        class="flex items-start space-x-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                      >
                        <!-- Checkbox -->
                        <div class="flex-shrink-0 mt-1">
                          <input
                            :id="`training-policy-${trainingPolicy.id}`"
                            v-model="selectedTrainingPolicies"
                            :value="trainingPolicy.id"
                            type="checkbox"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded transition-colors"
                          />
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                          <label
                            :for="`training-policy-${trainingPolicy.id}`"
                            class="block text-sm font-medium text-gray-900 cursor-pointer"
                          >
                            {{ trainingPolicy.title }}
                          </label>
                          <p v-if="trainingPolicy.description" class="mt-1 text-sm text-gray-600">
                            {{ trainingPolicy.description }}
                          </p>
                          <div class="mt-2 flex items-center space-x-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                              Policy
                            </span>
                            <span v-if="trainingPolicy.completed" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                              <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                              </svg>
                              Completed
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Empty State for Subcategory -->
                <div v-else class="text-center py-6 text-gray-500 ml-4">
                  <svg class="mx-auto h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                  <p class="mt-2 text-sm">No training policies in this subcategory</p>
                </div>
              </div>
            </div>

            <!-- Empty State for Category (no parent items and no children) -->
            <div v-if="(!category.trainings_and_policies || category.trainings_and_policies.length === 0) && (!category.children || category.children.length === 0)" class="text-center py-8 text-gray-500">
              <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <p class="mt-2 text-sm">No training policies available in this category</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No training policies</h3>
        <p class="mt-1 text-sm text-gray-500">No training policies have been added yet.</p>
      </div>

      <!-- Success Message -->
      <div v-if="successMessage" class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-lg z-50">
        <div class="flex items-center">
          <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
          </svg>
          {{ successMessage }}
        </div>
      </div>

      <!-- Error Message -->
      <div v-if="errorMessage" class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-lg z-50">
        <div class="flex items-center">
          <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
          </svg>
          {{ errorMessage }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

const loading = ref(true)
const error = ref(null)
const categories = ref([])
const selectedTrainingPolicies = ref([])
const isSaving = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

// Computed properties
const hasChanges = computed(() => {
  return selectedTrainingPolicies.value.length > 0
})

const loadTrainingPolicies = async () => {
  try {
    loading.value = true
    error.value = null
    
    const { data } = await axios.get('/employee/training-policies')
    
    if (data.success) {
      // Sort categories: training categories first, then policy categories
      categories.value = data.data.sort((a, b) => {
        // Training categories come first (type === 'training')
        if (a.type === 'training' && b.type === 'policy') {
          return -1
        }
        if (a.type === 'policy' && b.type === 'training') {
          return 1
        }
        // If same type, sort alphabetically by title
        return a.title.localeCompare(b.title)
      })
      
      // Initialize selected training policies with completed ones
      selectedTrainingPolicies.value = []
      categories.value.forEach(category => {
        // Handle parent category trainings and policies
        if (category.trainings_and_policies) {
          category.trainings_and_policies.forEach(trainingPolicy => {
            if (trainingPolicy.completed) {
              selectedTrainingPolicies.value.push(trainingPolicy.id)
            }
          })
        }
        
        // Handle child category trainings and policies
        if (category.children) {
          category.children.forEach(subcategory => {
            if (subcategory.trainings_and_policies) {
              subcategory.trainings_and_policies.forEach(trainingPolicy => {
                if (trainingPolicy.completed) {
                  selectedTrainingPolicies.value.push(trainingPolicy.id)
                }
              })
            }
          })
        }
      })
    } else {
      error.value = data.message || 'Failed to load training policies'
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load training policies'
    console.error('Error loading training policies:', err)
  } finally {
    loading.value = false
  }
}

const saveProgress = async () => {
  try {
    isSaving.value = true
    errorMessage.value = ''
    successMessage.value = ''
    
    const { data } = await axios.post('/employee/training-policies/save', {
      training_policy_ids: selectedTrainingPolicies.value
    })
    
    if (data.success) {
      successMessage.value = data.message || 'Progress saved successfully!'
      
      // Hide success message after 3 seconds
      setTimeout(() => {
        successMessage.value = ''
      }, 3000)
      
      // Reload data to update completion status
      await loadTrainingPolicies()
    } else {
      errorMessage.value = data.message || 'Failed to save progress'
    }
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'Failed to save progress'
    console.error('Error saving progress:', err)
  } finally {
    isSaving.value = false
  }
}

// Function to sort training policies: trainings first, then policies
const getSortedTrainingPolicies = (trainingPolicies) => {
  if (!trainingPolicies || !Array.isArray(trainingPolicies)) {
    return []
  }
  
  return [...trainingPolicies].sort((a, b) => {
    // Training items come first (type === 'training')
    if (a.type === 'training' && b.type === 'policy') {
      return -1
    }
    if (a.type === 'policy' && b.type === 'training') {
      return 1
    }
    // If same type, sort alphabetically by title
    return a.title.localeCompare(b.title)
  })
}

// Function to group training policies by type for better visual separation
const getGroupedTrainingPolicies = (trainingPolicies) => {
  if (!trainingPolicies || !Array.isArray(trainingPolicies)) {
    return { trainings: [], policies: [] }
  }
  
  const sorted = getSortedTrainingPolicies(trainingPolicies)
  return {
    trainings: sorted.filter(item => item.type === 'training'),
    policies: sorted.filter(item => item.type === 'policy')
  }
}

onMounted(() => {
  loadTrainingPolicies()
})
</script>
