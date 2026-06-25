<script setup lang="ts">
import { useOrderStore } from '@/stores/orderStore';
import OrderCard from '@/components/order/OrderCard.vue';
import { PackageOpen, ChevronLeft } from 'lucide-vue-next';

const orderStore = useOrderStore();
</script>

<template>
  <div class="space-y-6 pb-16 animate-fade-in" id="order-history-view-container">
    
    <!-- Top back link -->
    <div>
      <router-link 
        to="/" 
        class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-900 cursor-pointer"
        id="btn-history-back"
      >
        <ChevronLeft class="h-4 w-4" />
        Back to listings
      </router-link>
    </div>

    <!-- Header info -->
    <div>
      <h2 class="font-sans font-extrabold text-2xl tracking-tight text-slate-900">Your Order History</h2>
      <p class="text-xs text-slate-505 font-semibold mt-1">Track active deliveries, check logistics, and print receipt records</p>
    </div>

    <!-- List collection -->
    <div v-if="orderStore.orders.length > 0" class="space-y-6" id="history-items-list">
      <div 
        v-for="order in orderStore.orders" 
        :key="order.id"
      >
        <OrderCard :order="order" />
      </div>
    </div>

    <!-- Empty history screen -->
    <div 
      v-else 
      class="py-16 px-6 max-w-sm mx-auto text-center space-y-4 bg-slate-50 border border-slate-100 rounded-3xl"
      id="history-empty-case"
    >
      <span class="inline-block p-4 bg-slate-100 text-slate-400 rounded-full">
        <PackageOpen class="h-8 w-8" />
      </span>
      <h3 class="font-sans font-bold text-lg text-slate-900">No Orders Placed Yet</h3>
      <p class="text-xs text-slate-500 font-medium leading-relaxed max-w-xs mx-auto">
        When you submit checkouts, details will log here to support tracking, shipping status updates, and invoicing summaries.
      </p>
      <router-link 
        to="/" 
        class="inline-block px-5 py-2.5 bg-slate-900 text-white rounded-xl text-xs font-bold font-sans hover:bg-slate-800 transition-colors cursor-pointer"
      >
        Go to catalog
      </router-link>
    </div>

  </div>
</template>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.15s ease-out forwards;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(4px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
