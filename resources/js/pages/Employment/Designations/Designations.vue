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
              <span class="text-gray-500 font-medium">Employment</span>
            </li>
            <li>
              <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
              </svg>
            </li>
            <li>
              <span class="text-indigo-600 font-semibold">Designations</span>
            </li>
          </ol>
        </nav>
        
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6">
          <div class="flex items-center space-x-3">
            <div class="p-2 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
            </div>
  <div>
              <h1 class="text-2xl font-bold text-gray-900">Designations</h1>
              <p class="text-gray-600 mt-1">Manage job designations and their department assignments</p>
            </div>
          </div>
          <div class="mt-4 sm:mt-0">
            <router-link v-if="canManage" to="/designations/create" 
              class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
              </svg>
              Create Designation
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="w-full py-2">
      <!-- Search and Filters -->
      <div class="bg-white shadow-sm border border-gray-200 p-6 mb-2">
        <div class="flex flex-col lg:flex-row gap-4">
          <div class="flex-1">
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
              </div>
              <input v-model="q" @input="fetch" 
                placeholder="Search designations by name..." 
                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-gray-900 placeholder-gray-500">
            </div>
          </div>
          <div class="lg:w-64">
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
    </div>
              <select v-model="departmentId" @change="fetch" 
                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-gray-900 bg-white">
        <option value="">All Departments</option>
        <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
      </select>
    </div>
          </div>
          <div class="flex items-center space-x-2">
            <div class="text-sm text-gray-500">
              <span v-if="!loading">{{ items.length }} designation(s)</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="space-y-4">
        <div v-for="i in 3" :key="i" class="bg-white shadow-sm border border-gray-200 p-6 animate-pulse">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gray-200 rounded-lg"></div>
            <div class="flex-1 space-y-2">
              <div class="h-4 bg-gray-200 rounded w-1/4"></div>
              <div class="h-3 bg-gray-200 rounded w-1/2"></div>
            </div>
            <div class="flex space-x-2">
              <div class="w-16 h-8 bg-gray-200 rounded"></div>
              <div class="w-16 h-8 bg-gray-200 rounded"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else-if="items.length === 0" class="text-center py-4">
        <div class="mx-auto w-24 h-24 bg-gradient-to-r from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-6">
          <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
        </div>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">No designations found</h3>
        <p class="text-gray-600 mb-8 max-w-md mx-auto">
          {{ q || departmentId ? 'No designations match your search criteria. Try adjusting your filters.' : 'Get started by creating your first designation to organize job roles within departments.' }}
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <router-link v-if="canManage" to="/designations/create" 
            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Create First Designation
          </router-link>
          <button v-if="q || departmentId" @click="clearFilters" 
            class="inline-flex items-center px-6 py-3 bg-white text-gray-700 font-medium rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Clear Filters
          </button>
        </div>
      </div>

      <!-- Designations Grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6">
        <div v-for="d in items" :key="d.id" 
          class="bg-white shadow-sm border border-gray-200 hover:shadow-lg transition-all duration-200 transform hover:-translate-y-1 group">
          <div class="p-6">
            <!-- Designation Header -->
            <div class="flex items-start justify-between mb-4">
              <div class="flex items-center space-x-3">
                <div class="p-2 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-lg group-hover:from-emerald-600 group-hover:to-teal-600 transition-all duration-200">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">{{ d.name }}</h3>
                  <div class="flex items-center space-x-2 mt-1">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                      <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                      </svg>
                      Active
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Department Info -->
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                  <div class="p-1 bg-blue-100 rounded">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                  </div>
                  <div>
                    <span class="text-sm font-medium text-gray-900">{{ d.department?.name || 'No Department' }}</span>
                    <p class="text-xs text-gray-500">Department</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div v-if="canManage" class="flex items-center justify-end space-x-2 pt-4 border-t border-gray-100">
              <router-link :to="`/designations/${d.id}/edit`" 
                class="inline-flex items-center px-3 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
              </router-link>
              <button @click="remove(d)" 
                class="inline-flex items-center px-3 py-2 text-sm font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Delete
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Modern Pagination -->
      <div v-if="items.length > 0 && meta.last_page > 1" class="mt-8">
        <div class="bg-white shadow-sm border border-gray-200 p-6">
          <div class="flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
            <div class="text-sm text-gray-700">
              Showing page <span class="font-semibold">{{ meta.current_page }}</span> of 
              <span class="font-semibold">{{ meta.last_page }}</span>
            </div>
            
            <div class="flex items-center space-x-2">
              <!-- Previous Button -->
              <button @click="prev" :disabled="!links.prev" 
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Previous
              </button>

              <!-- Page Numbers -->
              <div class="flex items-center space-x-1">
                <template v-for="page in getPageNumbers()" :key="page">
                  <button v-if="page !== '...'" @click="fetch(page)" 
                    :class="[
                      'px-4 py-2 text-sm font-medium rounded-lg transition-colors',
                      page === meta.current_page 
                        ? 'bg-indigo-600 text-white shadow-md' 
                        : 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50'
                    ]">
                    {{ page }}
                  </button>
                  <span v-else class="px-4 py-2 text-sm text-gray-500">...</span>
                </template>
              </div>

              <!-- Next Button -->
              <button @click="next" :disabled="!links.next" 
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                Next
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <Transition name="modal" appear>
      <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeDeleteModal"></div>
        
        <!-- Modal panel -->
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
          <Transition name="modal-panel" appear>
            <div v-if="showDeleteModal" class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
              <!-- Modal header -->
              <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                  <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                  </div>
                  <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                    <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">
                      Delete Designation
                    </h3>
                    <div class="mt-2">
                      <p class="text-sm text-gray-500">
                        Are you sure you want to delete <strong>"{{ designationToDelete?.name }}"</strong>? This action cannot be undone.
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Designation Usage Information -->
              <div v-if="designationToDelete" class="px-4 pb-4 sm:px-6">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                  <div class="flex items-start">
                    <div class="flex-shrink-0">
                      <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                      </svg>
                    </div>
                    <div class="ml-3">
                      <h4 class="text-sm font-medium text-yellow-800">Designation Usage Information</h4>
                      <div class="mt-2 text-sm text-yellow-700">
                        <div class="space-y-2">
                          <div class="flex items-center justify-between">
                            <span>Department:</span>
                            <span class="font-semibold">{{ designationToDelete.department?.name || 'No Department' }}</span>
                          </div>
                          <div class="flex items-center justify-between">
                            <span>Employees:</span>
                            <span class="font-semibold">{{ designationToDelete.employees_count || 0 }}</span>
                          </div>
                        </div>
                        <p class="mt-2 text-xs">
                          <strong>Note:</strong> Deleting this designation will affect all employees assigned to this role. 
                          Consider reassigning them to other designations first.
                        </p>
                      </div>
      </div>
        </div>
      </div>
    </div>

              <!-- Modal footer -->
              <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                <button 
                  type="button" 
                  @click="confirmDelete"
                  :disabled="deleting"
                  class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 disabled:opacity-50 disabled:cursor-not-allowed sm:ml-3 sm:w-auto transition-colors"
                >
                  <svg v-if="deleting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <svg v-else class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                  {{ deleting ? 'Deleting...' : 'Delete Designation' }}
                </button>
                <button 
                  type="button" 
                  @click="closeDeleteModal"
                  :disabled="deleting"
                  class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed sm:mt-0 sm:w-auto transition-colors"
                >
                  Cancel
                </button>
              </div>
            </div>
          </Transition>
    </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import axios from 'axios'

