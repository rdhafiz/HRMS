<template>
  <div class="min-h-screen bg-gray-50 text-gray-900">
    <Header :user="user" @logout="logout"></Header>
    <div class="flex">
      <SideNav :user="user" />
      <main class="flex-1 p-6">
        <Transition enter-active-class="transition duration-200 ease-out"
                    enter-from-class="opacity-0 translate-y-1"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition duration-150 ease-in"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 translate-y-1">
          <router-view />
        </Transition>
      </main>
    </div>
  </div>
  
</template>

<script setup>
import Header from '@/components/Header.vue'
import SideNav from '@/components/SideNav.vue'
import { ref, onMounted } from 'vue'
import axios from 'axios'

const user = ref(null)

async function fetchUser() {
  try {
    const { data } = await axios.get('/auth/user')
    user.value = data
  } catch (e) {
    user.value = null
  }
}

async function logout() {
  try {
    await axios.post('/auth/logout')
  } finally {
    window.location.href = '/'
  }
}

onMounted(fetchUser)
</script>

