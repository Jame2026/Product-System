<script setup lang="ts">
import { useCartStore } from '@/stores/cartStore';
import CartItem from '@/components/cart/CartItem.vue';
import CartSummary from '@/components/cart/CartSummary.vue';
import { ShoppingBag, ChevronLeft } from 'lucide-vue-next';

const cartStore = useCartStore();
</script>

<template>
  <div class="space-y-6 pb-16 animate-fade-in" id="cart-view-container">
    <!-- Breadcrumb back link -->
    <div>
      <router-link 
        to="/" 
        class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-900 cursor-pointer"
        id="btn-cart-back-home"
      >
        <ChevronLeft class="h-4 w-4" />
        Continue shopping
      </router-link>
    </div>

    <!-- Header info -->
    <div>
      <h2 class="font-sans font-extrabold text-2xl tracking-tight text-slate-900">Your Shopping Cart</h2>
      <p class="text-xs text-slate-505 font-semibold mt-1">Review active selections and proceed to secure checkout</p>
    </div>

    <!-- Layout Grid: Two core columns on lg screen -->
    <div 
      v-if="cartStore.cartDetails.length > 0"
      class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start"
    >
      <!-- Left side (Iterated items) -->
      <div class="lg:col-span-8 space-y-4">
        <div 
          v-for="item in cartStore.cartDetails" 
          :key="`${item.productId}-${item.selectedColor || ''}-${item.selectedSize || ''}`"
        >
          <CartItem :item="item" />
        </div>

        <!-- Utility: Clear cart buffer -->
        <div class="flex items-center justify-between pt-2">
          <button 
            @click="cartStore.clearCart"
            class="text-xs font-bold text-slate-400 hover:text-rose-500 transition-colors flex items-center gap-1 cursor-pointer focus:outline-none"
            id="btn-clear-cart"
          >
            Clear All Items
          </button>
          
          <span class="text-xs text-slate-400 font-semibold">
            Subtotal of {{ cartStore.totalItemsCount }} item(s) to verify
          </span>
        </div>
      </div>

      <!-- Right side (Sums and coupons, sticky) -->
      <div class="lg:col-span-4">
        <CartSummary />
      </div>
    </div>

    <!-- Empty State if cart is cleared -->
    <div 
      v-else 
      class="py-16 px-6 max-w-sm mx-auto text-center space-y-4 bg-slate-50 border border-slate-100 rounded-3xl"
      id="cart-empty-case"
    >
      <span class="inline-block p-4 bg-slate-100 text-slate-400 rounded-full">
        <ShoppingBag class="h-8 w-8" />
      </span>
      <h3 class="font-sans font-bold text-lg text-slate-900">Your cart is empty</h3>
      <p class="text-xs text-slate-500 font-medium leading-relaxed max-w-xs mx-auto">
        Selections you add will display in this workspace. Review clothing, gear, or productivity accessories to populate your checkout queue.
      </p>
      <router-link 
        to="/" 
        class="inline-block px-5 py-2.5 bg-slate-900 text-white rounded-xl text-xs font-bold font-sans hover:bg-slate-800 transition-colors cursor-pointer"
      >
        Go Shop Curations
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
