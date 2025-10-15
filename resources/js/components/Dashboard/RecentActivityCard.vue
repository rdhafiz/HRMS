<template>
  <div class="bg-white rounded-xl shadow-lg p-6 transition-all duration-300 hover:shadow-xl">
    <!-- Header -->
    <div class="mb-4">
      <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="space-y-3">
      <div v-for="i in 3" :key="i" class="animate-pulse">
        <div class="flex items-start space-x-3">
          <div class="w-8 h-8 bg-gray-200 rounded-full"></div>
          <div class="flex-1">
            <div class="h-4 bg-gray-200 rounded w-3/4 mb-1"></div>
            <div class="h-3 bg-gray-200 rounded w-1/2"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Loaded State -->
    <div v-else class="space-y-3">
      <div v-for="activity in data" :key="activity.id" class="flex items-start space-x-3">
        <div class="flex-shrink-0">
          <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm text-gray-900">{{ activity.description }}</p>
          <p class="text-xs text-gray-500">{{ formatTime(activity.created_at) }}</p>
        </div>
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
const data = ref([])

const loadData = async () => {
  try {
    loading.value = true
    error.value = null
    
    const response = await axios.get('/dashboard/activity-logs')
    data.value = response.data
    
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load activity data'
    console.error('Error loading activity data:', err)
  } finally {
    loading.value = false
  }
}

const formatTime = (dateString) => {
  return new Date(dateString).toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

onMounted(() => {
  loadData()
})
</script>
