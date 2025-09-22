<template>
  <div class="bg-white rounded-xl shadow-lg p-6 transition-all duration-300 hover:shadow-xl">
    <!-- Header -->
    <div class="mb-6">
      <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
      <p class="text-sm text-gray-500 mt-1">Access frequently used features</p>
    </div>

    <!-- Actions Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-2 gap-4">
      <!-- Add Employee -->
      <button 
        @click="navigateTo('/employees/create')"
        class="flex flex-col items-center p-4 bg-blue-50 rounded-xl hover:bg-blue-100 transition-all duration-200 group border border-blue-100 hover:border-blue-200 hover:shadow-md"
      >
        <div class="p-3 bg-blue-100 rounded-full mb-3 group-hover:bg-blue-200 transition-colors">
          <svg class="w-6 h-6 text-blue-600 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
          </svg>
        </div>
        <span class="text-sm font-medium text-gray-900 text-center">Add Employee</span>
        <span class="text-xs text-gray-500 mt-1">Create new employee</span>
      </button>

      <!-- Mark Attendance -->
      <button 
        @click="navigateTo('/attendance/daily')"
        class="flex flex-col items-center p-4 bg-green-50 rounded-xl hover:bg-green-100 transition-all duration-200 group border border-green-100 hover:border-green-200 hover:shadow-md"
      >
        <div class="p-3 bg-green-100 rounded-full mb-3 group-hover:bg-green-200 transition-colors">
          <svg class="w-6 h-6 text-green-600 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
        <span class="text-sm font-medium text-gray-900 text-center">Mark Attendance</span>
        <span class="text-xs text-gray-500 mt-1">Daily attendance</span>
      </button>

      <!-- Review Leaves -->
      <button 
        @click="navigateTo('/attendance/leaves')"
        class="flex flex-col items-center p-4 bg-yellow-50 rounded-xl hover:bg-yellow-100 transition-all duration-200 group border border-yellow-100 hover:border-yellow-200 hover:shadow-md"
      >
        <div class="p-3 bg-yellow-100 rounded-full mb-3 group-hover:bg-yellow-200 transition-colors">
          <svg class="w-6 h-6 text-yellow-600 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
          </svg>
        </div>
        <span class="text-sm font-medium text-gray-900 text-center">Review Leaves</span>
        <span class="text-xs text-gray-500 mt-1">Leave requests</span>
      </button>

      <!-- Payroll -->
      <button 
        @click="navigateTo('/payroll/payslips')"
        class="flex flex-col items-center p-4 bg-purple-50 rounded-xl hover:bg-purple-100 transition-all duration-200 group border border-purple-100 hover:border-purple-200 hover:shadow-md"
      >
        <div class="p-3 bg-purple-100 rounded-full mb-3 group-hover:bg-purple-200 transition-colors">
          <svg class="w-6 h-6 text-purple-600 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
          </svg>
        </div>
        <span class="text-sm font-medium text-gray-900 text-center">Payroll</span>
        <span class="text-xs text-gray-500 mt-1">Pay slips</span>
      </button>

      
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

const navigateTo = (path) => {
  router.push(path)
}

// Quick action items with metadata
const quickActions = [
  {
    id: 'add-employee',
    title: 'Add Employee',
    description: 'Create new employee',
    path: '/employees/create',
    color: 'blue',
    icon: 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'
  },
  {
    id: 'mark-attendance',
    title: 'Mark Attendance',
    description: 'Daily attendance',
    path: '/attendance/daily',
    color: 'green',
    icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
  },
  {
    id: 'review-leaves',
    title: 'Review Leaves',
    description: 'Leave requests',
    path: '/attendance/leaves',
    color: 'yellow',
    icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'
  },
  {
    id: 'payroll',
    title: 'Payroll',
    description: 'Pay slips',
    path: '/payroll/payslips',
    color: 'purple',
    icon: 'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 01-2 2z'
  }
]
</script>
