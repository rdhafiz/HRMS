<template>
  <div class="bg-gray-900 min-h-screen">
    <router-view v-slot="{ Component }">
      <transition
        enter-active-class="transition-opacity duration-500"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-500"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <component :is="Component" />
      </transition>
    </router-view>
  </div>
</template>

<script setup>
// layout without header/sidenav
</script>

