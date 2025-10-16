<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
          <div class="flex items-center space-x-4">
            <router-link 
              :to="{ name: 'employees' }" 
              class="inline-flex items-center px-2 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
            </router-link>
            <h1 class="text-3xl font-bold text-gray-900">Employee Profile</h1>
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
        <h3 class="mt-2 text-sm font-medium text-gray-900">Error loading employee profile</h3>
        <p class="mt-1 text-sm text-gray-500">{{ error }}</p>
        <div class="mt-6">
          <button
            @click="loadEmployeeData"
            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            Try Again
          </button>
        </div>
      </div>

      <!-- Employee Profile Content -->
      <div v-else-if="employee" class="space-y-8">
        <!-- Profile Header Card -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
          <div class="px-6 py-8 bg-gradient-to-r from-blue-600 to-indigo-700">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
              <div class="flex items-center space-x-6">
                <!-- Avatar -->
                <div class="h-24 w-24 bg-white rounded-full flex items-center justify-center shadow-lg">
                  <img 
                    v-if="employee.user?.avatar" 
                    :src="employee.user.avatar" 
                    :alt="employee.name"
                    class="h-22 w-22 rounded-full object-cover"
                  />
                  <svg v-else class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                </div>
                
                <!-- Employee Info -->
                <div class="text-white">
                  <h2 class="text-2xl font-bold">{{ employee.first_name }} {{ employee.last_name }}</h2>
                  <p class="text-blue-100 text-lg">{{ employee.designation?.name || 'No Designation' }}</p>
                  <p class="text-blue-200">{{ employee.department?.name || 'No Department' }}</p>
                  <div class="mt-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                          :class="employee.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                      {{ employee.status || 'Unknown' }}
                    </span>
                  </div>
                </div>
              </div>
              
              <!-- Quick Stats -->
              <div class="mt-6 sm:mt-0">
                <div class="grid grid-cols-2 gap-4 text-center">
                  <div class="bg-white bg-opacity-20 rounded-lg p-4">
                    <div class="text-2xl font-bold text-white">{{ formatDate(employee.join_date) }}</div>
                    <div class="text-blue-100 text-sm">Join Date</div>
                  </div>
                  <div class="bg-white bg-opacity-20 rounded-lg p-4">
                    <div class="text-2xl font-bold text-white">{{ employee.employee_code }}</div>
                    <div class="text-blue-100 text-sm">Employee Code</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Information Cards Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <!-- Personal Information Card -->
          <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
              <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="h-5 w-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Personal Information
              </h3>
            </div>
            <div class="p-6 space-y-4">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-500">Full Name</label>
                  <p class="mt-1 text-sm text-gray-900">{{ employee.first_name }} {{ employee.last_name }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-500">Email Address</label>
                  <p class="mt-1 text-sm text-gray-900">{{ employee.user?.email || 'Not provided' }}</p>
                </div>
              </div>
              
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-500">Phone Number</label>
                  <p class="mt-1 text-sm text-gray-900">{{ employee.phone || 'Not provided' }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-500">Date of Birth</label>
                  <p class="mt-1 text-sm text-gray-900">{{ formatDate(employee.date_of_birth) || 'Not provided' }}</p>
                </div>
              </div>
              
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-500">Gender</label>
                  <p class="mt-1 text-sm text-gray-900 capitalize">{{ employee.gender || 'Not specified' }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-500">Account Source</label>
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                        :class="employee.user?.account_source === 'microsoft_login' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'">
                    {{ employee.user?.account_source === 'microsoft_login' ? 'Microsoft Login' : 'Regular Account' }}
                  </span>
                </div>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-500">Address</label>
                <p class="mt-1 text-sm text-gray-900">{{ employee.address || 'Not provided' }}</p>
              </div>
            </div>
          </div>

          <!-- Professional Information Card -->
          <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
              <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="h-5 w-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                </svg>
                Professional Information
              </h3>
            </div>
            <div class="p-6 space-y-4">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-500">Employee Code</label>
                  <p class="mt-1 text-sm text-gray-900">{{ employee.employee_code }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-500">Join Date</label>
                  <p class="mt-1 text-sm text-gray-900">{{ formatDate(employee.join_date) }}</p>
                </div>
		</div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-500">Department</label>
                  <p class="mt-1 text-sm text-gray-900">{{ employee.department?.name || 'Not assigned' }}</p>
			</div>
                <div>
                  <label class="block text-sm font-medium text-gray-500">Designation</label>
                  <p class="mt-1 text-sm text-gray-900">{{ employee.designation?.name || 'Not assigned' }}</p>
			</div>
		</div>

              <div>
                <label class="block text-sm font-medium text-gray-500">Employment Status</label>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium mt-1"
                      :class="employee.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                  {{ employee.status || 'Unknown' }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Salary Structure Card -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
              <svg class="h-5 w-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
              </svg>
              Salary Structure
            </h3>
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
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">No salary structure</h3>
              <p class="mt-1 text-sm text-gray-500">No salary structure has been assigned to this employee yet.</p>
            </div>
          </div>
        </div>

        <!-- Training & Policies Section -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
              <svg class="h-5 w-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
              </svg>
              Training & Policies Progress
            </h3>
          </div>
          <div class="p-6">
            <!-- Loading State for Training Policies -->
            <div v-if="trainingPoliciesLoading" class="flex justify-center items-center py-8">
              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
            </div>

            <!-- Training Policies Content -->
            <div v-else-if="trainingPolicies.length > 0" class="space-y-6">
              <div v-for="category in trainingPolicies" :key="category.id" class="border border-gray-200 rounded-lg overflow-hidden">
                <!-- Category Header -->
                <div class="px-4 py-3 border-b border-gray-200" :class="category.type === 'training' ? 'bg-blue-50' : 'bg-purple-50'">
                  <h4 class="text-md font-semibold text-gray-900 flex items-center">
                    <!-- Training Category Icon -->
                    <svg v-if="category.type === 'training'" class="h-4 w-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <!-- Policy Category Icon -->
                    <svg v-else class="h-4 w-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    {{ category.title }}
                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                          :class="category.type === 'training' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'">
                      {{ category.type === 'training' ? 'Training' : 'Policy' }}
                    </span>
                  </h4>
                </div>

                <!-- Category Content -->
                <div class="p-4">
                  <!-- Parent Category Items -->
                  <div v-if="category.trainings_and_policies && category.trainings_and_policies.length > 0" class="mb-6">
                    <div class="space-y-3">
                      <div
                        v-for="trainingPolicy in getGroupedTrainingPolicies(category.trainings_and_policies).trainings.concat(getGroupedTrainingPolicies(category.trainings_and_policies).policies)"
                        :key="trainingPolicy.id"
                        class="flex items-center space-x-3 p-3 border border-gray-200 rounded-lg"
                      >
                        <!-- Completion Status -->
                        <div class="flex-shrink-0">
                          <div v-if="trainingPolicy.completed" class="h-6 w-6 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="h-4 w-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                          </div>
                          <div v-else class="h-6 w-6 bg-gray-100 rounded-full flex items-center justify-center">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                          </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                          <p class="text-sm font-medium text-gray-900">{{ trainingPolicy.title }}</p>
                          <p v-if="trainingPolicy.description" class="text-xs text-gray-600 mt-1">{{ trainingPolicy.description }}</p>
                        </div>

                        <!-- Type Badge -->
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                              :class="trainingPolicy.type === 'training' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'">
                          {{ trainingPolicy.type === 'training' ? 'Training' : 'Policy' }}
                        </span>
                      </div>
                    </div>
                  </div>

                  <!-- Child Categories -->
                  <div v-if="category.children && category.children.length > 0">
                    <div v-for="subcategory in category.children" :key="subcategory.id" class="mb-4">
                      <h5 class="text-sm font-medium text-gray-800 mb-2 ml-2">{{ subcategory.title }}</h5>
                      <div v-if="subcategory.trainings_and_policies && subcategory.trainings_and_policies.length > 0" class="space-y-2 ml-4">
                        <div
                          v-for="trainingPolicy in getGroupedTrainingPolicies(subcategory.trainings_and_policies).trainings.concat(getGroupedTrainingPolicies(subcategory.trainings_and_policies).policies)"
                          :key="trainingPolicy.id"
                          class="flex items-center space-x-3 p-2 border border-gray-200 rounded-lg"
                        >
                          <!-- Completion Status -->
                          <div class="flex-shrink-0">
                            <div v-if="trainingPolicy.completed" class="h-5 w-5 bg-green-100 rounded-full flex items-center justify-center">
                              <svg class="h-3 w-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                              </svg>
                            </div>
                            <div v-else class="h-5 w-5 bg-gray-100 rounded-full flex items-center justify-center">
                              <svg class="h-3 w-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                              </svg>
                            </div>
                          </div>

                          <!-- Content -->
                          <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-900">{{ trainingPolicy.title }}</p>
                          </div>

                          <!-- Type Badge -->
                          <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium"
                                :class="trainingPolicy.type === 'training' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'">
                            {{ trainingPolicy.type === 'training' ? 'T' : 'P' }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Empty State for Training Policies -->
            <div v-else class="text-center py-8">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">No training policies</h3>
              <p class="mt-1 text-sm text-gray-500">No training policies have been assigned to this employee yet.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
	</div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const route = useRoute()

// Reactive data
const loading = ref(true)
const error = ref(null)
const employee = ref(null)
const trainingPolicies = ref([])
const trainingPoliciesLoading = ref(false)

// Load employee data
const loadEmployeeData = async () => {
  try {
    loading.value = true
    error.value = null
    
	const { data } = await axios.get(`/employment/employees/${route.params.id}`)
    
    if (data) {
      employee.value = data
      // Load training policies after employee data is loaded
      await loadTrainingPolicies()
    } else {
      error.value = 'Employee not found'
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load employee profile'
    console.error('Error loading employee:', err)
  } finally {
    loading.value = false
  }
}

// Load training policies for the employee
const loadTrainingPolicies = async () => {
  if (!employee.value?.id) return
  
  try {
    trainingPoliciesLoading.value = true
    
    // Use the new employment API endpoint for admin/HR access
    const { data } = await axios.get(`/employment/employees/${employee.value.id}/training-policies`)
    
    if (data.success) {
      // Sort categories: training categories first, then policy categories
      trainingPolicies.value = data.data.sort((a, b) => {
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
    } else {
      trainingPolicies.value = []
    }
  } catch (err) {
    console.error('Error loading training policies:', err)
    trainingPolicies.value = []
  } finally {
    trainingPoliciesLoading.value = false
  }
}

// Utility functions
const formatDate = (dateString) => {
  if (!dateString) return 'Not provided'
  try {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    })
  } catch (error) {
    return dateString
  }
}

const formatCurrency = (amount) => {
  if (!amount) return '$0.00'
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(amount)
}

// Function to group training policies by type for better visual separation
const getGroupedTrainingPolicies = (trainingPolicies) => {
  if (!trainingPolicies || !Array.isArray(trainingPolicies)) {
    return { trainings: [], policies: [] }
  }
  
  const sorted = [...trainingPolicies].sort((a, b) => {
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
  
  return {
    trainings: sorted.filter(item => item.type === 'training'),
    policies: sorted.filter(item => item.type === 'policy')
  }
}

// Lifecycle
onMounted(() => {
  loadEmployeeData()
})
</script>