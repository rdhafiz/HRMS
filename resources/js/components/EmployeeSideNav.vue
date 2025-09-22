<template>
  <aside class="bg-slate-50 border-r w-64 min-h-screen">
    <nav class="p-0 space-y-1 text-base text-gray-700">
      <router-link to="/employee/dashboard" class="flex items-center gap-3 px-3 py-4 rounded hover:bg-indigo-50"
                   :class="{ 'bg-indigo-50 text-indigo-700': isActive('/employee/dashboard') }">
        <span class="w-5 h-5 grid place-items-center">🏠</span>
        <span>Dashboard</span>
      </router-link>

      <router-link to="/employee/profile" class="flex items-center gap-3 px-3 py-4 rounded hover:bg-indigo-50"
                   :class="{ 'bg-indigo-50 text-indigo-700': isActive('/employee/profile') }">
        <span class="w-5 h-5 grid place-items-center">👤</span>
        <span>My Profile</span>
      </router-link>

      <!-- Leave Management with Submenu -->
      <div>
        <button
          @click="toggleLeaveMenu"
          class="w-full flex items-center justify-between gap-3 px-3 py-4 rounded hover:bg-indigo-50 transition-colors duration-200"
          :class="{ 'bg-indigo-50 text-indigo-700': isLeaveMenuActive }"
        >
          <div class="flex items-center gap-3">
            <span class="w-5 h-5 grid place-items-center">📅</span>
            <span>Leave Management</span>
          </div>
          <svg
            class="w-4 h-4 transition-transform duration-200"
            :class="{ 'rotate-180': isLeaveMenuOpen }"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>
        
        <!-- Submenu -->
        <div
          class="overflow-hidden transition-all duration-300 ease-in-out"
          :class="isLeaveMenuOpen ? 'max-h-32 opacity-100' : 'max-h-0 opacity-0'"
        >
          <div class="ml-6 space-y-1">
            <router-link
              to="/employee/leaves/apply"
              class="flex items-center gap-3 px-3 py-3 rounded hover:bg-indigo-50 transition-colors duration-200"
              :class="{ 'bg-indigo-50 text-indigo-700': isActive('/employee/leaves/apply') }"
            >
              <span class="w-4 h-4 grid place-items-center">📝</span>
              <span class="text-sm">Apply for Leave</span>
            </router-link>
            
            <router-link
              to="/employee/leaves/history"
              class="flex items-center gap-3 px-3 py-3 rounded hover:bg-indigo-50 transition-colors duration-200"
              :class="{ 'bg-indigo-50 text-indigo-700': isActive('/employee/leaves/history') }"
            >
              <span class="w-4 h-4 grid place-items-center">📋</span>
              <span class="text-sm">Leave History</span>
            </router-link>
          </div>
        </div>
      </div>

      <router-link to="/employee/attendance" class="flex items-center gap-3 px-3 py-4 rounded hover:bg-indigo-50"
                   :class="{ 'bg-indigo-50 text-indigo-700': isActive('/employee/attendance') }">
        <span class="w-5 h-5 grid place-items-center">⏰</span>
        <span>Attendance</span>
      </router-link>

      <router-link to="/employee/holidays" class="flex items-center gap-3 px-3 py-4 rounded hover:bg-indigo-50"
                   :class="{ 'bg-indigo-50 text-indigo-700': isActive('/employee/holidays') }">
        <span class="w-5 h-5 grid place-items-center">🎉</span>
        <span>Holidays</span>
      </router-link>
    </nav>
  </aside>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useRoute } from 'vue-router'

const props = defineProps({ user: { type: Object, default: null } })
const route = useRoute()

// Leave menu state
const isLeaveMenuOpen = ref(false)

// Check if any leave-related route is active
const isLeaveMenuActive = computed(() => {
  return route.path.startsWith('/employee/leaves')
})

// Toggle leave menu
const toggleLeaveMenu = () => {
  isLeaveMenuOpen.value = !isLeaveMenuOpen.value
}

// Auto-open menu if on leave-related route
watch(() => route.path, (newPath) => {
  if (newPath.startsWith('/employee/leaves')) {
    isLeaveMenuOpen.value = true
  }
}, { immediate: true })

function isActive(prefix) {
  return route.path.startsWith(prefix)
}
</script>

