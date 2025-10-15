<template>
  <nav class="bg-white border-b">
    <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
      <div class="font-semibold">HRM</div>
      <div v-if="user" class="relative">
        <button @click="open = !open" class="flex items-center gap-2">
          <img :src="avatar" alt="avatar" class="w-8 h-8 rounded-full object-cover" />
          <span class="text-sm text-gray-700">{{ user.name }}</span>
        </button>
        <div v-show="open" @click.outside="open=false" class="absolute right-0 mt-2 w-56 bg-white border rounded shadow-md">
          <div class="p-3 border-b flex items-center gap-2">
            <img :src="avatar" alt="avatar" class="w-10 h-10 rounded-full object-cover" />
            <div>
              <div class="text-sm font-medium">{{ user.name }}</div>
              <div class="text-xs text-gray-500">{{ user.admin_type_label || user.admin_type }}</div>
            </div>
          </div>
          <div class="py-1 text-sm">
            <a href="#" class="block px-3 py-2 hover:bg-gray-50">Profile</a>
            <a v-if="!isMicrosoftUser" href="#" class="block px-3 py-2 hover:bg-gray-50">Change Password</a>
            <a href="#" class="block px-3 py-2 hover:bg-gray-50">Settings</a>
            <button @click="$emit('logout')" class="w-full text-left px-3 py-2 hover:bg-gray-50">Logout</button>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
const props = defineProps({ user: Object })
const open = ref(false)
const avatar = computed(() => props.user?.avatar || 'https://via.placeholder.com/80x80.png?text=👤')

// Check if user is a Microsoft user
const isMicrosoftUser = computed(() => {
  return props.user?.account_source === 'microsoft_login' && props.user?.microsoft_id !== null
})

function onClick(e) {
  const el = e.target.closest('[data-dropdown]')
}

function handleOutside(event) {
  const dropdown = document.querySelector('[data-user-dropdown]')
  if (!dropdown) return
  if (!dropdown.contains(event.target)) open.value = false
}

onMounted(() => document.addEventListener('click', handleOutside))
onBeforeUnmount(() => document.removeEventListener('click', handleOutside))
</script>

