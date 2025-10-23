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
              <span class="text-indigo-600 font-semibold">Employees</span>
            </li>
          </ol>
        </nav>
        
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6">
          <div class="flex items-center space-x-3">
            <div class="p-2 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
              </svg>
            </div>
            <div>
              <h1 class="text-2xl font-bold text-gray-900">Employees</h1>
              <p class="text-gray-600 mt-1">Manage employee information, assignments, and employment details</p>
            </div>
          </div>
          <div class="mt-4 sm:mt-0">
            <router-link v-if="canManage" :to="{ name: 'employees.create' }" 
              class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
              </svg>
              Add Employee
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="w-full py-2">
      <!-- Search and Filters -->
      <div class="bg-white shadow-sm border border-gray-200 p-6 mb-2">
        <div class="flex flex-col sm:flex-row gap-4">
          <div class="flex-1">
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
              </div>
              <input v-model="q" @input="debouncedSearch" 
                type="text" 
                placeholder="Search by name, email, or employee code..." 
                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-gray-900 placeholder-gray-500">
            </div>
          </div>
          <div class="flex items-center space-x-2">
            <div class="text-sm text-gray-500">
              <span v-if="!loading">{{ items.length }} employee(s)</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="space-y-4">
        <div v-for="i in 3" :key="i" class="bg-white shadow-sm border border-gray-200 p-6 animate-pulse">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gray-200 rounded-full"></div>
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
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
          </svg>
        </div>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">No employees found</h3>
        <p class="text-gray-600 mb-8 max-w-md mx-auto">
          {{ q ? 'No employees match your search criteria. Try adjusting your search terms.' : 'Get started by adding your first employee to the system.' }}
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <router-link v-if="canManage" :to="{ name: 'employees.create' }" 
            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Add First Employee
          </router-link>
          <button v-if="q" @click="q = ''; debouncedSearch()" 
            class="inline-flex items-center px-6 py-3 bg-white text-gray-700 font-medium rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Clear Search
          </button>
        </div>
      </div>

      <!-- Employees Grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6">
        <div v-for="emp in items" :key="emp.id" 
          class="bg-white shadow-sm border border-gray-200 hover:shadow-lg transition-all duration-200 transform hover:-translate-y-1 group">
          <div class="p-6">
            <!-- Employee Header -->
            <div class="flex items-start justify-between mb-4">
              <div class="flex items-center space-x-3">
                <div class="p-2 bg-gradient-to-r from-orange-500 to-red-500 rounded-lg group-hover:from-orange-600 group-hover:to-red-600 transition-all duration-200">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">
                    {{ emp.first_name }} {{ emp.last_name }}
                  </h3>
                  <div class="flex items-center space-x-2 mt-1">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" :class="badgeClass(emp.status)">
                      <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                      </svg>
                      {{ emp.status }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Employee Details -->
            <div class="space-y-3 mb-4">
              <div class="flex items-center space-x-2">
                <div class="p-1 bg-blue-100 rounded">
                  <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                  </svg>
                </div>
                <div>
                  <span class="text-sm font-medium text-gray-900">{{ emp.employee_code }}</span>
                  <p class="text-xs text-gray-500">Employee Code</p>
                </div>
              </div>

              <div class="flex items-center space-x-2">
                <div class="p-1 bg-green-100 rounded">
                  <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                  </svg>
                </div>
                <div>
                  <span class="text-sm font-medium text-gray-900">{{ emp.email }}</span>
                  <p class="text-xs text-gray-500">Email</p>
                </div>
              </div>

              <div class="flex items-center space-x-2">
                <div class="p-1 bg-purple-100 rounded">
                  <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                  </svg>
                </div>
                <div>
                  <span class="text-sm font-medium text-gray-900">{{ emp.department?.name || 'No Department' }}</span>
                  <p class="text-xs text-gray-500">Department</p>
                </div>
              </div>

              <div class="flex items-center space-x-2">
                <div class="p-1 bg-emerald-100 rounded">
                  <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                  </svg>
                </div>
                <div>
                  <span class="text-sm font-medium text-gray-900">{{ emp.designation?.name || 'No Designation' }}</span>
                  <p class="text-xs text-gray-500">Designation</p>
                </div>
              </div>

              <div class="flex items-center space-x-2">
                <div class="p-1 bg-yellow-100 rounded">
                  <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                </div>
                <div>
                  <span class="text-sm font-medium text-gray-900">{{ formatDate(emp.join_date) }}</span>
                  <p class="text-xs text-gray-500">Join Date</p>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-2 pt-4 border-t border-gray-100">
              <router-link :to="{ name: 'employees.view', params: { id: emp.id } }" 
                class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                View
              </router-link>
              <router-link v-if="canManage" :to="{ name: 'employees.edit', params: { id: emp.id } }" 
                class="inline-flex items-center px-3 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
              </router-link>
              <button v-if="canManage" @click="confirmDelete(emp)" 
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
      <div v-if="items.length > 0 && meta && meta.total > 10" class="mt-8">
        <div class="bg-white shadow-sm border border-gray-200 p-6">
          <div class="flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
            <div class="text-sm text-gray-700">
              Showing <span class="font-semibold">{{ meta.from || 0 }}</span> to 
              <span class="font-semibold">{{ meta.to || 0 }}</span> of 
              <span class="font-semibold">{{ meta.total || 0 }}</span> employees
            </div>
            
            <div class="flex items-center space-x-2">
              <!-- Previous Button -->
              <button @click="go(meta.current_page - 1)" :disabled="!links.prev" 
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Previous
              </button>

              <!-- Page Numbers -->
              <div class="flex items-center space-x-1">
                <template v-for="pageNum in getPageNumbers()" :key="pageNum">
                  <button v-if="pageNum !== '...'" @click="go(pageNum)" 
                    :class="[
                      'px-4 py-2 text-sm font-medium rounded-lg transition-colors',
                      pageNum === meta.current_page 
                        ? 'bg-indigo-600 text-white shadow-md' 
                        : 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50'
                    ]">
                    {{ pageNum }}
                  </button>
                  <span v-else class="px-4 py-2 text-sm text-gray-500">...</span>
                </template>
              </div>

              <!-- Next Button -->
              <button @click="go(meta.current_page + 1)" :disabled="!links.next" 
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
      <div v-if="toDelete" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="toDelete = null"></div>
        
        <!-- Modal panel -->
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
          <Transition name="modal-panel" appear>
            <div v-if="toDelete" class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
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
                      Delete Employee
                    </h3>
                    <div class="mt-2">
                      <p class="text-sm text-gray-500">
                        Are you sure you want to delete <strong>{{ toDelete.first_name }} {{ toDelete.last_name }}</strong>? This action cannot be undone.
                      </p>
                    </div>
                  </div>
			</div>
		</div>

              <!-- Employee Information -->
              <div v-if="toDelete" class="px-4 pb-4 sm:px-6">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                  <div class="flex items-start">
                    <div class="flex-shrink-0">
                      <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                      </svg>
                    </div>
                    <div class="ml-3">
                      <h4 class="text-sm font-medium text-yellow-800">Employee Information</h4>
                      <div class="mt-2 text-sm text-yellow-700">
                        <div class="space-y-1">
                          <div class="flex items-center justify-between">
                            <span>Employee Code:</span>
                            <span class="font-semibold">{{ toDelete.employee_code }}</span>
                          </div>
                          <div class="flex items-center justify-between">
                            <span>Department:</span>
                            <span class="font-semibold">{{ toDelete.department?.name || 'No Department' }}</span>
                          </div>
                          <div class="flex items-center justify-between">
                            <span>Designation:</span>
                            <span class="font-semibold">{{ toDelete.designation?.name || 'No Designation' }}</span>
                          </div>
                        </div>
                        <p class="mt-2 text-xs">
                          <strong>Note:</strong> Deleting this employee will permanently remove all their data from the system. 
                          Consider deactivating instead if you need to preserve records.
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
                  @click="doDelete"
                  class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto transition-colors"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                  Delete Employee
                </button>
                <button 
                  type="button" 
                  @click="toDelete = null"
                  class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors"
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
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

