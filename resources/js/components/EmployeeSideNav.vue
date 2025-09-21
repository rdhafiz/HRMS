<template>
  <aside class="bg-slate-50 border-r w-64 min-h-screen">
    <nav class="p-0 space-y-1 text-base text-gray-700">
      <router-link to="/employee/dashboard" class="flex items-center gap-3 px-3 py-4 rounded hover:bg-indigo-50"
                   :class="{ 'bg-indigo-50 text-indigo-700': isActive('/employee/dashboard') }">
        <span class="w-5 h-5 grid place-items-center">🏠</span>
        <span>Dashboard</span>
      </router-link>

      <router-link to="/employee/profile" class="flex items-center gap-3 px-3 py-4 rounded hover:bg-indigo-50"
                   :class="{ 'bg-indigo-50 text-indigo-700': isActive('/employee/profile') }">
        <span class="w-5 h-5 grid place-items-center">👤</span>
        <span>My Profile</span>
      </router-link>

      <router-link to="/employee/leaves" class="flex items-center gap-3 px-3 py-4 rounded hover:bg-indigo-50"
                   :class="{ 'bg-indigo-50 text-indigo-700': isActive('/employee/leaves') }">
        <span class="w-5 h-5 grid place-items-center">📅</span>
        <span>Leave Management</span>
      </router-link>

      <router-link to="/employee/attendance" class="flex items-center gap-3 px-3 py-4 rounded hover:bg-indigo-50"
                   :class="{ 'bg-indigo-50 text-indigo-700': isActive('/employee/attendance') }">
        <span class="w-5 h-5 grid place-items-center">⏰</span>
        <span>Attendance</span>
      </router-link>

      <router-link to="/employee/holidays" class="flex items-center gap-3 px-3 py-4 rounded hover:bg-indigo-50"
                   :class="{ 'bg-indigo-50 text-indigo-700': isActive('/employee/holidays') }">
        <span class="w-5 h-5 grid place-items-center">🎉</span>
        <span>Holidays</span>
      </router-link>
    </nav>
  </aside>
</template>

<script setup>
import { useRoute } from 'vue-router'

const props = defineProps({ user: { type: Object, default: null } })
const route = useRoute()

function isActive(prefix) {
  return route.path.startsWith(prefix)
}
</script>

