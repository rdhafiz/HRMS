<template>
  <div>
    <!-- Header Section -->
    <div class="mb-8">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Welcome back, {{ user?.name || 'User' }}! 👋</h1>
          <p class="text-gray-600 mt-1">{{ user?.admin_type_label || 'Admin Profile' }}</p>
        </div>
        <div class="text-left sm:text-right">
          <p class="text-sm text-gray-500">{{ currentDate }}</p>
          <p class="text-lg font-semibold text-gray-900">{{ currentTime }}</p>
        </div>
      </div>
    </div>

    <!-- Main Dashboard Content -->
    <div class="space-y-6">
      <!-- KPI Cards Row -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <EmployeeCountCard />
        <AttendanceSummaryCard />
        <LeaveSummaryCard />
        <UpcomingHolidayCard />
      </div>

      <!-- Charts and Analytics Row -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <ActivityChartCard />
        <AttendanceChartCard />
      </div>

      <!-- Additional Statistics Row -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <DepartmentDistributionCard />
        <RecentActivityCard />
        <QuickActionsCard />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import EmployeeCountCard from '@/components/Dashboard/EmployeeCountCard.vue'
import AttendanceSummaryCard from '@/components/Dashboard/AttendanceSummaryCard.vue'
import LeaveSummaryCard from '@/components/Dashboard/LeaveSummaryCard.vue'
import UpcomingHolidayCard from '@/components/Dashboard/UpcomingHolidayCard.vue'
import ActivityChartCard from '@/components/Dashboard/ActivityChartCard.vue'
import AttendanceChartCard from '@/components/Dashboard/AttendanceChartCard.vue'
import DepartmentDistributionCard from '@/components/Dashboard/DepartmentDistributionCard.vue'
import RecentActivityCard from '@/components/Dashboard/RecentActivityCard.vue'
import QuickActionsCard from '@/components/Dashboard/QuickActionsCard.vue'

const user = ref(null)

const currentDate = computed(() => {
  return new Date().toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
})

const currentTime = computed(() => {
  return new Date().toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit'
  })
})

const loadUserData = async () => {
  try {
    const response = await axios.get('/auth/user')
    user.value = response.data
  } catch (error) {
    console.error('Error loading user data:', error)
  }
}

onMounted(() => {
  loadUserData()
})
</script>