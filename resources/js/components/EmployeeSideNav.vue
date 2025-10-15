<template>
  <aside class="bg-white border-r border-gray-200 w-72 min-h-screen shadow-lg">
    <!-- Sidebar Header -->
    <div class="p-6 border-b border-gray-200">
      <div class="flex items-center gap-3">
        <div class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center">
          <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
          </svg>
        </div>
        <div>
          <h2 class="text-lg font-semibold text-gray-900">Employee Portal</h2>
          <p class="text-xs text-gray-500">My Dashboard</p>
        </div>
      </div>
    </div>

    <nav class="p-4 space-y-2">
      <!-- Dashboard -->
      <router-link to="/employee/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group hover:bg-indigo-50 hover:shadow-sm"
                   :class="{ 'bg-indigo-50 text-indigo-700 shadow-sm border border-indigo-200': isActive('/employee/dashboard') }">
        <div class="w-6 h-6 flex items-center justify-center">
          <svg class="w-5 h-5 transition-colors duration-200" :class="isActive('/employee/dashboard') ? 'text-indigo-600' : 'text-gray-500 group-hover:text-indigo-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
          </svg>
        </div>
        <span class="font-medium whitespace-nowrap">Dashboard</span>
      </router-link>

      <!-- My Profile -->
      <router-link to="/employee/profile" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group hover:bg-blue-50 hover:shadow-sm"
                   :class="{ 'bg-blue-50 text-blue-700 shadow-sm border border-blue-200': isActive('/employee/profile') }">
        <div class="w-6 h-6 flex items-center justify-center">
          <svg class="w-5 h-5 transition-colors duration-200" :class="isActive('/employee/profile') ? 'text-blue-600' : 'text-gray-500 group-hover:text-blue-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
          </svg>
        </div>
        <span class="font-medium whitespace-nowrap">My Profile</span>
      </router-link>

      <!-- Leave Management -->
      <div class="space-y-1">
        <button @click="toggleLeaveMenu" class="flex w-full items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group hover:bg-green-50 hover:shadow-sm"
                :class="{ 'bg-green-50 text-green-700 shadow-sm border border-green-200': isLeaveMenuActive }">
          <div class="w-6 h-6 flex items-center justify-center">
            <svg class="w-5 h-5 transition-colors duration-200" :class="isLeaveMenuActive ? 'text-green-600' : 'text-gray-500 group-hover:text-green-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
          </div>
          <span class="flex-1 text-left font-medium whitespace-nowrap">Leave Management</span>
          <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': isLeaveMenuOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>
        
        <Transition enter-active-class="transition-all duration-300 ease-out" enter-from-class="opacity-0 max-h-0 transform -translate-y-2"
                    enter-to-class="opacity-100 max-h-96 transform translate-y-0" leave-active-class="transition-all duration-300 ease-in"
                    leave-from-class="opacity-100 max-h-96 transform translate-y-0" leave-to-class="opacity-0 max-h-0 transform -translate-y-2">
          <div v-show="isLeaveMenuOpen" class="ml-6 space-y-1 overflow-hidden border-l-2 border-green-100 pl-4">
            <router-link to="/employee/leaves/apply" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 group hover:bg-green-50"
                         :class="{ 'bg-green-50 text-green-700 font-medium': isActive('/employee/leaves/apply') }">
              <div class="w-4 h-4 flex items-center justify-center">
                <svg class="w-4 h-4 transition-colors duration-200" :class="isActive('/employee/leaves/apply') ? 'text-green-600' : 'text-gray-400 group-hover:text-green-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
              </div>
              <span class="text-sm">Apply for Leave</span>
            </router-link>
            
            <router-link to="/employee/leaves/history" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 group hover:bg-green-50"
                         :class="{ 'bg-green-50 text-green-700 font-medium': isActive('/employee/leaves/history') }">
              <div class="w-4 h-4 flex items-center justify-center">
                <svg class="w-4 h-4 transition-colors duration-200" :class="isActive('/employee/leaves/history') ? 'text-green-600' : 'text-gray-400 group-hover:text-green-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
              </div>
              <span class="text-sm">Leave History</span>
            </router-link>
          </div>
        </Transition>
      </div>

      <!-- Attendance -->
      <router-link to="/employee/attendance" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group hover:bg-yellow-50 hover:shadow-sm"
                   :class="{ 'bg-yellow-50 text-yellow-700 shadow-sm border border-yellow-200': isActive('/employee/attendance') }">
        <div class="w-6 h-6 flex items-center justify-center">
          <svg class="w-5 h-5 transition-colors duration-200" :class="isActive('/employee/attendance') ? 'text-yellow-600' : 'text-gray-500 group-hover:text-yellow-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
        <span class="font-medium whitespace-nowrap">Attendance</span>
      </router-link>

      <!-- Holidays -->
      <router-link to="/employee/holidays" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group hover:bg-purple-50 hover:shadow-sm"
                   :class="{ 'bg-purple-50 text-purple-700 shadow-sm border border-purple-200': isActive('/employee/holidays') }">
        <div class="w-6 h-6 flex items-center justify-center">
          <svg class="w-5 h-5 transition-colors duration-200" :class="isActive('/employee/holidays') ? 'text-purple-600' : 'text-gray-500 group-hover:text-purple-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
          </svg>
        </div>
        <span class="font-medium whitespace-nowrap">Holidays</span>
      </router-link>

      <!-- Training & Policies -->
      <router-link to="/employee/training-policies" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group hover:bg-indigo-50 hover:shadow-sm"
                   :class="{ 'bg-indigo-50 text-indigo-700 shadow-sm border border-indigo-200': isActive('/employee/training-policies') }">
        <div class="w-6 h-6 flex items-center justify-center">
          <svg class="w-5 h-5 transition-colors duration-200" :class="isActive('/employee/training-policies') ? 'text-indigo-600' : 'text-gray-500 group-hover:text-indigo-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
          </svg>
        </div>
        <span class="font-medium whitespace-nowrap">Training & Policies</span>
      </router-link>

      <!-- Payroll -->
      <router-link to="/employee/payroll" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group hover:bg-orange-50 hover:shadow-sm"
                   :class="{ 'bg-orange-50 text-orange-700 shadow-sm border border-orange-200': isActive('/employee/payroll') }">
        <div class="w-6 h-6 flex items-center justify-center">
          <svg class="w-5 h-5 transition-colors duration-200" :class="isActive('/employee/payroll') ? 'text-orange-600' : 'text-gray-500 group-hover:text-orange-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
          </svg>
        </div>
        <span class="font-medium whitespace-nowrap">Payroll</span>
      </router-link>

    </nav>
  </aside>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useRoute } from 'vue-router'

const props = defineProps({ user: { type: Object, default: null } })
const route = useRoute()

// Menu state management
const open = ref({
  leave: false
})

// Check if any leave-related route is active
const isLeaveMenuActive = computed(() => {
  return route.path.startsWith('/employee/leaves')
})

// Computed properties for menu states
const isLeaveMenuOpen = computed(() => open.value.leave)

// Toggle functions
const toggleLeaveMenu = () => {
  open.value.leave = !open.value.leave
}

// Check if a group is active (has open submenu or active child)
const groupActive = (group) => {
  if (group === 'leave') {
    return isLeaveMenuActive.value || open.value.leave
  }
  return false
}

// Auto-open menu if on leave-related route
watch(() => route.path, (newPath) => {
  if (newPath.startsWith('/employee/leaves')) {
    open.value.leave = true
  }
}, { immediate: true })

// Check if a route is active
function isActive(prefix) {
  return route.path.startsWith(prefix)
}
</script>

