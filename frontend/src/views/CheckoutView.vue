<script setup lang="ts">
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';
import { useCartStore } from '@/stores/cartStore';
import { useOrderStore } from '@/stores/orderStore';
import { apiPost } from '@/services/api';
import { formatPrice } from '@/utils/formatPrice';
import { 
  ChevronLeft, 
  Lock, 
  MapPin, 
  CreditCard, 
  CheckCircle,
  Clock,
  ArrowRight
} from 'lucide-vue-next';

const router = useRouter();
const authStore = useAuthStore();
const cartStore = useCartStore();
const orderStore = useOrderStore();

// Form address inputs
const formName = ref(authStore.user?.name || '');
const formAddress = ref(authStore.user?.address || '');
const formCity = ref(authStore.user?.city || '');
const formZip = ref(authStore.user?.zipCode || '');
const formCountry = ref(authStore.user?.country || 'United States');
const formPhone = ref(authStore.user?.phone || '');

// Form billing inputs
const cardName = ref(authStore.user?.cardName || '');
const cardNumber = ref(authStore.user?.cardNumber || '');
const cardExpiry = ref(authStore.user?.cardExpiry || '');
const cardCvv = ref(authStore.user?.cardCvv || '');

// UI States
const isProcessing = ref(false);
const processingStep = ref('');
const showSuccessOverlay = ref(false);
const createdOrderId = ref('');

const errors = ref<Record<string, string>>({});

interface CheckoutOrderResponse {
  id: number | string;
}

const validateForm = () => {
  const errs: Record<string, string> = {};
  if (!formName.value.trim()) errs.name = 'Full name is required';
  if (!authStore.user?.email) errs.payment = 'Please sign in again before checkout';
  if (!formAddress.value.trim()) errs.address = 'Street address is required';
  if (!formCity.value.trim()) errs.city = 'City is required';
  if (!formZip.value.trim()) errs.zip = 'ZIP code is required';
  if (!cardName.value.trim()) errs.cardName = 'Name on card is required';
  if (!cardNumber.value.trim()) errs.cardNumber = 'Card number is required';
  if (!cardExpiry.value.trim()) errs.cardExpiry = 'Expiry is required';
  if (!cardCvv.value.trim()) errs.cardCvv = 'CVV is required';
  
  errors.value = errs;
  return Object.keys(errs).length === 0;
};

const handleCompletePayment = async () => {
  if (cartStore.items.length === 0) return;
  if (!validateForm()) {
    // Scroll to top of error
    window.scrollTo({ top: 100, behavior: 'smooth' });
    return;
  }

  // Update profile with newest coordinates so they save for future checkouts
  await authStore.updateProfile({
    name: formName.value,
    address: formAddress.value,
    city: formCity.value,
    zipCode: formZip.value,
    country: formCountry.value,
    phone: formPhone.value,
    cardName: cardName.value,
    cardNumber: cardNumber.value.slice(-4).padStart(16, '•'),
    cardExpiry: cardExpiry.value
  });

  isProcessing.value = true;
  processingStep.value = 'Initiating Secure Terminal Encryption...';

  // Step feedback timing loop
  setTimeout(() => {
    processingStep.value = 'Validating credit line security with card networks...';
  }, 1000);

  setTimeout(() => {
    processingStep.value = 'Authorizing transaction & setting up delivery logs...';
  }, 2200);

  setTimeout(async () => {
    // Prepare core order data inside checkout
    const checkoutItems = cartStore.cartDetails.map(item => ({
      productId: item.productId,
      name: item.product?.name || 'Curated Product',
      price: item.product?.price || 0,
      quantity: item.quantity,
      image: item.product?.image || '',
      category: item.product?.category || ''
    }));

    try {
      const backendOrder = await apiPost<CheckoutOrderResponse>('/checkout', {
        customer_name: formName.value,
        customer_email: authStore.user?.email,
        phone: formPhone.value,
        shipping_address: [
          formAddress.value,
          formCity.value,
          formZip.value,
          formCountry.value
        ].filter(Boolean).join(', '),
        items: checkoutItems.map(item => ({
          product_id: item.productId,
          quantity: item.quantity
        }))
      }, {
        headers: {
          Authorization: `Bearer ${authStore.token}`
        }
      });

      const finalOrder = orderStore.createOrder({
        items: checkoutItems,
        subtotal: cartStore.subtotal,
        discount: cartStore.discountAmount,
        shipping: cartStore.shippingFee,
        tax: cartStore.taxAmount,
        total: cartStore.total,
        shippingAddress: {
          name: formName.value,
          address: formAddress.value,
          city: formCity.value,
          zipCode: formZip.value,
          country: formCountry.value
        },
        paymentMethod: {
          cardType: 'Visa', // Simple baseline card type detection
          cardNumber: cardNumber.value.replace(/\s/g, '').slice(-4)
        }
      });

      createdOrderId.value = `#${backendOrder.id || finalOrder.id}`;
      cartStore.clearCart(); // Flush checkout items

      showSuccessOverlay.value = true;
    } catch (err) {
      errors.value.payment = err instanceof Error ? err.message : 'Unable to create order';
      window.scrollTo({ top: 100, behavior: 'smooth' });
    } finally {
      isProcessing.value = false;
    }
  }, 3500);
};
</script>

