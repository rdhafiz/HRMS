<template>
  <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500 transition-all duration-300 hover:shadow-xl">
    <!-- Loading State -->
    <div v-if="loading" class="animate-pulse">
      <div class="flex items-center justify-between">
        <div class="flex-1">
          <div class="h-4 bg-gray-200 rounded w-32 mb-2"></div>
          <div class="h-8 bg-gray-200 rounded w-20 mb-2"></div>
          <div class="h-3 bg-gray-200 rounded w-24"></div>
        </div>
        <div class="w-12 h-12 bg-gray-200 rounded-full"></div>
      </div>
    </div>

    <!-- Loaded State -->
    <div v-else class="flex items-center justify-between">
      <div>
        <p class="text-sm font-medium text-gray-600">Today's Attendance</p>
        <p class="text-3xl font-bold text-gray-900">{{ data.present }}</p>
        <p class="text-sm text-gray-500">out of {{ data.totalEmployees }} employees</p>
        <div class="flex items-center mt-2 space-x-4 text-xs">
          <span class="text-red-600">Absent: {{ data.absent }}</span>
          <span class="text-yellow-600">Late: {{ data.late }}</span>
          <span class="text-gray-600">Half-day: {{ data.halfDay }}</span>
        </div>
      </div>
      <div class="p-3 bg-green-100 rounded-full">
        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
      </div>
    </div>

    <!-- Error State -->
    <div v-if="error" class="text-center py-4">
      <div class="text-red-500 text-sm">{{ error }}</div>
      <button @click="loadData" class="mt-2 text-blue-600 hover:text-blue-800 text-sm font-medium">
        Try Again
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const loading = ref(true)
const error = ref(null)
const data = ref({
  present: 0,
  absent: 0,
  late: 0,
  halfDay: 0,
  totalEmployees: 0
})

const loadData = async () => {
  try {
    loading.value = true
    error.value = null
    
    const response = await axios.get('/dashboard/attendance')
    data.value = response.data
    
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load attendance data'
    console.error('Error loading attendance data:', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadData()
})
</script>