const items = ref([])
const meta = ref({ current_page: 1, last_page: 1 })
const links = ref({})
const q = ref('')
const departments = ref([])
const departmentId = ref('')
const me = ref(null)
const loading = ref(false)
const showDeleteModal = ref(false)
const designationToDelete = ref(null)
const deleting = ref(false)
const canManage = computed(() => me.value?.admin_type === 1 || me.value?.admin_type === 2)

async function fetch(page = 1) {
  loading.value = true
  try {
  const { data } = await axios.get('/employment/designations', { params: { page, q: q.value, department_id: departmentId.value } })
  items.value = data.data
  meta.value = { current_page: data.current_page, last_page: data.last_page }
  links.value = { next: data.next_page_url, prev: data.prev_page_url }
  } catch (error) {
    console.error('Error fetching designations:', error)
  } finally {
    loading.value = false
  }
}

async function loadDepartments() {
  try {
  const { data } = await axios.get('/employment/departments', { params: { page: 1 } })
  departments.value = data.data
  } catch (error) {
    console.error('Error loading departments:', error)
  }
}

function next() { if (links.value.next) fetch(meta.value.current_page + 1) }
function prev() { if (links.value.prev) fetch(meta.value.current_page - 1) }

function remove(row) {
  designationToDelete.value = row
  showDeleteModal.value = true
}

