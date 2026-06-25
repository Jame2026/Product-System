<script setup lang="ts">
import { ref, defineProps } from 'vue';
import { Order } from '@/stores/orderStore';
import { formatPrice } from '@/utils/formatPrice';
import OrderItem from '@/components/order/OrderItem.vue';
import { 
  Package, 
  Truck, 
  CheckCircle, 
  Calendar, 
  MapPin, 
  CreditCard,
  ChevronDown,
  ExternalLink
} from 'lucide-vue-next';

const props = defineProps<{
  order: Order;
}>();

const isExpanded = ref(false);

const getStatusColorClass = (status: Order['status']) => {
  switch (status) {
    case 'Processing': return 'bg-amber-100 text-amber-700 border-amber-200';
    case 'Shipped': return 'bg-blue-100 text-blue-700 border-blue-200';
    case 'Delivered': return 'bg-emerald-100 text-emerald-700 border-emerald-200';
    case 'Returned': return 'bg-rose-100 text-rose-700 border-rose-200';
  }
};

const getDeliveryLabel = (order: Order) => {
  if (order.status === 'Delivered') {
    return `Delivered on ${order.estimatedDelivery}`;
  }
  return `Estimated Delivery: ${order.estimatedDelivery}`;
};
</script>

<template>
  <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden" :id="`order-card-${order.id}`">
    
    <!-- Header Block (Order basic metrics) -->
    <div class="bg-slate-50/50 p-4 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div class="flex flex-wrap items-center gap-x-6 gap-y-2">
        <div>
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Order ID</span>
          <span class="text-sm font-bold text-slate-900 font-mono">{{ order.id }}</span>
        </div>
        <div>
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Date Placed</span>
          <span class="text-sm font-medium text-slate-700">{{ order.date }}</span>
        </div>
        <div>
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Total Amount</span>
          <span class="text-sm font-bold text-slate-900">{{ formatPrice(order.total) }}</span>
        </div>
      </div>

      <!-- Action tags: status label and expanding indicator -->
      <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-end">
        <span 
          class="border text-xs px-3 py-1 rounded-full font-bold select-none"
          :class="getStatusColorClass(order.status)"
        >
          {{ order.status }}
        </span>

        <button 
          @click="isExpanded = !isExpanded"
          class="p-2 text-slate-500 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition-all cursor-pointer flex items-center gap-1 text-xs font-bold"
        >
          <span>{{ isExpanded ? 'Hide Details' : 'View Details' }}</span>
          <ChevronDown class="h-4 w-4 transition-transform duration-250" :class="isExpanded ? 'rotate-180' : ''" />
        </button>
      </div>
    </div>

    <!-- Live Step Tracker Graphics -->
    <div class="p-6 border-b border-slate-50">
      <div class="max-w-xl mx-auto">
        <div class="relative flex items-center justify-between">
          
          <!-- Line connector background -->
          <div class="absolute left-0 right-0 top-1/2 -translate-y-1/2 h-1 bg-slate-100 -z-[1]"></div>
          <!-- Filled connector depending on status -->
          <div 
            class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-slate-900 transition-all duration-300 -z-[1]"
            :style="{ 
              width: order.status === 'Processing' ? '0%' : order.status === 'Shipped' ? '50%' : '100%' 
            }"
          ></div>

          <!-- Step 1: Processing -->
          <div class="flex flex-col items-center.">
            <span 
              class="h-9 w-9 rounded-full flex items-center justify-center border-2 transition-colors duration-250 font-bold text-xs"
              :class="order.status === 'Processing' || order.status === 'Shipped' || order.status === 'Delivered'
                ? 'bg-slate-900 text-white border-slate-900 shadow-sm'
                : 'bg-white text-slate-400 border-slate-200'"
            >
              <Package class="h-4 w-4" />
            </span>
            <span class="text-[10px] font-bold mt-2 text-slate-800 text-center block">Processing</span>
          </div>

          <!-- Step 2: Shipped -->
          <div class="flex flex-col items-center">
            <span 
              class="h-9 w-9 rounded-full flex items-center justify-center border-2 transition-colors duration-250"
              :class="order.status === 'Shipped' || order.status === 'Delivered'
                ? 'bg-slate-900 text-white border-slate-900 shadow-sm'
                : 'bg-white text-slate-400 border-slate-200'"
            >
              <Truck class="h-4 w-4" />
            </span>
            <span class="text-[10px] font-bold mt-2 text-slate-800 text-center block">Shipped</span>
          </div>

          <!-- Step 3: Delivered -->
          <div class="flex flex-col items-center">
            <span 
              class="h-9 w-9 rounded-full flex items-center justify-center border-2 transition-colors duration-250"
              :class="order.status === 'Delivered'
                ? 'bg-slate-900 text-white border-slate-900 shadow-sm'
                : 'bg-white text-slate-400 border-slate-200'"
            >
              <CheckCircle class="h-4 w-4" />
            </span>
            <span class="text-[10px] font-bold mt-2 text-slate-800 text-center block">Delivered</span>
          </div>

        </div>

        <div class="flex items-center justify-center gap-2 mt-6 pt-4 border-t border-slate-50 text-xs font-semibold text-slate-500">
          <Calendar class="h-4 w-4 text-slate-400" />
          <span>{{ getDeliveryLabel(order) }}</span>
        </div>
      </div>
    </div>

    <!-- Expandable Detailed Block -->
    <div 
      v-if="isExpanded"
      class="p-6 bg-slate-50/20 border-t border-slate-50 divide-y divide-slate-100 flex flex-col gap-6"
    >
      <!-- Item summary loops -->
      <div class="space-y-4">
        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Items in Order</h4>
        <div class="space-y-3">
          <OrderItem 
            v-for="item in props.order.items" 
            :key="item.productId"
            :item="item"
          />
        </div>
      </div>

      <!-- Shipping and payment reviews -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6">
        
        <!-- Shipping details segment -->
        <div class="space-y-2">
          <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
            <MapPin class="h-3.5 w-3.5" />
            Shipping Coordinates
          </h4>
          <div class="text-xs text-slate-600 font-medium leading-relaxed bg-white/40 p-4 rounded-xl border border-slate-100">
            <p class="font-bold text-slate-850 text-xs mb-1">{{ order.shippingAddress.name }}</p>
            <p>{{ order.shippingAddress.address }}</p>
            <p>{{ order.shippingAddress.city }}, {{ order.shippingAddress.zipCode }}</p>
            <p>{{ order.shippingAddress.country }}</p>
          </div>
        </div>

        <!-- Billing details segment -->
        <div class="space-y-4">
          <div class="space-y-2">
            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
              <CreditCard class="h-3.5 w-3.5" />
              Payment Details
            </h4>
            <div class="text-xs text-slate-600 font-medium bg-white/40 p-4 rounded-xl border border-slate-100 flex items-center gap-3">
              <span class="px-2 py-1 bg-slate-100 rounded text-[10px] font-mono font-bold uppercase">{{ order.paymentMethod.cardType }}</span>
              <span>Card ending in &bull;&bull;&bull;&bull; {{ order.paymentMethod.cardNumber }}</span>
            </div>
          </div>

          <!-- Carrier code -->
          <div v-if="order.trackingNumber" class="space-y-1 bg-slate-100/60 p-3.5 rounded-xl border border-slate-150/40">
            <span class="text-[9.5px] font-bold text-slate-400 uppercase tracking-wider block">Carrier Tracking</span>
            <div class="flex items-center justify-between text-xs font-bold text-slate-850">
              <span class="font-mono">{{ order.trackingNumber }}</span>
              <a href="#" class="text-slate-900 hover:underline flex items-center gap-1 text-[11px] font-bold">
                Track
                <ExternalLink class="h-3 w-3" />
              </a>
            </div>
          </div>
        </div>

      </div>

      <!-- Cost review lines -->
      <div class="pt-6">
        <div class="max-w-xs ml-auto space-y-2 text-xs font-medium text-slate-500">
          <div class="flex items-center justify-between">
            <span>Subtotal</span>
            <span class="text-slate-850 font-bold">{{ formatPrice(order.subtotal) }}</span>
          </div>
          <div v-if="order.discount > 0" class="flex items-center justify-between text-emerald-600">
            <span>Promotion Code</span>
            <span>-{{ formatPrice(order.discount) }}</span>
          </div>
          <div class="flex items-center justify-between">
            <span>Service & Shipping</span>
            <span v-if="order.shipping === 0" class="text-emerald-600 font-bold">FREE</span>
            <span v-else class="text-slate-850 font-bold">{{ formatPrice(order.shipping) }}</span>
          </div>
          <div class="flex items-center justify-between">
            <span>Sales Tax</span>
            <span class="text-slate-850 font-bold">{{ formatPrice(order.tax) }}</span>
          </div>
          <div class="flex items-center justify-between pt-3 border-t border-slate-150 text-sm font-bold text-slate-900">
            <span>Total Paid</span>
            <span>{{ formatPrice(props.order.total) }}</span>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>
