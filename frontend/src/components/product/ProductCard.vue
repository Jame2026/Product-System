<script setup lang="ts">
import { defineProps } from 'vue';
import { useRouter } from 'vue-router';
import { useCartStore } from '@/stores/cartStore';
import { useProductStore, Product } from '@/stores/productStore';
import { formatPrice } from '@/utils/formatPrice';
import { Star, Heart, ShoppingCart, Eye } from 'lucide-vue-next';

const props = defineProps<{
  product: Product;
}>();

const router = useRouter();
const cartStore = useCartStore();
const productStore = useProductStore();

const handleCardClick = () => {
  router.push({ name: 'ProductDetail', params: { id: props.product.id } });
};

const handleFavoriteClick = (e: Event) => {
  e.stopPropagation(); // Stop routing click
  productStore.toggleFavorite(props.product.id);
};

const handleAddToCart = (e: Event) => {
  e.stopPropagation(); // Stop routing click
  cartStore.addToCart(props.product.id, 1);
};
</script>

<template>
  <div 
    @click="handleCardClick"
    class="group bg-white border border-slate-100 rounded-2xl overflow-hidden cursor-pointer shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between h-full relative"
    :id="`product-card-${product.id}`"
  >
    <!-- Image block and absolute triggers -->
    <div class="relative aspect-square w-full bg-slate-50 overflow-hidden">
      <!-- Hover overlay for upscale action cues -->
      <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10 flex items-center justify-center gap-2">
        <button 
          @click.stop="handleCardClick"
          class="p-2.5 bg-white text-slate-900 rounded-full shadow hover:scale-110 active:scale-95 transition-all cursor-pointer"
          title="View Details"
        >
          <Eye class="h-4.5 w-4.5" />
        </button>
        <button 
          @click.stop="handleAddToCart"
          class="p-2.5 bg-slate-900 text-white rounded-full shadow hover:scale-110 active:scale-95 transition-all cursor-pointer"
          title="Quick Add to Cart"
        >
          <ShoppingCart class="h-4.5 w-4.5" />
        </button>
      </div>

      <img 
        :src="product.image" 
        :alt="product.name" 
        referrerpolicy="no-referrer"
        class="h-full w-full object-cover object-center group-hover:scale-105 transition-transform duration-500"
      />

      <!-- Absolute Popular badge -->
      <div 
        v-if="product.isPopular"
        class="absolute top-3 left-3 bg-slate-900/90 text-white text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-lg z-20"
      >
        Best Seller
      </div>

      <!-- Category badge -->
      <div 
        v-else
        class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm text-slate-800 border border-slate-100 text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-lg z-20"
      >
        {{ product.category }}
      </div>

      <!-- Favorite heart button -->
      <button 
        @click="handleFavoriteClick"
        class="absolute top-3 right-3 p-2 bg-white/95 backdrop-blur-sm shadow-sm hover:shadow text-rose-500 rounded-full hover:scale-110 active:scale-95 transition-all z-20 cursor-pointer focus:outline-none"
        :id="`btn-fav-${product.id}`"
        :title="productStore.isFavorite(product.id) ? 'Remove from favorites' : 'Add to favorites'"
      >
        <Heart 
          class="h-4 w-4 transition-colors" 
          :class="productStore.isFavorite(product.id) ? 'fill-rose-500 text-rose-500' : 'text-slate-450'" 
        />
      </button>
    </div>

    <!-- Product Text Content -->
    <div class="p-4 sm:p-5 flex flex-col flex-1 justify-between gap-3">
      <div class="space-y-1">
        <!-- Category label -->
        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ product.category }}</span>
        <!-- Product name -->
        <h4 class="font-sans font-semibold text-sm sm:text-base text-slate-900 group-hover:text-slate-700 transition-colors line-clamp-1">
          {{ product.name }}
        </h4>
      </div>

      <!-- Ratings and pricing line -->
      <div class="flex items-center justify-between mt-auto pt-2 border-t border-slate-50">
        <!-- Price -->
        <div class="font-sans font-bold text-base sm:text-lg text-slate-900">
          {{ formatPrice(product.price) }}
        </div>
        
        <!-- Star rating -->
        <div class="flex items-center gap-1">
          <Star class="h-3.5 w-3.5 fill-yellow-400 text-yellow-400" />
          <span class="text-xs font-bold text-slate-700">{{ product.rating }}</span>
          <span class="text-[10.5px] text-slate-400">({{ product.reviewsCount }})</span>
        </div>
      </div>
    </div>
  </div>
</template>
