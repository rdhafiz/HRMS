<template>
  <div class="min-h-screen bg-gray-50 text-gray-900">
    <Header :user="user" @logout="logout"></Header>
    <div class="flex">
      <EmployeeSideNav :user="user" />
      <main class="flex-1">
        <!-- Universal Container -->
        <div class="max-w-7xl mx-auto px-4 py-6">
          <router-view v-slot="{ Component }">
            <Transition enter-active-class="transition duration-200 ease-out"
                        enter-from-class="opacity-0 translate-y-1"
                        enter-to-class="opacity-100 translate-y-0"
                        leave-active-class="transition duration-150 ease-in"
                        leave-from-class="opacity-100 translate-y-0"
                        leave-to-class="opacity-0 translate-y-1">
              <component :is="Component" />
            </Transition>
          </router-view>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import Header from '@/components/Header.vue'
import EmployeeSideNav from '@/components/EmployeeSideNav.vue'
import { computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const router = useRouter()

const user = computed(() => authStore.user)

async function logout() {
  await authStore.logout()
  router.push({ name: 'login' })
}

onMounted(async () => {
  if (!authStore.user && authStore.token) {
    try {
      await authStore.fetchUser()
    } catch (error) {
      // Token is invalid, user will be redirected by router guard
    }
  }
})
</script>

