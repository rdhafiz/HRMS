<template>
  <div class="bg-white rounded-xl shadow-lg p-6 transition-all duration-300 hover:shadow-xl">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-lg font-semibold text-gray-900">Employee Activity</h3>
      <div class="flex space-x-2">
        <button 
          @click="changePeriod('7d')" 
          :class="period === '7d' ? 'bg-blue-100 text-blue-700' : 'text-gray-500'"
          class="px-3 py-1 rounded-lg text-sm font-medium transition-colors"
        >
          7 Days
        </button>
        <button 
          @click="changePeriod('30d')" 
          :class="period === '30d' ? 'bg-blue-100 text-blue-700' : 'text-gray-500'"
          class="px-3 py-1 rounded-lg text-sm font-medium transition-colors"
        >
          30 Days
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="h-64 flex items-center justify-center">
      <div class="flex flex-col items-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        <p class="text-sm text-gray-500 mt-2">Loading chart data...</p>
      </div>
    </div>

    <!-- Chart Container -->
    <div v-else-if="!error && hasData" class="h-64 relative">
      <canvas 
        ref="chartCanvas" 
        id="activity-chart-canvas"
        class="w-full h-full"
        :style="{ minHeight: '256px' }"
      ></canvas>
    </div>

    <!-- No Data State -->
    <div v-else-if="!error && !hasData" class="h-64 flex items-center justify-center">
      <div class="text-center">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
        </svg>
        <p class="text-gray-500 text-sm">No activity data available</p>
        <p class="text-gray-400 text-xs mt-1">Try selecting a different time period</p>
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
import { ref, onMounted, nextTick, watch, computed } from 'vue'
import axios from 'axios'
import Chart from 'chart.js/auto'

const chartCanvas = ref(null)
const loading = ref(true)
const error = ref(null)
const period = ref('7d')
const chartInstance = ref(null)
const chartData = ref(null)

// Computed property to check if we have valid data
const hasData = computed(() => {
  if (!chartData.value) return false
  const { labels, logins, actions } = chartData.value
  return labels && labels.length > 0 && 
         logins && logins.length > 0 && 
         actions && actions.length > 0 &&
         (logins.some(val => val > 0) || actions.some(val => val > 0))
})

const loadData = async () => {
  try {
    loading.value = true
    error.value = null
    
    const response = await axios.get(`/dashboard/activity?period=${period.value}`)
    const data = response.data
    
    // Validate data structure
    if (!data || !data.labels || !data.logins || !data.actions) {
      throw new Error('Invalid data structure received from API')
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
    error.value = err.response?.data?.message || err.message || 'Failed to load activity data'
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
    
    chartInstance.value = new Chart(ctx, {
      type: 'line',
      data: {
        labels: data.labels,
        datasets: [{
          label: 'Employee Logins',
          data: data.logins,
          borderColor: 'rgb(59, 130, 246)',
          backgroundColor: 'rgba(59, 130, 246, 0.1)',
          tension: 0.4,
          fill: true,
          pointBackgroundColor: 'rgb(59, 130, 246)',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointRadius: 4
        }, {
          label: 'Actions Performed',
          data: data.actions,
          borderColor: 'rgb(16, 185, 129)',
          backgroundColor: 'rgba(16, 185, 129, 0.1)',
          tension: 0.4,
          fill: true,
          pointBackgroundColor: 'rgb(16, 185, 129)',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointRadius: 4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
          intersect: false,
          mode: 'index'
        },
        plugins: {
          legend: {
            position: 'top',
            labels: {
              usePointStyle: true,
              padding: 20
            }
          },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleColor: '#fff',
            bodyColor: '#fff',
            borderColor: 'rgba(255, 255, 255, 0.1)',
            borderWidth: 1
          }
        },
        scales: {
          x: {
            grid: {
              display: false
            },
            ticks: {
              color: '#6B7280'
            }
          },
          y: {
            beginAtZero: true,
            grid: {
              color: 'rgba(0, 0, 0, 0.05)'
            },
            ticks: {
              color: '#6B7280'
            }
          }
        },
        elements: {
          line: {
            borderWidth: 2
          }
        }
      }
    })
    
  } catch (chartError) {
    error.value = 'Failed to render chart'
  }
}

const changePeriod = (newPeriod) => {
  if (period.value !== newPeriod) {
    period.value = newPeriod
  }
}

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

// Watch for period changes
watch(period, () => {
  loadData()
})

// Watch for chartCanvas to be available
watch(chartCanvas, (newCanvas) => {
  if (newCanvas && chartData.value && hasData.value && !chartInstance.value) {
    createChart(chartData.value)
  }
})

// Handle window resize
const handleResize = () => {
  if (chartInstance.value) {
    chartInstance.value.resize()
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
