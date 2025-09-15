<template>
  <aside class="bg-slate-50 border-r w-64 min-h-screen">

    <nav class="p-0 space-y-1 text-base text-gray-700">
      <router-link to="/dashboard" class="flex items-center gap-3 px-3 py-4 rounded hover:bg-indigo-50"
                   :class="{ 'bg-indigo-50 text-indigo-700': isActive('/dashboard') }">
        <span class="w-5 h-5 grid place-items-center">🏠</span>
        <span>Dashboard</span>
      </router-link>

      <button @click="toggle('employee')" class="flex w-full items-center gap-3 px-3 py-4 rounded hover:bg-indigo-50"
              :class="{ 'bg-indigo-50 text-indigo-700': groupActive('employee') }">
        <span class="w-5 h-5 grid place-items-center">👥</span>
        <span class="flex-1 text-left">Employee Management</span>
        <span class="text-gray-500">{{ open.employee ? '▾' : '▸' }}</span>
      </button>
      <Transition enter-active-class="transition-all duration-200 ease-out" enter-from-class="opacity-0 max-h-0"
                  enter-to-class="opacity-100 max-h-96" leave-active-class="transition-all duration-200 ease-in"
                  leave-from-class="opacity-100 max-h-96" leave-to-class="opacity-0 max-h-0">
        <div v-show="open.employee" class="ml-8 space-y-1 overflow-hidden">
          <router-link to="/departments" class="block px-3 py-1 rounded hover:bg-indigo-50"
                       :class="{ 'bg-indigo-50 text-indigo-700': isActive('/departments') }">Departments</router-link>
          <router-link to="/designations" class="block px-3 py-1 rounded hover:bg-indigo-50"
                       :class="{ 'bg-indigo-50 text-indigo-700': isActive('/designations') }">Designations</router-link>
          <router-link to="/employees" class="block px-3 py-1 rounded hover:bg-indigo-50"
                       :class="{ 'bg-indigo-50 text-indigo-700': isActive('/employees') }">Employees</router-link>
        </div>
      </Transition>

      <button @click="toggle('attendance')" class="flex w-full items-center gap-3 px-3 py-4 rounded hover:bg-indigo-50"
              :class="{ 'bg-indigo-50 text-indigo-700': groupActive('attendance') }">
        <span class="w-5 h-5 grid place-items-center">🗓</span>
        <span class="flex-1 text-left">Attendance</span>
        <span class="text-gray-500">{{ open.attendance ? '▾' : '▸' }}</span>
      </button>
      <Transition enter-active-class="transition-all duration-200 ease-out" enter-from-class="opacity-0 max-h-0"
                  enter-to-class="opacity-100 max-h-96" leave-active-class="transition-all duration-200 ease-in"
                  leave-from-class="opacity-100 max-h-96" leave-to-class="opacity-0 max-h-0">
        <div v-show="open.attendance" class="ml-8 space-y-1 overflow-hidden">
          <router-link to="/attendance/daily" class="block px-3 py-1 rounded hover:bg-indigo-50"
                       :class="{ 'bg-indigo-50 text-indigo-700': isActive('/attendance/daily') }">Daily Attendance</router-link>
          <router-link to="/attendance/leaves" class="block px-3 py-1 rounded hover:bg-indigo-50"
                       :class="{ 'bg-indigo-50 text-indigo-700': isActive('/attendance/leaves') }">Leaves</router-link>
          <router-link to="/attendance/holidays" class="block px-3 py-1 rounded hover:bg-indigo-50"
                       :class="{ 'bg-indigo-50 text-indigo-700': isActive('/attendance/holidays') }">Holidays</router-link>
        </div>
      </Transition>

      <button @click="toggle('payroll')" class="flex w-full items-center gap-3 px-3 py-4 rounded hover:bg-indigo-50"
              :class="{ 'bg-indigo-50 text-indigo-700': groupActive('payroll') }">
        <span class="w-5 h-5 grid place-items-center">💳</span>
        <span class="flex-1 text-left">Payroll Management</span>
        <span class="text-gray-500">{{ open.payroll ? '▾' : '▸' }}</span>
      </button>
      <Transition enter-active-class="transition-all duration-200 ease-out" enter-from-class="opacity-0 max-h-0"
                  enter-to-class="opacity-100 max-h-96" leave-active-class="transition-all duration-200 ease-in"
                  leave-from-class="opacity-100 max-h-96" leave-to-class="opacity-0 max-h-0">
       <div v-show="open.payroll" class="ml-8 space-y-1 overflow-hidden">
          <router-link to="/payroll/salary-structures" class="block px-3 py-1 rounded hover:bg-indigo-50"
                       :class="{ 'bg-indigo-50 text-indigo-700': isActive('/payroll/salary-structures') }">Salary Structures</router-link>
          <router-link to="/payroll/payslips" class="block px-3 py-1 rounded hover:bg-indigo-50"
                       :class="{ 'bg-indigo-50 text-indigo-700': isActive('/payroll/payslips') }">Pay Slip History</router-link>
          <router-link v-if="isSystemAdmin || isHRManager" to="/payroll/payslips/generate" class="block px-3 py-1 rounded hover:bg-indigo-50"
                       :class="{ 'bg-indigo-50 text-indigo-700': isActive('/payroll/payslips/generate') }">Generate Pay Slips</router-link>
        </div>
      </Transition>

      <button @click="toggle('notifications')" class="flex w-full items-center gap-3 px-3 py-4 rounded hover:bg-indigo-50"
              :class="{ 'bg-indigo-50 text-indigo-700': groupActive('notifications') }">
        <span class="w-5 h-5 grid place-items-center">🔔</span>
        <span class="flex-1 text-left">Notifications</span>
        <span class="text-gray-500">{{ open.notifications ? '▾' : '▸' }}</span>
      </button>
      <Transition enter-active-class="transition-all duration-200 ease-out" enter-from-class="opacity-0 max-h-0"
                  enter-to-class="opacity-100 max-h-96" leave-active-class="transition-all duration-200 ease-in"
                  leave-from-class="opacity-100 max-h-96" leave-to-class="opacity-0 max-h-0">
        <div v-show="open.notifications" class="ml-8 space-y-1 overflow-hidden">
          <a href="#" class="block px-3 py-1 rounded hover:bg-indigo-50">Email Notifications</a>
          <a href="#" class="block px-3 py-1 rounded hover:bg-indigo-50">Announcements</a>
        </div>
      </Transition>

      <button @click="toggle('reports')" class="flex w-full items-center gap-3 px-3 py-4 rounded hover:bg-indigo-50"
              :class="{ 'bg-indigo-50 text-indigo-700': groupActive('reports') }">
        <span class="w-5 h-5 grid place-items-center">📊</span>
        <span class="flex-1 text-left">Reports</span>
        <span class="text-gray-500">{{ open.reports ? '▾' : '▸' }}</span>
      </button>
      <Transition enter-active-class="transition-all duration-200 ease-out" enter-from-class="opacity-0 max-h-0"
                  enter-to-class="opacity-100 max-h-96" leave-active-class="transition-all duration-200 ease-in"
                  leave-from-class="opacity-100 max-h-96" leave-to-class="opacity-0 max-h-0">
        <div v-show="open.reports" class="ml-8 space-y-1 overflow-hidden">
          <a href="#" class="block px-3 py-1 rounded hover:bg-indigo-50">Attendance Report</a>
          <a href="#" class="block px-3 py-1 rounded hover:bg-indigo-50">Payroll Report</a>
          <a href="#" class="block px-3 py-1 rounded hover:bg-indigo-50">Performance Report</a>
        </div>
      </Transition>

      <button v-if="isSystemAdmin" @click="toggle('settings')" class="flex w-full items-center gap-3 px-3 py-4 rounded hover:bg-indigo-50"
              :class="{ 'bg-indigo-50 text-indigo-700': groupActive('settings') }">
        <span class="w-5 h-5 grid place-items-center">⚙️</span>
        <span class="flex-1 text-left">Settings</span>
        <span class="text-gray-500">{{ open.settings ? '▾' : '▸' }}</span>
      </button>
      <Transition v-if="isSystemAdmin" enter-active-class="transition-all duration-200 ease-out" enter-from-class="opacity-0 max-h-0"
                  enter-to-class="opacity-100 max-h-96" leave-active-class="transition-all duration-200 ease-in"
                  leave-from-class="opacity-100 max-h-96" leave-to-class="opacity-0 max-h-0">
        <div v-show="open.settings" class="ml-8 space-y-1 overflow-hidden">
          <router-link to="/branding" class="block px-3 py-1 rounded hover:bg-indigo-50"
                       :class="{ 'bg-indigo-50 text-indigo-700': isActive('/branding') }">Branding</router-link>
        </div>
      </Transition>

      <router-link v-if="isSystemAdmin" to="/admins" class="flex items-center gap-3 px-3 py-4 rounded hover:bg-indigo-50"
                   :class="{ 'bg-indigo-50 text-indigo-700': isActive('/admins') }">
        <span class="w-5 h-5 grid place-items-center">🛠</span>
        <span>Admins</span>
      </router-link>

    </nav>
  </aside>
