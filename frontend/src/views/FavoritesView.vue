<script setup lang="ts">
import { useProductStore } from '@/stores/productStore';
import ProductCard from '@/components/product/ProductCard.vue';
import { Heart, ChevronLeft } from 'lucide-vue-next';

const productStore = useProductStore();
</script>

<template>
  <div class="space-y-6 pb-16 animate-fade-in" id="favorites-view-container">
    <!-- Breadcrumb back link -->
    <div>
      <router-link 
        to="/" 
        class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-900 cursor-pointer"
        id="btn-favs-back-home"
      >
        <ChevronLeft class="h-4 w-4" />
        Back to listings
      </router-link>
    </div>

    <!-- Header details -->
    <div>
      <h2 class="font-sans font-extrabold text-2xl tracking-tight text-slate-900">Your Saved Favorites</h2>
      <p class="text-xs text-slate-500 font-semibold mt-1">Review items you bookmarked for later consideration</p>
    </div>

    <!-- Listings Grid -->
    <div v-if="productStore.favoriteProducts.length > 0">
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 sm:gap-8">
        <div 
          v-for="product in productStore.favoriteProducts" 
          :key="product.id"
        >
          <ProductCard :product="product" />
        </div>
      </div>
    </div>

    <!-- Empty State card -->
    <div 
      v-else 
      class="py-16 px-6 max-w-sm mx-auto text-center space-y-4 bg-slate-50 border border-slate-100 rounded-3xl"
      id="favorites-empty-case"
    >
      <span class="inline-block p-4 bg-slate-100 text-rose-500 rounded-full">
        <Heart class="h-8 w-8" />
      </span>
      <h3 class="font-sans font-bold text-lg text-slate-900">No favorites bookmarked</h3>
      <p class="text-xs text-slate-505 font-medium leading-relaxed max-w-xs mx-auto">
        When browsing our collection, tap the heart button in the corner of product panels to bookmark creations you love and save them to this list.
      </p>
      <router-link 
        to="/" 
        class="inline-block px-5 py-2.5 bg-slate-900 text-white rounded-xl text-xs font-bold font-sans hover:bg-slate-800 transition-colors cursor-pointer"
      >
        Explore Curations
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
