<template>
  <div class="p-6 space-y-6">
    <div class="flex items-center gap-4">
      <img :src="profile?.user?.avatar" alt="avatar" class="w-20 h-20 rounded-full object-cover border" />
      <div>
        <div class="text-2xl font-semibold">{{ profile?.user?.name }}</div>
        <div class="text-gray-600">{{ profile?.user?.email }}</div>
        <div class="mt-1 inline-flex items-center gap-2 text-sm">
          <span class="px-2 py-0.5 rounded bg-indigo-100 text-indigo-700">{{ profile?.user?.admin_type_label }}</span>
          <span v-if="profile?.last_login_at" class="text-gray-500">Last login: {{ formatDate(profile.last_login_at) }}</span>
        </div>
      </div>
    </div>

    <div class="flex items-center gap-3">
      <router-link class="px-3 py-2 bg-indigo-600 text-white rounded" :to="{ name: 'profile.update' }">Update Profile</router-link>
      <router-link class="px-3 py-2 bg-slate-700 text-white rounded" :to="{ name: 'profile.password' }">Change Password</router-link>
    </div>

    <div class="bg-white border rounded">
      <div class="px-4 py-3 border-b font-medium">Activity Logs</div>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="bg-slate-50 text-left">
              <th class="px-4 py-2">Action</th>
              <th class="px-4 py-2">Timestamp</th>
              <th class="px-4 py-2">IP Address</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="log in logs?.data || []" :key="log.id" class="border-t">
              <td class="px-4 py-2 uppercase">{{ log.type.replace('_',' ') }}</td>
              <td class="px-4 py-2">{{ formatDate(log.created_at) }}</td>
              <td class="px-4 py-2">{{ log.ip }}</td>
            </tr>
            <tr v-if="(logs?.data || []).length === 0">
              <td colspan="3" class="px-4 py-6 text-center text-gray-500">No logs found</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="p-3 flex items-center justify-between text-sm">
        <div>Page {{ logs?.current_page }} of {{ logs?.last_page }}</div>
        <div class="flex gap-2">
          <button class="px-3 py-1 border rounded disabled:opacity-50" :disabled="!logs || logs.current_page <= 1" @click="fetchLogs(logs.current_page - 1)">Prev</button>
          <button class="px-3 py-1 border rounded disabled:opacity-50" :disabled="!logs || logs.current_page >= logs.last_page" @click="fetchLogs(logs.current_page + 1)">Next</button>
        </div>
      </div>
    </div>
  </div>
  
</template>

<script setup>
import { onMounted, ref } from 'vue'
import axios from 'axios'

const profile = ref(null)
const logs = ref(null)

function formatDate(value) {
  return new Date(value).toLocaleString()
}

async function fetchProfile() {
  const { data } = await axios.get('/auth/profile')
  profile.value = data
}

async function fetchLogs(page = 1) {
  const { data } = await axios.get('/auth/profile/logs', { params: { page, per_page: 50 } })
  logs.value = data
}

onMounted(async () => {
  await Promise.all([fetchProfile(), fetchLogs(1)])
})
</script>

<style scoped>
</style>


