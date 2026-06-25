<script setup lang="ts">
import { computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useProductStore } from '@/stores/productStore';
import ProductCard from '@/components/product/ProductCard.vue';
import Sidebar from '@/components/layout/Sidebar.vue';
import { ChevronLeft, Compass } from 'lucide-vue-next';

const route = useRoute();
const router = useRouter();
const productStore = useProductStore();

const categoryId = computed(() => {
  return route.params.id as string;
});

// Update active state in store
computed(() => {
  if (categoryId.value) {
    productStore.selectedCategory = categoryId.value;
  }
});

const categoryProducts = computed(() => {
  // Filter products by category
  return productStore.products.filter(p => p.category.toLowerCase() === categoryId.value.toLowerCase());
});

const handleGoBack = () => {
  productStore.selectedCategory = 'All';
  router.push('/');
};
</script>

<template>
  <div class="space-y-8 pb-16 animate-fade-in" id="category-view-container">
    <!-- Breadcrumb back link -->
    <div>
      <button 
        @click="handleGoBack"
        class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-900 cursor-pointer"
        id="btn-category-back"
      >
        <ChevronLeft class="h-4 w-4" />
        Back to All products
      </button>
    </div>

    <!-- Category Header and listings -->
    <div>
      <h2 class="font-sans font-extrabold text-3xl tracking-tight text-slate-900 capitalize">{{ categoryId }} Collection</h2>
      <p class="text-xs text-slate-500 font-semibold mt-1">Found {{ categoryProducts.length }} specialized products</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
      <!-- Sidebar Filters on Desktop -->
      <div class="hidden md:block col-span-1">
        <Sidebar />
      </div>

      <!-- Specialized Gallery Grid -->
      <div class="col-span-1 md:col-span-3">
        <div v-if="categoryProducts.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
          <div 
            v-for="product in categoryProducts" 
            :key="product.id"
          >
            <ProductCard :product="product" />
          </div>
        </div>

        <div v-else class="text-center py-16 bg-slate-50 border border-slate-100 rounded-2xl max-w-sm mx-auto">
          <Compass class="h-8 w-8 text-slate-400 mx-auto mb-3" />
          <h4 class="text-sm font-bold text-slate-900">Unrecognized Category</h4>
          <p class="text-xs text-slate-500 mt-1 max-w-xs mx-auto leading-relaxed">
            The category "{{ categoryId }}" does not map directly. Tap below to navigate home.
          </p>
          <button 
            @click="handleGoBack"
            class="mt-4 px-4 py-2 bg-slate-950 text-white rounded-xl text-xs font-bold leading-none cursor-pointer"
          >
            Go to Shop All
          </button>
        </div>
      </div>
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