const q = ref('')
const items = ref([])
const loading = ref(false)
const meta = ref(null)
const links = ref({ prev: null, next: null })
const page = ref(1)
const toDelete = ref(null)
const me = ref(null)
const canManage = computed(() => me.value?.admin_type === 1 || me.value?.admin_type === 2)

// Debounced search functionality
let searchTimeout = null
const debouncedSearch = () => {
  // Clear existing timeout
  if (searchTimeout) {
    clearTimeout(searchTimeout)
  }
  
  // Set new timeout for search
  searchTimeout = setTimeout(() => {
    page.value = 1 // Reset to first page when searching
    fetchData()
  }, 500) // 500ms delay
}

const fetchData = async () => {
	loading.value = true
	try {
		const { data } = await axios.get('/employment/employees', { params: { page: page.value, q: q.value } })
		items.value = data.data || []
		meta.value = {
			current_page: data.current_page,
			from: data.from,
			to: data.to,
			total: data.total,
		}
		links.value = { prev: data.prev_page_url, next: data.next_page_url }
  } catch (error) {
    console.error('Error fetching employees:', error)
    items.value = []
	} finally {
		loading.value = false
	}
}

const go = (p) => {
	page.value = p
	fetchData()
}

const confirmDelete = (emp) => {
	toDelete.value = emp
}

