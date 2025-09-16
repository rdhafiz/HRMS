<template>
  <header class="bg-white border-b">
    <div class="h-16 flex items-center justify-between px-4">
      <div class="flex items-center">
        <template v-if="logoUrl">
          <img :src="logoUrl" alt="logo" class="h-9 w-auto" />
        </template>
        <template v-else>
          <div class="text-lg font-bold">HRMS</div>
        </template>
      </div>
      <div class="flex items-center gap-3 relative" data-user-dropdown>
        <button @click="open = !open" class="flex items-center gap-2">
          <img :src="avatarUrl" alt="avatar" class="w-10 h-10 rounded-full object-cover ring-4 ring-amber-300" />
        </button>
        <div v-show="open" class="absolute right-0 top-12 w-64 bg-white border rounded shadow-md z-10">
          <div class="p-3 border-b flex items-center gap-2">
            <img :src="avatarUrl" alt="avatar" class="w-10 h-10 rounded-full object-cover" />
            <div>
              <div class="text-sm font-medium">{{ user?.name || 'Admin Name' }}</div>
              <div class="text-xs text-gray-500">{{ user?.admin_type_label || 'system_admin' }}</div>
            </div>
          </div>
          <div class="py-1 text-sm">
            <router-link to="/profile" class="block px-3 py-2 hover:bg-gray-50">Profile</router-link>
            <button @click="$emit('logout')" class="w-full text-left px-3 py-2 hover:bg-gray-50">Logout</button>
          </div>
        </div>
        <slot name="right"></slot>
      </div>
    </div>
  </header>
  
</template>

<script setup>
import { computed, ref, onMounted, onBeforeUnmount } from 'vue'
import axios from 'axios'
const props = defineProps({ user: Object })
const avatarUrl = computed(() => props.user?.avatar || 'https://avatar.iran.liara.run/public')
const open = ref(false)
const logoUrl = ref('')

function handleOutside(event) {
  const dropdown = document.querySelector('[data-user-dropdown]')
  if (!dropdown) return
  if (!dropdown.contains(event.target)) open.value = false
}

onMounted(() => document.addEventListener('click', handleOutside))
onBeforeUnmount(() => document.removeEventListener('click', handleOutside))

onMounted(async () => {
  try {
    const { data } = await axios.get('/branding')
    logoUrl.value = data?.site_logo_url || ''
  } catch (e) {
    logoUrl.value = ''
  }
})
</script>

