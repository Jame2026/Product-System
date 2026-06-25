<script setup lang="ts">
import Navbar from '@/components/layout/Navbar.vue';
import Footer from '@/components/layout/Footer.vue';
</script>

<template>
  <div class="min-h-screen bg-slate-50/50 flex flex-col justify-between" id="app-viewport">
    
    <!-- Navigation row -->
    <Navbar />

    <!-- Core Body Route Section -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1 w-full min-h-[64vh]">
      <router-view v-slot="{ Component }">
        <transition name="route-switch" mode="out-in">
          <component :is="Component" class="w-full" />
        </transition>
      </router-view>
    </main>

    <!-- Footer row -->
    <Footer />

  </div>
</template>

<style>
/* Global Router transition definitions */
.route-switch-enter-active,
.route-switch-leave-active {
  transition: opacity 0.15s ease, transform 0.15s ease;
}

.route-switch-enter-from {
  opacity: 0;
  transform: translateY(4px);
}

.route-switch-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}

/* Custom Scrollbar for premium retail vibes */
::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}

::-webkit-scrollbar-track {
  background: transparent;
}

::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 99px;
}

::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>