const doDelete = async () => {
	if (!toDelete.value) return
  
  try {
	await axios.delete(`/employment/employees/${toDelete.value.id}`)
	toDelete.value = null
    await fetchData()
  } catch (error) {
    console.error('Error deleting employee:', error)
    // You could add error handling here, like showing a toast notification
  }
}

const badgeClass = (status) => {
	if (status === 'active') return 'bg-green-100 text-green-700'
	if (status === 'inactive') return 'bg-amber-100 text-amber-700'
	return 'bg-red-100 text-red-700'
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  
  try {
    const date = new Date(dateString)
    return date.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    })
  } catch (error) {
    console.error('Error formatting date:', error)
    return dateString
  }
}

// Enhanced pagination logic
const getPageNumbers = () => {
  if (!meta.value) return []
  
  const current = meta.value.current_page
  const total = Math.ceil(meta.value.total / 10) // Assuming 10 items per page
  const pages = []
  
  if (total <= 7) {
    // Show all pages if 7 or fewer
    for (let i = 1; i <= total; i++) {
      pages.push(i)
    }
  } else {
    // Always show first page
    pages.push(1)
    
    if (current > 4) {
      pages.push('...')
    }
    
    // Show pages around current page
    const start = Math.max(2, current - 1)
    const end = Math.min(total - 1, current + 1)
    
    for (let i = start; i <= end; i++) {
      if (i !== 1 && i !== total) {
        pages.push(i)
      }
    }
    
    if (current < total - 3) {
      pages.push('...')
    }
    
    // Always show last page
    if (total > 1) {
      pages.push(total)
    }
  }
  
  return pages
}

onMounted(async () => { 
  try {
    const { data } = await axios.get('/auth/user')
    me.value = data
    await fetchData()
  } catch (error) {
    console.error('Error loading user data:', error)
  }
})
</script>

<style scoped>
/* Modal animations */
.modal-enter-active {
  transition: opacity 0.3s ease-out;
}

.modal-leave-active {
  transition: opacity 0.2s ease-in;
}

.modal-enter-from {
  opacity: 0;
}

.modal-leave-to {
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

/* Card hover effects */
.group:hover .group-hover\:from-orange-600 {
  background: linear-gradient(to right, #ea580c, #dc2626);
}

.group:hover .group-hover\:to-red-600 {
  background: linear-gradient(to right, #ea580c, #dc2626);
}

/* Loading animation */
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Button hover effects */
button:not(:disabled):hover {
  transform: translateY(-1px);
}

button:not(:disabled):active {
  transform: translateY(0);
}

/* Status badge animations */
.badge-animate {
  transition: all 0.2s ease-in-out;
}

.badge-animate:hover {
  transform: scale(1.05);
}

/* Card animations */
.card-hover {
  transition: all 0.3s ease-in-out;
}

.card-hover:hover {
  transform: translateY(-4px);
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Responsive grid adjustments */
@media (max-width: 640px) {
  .grid {
    grid-template-columns: 1fr;
  }
}

@media (min-width: 768px) and (max-width: 1023px) {
  .grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (min-width: 1024px) and (max-width: 1279px) {
  .grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (min-width: 1280px) {
  .grid {
    grid-template-columns: repeat(4, 1fr);
  }
}
</style>