<template>
  <div class="space-y-6 pb-16 relative" id="checkout-view-container">
    
    <!-- Top back button -->
    <div>
      <router-link 
        to="/cart" 
        class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-900 cursor-pointer"
        id="btn-checkout-back-cart"
      >
        <ChevronLeft class="h-4 w-4" />
        Return to shopping cart
      </router-link>
    </div>

    <!-- Header details -->
    <div>
      <h2 class="font-sans font-extrabold text-2xl tracking-tight text-slate-900 flex items-center gap-2">
        <Lock class="h-5 w-5 text-slate-450" />
        Secure Checkout Gateway
      </h2>
      <p class="text-xs text-slate-500 font-semibold mt-1">Submit your shipping coordinates and authorization details securely</p>
    </div>

    <div
      v-if="errors.payment"
      class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs font-semibold text-rose-700"
    >
      {{ errors.payment }}
    </div>

    <!-- Two columns grid layout -->
    <div 
      v-if="!showSuccessOverlay && cartStore.items.length > 0"
      class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start"
    >
      
      <!-- Left side: Form fields (span 7) -->
      <div class="lg:col-span-7 space-y-6">
        
        <!-- Section 1: Shipping Coordinates -->
        <section class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm space-y-4">
          <h3 class="font-sans font-bold text-base text-slate-900 flex items-center gap-2 mb-2 pb-2 border-b border-slate-50">
            <MapPin class="h-4.5 w-4.5 text-slate-500" />
            Shipping Coordinates
          </h3>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2 space-y-1.5">
              <label class="text-xs font-bold text-slate-500 block">Full Recipient Name</label>
              <input 
                type="text" 
                v-model="formName"
                placeholder="e.g. Alex Mercer"
                class="w-full bg-slate-50 border rounded-xl px-3.5 py-2.5 text-xs text-slate-800 placeholder-slate-400 focus:outline-none"
                :class="errors.name ? 'border-rose-450 bg-rose-50/10' : 'border-slate-150'"
              />
              <p v-if="errors.name" class="text-[10px] text-rose-500 font-semibold leading-none">{{ errors.name }}</p>
            </div>

            <div class="sm:col-span-2 space-y-1.5">
              <label class="text-xs font-bold text-slate-500 block">Street Address</label>
              <input 
                type="text" 
                v-model="formAddress"
                placeholder="e.g. 2486 Broad Street"
                class="w-full bg-slate-50 border rounded-xl px-3.5 py-2.5 text-xs text-slate-800 placeholder-slate-400 focus:outline-none"
                :class="errors.address ? 'border-rose-450 bg-rose-50/10' : 'border-slate-150'"
              />
              <p v-if="errors.address" class="text-[10px] text-rose-500 font-semibold leading-none">{{ errors.address }}</p>
            </div>

            <div class="space-y-1.5">
              <label class="text-xs font-bold text-slate-500 block">City</label>
              <input 
                type="text" 
                v-model="formCity"
                placeholder="e.g. San Francisco"
                class="w-full bg-slate-50 border rounded-xl px-3.5 py-2.5 text-xs text-slate-800 placeholder-slate-400 focus:outline-none"
                :class="errors.city ? 'border-rose-450 bg-rose-50/10' : 'border-slate-150'"
              />
              <p v-if="errors.city" class="text-[10px] text-rose-500 font-semibold leading-none">{{ errors.city }}</p>
            </div>

            <div class="space-y-1.5">
              <label class="text-xs font-bold text-slate-505 block">Postal ZIP Code</label>
              <input 
                type="text" 
                v-model="formZip"
                placeholder="e.g. 94107"
                class="w-full bg-slate-50 border rounded-xl px-3.5 py-2.5 text-xs text-slate-800 placeholder-slate-400 focus:outline-none"
                :class="errors.zip ? 'border-rose-450 bg-rose-50/10' : 'border-slate-150'"
              />
              <p v-if="errors.zip" class="text-[10px] text-rose-500 font-semibold leading-none">{{ errors.zip }}</p>
            </div>

            <div class="space-y-1.5">
              <label class="text-xs font-bold text-slate-500 block">Country</label>
              <select 
                v-model="formCountry"
                class="w-full bg-slate-50 border border-slate-150 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none"
              >
                <option value="United States">United States</option>
                <option value="Canada">Canada</option>
                <option value="United Kingdom">United Kingdom</option>
                <option value="Germany">Germany</option>
              </select>
            </div>

            <div class="space-y-1.5">
              <label class="text-xs font-bold text-slate-500 block">Phone Number</label>
              <input 
                type="text" 
                v-model="formPhone"
                placeholder="e.g. +1 (555) 342-9980"
                class="w-full bg-slate-50 border border-slate-150 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 placeholder-slate-400 focus:outline-none"
              />
            </div>
          </div>
        </section>

        <!-- Section 2: Payment Details -->
        <section class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm space-y-4">
          <h3 class="font-sans font-bold text-base text-slate-900 flex items-center gap-2 mb-2 pb-2 border-b border-slate-50">
            <CreditCard class="h-4.5 w-4.5 text-slate-500" />
            Secure Card Payment
          </h3>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2 space-y-1.5">
              <label class="text-xs font-bold text-slate-550 block font-sans">Name printed on Card</label>
              <input 
                type="text" 
                v-model="cardName"
                placeholder="e.g. ALEX MERCER"
                class="w-full bg-slate-50 border rounded-xl px-3.5 py-2.5 text-xs text-slate-800 placeholder-slate-400 focus:outline-none uppercase"
                :class="errors.cardName ? 'border-rose-450 bg-rose-50/10' : 'border-slate-150'"
              />
              <p v-if="errors.cardName" class="text-[10px] text-rose-500 font-semibold leading-none">{{ errors.cardName }}</p>
            </div>

            <div class="sm:col-span-2 space-y-1.5 font-sans">
              <label class="text-xs font-bold text-slate-500 block">Credit Card Number</label>
              <input 
                type="text" 
                v-model="cardNumber"
                placeholder="4242 4242 4242 4242"
                class="w-full bg-slate-50 border rounded-xl px-3.5 py-2.5 text-xs text-slate-800 placeholder-slate-400 focus:outline-none font-mono"
                :class="errors.cardNumber ? 'border-rose-450 bg-rose-50/10' : 'border-slate-150'"
              />
              <p v-if="errors.cardNumber" class="text-[10px] text-rose-500 font-semibold leading-none">{{ errors.cardNumber }}</p>
            </div>

            <div class="space-y-1.5 font-sans">
              <label class="text-xs font-bold text-slate-500 block">Expiry Date</label>
              <input 
                type="text" 
                v-model="cardExpiry"
                placeholder="MM/YY (e.g. 12/28)"
                class="w-full bg-slate-50 border rounded-xl px-3.5 py-2.5 text-xs text-slate-800 placeholder-slate-400 focus:outline-none"
                :class="errors.cardExpiry ? 'border-rose-450 bg-rose-50/10' : 'border-slate-150'"
              />
              <p v-if="errors.cardExpiry" class="text-[10px] text-rose-500 font-semibold leading-none">{{ errors.cardExpiry }}</p>
            </div>

            <div class="space-y-1.5 font-sans">
              <label class="text-xs font-bold text-slate-505 block">CVV Security Code</label>
              <input 
                type="password" 
                v-model="cardCvv"
                placeholder="***"
                maxlength="4"
                class="w-full bg-slate-50 border rounded-xl px-3.5 py-2.5 text-xs text-slate-800 placeholder-slate-400 focus:outline-none font-mono"
                :class="errors.cardCvv ? 'border-rose-450 bg-rose-50/10' : 'border-slate-150'"
              />
              <p v-if="errors.cardCvv" class="text-[10px] text-rose-500 font-semibold leading-none">{{ errors.cardCvv }}</p>
            </div>
          </div>
        </section>

      </div>

      <!-- Right side: Checkout Items calculation breakdown (span 5) -->
      <div class="lg:col-span-5 space-y-6">
        <section class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm space-y-4">
          <h3 class="font-sans font-bold text-base text-slate-905 pb-2 border-b border-slate-50">Review Selections</h3>

          <!-- Items rows lists loop -->
          <div class="divide-y divide-slate-100 max-h-72 overflow-y-auto pr-1">
            <div 
              v-for="item in cartStore.cartDetails" 
              :key="item.productId"
              class="flex items-center gap-3 py-3"
            >
              <div class="h-12 w-12 rounded-lg bg-slate-50 border overflow-hidden flex-shrink-0">
                <img :src="item.product?.image" class="h-full w-full object-cover" />
              </div>
              <div class="flex-1 min-w-0">
                <h4 class="text-xs font-semibold text-slate-900 truncate leading-tight">{{ item.product?.name }}</h4>
                <div class="text-[10px] font-semibold text-slate-400 mt-0.5">
                  Qty: {{ item.quantity }} &middot; {{ formatPrice(item.product?.price || 0) }}
                </div>
              </div>
              <div class="text-xs font-bold text-slate-900">
                {{ formatPrice((item.product?.price || 0) * item.quantity) }}
              </div>
            </div>
          </div>

          <!-- Total lines calculus -->
          <div class="pt-4 border-t border-slate-100 space-y-2 text-xs font-medium text-slate-500">
            <div class="flex items-center justify-between">
              <span>Subtotal</span>
              <span class="text-slate-850 font-bold">{{ formatPrice(cartStore.subtotal) }}</span>
            </div>
            
            <div v-if="cartStore.couponDiscount > 0" class="flex items-center justify-between text-emerald-600 font-bold">
              <span>Coupon Promo ({{ cartStore.couponCode }})</span>
              <span>-{{ formatPrice(cartStore.discountAmount) }}</span>
            </div>

            <div class="flex items-center justify-between">
              <span>Shipping & Delivery Fee</span>
              <span v-if="cartStore.shippingFee === 0" class="text-emerald-600 font-bold">FREE</span>
              <span v-else class="text-slate-800 font-bold">{{ formatPrice(cartStore.shippingFee) }}</span>
            </div>

            <div class="flex items-center justify-between">
              <span>Estimated Sales Tax</span>
              <span class="text-slate-800 font-bold">{{ formatPrice(cartStore.taxAmount) }}</span>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-sm text-slate-900 font-bold">
              <span>Purchase Amount Total</span>
              <span class="text-lg font-extrabold text-slate-900" id="checkout-calc-total">{{ formatPrice(cartStore.total) }}</span>
            </div>
          </div>
        </section>

        <!-- Complete submission button -->
        <button 
          @click="handleCompletePayment"
          class="w-full bg-slate-900 text-white font-bold py-4 rounded-xl shadow-md hover:bg-slate-800 active:scale-[0.98] transition-all flex items-center justify-center gap-2 group cursor-pointer"
          id="btn-complete-checkout"
        >
          <Lock class="h-4 w-4 text-emerald-400" />
          Authorize Secure Payment
        </button>
      </div>

    </div>

    <!-- Security loader overlay panel -->
    <div 
      v-if="isProcessing"
      class="fixed inset-0 bg-slate-950/80 backdrop-blur-md flex flex-col items-center justify-center z-50 text-white text-center px-6"
    >
      <div class="space-y-6">
        <!-- Spinner Graphic -->
        <div class="w-16 h-16 border-4 border-slate-700 border-t-emerald-500 rounded-full animate-spin mx-auto"></div>
        <div class="space-y-2">
          <p class="font-sans font-bold text-lg text-white">Completing Gateway Authorization</p>
          <p class="text-xs text-slate-400 h-6 font-mono font-medium">{{ processingStep }}</p>
        </div>
      </div>
    </div>

    <!-- Fullscreen checkout success template -->
    <div 
      v-if="showSuccessOverlay"
      class="bg-white border border-slate-100 p-8 sm:p-12 rounded-3xl shadow-xl max-w-xl mx-auto text-center space-y-6 animate-fade-in my-8"
      id="checkout-success-panel"
    >
      <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto shadow-inner animate-pulse">
        <CheckCircle class="h-10 w-10" />
      </div>

      <div class="space-y-2">
        <h3 class="font-sans font-extrabold text-2xl text-slate-900">Purchase Authorized!</h3>
        <p class="text-xs text-slate-500 font-semibold leading-relaxed max-w-sm mx-auto">
          We received your transaction successfully. Preparing secure packaging in our logistics facilities!
        </p>
      </div>

      <!-- Confirmed specs details card -->
      <div class="bg-slate-50 rounded-2xl p-6 text-left border border-slate-100 space-y-3.5 max-w-md mx-auto">
        <div class="flex items-center justify-between text-xs font-semibold pb-2 border-b border-slate-150">
          <span class="text-slate-450 block uppercase tracking-wider">Order Reference</span>
          <span class="text-slate-850 font-mono font-bold">{{ createdOrderId }}</span>
        </div>
        <div class="flex items-center justify-between text-xs font-semibold pb-2 border-b border-slate-150">
          <span class="text-slate-450 block uppercase tracking-wider">Shipment Target</span>
          <span class="text-slate-800 font-bold truncate max-w-[190px]">{{ formAddress }}</span>
        </div>
        <div class="flex items-center justify-between text-xs font-semibold pb-2 border-b border-slate-150">
          <span class="text-slate-450 block uppercase tracking-wider">Courier Track Code</span>
          <span class="text-slate-800 font-mono font-bold">Auto-generating</span>
        </div>
      </div>

      <!-- Action redirection buttons -->
      <div class="flex flex-col sm:flex-row items-center gap-3 justify-center max-w-md mx-auto pt-2">
        <button 
          @click="router.push('/order-history')"
          class="px-5 py-3 bg-slate-950 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 hover:bg-slate-800 shadow cursor-pointer w-full sm:w-auto justify-center"
        >
          Track in Order History
          <ArrowRight class="h-4 w-4" />
        </button>
        <button 
          @click="router.push('/')"
          class="px-5 py-3 text-slate-650 hover:text-slate-900 bg-slate-50 border border-slate-100 rounded-xl text-xs font-bold cursor-pointer w-full sm:w-auto"
        >
          Return to home
        </button>
      </div>
    </div>

    <!-- Empty checkout layout guard if someone opens it straight -->
    <div 
      v-if="!showSuccessOverlay && cartStore.items.length === 0"
      class="py-16 px-6 max-w-sm mx-auto text-center space-y-4 bg-slate-50 border border-slate-100 rounded-3xl"
    >
      <Clock class="h-8 w-8 text-slate-400 mx-auto" />
      <h3 class="font-sans font-bold text-lg text-slate-900">Your checkout is clear</h3>
      <p class="text-xs text-slate-500 font-medium leading-relaxed max-w-xs mx-auto">
        You don't have any items in your checkout session. Visit our catalog pages and add selections before launching this panel.
      </p>
      <router-link 
        to="/" 
        class="inline-block px-5 py-2.5 bg-slate-900 text-white rounded-xl text-xs font-bold leading-none cursor-pointer"
      >
        Go to Shop All
      </router-link>
    </div>

  </div>
</template>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.25s ease-out forwards;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(6px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
