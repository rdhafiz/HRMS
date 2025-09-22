<template>
  <div class="bg-white rounded-xl shadow-lg p-6 transition-all duration-300 hover:shadow-xl">
    <!-- Header -->
    <div class="mb-4">
      <h3 class="text-lg font-semibold text-gray-900">Department Distribution</h3>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="space-y-3">
      <div v-for="i in 3" :key="i" class="animate-pulse">
        <div class="flex items-center justify-between">
          <div class="h-4 bg-gray-200 rounded w-24"></div>
          <div class="flex items-center">
            <div class="w-16 bg-gray-200 rounded h-2 mr-3"></div>
            <div class="h-4 bg-gray-200 rounded w-8"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Loaded State -->
    <div v-else class="space-y-3">
      <div v-for="dept in data" :key="dept.name" class="flex items-center justify-between">
        <span class="text-sm text-gray-600">{{ dept.name }}</span>
        <div class="flex items-center">
          <div class="w-16 bg-gray-200 rounded-full h-2 mr-3">
            <div 
              class="bg-blue-500 h-2 rounded-full transition-all duration-300" 
              :style="{ width: `${(dept.count / totalEmployees) * 100}%` }"
            ></div>
          </div>
          <span class="text-sm font-medium text-gray-900">{{ dept.count }}</span>
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
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

const loading = ref(true)
const error = ref(null)
const data = ref([])

const totalEmployees = computed(() => {
  return data.value.reduce((sum, dept) => sum + dept.count, 0)
})

const loadData = async () => {
  try {
    loading.value = true
    error.value = null
    
    const response = await axios.get('/dashboard/departments')
    data.value = response.data
    
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load department data'
    console.error('Error loading department data:', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadData()
})
</script>
