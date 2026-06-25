<script setup lang="ts">
import { ref, computed } from 'vue';
import { useProductStore } from '@/stores/productStore';
import { useCartStore } from '@/stores/cartStore';
import ProductCard from '@/components/product/ProductCard.vue';
import Sidebar from '@/components/layout/Sidebar.vue';
import { 
  Sparkles, 
  ArrowRight, 
  Tag, 
  Award, 
  Percent, 
  SlidersHorizontal,
  X,
  Compass,
  ShoppingBag
} from 'lucide-vue-next';

const productStore = useProductStore();
const cartStore = useCartStore();

const showMobileDrawer = ref(false);

const activeFilterLabel = computed(() => {
  if (productStore.selectedCategory !== 'All') {
    return `Category: ${productStore.selectedCategory}`;
  }
  if (productStore.searchQuery !== '') {
    return `Search: "${productStore.searchQuery}"`;
  }
  return 'All Curated Products';
});

const featuredProducts = computed(() => {
  return productStore.filteredProducts.slice(0, 4);
});

const clearSearch = () => {
  productStore.searchQuery = '';
};

const clearCategory = () => {
  productStore.selectedCategory = 'All';
};
</script>

<template>
  <div class="space-y-12 pb-16 animate-fade-in" id="home-view-container">
    
    <!-- Hero Banner (Sleek minimalist promo layout) -->
    <section class="relative bg-slate-950 text-white rounded-3xl overflow-hidden min-h-[460px] flex items-center px-6 sm:px-12 md:px-16" id="hero-banner">
      <!-- Ambient background visual design -->
      <div class="absolute inset-0 z-0 opacity-40">
        <img 
          src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&q=80&w=1200" 
          alt="Luxury Backdrop Store" 
          referrerpolicy="no-referrer"
          class="w-full h-full object-cover object-center scale-105 filter blur-xs"
        />
      </div>
      <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-950/80 to-transparent z-[1]"></div>
      
      <!-- Content Panel -->
      <div class="relative z-10 max-w-lg space-y-6">
        <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 rounded-full text-xs font-semibold tracking-wider text-slate-300 backdrop-blur-sm border border-white/5">
          <Sparkles class="h-3.5 w-3.5 text-yellow-400" />
          <span>Exclusive Summer Launch 2026</span>
        </div>
        
        <h1 class="font-sans font-extrabold text-4xl sm:text-5xl leading-tight tracking-tight">
          Redesigning Everyday Performance.
        </h1>
        
        <p class="text-sm sm:text-base text-slate-350 leading-relaxed font-medium">
          Explore our signature collection of professional urban commuter bags, tactile mechanical interfaces, and studio-grade audio equipment built with flawless design ergonomics.
        </p>

        <div class="flex flex-wrap items-center gap-4 pt-2">
          <a 
            href="#shop-catalog" 
            class="px-6 py-3 bg-white text-slate-950 rounded-xl text-sm font-bold shadow hover:bg-slate-100 transition-colors duration-200 flex items-center gap-2 group"
          >
            Browse Catalog
            <ArrowRight class="h-4 w-4 group-hover:translate-x-0.5 transition-transform" />
          </a>
          <router-link 
            to="/favorites" 
            class="px-5 py-3 bg-white/10 text-white hover:bg-white/15 rounded-xl text-sm font-bold border border-white/10 transition-colors duration-200"
          >
            My Favorites
          </router-link>
        </div>
      </div>
    </section>

    <!-- Bullet Value Props Strip -->
    <section class="grid grid-cols-1 sm:grid-cols-3 gap-6 py-6 border-y border-slate-100" id="value-props">
      <div class="flex items-start gap-4">
        <span class="p-3 bg-slate-50 text-slate-900 rounded-2xl">
          <Award class="h-5 w-5" />
        </span>
        <div>
          <h4 class="font-bold text-sm text-slate-900">Certified Quality</h4>
          <p class="text-xs text-slate-500 leading-relaxed mt-0.5">Every piece carries our signature 2-year premium hardware warranty.</p>
        </div>
      </div>
      <div class="flex items-start gap-4">
        <span class="p-3 bg-slate-50 text-slate-900 rounded-2xl">
          <Tag class="h-5 w-5" />
        </span>
        <div>
          <h4 class="font-bold text-sm text-slate-900">Direct-to-Consumer</h4>
          <p class="text-xs text-slate-500 leading-relaxed mt-0.5">We design and curate everything in-house, bypassing premium retail markups.</p>
        </div>
      </div>
      <div class="flex items-start gap-4">
        <span class="p-3 bg-slate-50 text-slate-900 rounded-2xl">
          <Percent class="h-5 w-5" />
        </span>
        <div>
          <h4 class="font-bold text-sm text-slate-900">Flexible Checkout</h4>
          <p class="text-xs text-slate-500 leading-relaxed mt-0.5">Enjoy zero-interest monthly payment schedules and simple instant returns.</p>
        </div>
      </div>
    </section>

    <!-- Grid Catalog and Sidebar layout -->
    <section class="space-y-6" id="shop-catalog">
      <!-- Title header bar -->
      <div class="flex items-center justify-between">
        <div>
          <h2 class="font-sans font-extrabold text-2xl tracking-tight text-slate-900">Shop Curated Collection</h2>
          <p class="text-xs text-slate-500 font-semibold mt-1">Viewing {{ productStore.filteredProducts.length }} distinctive products</p>
        </div>

        <!-- Mobile Filter overlay trigger -->
        <button 
          @click="showMobileDrawer = true"
          class="md:hidden flex items-center gap-2 px-4 py-2.5 bg-slate-950 text-white rounded-xl text-xs font-bold cursor-pointer"
          id="btn-trigger-mobile-filter"
        >
          <SlidersHorizontal class="h-4 w-4" />
          Filter & Sort
        </button>
      </div>

      <!-- Active selection tags row -->
      <div 
        v-if="productStore.selectedCategory !== 'All' || productStore.searchQuery !== ''"
        class="flex flex-wrap gap-2 py-1 items-center"
      >
        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Active Filters:</span>
        
        <span 
          v-if="productStore.selectedCategory !== 'All'"
          class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-50 border border-slate-100 rounded-full text-xs font-semibold text-slate-800"
        >
          Category: {{ productStore.selectedCategory }}
          <button @click="clearCategory" class="text-slate-405 hover:text-slate-800 focus:outline-none cursor-pointer">
            <X class="h-3 w-3" />
          </button>
        </span>

        <span 
          v-if="productStore.searchQuery !== ''"
          class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-50 border border-slate-100 rounded-full text-xs font-semibold text-slate-800"
        >
          Search: "{{ productStore.searchQuery }}"
          <button @click="clearSearch" class="text-slate-400 hover:text-slate-800 focus:outline-none cursor-pointer">
            <X class="h-3 w-3" />
          </button>
        </span>
      </div>

      <!-- Layout Grid -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        
        <!-- Sidebar filters (Only on desktop) -->
        <div class="hidden md:block md:col-span-1">
          <Sidebar />
        </div>

        <!-- Products lists grid -->
        <div class="md:col-span-3">
          
          <div 
            v-if="productStore.filteredProducts.length > 0"
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8"
            id="products-gallery-grid"
          >
            <div 
              v-for="product in productStore.filteredProducts" 
              :key="product.id"
            >
              <ProductCard :product="product" />
            </div>
          </div>

          <!-- Empty Case if search falls short -->
          <div 
            v-else 
            class="bg-slate-50 border border-slate-100 rounded-2xl py-16 px-6 text-center space-y-4 max-w-md mx-auto"
            id="search-empty-state"
          >
            <span class="inline-block p-4 bg-slate-100 text-slate-500 rounded-full">
              <Compass class="h-8 w-8" />
            </span>
            <h3 class="font-sans font-bold text-lg text-slate-900">No products match your filters</h3>
            <p class="text-xs text-slate-500 leading-relaxed font-semibold">
              We couldn't find any products matching your active criteria. Re-type your keywords or reset categories to browse our primary collection.
            </p>
            <button 
              @click="productStore.selectedCategory = 'All'; productStore.searchQuery = ''"
              class="px-5 py-2 px-6 bg-slate-950 text-white rounded-xl text-xs font-bold hover:bg-slate-800 active:scale-95 transition-all cursor-pointer"
            >
              Browse All Products
            </button>
          </div>

        </div>

      </div>
    </section>

    <!-- Mobile filtering slide Drawer (portal structure) -->
    <div 
      v-if="showMobileDrawer"
      class="fixed inset-0 z-50 flex md:hidden"
    >
      <!-- Backdrop -->
      <div 
        @click="showMobileDrawer = false"
        class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs"
      ></div>

      <!-- Left sidebar slides content -->
      <div class="relative flex flex-col w-full max-w-sm bg-white h-full shadow-2xl p-6 overflow-y-auto space-y-4 ml-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
          <span class="font-sans font-bold text-slate-900 text-lg uppercase tracking-wide">Filters</span>
          <button 
            @click="showMobileDrawer = false"
            class="p-2 text-slate-500 hover:text-slate-900 hover:bg-slate-50 rounded-xl"
          >
            <X class="h-5 w-5" />
          </button>
        </div>

        <!-- Sidebar instance -->
        <Sidebar />
        
        <button 
          @click="showMobileDrawer = false"
          class="w-full bg-slate-900 text-white py-3 rounded-xl text-sm font-bold"
        >
          Apply Filters
        </button>
      </div>
    </div>

  </div>
</template>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.3s ease-out forwards;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(8px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
