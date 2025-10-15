<template>
  <aside class="bg-white border-r border-gray-200 w-72 min-h-screen shadow-lg">
    <!-- Sidebar Header -->
    <div class="p-6 border-b border-gray-200">
      <div class="flex items-center gap-3">
        <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
          <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
          </svg>
        </div>
        <div>
          <h2 class="text-lg font-semibold text-gray-900">HR Management</h2>
          <p class="text-xs text-gray-500">Admin Panel</p>
        </div>
      </div>
    </div>

    <nav class="p-4 space-y-2">
      <!-- Dashboard -->
      <router-link to="/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group hover:bg-indigo-50 hover:shadow-sm"
                   :class="{ 'bg-indigo-50 text-indigo-700 shadow-sm border border-indigo-200': isActive('/dashboard') }">
        <div class="w-6 h-6 flex items-center justify-center">
          <svg class="w-5 h-5 transition-colors duration-200" :class="isActive('/dashboard') ? 'text-indigo-600' : 'text-gray-500 group-hover:text-indigo-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
          </svg>
        </div>
        <span class="font-medium whitespace-nowrap">Dashboard</span>
      </router-link>

      <!-- Employee Management -->
      <div class="space-y-1">
        <button @click="toggle('employee')" class="flex w-full items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group hover:bg-blue-50 hover:shadow-sm"
                :class="{ 'bg-blue-50 text-blue-700 shadow-sm border border-blue-200': groupActive('employee') }">
          <div class="w-6 h-6 flex items-center justify-center">
            <svg class="w-5 h-5 transition-colors duration-200" :class="groupActive('employee') ? 'text-blue-600' : 'text-gray-500 group-hover:text-blue-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
          </div>
          <span class="flex-1 text-left font-medium whitespace-nowrap">Employee Management</span>
          <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open.employee }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>
        
        <Transition enter-active-class="transition-all duration-300 ease-out" enter-from-class="opacity-0 max-h-0 transform -translate-y-2"
                    enter-to-class="opacity-100 max-h-96 transform translate-y-0" leave-active-class="transition-all duration-300 ease-in"
                    leave-from-class="opacity-100 max-h-96 transform translate-y-0" leave-to-class="opacity-0 max-h-0 transform -translate-y-2">
          <div v-show="open.employee" class="ml-6 space-y-1 overflow-hidden border-l-2 border-blue-100 pl-4">
            <router-link to="/departments" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 group hover:bg-blue-50"
                         :class="{ 'bg-blue-50 text-blue-700 font-medium': isActive('/departments') }">
              <div class="w-4 h-4 flex items-center justify-center">
                <svg class="w-4 h-4 transition-colors duration-200" :class="isActive('/departments') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
              </div>
              <span class="text-sm">Departments</span>
            </router-link>
            <router-link to="/designations" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 group hover:bg-blue-50"
                         :class="{ 'bg-blue-50 text-blue-700 font-medium': isActive('/designations') }">
              <div class="w-4 h-4 flex items-center justify-center">
                <svg class="w-4 h-4 transition-colors duration-200" :class="isActive('/designations') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                </svg>
              </div>
              <span class="text-sm">Designations</span>
            </router-link>
            <router-link to="/employees" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 group hover:bg-blue-50"
                         :class="{ 'bg-blue-50 text-blue-700 font-medium': isActive('/employees') }">
              <div class="w-4 h-4 flex items-center justify-center">
                <svg class="w-4 h-4 transition-colors duration-200" :class="isActive('/employees') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
              </div>
              <span class="text-sm">Employees</span>
            </router-link>
            <router-link to="/training-policies" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 group hover:bg-blue-50"
                         :class="{ 'bg-blue-50 text-blue-700 font-medium': isActive('/training-policies') }">
              <div class="w-4 h-4 flex items-center justify-center">
                <svg class="w-4 h-4 transition-colors duration-200" :class="isActive('/training-policies') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
              </div>
              <span class="text-sm">Training & Policies</span>
            </router-link>
          </div>
        </Transition>
      </div>

      <!-- Attendance Management -->
      <div class="space-y-1">
        <button @click="toggle('attendance')" class="flex w-full items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group hover:bg-green-50 hover:shadow-sm"
                :class="{ 'bg-green-50 text-green-700 shadow-sm border border-green-200': groupActive('attendance') }">
          <div class="w-6 h-6 flex items-center justify-center">
            <svg class="w-5 h-5 transition-colors duration-200" :class="groupActive('attendance') ? 'text-green-600' : 'text-gray-500 group-hover:text-green-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <span class="flex-1 text-left font-medium whitespace-nowrap">Attendance</span>
          <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open.attendance }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>
        
        <Transition enter-active-class="transition-all duration-300 ease-out" enter-from-class="opacity-0 max-h-0 transform -translate-y-2"
                    enter-to-class="opacity-100 max-h-96 transform translate-y-0" leave-active-class="transition-all duration-300 ease-in"
                    leave-from-class="opacity-100 max-h-96 transform translate-y-0" leave-to-class="opacity-0 max-h-0 transform -translate-y-2">
          <div v-show="open.attendance" class="ml-6 space-y-1 overflow-hidden border-l-2 border-green-100 pl-4">
            <router-link to="/attendance/daily" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 group hover:bg-green-50"
                         :class="{ 'bg-green-50 text-green-700 font-medium': isActive('/attendance/daily') }">
              <div class="w-4 h-4 flex items-center justify-center">
                <svg class="w-4 h-4 transition-colors duration-200" :class="isActive('/attendance/daily') ? 'text-green-600' : 'text-gray-400 group-hover:text-green-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
              </div>
              <span class="text-sm">Daily Attendance</span>
            </router-link>
            <router-link to="/attendance/leaves" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 group hover:bg-green-50"
                         :class="{ 'bg-green-50 text-green-700 font-medium': isActive('/attendance/leaves') }">
              <div class="w-4 h-4 flex items-center justify-center">
                <svg class="w-4 h-4 transition-colors duration-200" :class="isActive('/attendance/leaves') ? 'text-green-600' : 'text-gray-400 group-hover:text-green-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
              </div>
              <span class="text-sm">Leaves</span>
            </router-link>
            <router-link to="/attendance/holidays" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 group hover:bg-green-50"
                         :class="{ 'bg-green-50 text-green-700 font-medium': isActive('/attendance/holidays') }">
              <div class="w-4 h-4 flex items-center justify-center">
                <svg class="w-4 h-4 transition-colors duration-200" :class="isActive('/attendance/holidays') ? 'text-green-600' : 'text-gray-400 group-hover:text-green-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
              </div>
              <span class="text-sm">Holidays</span>
            </router-link>
          </div>
        </Transition>
      </div>

      <!-- Payroll Management -->
      <div class="space-y-1">
        <button @click="toggle('payroll')" class="flex w-full items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group hover:bg-purple-50 hover:shadow-sm"
                :class="{ 'bg-purple-50 text-purple-700 shadow-sm border border-purple-200': groupActive('payroll') }">
          <div class="w-6 h-6 flex items-center justify-center">
            <svg class="w-5 h-5 transition-colors duration-200" :class="groupActive('payroll') ? 'text-purple-600' : 'text-gray-500 group-hover:text-purple-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
            </svg>
          </div>
          <span class="flex-1 text-left font-medium whitespace-nowrap">Payroll Management</span>
          <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open.payroll }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>
        
        <Transition enter-active-class="transition-all duration-300 ease-out" enter-from-class="opacity-0 max-h-0 transform -translate-y-2"
                    enter-to-class="opacity-100 max-h-96 transform translate-y-0" leave-active-class="transition-all duration-300 ease-in"
                    leave-from-class="opacity-100 max-h-96 transform translate-y-0" leave-to-class="opacity-0 max-h-0 transform -translate-y-2">
          <div v-show="open.payroll" class="ml-6 space-y-1 overflow-hidden border-l-2 border-purple-100 pl-4">
            <router-link to="/payroll/salary-structures" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 group hover:bg-purple-50"
                         :class="{ 'bg-purple-50 text-purple-700 font-medium': isActive('/payroll/salary-structures') }">
              <div class="w-4 h-4 flex items-center justify-center">
                <svg class="w-4 h-4 transition-colors duration-200" :class="isActive('/payroll/salary-structures') ? 'text-purple-600' : 'text-gray-400 group-hover:text-purple-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
              </div>
              <span class="text-sm">Salary Structures</span>
            </router-link>
            <router-link to="/payroll/payslips" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 group hover:bg-purple-50"
                         :class="{ 'bg-purple-50 text-purple-700 font-medium': isActive('/payroll/payslips') }">
              <div class="w-4 h-4 flex items-center justify-center">
                <svg class="w-4 h-4 transition-colors duration-200" :class="isActive('/payroll/payslips') ? 'text-purple-600' : 'text-gray-400 group-hover:text-purple-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </div>
              <span class="text-sm">Pay Slip History</span>
            </router-link>
            <router-link v-if="isSystemAdmin || isHRManager" to="/payroll/payslips/generate" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 group hover:bg-purple-50"
                         :class="{ 'bg-purple-50 text-purple-700 font-medium': isActive('/payroll/payslips/generate') }">
              <div class="w-4 h-4 flex items-center justify-center">
                <svg class="w-4 h-4 transition-colors duration-200" :class="isActive('/payroll/payslips/generate') ? 'text-purple-600' : 'text-gray-400 group-hover:text-purple-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
              </div>
              <span class="text-sm">Generate Pay Slips</span>
            </router-link>
          </div>
        </Transition>
      </div>

      <!-- Notifications -->
      <div class="space-y-1">
        <button @click="toggle('notifications')" class="flex w-full items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group hover:bg-orange-50 hover:shadow-sm"
                :class="{ 'bg-orange-50 text-orange-700 shadow-sm border border-orange-200': groupActive('notifications') }">
          <div class="w-6 h-6 flex items-center justify-center">
            <svg class="w-5 h-5 transition-colors duration-200" :class="groupActive('notifications') ? 'text-orange-600' : 'text-gray-500 group-hover:text-orange-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.828 7l2.586 2.586a2 2 0 002.828 0L16 7l-6 6-6-6z"></path>
            </svg>
          </div>
          <span class="flex-1 text-left font-medium whitespace-nowrap">Notifications</span>
          <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open.notifications }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>
        
        <Transition enter-active-class="transition-all duration-300 ease-out" enter-from-class="opacity-0 max-h-0 transform -translate-y-2"
                    enter-to-class="opacity-100 max-h-96 transform translate-y-0" leave-active-class="transition-all duration-300 ease-in"
                    leave-from-class="opacity-100 max-h-96 transform translate-y-0" leave-to-class="opacity-0 max-h-0 transform -translate-y-2">
          <div v-show="open.notifications" class="ml-6 space-y-1 overflow-hidden border-l-2 border-orange-100 pl-4">
            <router-link v-if="isSystemAdmin || isHRManager" to="/email-notifications/send" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 group hover:bg-orange-50"
                         :class="{ 'bg-orange-50 text-orange-700 font-medium': isActive('/email-notifications/send') }">
              <div class="w-4 h-4 flex items-center justify-center">
                <svg class="w-4 h-4 transition-colors duration-200" :class="isActive('/email-notifications/send') ? 'text-orange-600' : 'text-gray-400 group-hover:text-orange-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
              </div>
              <span class="text-sm">Send Notification</span>
            </router-link>
            <router-link to="/email-notifications/history" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 group hover:bg-orange-50"
                         :class="{ 'bg-orange-50 text-orange-700 font-medium': isActive('/email-notifications/history') }">
              <div class="w-4 h-4 flex items-center justify-center">
                <svg class="w-4 h-4 transition-colors duration-200" :class="isActive('/email-notifications/history') ? 'text-orange-600' : 'text-gray-400 group-hover:text-orange-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <span class="text-sm">Email History</span>
            </router-link>
          </div>
        </Transition>
      </div>

      <!-- Settings (System Admin only) -->
      <div v-if="isSystemAdmin" class="space-y-1">
        <button @click="toggle('settings')" class="flex w-full items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group hover:bg-gray-50 hover:shadow-sm"
                :class="{ 'bg-gray-50 text-gray-700 shadow-sm border border-gray-200': groupActive('settings') }">
          <div class="w-6 h-6 flex items-center justify-center">
            <svg class="w-5 h-5 transition-colors duration-200" :class="groupActive('settings') ? 'text-gray-600' : 'text-gray-500 group-hover:text-gray-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
          </div>
          <span class="flex-1 text-left font-medium whitespace-nowrap">Settings</span>
          <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open.settings }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>
        
        <Transition enter-active-class="transition-all duration-300 ease-out" enter-from-class="opacity-0 max-h-0 transform -translate-y-2"
                    enter-to-class="opacity-100 max-h-96 transform translate-y-0" leave-active-class="transition-all duration-300 ease-in"
                    leave-from-class="opacity-100 max-h-96 transform translate-y-0" leave-to-class="opacity-0 max-h-0 transform -translate-y-2">
          <div v-show="open.settings" class="ml-6 space-y-1 overflow-hidden border-l-2 border-gray-100 pl-4">
            <router-link to="/branding" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 group hover:bg-gray-50"
                         :class="{ 'bg-gray-50 text-gray-700 font-medium': isActive('/branding') }">
              <div class="w-4 h-4 flex items-center justify-center">
                <svg class="w-4 h-4 transition-colors duration-200" :class="isActive('/branding') ? 'text-gray-600' : 'text-gray-400 group-hover:text-gray-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"></path>
                </svg>
              </div>
              <span class="text-sm">Branding</span>
            </router-link>
          </div>
        </Transition>
      </div>

      <!-- Admins (System Admin only) -->
      <router-link v-if="isSystemAdmin" to="/admins" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group hover:bg-red-50 hover:shadow-sm"
                   :class="{ 'bg-red-50 text-red-700 shadow-sm border border-red-200': isActive('/admins') }">
        <div class="w-6 h-6 flex items-center justify-center">
          <svg class="w-5 h-5 transition-colors duration-200" :class="isActive('/admins') ? 'text-red-600' : 'text-gray-500 group-hover:text-red-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
          </svg>
        </div>
        <span class="font-medium whitespace-nowrap">Admins</span>
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
  if (group === 'employee') return ['/departments','/designations','/employees','/training-policies'].some(isActive)
  if (group === 'attendance') return ['/attendance/daily','/attendance/leaves','/attendance/holidays'].some(isActive)
  if (group === 'payroll') return ['/payroll/salary-structures','/payroll/payslips'].some(isActive)
  if (group === 'notifications') return ['/email-notifications/send','/email-notifications/history'].some(isActive)
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