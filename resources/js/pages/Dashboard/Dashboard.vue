<template>
  <div class="min-h-screen p-6 space-y-6">
    <div>
      <h1 class="text-2xl font-semibold">Welcome Back, {{ user?.name || 'User' }} 👋</h1>
      <p class="text-sm text-gray-500">{{ user?.admin_type_label || 'Admin Profile' }}</p>
    </div>

    <!-- Top KPI cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow p-4">
        <div class="text-sm text-gray-500">Total Employees</div>
        <div class="text-2xl font-semibold">200<span class="text-sm text-gray-400">/200</span></div>
        <div class="text-emerald-600 text-xs mt-1">▲ +15%</div>
      </div>
      <div class="bg-white rounded-xl shadow p-4">
        <div class="text-sm text-gray-500">On Leaves</div>
        <div class="text-2xl font-semibold">20<span class="text-sm text-gray-400">/200</span></div>
        <div class="text-rose-600 text-xs mt-1">▼ -10%</div>
      </div>
      <div class="bg-white rounded-xl shadow p-4">
        <div class="text-sm text-gray-500">New Joinee</div>
        <div class="text-2xl font-semibold">10<span class="text-sm text-gray-400">/200</span></div>
        <div class="text-emerald-600 text-xs mt-1">▲ +12%</div>
      </div>
      <div class="bg-white rounded-xl shadow p-4">
        <div class="text-sm text-gray-500">Distribution</div>
        <div class="flex items-center gap-4 mt-2">
          <div class="w-16 h-16 rounded-full border-[10px] border-amber-400 relative">
            <div class="absolute inset-2 rounded-full border-[8px] border-indigo-400"></div>
          </div>
          <div class="text-xs text-gray-600">
            <div><span class="inline-block w-2 h-2 rounded-full bg-amber-400 mr-2"></span>Remote 23%</div>
            <div><span class="inline-block w-2 h-2 rounded-full bg-indigo-400 mr-2"></span>Onsite 77%</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Widgets grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="bg-white rounded-xl shadow p-4">
        <div class="flex items-center justify-between text-sm text-gray-500 mb-2">
          <span>My Attendance</span>
          <a href="#" class="text-xs text-gray-400">View Full Stats</a>
        </div>
        <div class="flex items-center gap-4">
          <div class="w-28 h-28 rounded-full border-[12px] border-emerald-500 flex items-center justify-center">
            <div class="text-center">
              <div class="text-xl font-semibold">130</div>
              <div class="text-xs text-gray-400">/45 hour spend</div>
            </div>
          </div>
          <ul class="text-xs text-gray-600 space-y-1">
            <li><span class="inline-block w-2 h-2 rounded-full bg-emerald-500 mr-2"></span>45 Hrs Spending Hours</li>
            <li><span class="inline-block w-2 h-2 rounded-full bg-blue-500 mr-2"></span>130 Hrs Total Hours</li>
            <li><span class="inline-block w-2 h-2 rounded-full bg-indigo-500 mr-2"></span>75 Hrs Remaining Hours</li>
            <li><span class="inline-block w-2 h-2 rounded-full bg-rose-500 mr-2"></span>10 Hrs not spent this week</li>
          </ul>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow p-4">
        <div class="flex items-center justify-between text-sm text-gray-500 mb-2">
          <span>Salary Slip</span>
          <span class="text-xs">Current Month</span>
        </div>
        <div class="grid place-items-center py-6">
          <div class="w-16 h-20 bg-gray-100 rounded shadow-inner grid place-items-center">PDF</div>
          <button class="mt-4 px-4 py-1.5 rounded bg-indigo-600 text-white text-xs">Download</button>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow p-4">
        <div class="flex items-center justify-between text-sm text-gray-500 mb-2">
          <span>Requests</span>
          <div class="text-xs space-x-2">
            <span class="text-emerald-600">● Approved</span>
            <span class="text-rose-600">● Reject</span>
            <span class="text-amber-500">● Pending</span>
          </div>
        </div>
        <ul class="text-xs text-gray-700 space-y-2">
          <li class="flex items-center justify-between"><span>New Laptop Request</span><span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700">Accepted</span><span class="text-gray-400">09-Oct-2023</span></li>
          <li class="flex items-center justify-between"><span>Loan Request</span><span class="px-2 py-0.5 rounded bg-rose-50 text-rose-700">Rejected</span><span class="text-gray-400">08-Oct-2023</span></li>
          <li class="flex items-center justify-between"><span>Salary Request</span><span class="px-2 py-0.5 rounded bg-amber-50 text-amber-700">Pending</span><span class="text-gray-400">05-Oct-2023</span></li>
        </ul>
      </div>

      <div class="bg-white rounded-xl shadow p-4 md:col-span-1">
        <div class="text-sm text-gray-500 mb-2">Announcements & Notice Board</div>
        <ul class="text-xs text-gray-700 space-y-2">
          <li class="flex items-center justify-between"><span>Internal News</span><span class="text-gray-400">09-Oct-2023</span></li>
          <li class="flex items-center justify-between"><span>Industry News</span><span class="text-gray-400">09-Oct-2023</span></li>
          <li class="flex items-center justify-between"><span>Calendar Events</span><span class="text-gray-400">09-Oct-2023</span></li>
        </ul>
      </div>

      <div class="bg-white rounded-xl shadow p-4">
        <div class="text-sm text-gray-500 mb-2">HR Policies</div>
        <div class="grid grid-cols-2 gap-3">
          <div class="bg-gray-100 rounded h-24 grid place-items-center">PDF</div>
          <div class="bg-gray-100 rounded h-24 grid place-items-center">PDF</div>
        </div>
        <button class="mt-4 px-4 py-1.5 rounded bg-indigo-600 text-white text-xs">Download</button>
      </div>

      <div class="bg-white rounded-xl shadow p-4">
        <div class="flex items-center justify-between text-sm text-gray-500 mb-2">
          <span>My Team</span>
          <a href="#" class="text-xs">View All</a>
        </div>
        <ul class="text-xs text-gray-700 space-y-2">
          <li class="flex items-center justify-between"><span>Umair Ali</span><span class="text-gray-500">Devops</span><span>📞</span></li>
          <li class="flex items-center justify-between"><span>Hassan</span><span class="text-gray-500">Front-end Developer</span><span>📞</span></li>
          <li class="flex items-center justify-between"><span>Murtaza</span><span class="text-gray-500">UI UX Designer</span><span>📞</span></li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const data = ref(null)
const user = ref(null)

async function load() {
  const res = await axios.get('/dashboard/data')
  data.value = res.data
  try {
    const me = await axios.get('/auth/user')
    user.value = me.data
  } catch (e) {
    user.value = null
  }
}

onMounted(load)
</script>