function closeDeleteModal() {
  if (deleting.value) return // Prevent closing while deleting
  showDeleteModal.value = false
  designationToDelete.value = null
}

async function confirmDelete() {
  if (!designationToDelete.value) return
  
  deleting.value = true
  try {
    await axios.delete(`/employment/designations/${designationToDelete.value.id}`)
    showDeleteModal.value = false
    designationToDelete.value = null
  fetch(meta.value.current_page)
  } catch (error) {
    console.error('Error deleting designation:', error)
    alert('Failed to delete designation. Please try again.')
  } finally {
    deleting.value = false
  }
}

function clearFilters() {
  q.value = ''
  departmentId.value = ''
  fetch()
}

async function loadMe() { 
  try {
    const { data } = await axios.get('/auth/user')
    me.value = data 
  } catch (error) {
    console.error('Error loading user:', error)
  }
}

// Enhanced pagination with page numbers
function getPageNumbers() {
  const current = meta.value.current_page
  const last = meta.value.last_page
  const pages = []
  
  if (last <= 7) {
    // Show all pages if 7 or fewer
    for (let i = 1; i <= last; i++) {
      pages.push(i)
    }
  } else {
    // Always show first page
    pages.push(1)
    
    if (current <= 4) {
      // Near the beginning
      for (let i = 2; i <= 5; i++) {
        pages.push(i)
      }
      pages.push('...')
      pages.push(last)
    } else if (current >= last - 3) {
      // Near the end
      pages.push('...')
      for (let i = last - 4; i <= last; i++) {
        pages.push(i)
      }
    } else {
      // In the middle
      pages.push('...')
      for (let i = current - 1; i <= current + 1; i++) {
        pages.push(i)
      }
      pages.push('...')
      pages.push(last)
    }
  }
  
  return pages
}

// Handle escape key to close modal
function handleEscapeKey(event) {
  if (event.key === 'Escape' && showDeleteModal.value) {
    closeDeleteModal()
  }
}

onMounted(async () => { 
  await loadMe()
  loadDepartments()
  fetch()
  document.addEventListener('keydown', handleEscapeKey)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleEscapeKey)
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

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

/* Hover effects for cards */
.group:hover .group-hover\:from-emerald-600 {
  background: linear-gradient(to right, rgb(5 150 105), rgb(13 148 136));
}

.group:hover .group-hover\:to-teal-600 {
  background: linear-gradient(to right, rgb(5 150 105), rgb(13 148 136));
}

/* Modal animations */
.modal-enter-active {
  transition: opacity 0.3s ease-out;
}

.modal-leave-active {
  transition: opacity 0.2s ease-in;
}

.modal-enter-from, .modal-leave-to {
  opacity: 0;
}

.modal-panel-enter-active {
  transition: all 0.3s ease-out;
}

.modal-panel-leave-active {
  transition: all 0.2s ease-in;
}

.modal-panel-enter-from {
  opacity: 0;
  transform: scale(0.95) translateY(-20px);
}

.modal-panel-leave-to {
  opacity: 0;
  transform: scale(0.95) translateY(20px);
}

/* Prevent body scroll when modal is open */
body.modal-open {
  overflow: hidden;
}
</style>

