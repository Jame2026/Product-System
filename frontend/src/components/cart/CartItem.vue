<script setup lang="ts">
import { defineProps } from 'vue';
import { useRouter } from 'vue-router';
import { useCartStore, CartItem } from '@/stores/cartStore';
import { Product } from '@/stores/productStore';
import { formatPrice } from '@/utils/formatPrice';
import { Trash2, Plus, Minus } from 'lucide-vue-next';

const props = defineProps<{
  item: {
    productId: string;
    quantity: number;
    selectedColor?: string;
    selectedSize?: string;
    product?: Product;
  };
}>();

const router = useRouter();
const cartStore = useCartStore();

const handleProductRedirect = () => {
  if (props.item.product) {
    router.push({ name: 'ProductDetail', params: { id: props.item.productId } });
  }
};

const handleIncrement = () => {
  if (props.item.product) {
    if (props.item.quantity < props.item.product.stock) {
      cartStore.updateQuantity(
        props.item.productId, 
        props.item.quantity + 1, 
        props.item.selectedColor, 
        props.item.selectedSize
      );
    }
  }
};

const handleDecrement = () => {
  cartStore.updateQuantity(
    props.item.productId, 
    props.item.quantity - 1, 
    props.item.selectedColor, 
    props.item.selectedSize
  );
};

const handleRemove = () => {
  cartStore.removeFromCart(
    props.item.productId, 
    props.item.selectedColor, 
    props.item.selectedSize
  );
};
</script>

<template>
  <div 
    v-if="item.product"
    class="flex items-start sm:items-center justify-between p-4 bg-white border border-slate-100 rounded-2xl shadow-sm gap-4 relative"
    :id="`cart-item-${item.productId}`"
  >
    <!-- Left product thumbnail and title -->
    <div class="flex items-center gap-4 flex-1">
      <div 
        @click="handleProductRedirect"
        class="h-20 w-20 flex-shrink-0 bg-slate-50 border border-slate-100 rounded-xl overflow-hidden cursor-pointer"
      >
        <img 
          :src="item.product.image" 
          :alt="item.product.name" 
          referrerpolicy="no-referrer"
          class="h-full w-full object-cover object-center"
        />
      </div>

      <div class="space-y-1">
        <span class="text-[9.5px] font-bold text-slate-400 uppercase tracking-wider">{{ item.product.category }}</span>
        <h4 
          @click="handleProductRedirect"
          class="font-sans font-semibold text-sm sm:text-base text-slate-900 hover:text-slate-700 cursor-pointer line-clamp-1 transition-colors"
        >
          {{ item.product.name }}
        </h4>
        
        <!-- Options layer (if color or size chosen) -->
        <div class="flex flex-wrap gap-2 text-xs font-medium text-slate-500">
          <span v-if="item.selectedColor" class="flex items-center gap-1">
            Color: <span class="font-semibold text-slate-800">{{ item.selectedColor }}</span>
          </span>
          <span v-if="item.selectedSize" class="flex items-center gap-1">
            Size: <span class="font-semibold text-slate-800">{{ item.selectedSize }}</span>
          </span>
          <span class="text-xs text-slate-400">Unit: {{ formatPrice(item.product.price) }}</span>
        </div>
      </div>
    </div>

    <!-- Right Side: Counter & Subtotals -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 sm:min-w-[220px]">
      
      <!-- Counter Controller -->
      <div class="flex items-center border border-slate-150 rounded-xl px-2.5 py-1.5 w-max">
        <button 
          @click="handleDecrement"
          class="p-1 text-slate-500 hover:text-slate-950 hover:bg-slate-50 rounded-lg active:scale-90 transition-all cursor-pointer"
          :title="item.quantity === 1 ? 'Remove item' : 'Decrease Quantity'"
        >
          <Minus class="h-3.5 w-3.5" />
        </button>
        
        <span class="px-3.5 text-xs sm:text-sm font-bold text-slate-800 select-none min-w-[24px] text-center">
          {{ item.quantity }}
        </span>
        
        <button 
          @click="handleIncrement"
          class="p-1 text-slate-500 hover:text-slate-955 hover:bg-slate-50 rounded-lg active:scale-90 transition-all cursor-pointer"
          :disabled="item.quantity >= item.product.stock"
          :class="item.quantity >= item.product.stock ? 'opacity-30 cursor-not-allowed' : ''"
          title="Increase Quantity"
        >
          <Plus class="h-3.5 w-3.5" />
        </button>
      </div>

      <!-- Price details & Trash Trigger -->
      <div class="flex items-center justify-between sm:justify-end gap-6 flex-1 w-full sm:w-auto">
        <!-- Subtotal Price -->
        <div class="font-sans font-bold text-sm sm:text-base text-slate-900 text-right min-w-[80px]">
          {{ formatPrice(item.product.price * item.quantity) }}
        </div>

        <!-- Trashing item -->
        <button 
          @click="handleRemove"
          class="p-2 text-slate-400 hover:text-rose-500 rounded-xl hover:bg-rose-50/50 transition-all cursor-pointer"
          title="Remove from Cart"
          :id="`btn-trash-${item.productId}`"
        >
          <Trash2 class="h-4.5 w-4.5" />
        </button>
      </div>
    </div>
  </div>
</template>
