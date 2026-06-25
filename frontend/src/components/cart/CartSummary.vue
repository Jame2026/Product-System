<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useCartStore } from '@/stores/cartStore';
import { formatPrice } from '@/utils/formatPrice';
import { 
  Tag, 
  Trash2, 
  Sparkles, 
  ShieldCheck, 
  ChevronRight,
  Info
} from 'lucide-vue-next';

const router = useRouter();
const cartStore = useCartStore();

const couponInput = ref('');
const couponError = ref(false);

const handleApplyCoupon = () => {
  if (couponInput.value.trim() === '') return;
  const success = cartStore.applyCoupon(couponInput.value);
  if (success) {
    couponError.value = false;
    couponInput.value = '';
  } else {
    couponError.value = true;
  }
};

const handleRemoveCoupon = () => {
  cartStore.removeCoupon();
  couponError.value = false;
};

const handleProceedToCheckout = () => {
  router.push({ name: 'Checkout' });
};

// Compute progress towards Free Shipping ($150 limit)
const freeShippingLimit = 150;
const percentToFreeShipping = ref(0);

const calcShippingProgress = () => {
  const currentSubtotal = cartStore.subtotal;
  if(currentSubtotal >= freeShippingLimit){
    return 100;
  }
  return (currentSubtotal / freeShippingLimit) * 100;
};

const neededForFreeShipping = () => {
  const diff = freeShippingLimit - cartStore.subtotal;
  return Math.max(0, diff);
};
</script>

<template>
  <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm space-y-6 sticky top-24" id="cart-summary-box">
    <h3 class="font-sans font-bold text-lg text-slate-900 border-b border-slate-50 pb-3">Order Summary</h3>

    <!-- Free Shipping Promo Line -->
    <div class="space-y-2">
      <div class="flex items-center justify-between text-xs font-semibold text-slate-700">
        <span class="flex items-center gap-1.5">
          <Sparkles class="h-4 w-4 text-yellow-500" />
          <span v-if="cartStore.shippingFee === 0">Congratulations! You get Free Shipping!</span>
          <span v-else>Free express shipping over {{ formatPrice(freeShippingLimit) }}</span>
        </span>
        <span v-if="cartStore.shippingFee > 0">{{ formatPrice(neededForFreeShipping()) }} left</span>
      </div>
      
      <!-- Progress Bar Graphic -->
      <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
        <div 
          class="bg-slate-950 h-full rounded-full transition-all duration-300"
          :style="{ width: `${calcShippingProgress()}%` }"
        ></div>
      </div>
    </div>

    <!-- Calculations lines -->
    <div class="space-y-3 pt-2">
      <div class="flex items-center justify-between text-sm text-slate-500 font-medium">
        <span>Subtotal</span>
        <span class="text-slate-850 font-bold">{{ formatPrice(cartStore.subtotal) }}</span>
      </div>

      <!-- Copuon Discount details if active -->
      <div 
        v-if="cartStore.couponDiscount > 0"
        class="flex items-center justify-between text-sm text-emerald-600 font-semibold bg-emerald-50/50 p-2.5 rounded-xl border border-emerald-100/40"
      >
        <span class="flex items-center gap-1.5">
          <Tag class="h-3.5 w-3.5" />
          Promo code ({{ cartStore.couponCode }})
        </span>
        <span class="flex items-center gap-1">
          -{{ formatPrice(cartStore.discountAmount) }}
          <button 
            @click="handleRemoveCoupon" 
            class="p-0.5 text-slate-400 hover:text-rose-500 hover:bg-white rounded cursor-pointer"
            title="Remove Promo"
          >
            <Trash2 class="h-3.5 w-3.5" />
          </button>
        </span>
      </div>

      <div class="flex items-center justify-between text-sm text-slate-500 font-medium">
        <span>Estimated Services & Shipping</span>
        <span v-if="cartStore.shippingFee === 0" class="text-emerald-600 font-bold">FREE</span>
        <span v-else class="text-slate-850 font-bold">{{ formatPrice(cartStore.shippingFee) }}</span>
      </div>

      <div class="flex items-center justify-between text-sm text-slate-500 font-medium">
        <span>Sales Tax (8.25%)</span>
        <span class="text-slate-850 font-bold">{{ formatPrice(cartStore.taxAmount) }}</span>
      </div>

      <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
        <span class="text-base font-bold text-slate-900">Estimated Total</span>
        <span class="text-xl sm:text-2xl font-bold text-slate-900" id="cart-summary-total-price">
          {{ formatPrice(cartStore.total) }}
        </span>
      </div>
    </div>

    <!-- Coupon Input Field -->
    <div class="space-y-2 pt-2 border-t border-slate-100">
      <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Promo Code</label>
      <div class="flex gap-2">
        <input 
          type="text" 
          placeholder="e.g. WELCOME10, SAVE20" 
          v-model="couponInput"
          @keydown.enter.prevent="handleApplyCoupon"
          class="bg-slate-50 border border-slate-150 rounded-xl px-3.5 py-2 text-xs w-full text-slate-800 placeholder-slate-400 focus:outline-none focus:border-slate-350"
          id="coupon-input-field"
        />
        <button 
          @click="handleApplyCoupon"
          class="bg-slate-900 text-white rounded-xl text-xs font-bold px-4 py-2 hover:bg-slate-800 active:scale-95 transition-all cursor-pointer"
        >
          Apply
        </button>
      </div>
      
      <!-- Coupon Error message -->
      <p 
        v-if="couponError" 
        class="text-[11px] text-rose-500 font-semibold"
      >
        Invalid code. Try "WELCOME10" (10% off) or "SAVE20" (20% off).
      </p>

      <div class="flex items-center gap-1 text-[10.5px] text-slate-400 leading-relaxed font-semibold">
        <Info class="h-3 w-3 text-slate-350 flex-shrink-0" />
        <span>Valid Codes: <strong class="text-slate-650">WELCOME10</strong>, <strong class="text-slate-650">SAVE20</strong></span>
      </div>
    </div>

    <!-- Checkout Action buttons -->
    <div class="pt-4 space-y-2">
      <button 
        @click="handleProceedToCheckout"
        class="w-full bg-slate-900 text-white text-sm font-bold py-3.5 rounded-xl shadow hover:bg-slate-800 active:scale-[0.98] transition-all flex items-center justify-center gap-2 group cursor-pointer"
        id="btn-proceed-checkout"
      >
        Secure Checkout
        <ChevronRight class="h-4 w-4 group-hover:translate-x-0.5 transition-transform" />
      </button>

      <router-link 
        to="/" 
        class="block w-full text-center text-xs text-slate-500 hover:text-slate-800 font-semibold py-2"
      >
        Continue Shopping
      </router-link>
    </div>

    <!-- Trust Seals -->
    <div class="flex items-center justify-center gap-2 pt-2 text-[10.5px] text-slate-400 border-t border-slate-50">
      <ShieldCheck class="h-4 w-4 text-emerald-500" />
      <span>Secure Checkout Powered by 256-bit SSL</span>
    </div>
  </div>
</template>
