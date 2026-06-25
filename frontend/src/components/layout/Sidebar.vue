<script setup lang="ts">
import { useProductStore } from '@/stores/productStore';
import { 
  Sparkles, 
  RotateCcw,
  SlidersHorizontal,
  ChevronRight,
  Filter
} from 'lucide-vue-next';

const productStore = useProductStore();

const setCategory = (cat: string) => {
  productStore.selectedCategory = cat;
};

const clearFilters = () => {
  productStore.selectedCategory = 'All';
  productStore.searchQuery = '';
  productStore.sortBy = 'featured';
};
</script>

<template>
  <aside class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm sticky top-24" id="sidebar-filters">
    <!-- Title -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-150 mb-6">
      <div class="flex items-center gap-2 text-slate-900 font-semibold">
        <Filter class="h-4.5 w-4.5 text-slate-500" />
        <h3 class="text-sm uppercase tracking-wider font-bold">Filters</h3>
      </div>
      
      <button 
        @click="clearFilters" 
        class="text-xs text-slate-400 hover:text-slate-900 flex items-center gap-1.5 cursor-pointer font-medium transition-colors"
        id="btn-reset-filters"
      >
        <RotateCcw class="h-3 w-3" />
        Reset
      </button>
    </div>

    <!-- Category Filter Segment -->
    <div class="mb-6">
      <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Categories</h4>
      <div class="flex flex-col gap-1">
        <button 
          v-for="cat in productStore.categories" 
          :key="cat"
          @click="setCategory(cat)"
          class="flex items-center justify-between px-3 py-2 rounded-xl text-sm font-medium transition-all text-left cursor-pointer"
          :class="productStore.selectedCategory === cat 
            ? 'bg-slate-900 text-white font-semibold' 
            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'"
          :id="`filter-cat-${cat.toLowerCase()}`"
        >
          <span>{{ cat }}</span>
          <ChevronRight 
            class="h-3.5 w-3.5 transition-transform" 
            :class="productStore.selectedCategory === cat ? 'rotate-90 text-white' : 'text-slate-400'"
          />
        </button>
      </div>
    </div>

    <!-- Sorting Selection Segment -->
    <div class="mb-6">
      <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Sort By</h4>
      <select 
        v-model="productStore.sortBy"
        class="w-full bg-slate-50 text-slate-755 text-sm px-3.5 py-2.5 rounded-xl border border-slate-100 focus:outline-none focus:border-slate-300 transition-colors cursor-pointer"
        id="sort-select-sidebar"
      >
        <option value="featured">Featured products</option>
        <option value="price-low-high">Price: Low to High</option>
        <option value="price-high-low">Price: High to Low</option>
        <option value="rating">Highest Rated</option>
      </select>
    </div>

    <!-- Aesthetic Promo Card nested inside Sidebar -->
    <div class="p-4 bg-slate-900 rounded-2xl text-white relative overflow-hidden" id="sidebar-promo">
      <div class="absolute -right-10 -bottom-10 w-28 h-28 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
      <Sparkles class="h-5 w-5 text-yellow-400 mb-2" />
      <h5 class="font-bold text-sm mb-1">Free Shipping</h5>
      <p class="text-[10px] text-slate-300 leading-relaxed mb-3">Get free express shipping on order totals over $150.00 USD.</p>
      <div class="inline-block py-1 px-2.5 bg-white/15 rounded-lg text-[9px] font-bold tracking-wider uppercase text-white">
        Auto-applied
      </div>
    </div>
  </aside>
</template>
