<template>
  <div class="bg-white rounded-xl shadow-lg p-6 transition-all duration-300 hover:shadow-xl">
    <!-- Header -->
    <div class="mb-6">
      <h3 class="text-lg font-semibold text-gray-900">Attendance Summary</h3>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="h-64 flex items-center justify-center">
      <div class="flex flex-col items-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
        <p class="text-sm text-gray-500 mt-2">Loading attendance data...</p>
      </div>
    </div>

    <!-- Chart Container -->
    <div v-else-if="!error && hasData" class="h-64 relative">
      <canvas 
        ref="chartCanvas" 
        id="attendance-chart-canvas"
        class="w-full h-full"
        :style="{ minHeight: '256px' }"
      ></canvas>
    </div>

    <!-- No Data State -->
    <div v-else-if="!error && !hasData" class="h-64 flex items-center justify-center">
      <div class="text-center">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <p class="text-gray-500 text-sm">No attendance data available</p>
        <p class="text-gray-400 text-xs mt-1">Attendance will appear here once employees start marking attendance</p>
      </div>
    </div>

    <!-- Error State -->
    <div v-else class="h-64 flex items-center justify-center">
      <div class="text-center">
        <svg class="w-16 h-16 text-red-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
        </svg>
        <p class="text-red-500 text-sm mb-2">{{ error }}</p>
        <button @click="loadData" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
          Try Again
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick, computed, watch } from 'vue'
import axios from 'axios'
import Chart from 'chart.js/auto'

const chartCanvas = ref(null)
const loading = ref(true)
const error = ref(null)
const chartInstance = ref(null)
const chartData = ref(null)

// Computed property to check if we have valid data
const hasData = computed(() => {
  if (!chartData.value) return false
  const { present, absent, late, halfDay } = chartData.value
  return (present + absent + late + halfDay) > 0
})

const loadData = async () => {
  try {
    loading.value = true
    error.value = null
    
    const response = await axios.get('/dashboard/attendance')
    const data = response.data
    
    // Validate data structure
    if (!data || typeof data.present === 'undefined' || typeof data.absent === 'undefined' || 
        typeof data.late === 'undefined' || typeof data.halfDay === 'undefined') {
      throw new Error('Invalid attendance data structure received from API')
    }
    
    chartData.value = data
    
    // Destroy existing chart
    destroyChart()
    
    // Create new chart only if we have data
    if (hasData.value) {
      await nextTick()
      // Wait for the canvas to be rendered in the DOM
      const waitForCanvas = () => {
        if (chartCanvas.value && chartCanvas.value.offsetWidth > 0) {
          createChart(data)
        } else {
          setTimeout(waitForCanvas, 50)
        }
      }
      waitForCanvas()
    }
    
  } catch (err) {
    error.value = err.response?.data?.message || err.message || 'Failed to load attendance data'
    chartData.value = null
  } finally {
    loading.value = false
  }
}

const createChart = (data) => {
  
  // Wait for canvas to be available
  if (!chartCanvas.value) {
    setTimeout(() => createChart(data), 100)
    return
  }
  
  try {
    // Check if canvas is already in use by another chart
    if (Chart.getChart(chartCanvas.value)) {
      Chart.getChart(chartCanvas.value).destroy()
      // Wait a bit for the destruction to complete
      setTimeout(() => {
        createChart(data)
      }, 50)
      return
    }
    
    const ctx = chartCanvas.value.getContext('2d')
    
    // Ensure canvas has proper dimensions
    if (chartCanvas.value.offsetWidth === 0 || chartCanvas.value.offsetHeight === 0) {
      setTimeout(() => createChart(data), 100)
      return
    }
    
    chartCanvas.value.width = chartCanvas.value.offsetWidth
    chartCanvas.value.height = chartCanvas.value.offsetHeight
    
    const total = data.present + data.absent + data.late + data.halfDay
    
    chartInstance.value = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ['Present', 'Absent', 'Late', 'Half Day'],
        datasets: [{
          data: [
            data.present,
            data.absent,
            data.late,
            data.halfDay
          ],
          backgroundColor: [
            'rgb(34, 197, 94)',   // Green for Present
            'rgb(239, 68, 68)',   // Red for Absent
            'rgb(245, 158, 11)',  // Yellow for Late
            'rgb(156, 163, 175)'  // Gray for Half Day
          ],
          borderWidth: 0,
          hoverOffset: 4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '60%',
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              usePointStyle: true,
              padding: 20,
              font: {
                size: 12
              }
            }
          },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleColor: '#fff',
            bodyColor: '#fff',
            borderColor: 'rgba(255, 255, 255, 0.1)',
            borderWidth: 1,
            callbacks: {
              label: function(context) {
                const label = context.label || ''
                const value = context.parsed
                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0
                return `${label}: ${value} (${percentage}%)`
              }
            }
          }
        },
        elements: {
          arc: {
            borderWidth: 0
          }
        }
      }
    })
    
  } catch (chartError) {
    error.value = 'Failed to render attendance chart'
  }
}

// Handle window resize
const handleResize = () => {
  if (chartInstance.value) {
    chartInstance.value.resize()
  }
}

// Watch for chartCanvas to be available
watch(chartCanvas, (newCanvas) => {
  if (newCanvas && chartData.value && hasData.value && !chartInstance.value) {
    createChart(chartData.value)
  }
})

// Helper function to safely destroy chart
const destroyChart = () => {
  if (chartInstance.value) {
    chartInstance.value.destroy()
    chartInstance.value = null
  }
  
  if (chartCanvas.value && Chart.getChart(chartCanvas.value)) {
    Chart.getChart(chartCanvas.value).destroy()
  }
}

onMounted(() => {
  loadData()
  window.addEventListener('resize', handleResize)
})

// Cleanup on unmount
import { onUnmounted } from 'vue'
onUnmounted(() => {
  destroyChart()
  window.removeEventListener('resize', handleResize)
})
</script>