</template>

<script setup>
import { reactive, watch, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'

const props = defineProps({ user: { type: Object, default: null } })
const route = useRoute()
const open = reactive({ employee: false, attendance: false, payroll: false, notifications: false, reports: false, settings: false })

function toggle(key) {
  // close others for accordion behavior
  Object.keys(open).forEach(k => { if (k !== key) open[k] = false })
  open[key] = !open[key]
}

function isActive(prefix) {
  return route.path.startsWith(prefix)
}

function groupActive(group) {
  if (group === 'employee') return ['/departments','/designations','/employees'].some(isActive)
  if (group === 'attendance') return ['/attendance/daily','/attendance/leaves','/attendance/holidays'].some(isActive)
  if (group === 'payroll') return ['/payroll/salary-structures','/payroll/payslips'].some(isActive)
  if (group === 'notifications') return false
  if (group === 'reports') return false
  if (group === 'settings') return ['/branding'].some(isActive)
  return false
}

function syncOpenByRoute() {
  open.employee = groupActive('employee')
  open.attendance = groupActive('attendance')
  open.payroll = groupActive('payroll')
  open.notifications = groupActive('notifications')
  open.reports = groupActive('reports')
  open.settings = groupActive('settings')
}

onMounted(syncOpenByRoute)
watch(() => route.path, () => { syncOpenByRoute() })

// Role-based menu visibility
const isSystemAdmin = computed(() => props.user?.admin_type_label === 'SYSTEM ADMIN')
const isHRManager = computed(() => props.user?.admin_type_label === 'HR MANAGER')
const isSupervisor = computed(() => props.user?.admin_type_label === 'SUPERVISOR')
</script>