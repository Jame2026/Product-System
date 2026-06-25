<script setup lang="ts">
import { defineProps } from 'vue';
import { formatPrice } from '@/utils/formatPrice';

export interface OrderItemProps {
  productId: string;
  name: string;
  price: number;
  quantity: number;
  image: string;
  category: string;
}

const props = defineProps<{
  item: OrderItemProps;
}>();
</script>

<template>
  <div class="flex items-center justify-between gap-4 py-2 border-b border-slate-50 last:border-b-0" :id="`order-item-${item.productId}`">
    <div class="flex items-center gap-3">
      <!-- Thumbnail image -->
      <div class="h-12 w-12 rounded-lg bg-slate-50 border border-slate-150 overflow-hidden flex-shrink-0">
        <img :src="item.image" :alt="item.name" class="h-full w-full object-cover" referrerpolicy="no-referrer" />
      </div>
      <!-- Label Details -->
      <div>
        <h5 class="text-xs sm:text-sm font-semibold text-slate-900 line-clamp-1">{{ item.name }}</h5>
        <div class="flex gap-2 text-[10.5px] text-slate-400 font-medium">
          <span>Qty: <strong class="text-slate-600 font-semibold">{{ item.quantity }}</strong></span>
          <span>&middot;</span>
          <span>Unit: {{ formatPrice(item.price) }}</span>
        </div>
      </div>
    </div>

    <!-- Right Side Price total -->
    <div class="text-xs sm:text-sm font-bold text-slate-900 text-right min-w-[70px]">
      {{ formatPrice(item.price * item.quantity) }}
    </div>
  </div>
</template>
