<template>
  <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500 transition-all duration-300 hover:shadow-xl">
    <!-- Loading State -->
    <div v-if="loading" class="animate-pulse">
      <div class="flex items-center justify-between">
        <div class="flex-1">
          <div class="h-4 bg-gray-200 rounded w-32 mb-2"></div>
          <div class="h-8 bg-gray-200 rounded w-24 mb-2"></div>
          <div class="h-3 bg-gray-200 rounded w-20"></div>
        </div>
        <div class="w-12 h-12 bg-gray-200 rounded-full"></div>
      </div>
    </div>

    <!-- Loaded State -->
    <div v-else class="flex items-center justify-between">
      <div>
        <p class="text-sm font-medium text-gray-600">Next Holiday</p>
        <p class="text-lg font-bold text-gray-900">{{ data.name || 'No upcoming holidays' }}</p>
        <p v-if="data.date" class="text-sm text-purple-600">
          {{ formatDate(data.date) }}
        </p>
        <p v-if="data.daysUntil" class="text-xs text-gray-500 mt-1">
          {{ data.daysUntil }} days from now
        </p>
      </div>
      <div class="p-3 bg-purple-100 rounded-full">
        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
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
  name: null,
  date: null,
  daysUntil: null
})

const loadData = async () => {
  try {
    loading.value = true
    error.value = null
    
    const response = await axios.get('/dashboard/holidays')
    data.value = response.data
    
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load holiday data'
    console.error('Error loading holiday data:', err)
  } finally {
    loading.value = false
  }
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}

onMounted(() => {
  loadData()
})
</script>
