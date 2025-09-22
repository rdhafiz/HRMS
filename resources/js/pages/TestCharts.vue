<template>
  <div class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-6xl mx-auto">
      <h1 class="text-3xl font-bold text-gray-900 mb-8">Chart Debug Page</h1>
      
      <!-- API Test Section -->
      <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">API Endpoints Test</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <button @click="testApi('/api/dashboard/employees')" class="bg-blue-500 text-white px-4 py-2 rounded">
            Test Employees API
          </button>
          <button @click="testApi('/api/dashboard/attendance')" class="bg-green-500 text-white px-4 py-2 rounded">
            Test Attendance API
          </button>
          <button @click="testApi('/api/dashboard/leaves')" class="bg-yellow-500 text-white px-4 py-2 rounded">
            Test Leaves API
          </button>
          <button @click="testApi('/api/dashboard/holidays')" class="bg-purple-500 text-white px-4 py-2 rounded">
            Test Holidays API
          </button>
          <button @click="testApi('/api/dashboard/activity?period=7d')" class="bg-indigo-500 text-white px-4 py-2 rounded">
            Test Activity API (7d)
          </button>
          <button @click="testApi('/api/dashboard/departments')" class="bg-pink-500 text-white px-4 py-2 rounded">
            Test Departments API
          </button>
        </div>
        
        <div v-if="apiResult" class="mt-4 p-4 bg-gray-100 rounded">
          <h3 class="font-semibold">API Response:</h3>
          <pre class="text-sm overflow-auto">{{ JSON.stringify(apiResult, null, 2) }}</pre>
        </div>
      </div>

      <!-- Chart Test Section -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-semibold mb-4">Activity Chart Test</h3>
          <ActivityChartCard />
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-semibold mb-4">Attendance Chart Test</h3>
          <AttendanceChartCard />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'
import ActivityChartCard from '@/components/Dashboard/ActivityChartCard.vue'
import AttendanceChartCard from '@/components/Dashboard/AttendanceChartCard.vue'

const apiResult = ref(null)

const testApi = async (endpoint) => {
  try {
    console.log(`Testing API: ${endpoint}`)
    const response = await axios.get(endpoint)
    apiResult.value = {
      endpoint,
      status: response.status,
      data: response.data
    }
    console.log('API Response:', response.data)
  } catch (error) {
    console.error('API Error:', error)
    apiResult.value = {
      endpoint,
      error: error.response?.data || error.message,
      status: error.response?.status
    }
  }
}
</script>
