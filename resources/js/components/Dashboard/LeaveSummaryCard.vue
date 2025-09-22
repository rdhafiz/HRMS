<template>
  <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500 transition-all duration-300 hover:shadow-xl">
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
        <p class="text-sm font-medium text-gray-600">Pending Leaves</p>
        <p class="text-3xl font-bold text-gray-900">{{ data.pending }}</p>
        <p class="text-sm text-yellow-600 flex items-center mt-1">
          <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
          </svg>
          Needs attention
        </p>
        <div class="flex items-center mt-2 space-x-4 text-xs">
          <span class="text-green-600">Approved: {{ data.approved }}</span>
          <span class="text-red-600">Rejected: {{ data.rejected }}</span>
        </div>
      </div>
      <div class="p-3 bg-yellow-100 rounded-full">
        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
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
  pending: 0,
  approved: 0,
  rejected: 0
})

const loadData = async () => {
  try {
    loading.value = true
    error.value = null
    
    const response = await axios.get('/dashboard/leaves')
    data.value = response.data
    
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load leave data'
    console.error('Error loading leave data:', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadData()
})
</script>
