<script setup lang="ts">
import { ShoppingBag, Github, Twitter, Instagram } from 'lucide-vue-next';
</script>

<template>
  <footer class="bg-slate-900 text-white mt-auto border-t border-slate-800" id="app-footer">
    <!-- Top Footer Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8 md:gap-12">
        
        <!-- Logo and brand pitch -->
        <div class="space-y-4">
          <div class="flex items-center gap-2">
            <span class="p-1.5 bg-white text-slate-900 rounded-lg">
              <ShoppingBag class="h-4 w-4" />
            </span>
            <span class="font-sans font-bold text-lg tracking-tight">ShopVue</span>
          </div>
          <p class="text-xs text-slate-400 leading-relaxed max-w-xs">
            Designing sustainable, high-performance urban gear, lifestyle accessories, and productivity-oriented electronics. Powered by Vue 3 and modern design ergonomics.
          </p>
          <div class="flex items-center gap-3">
            <a href="#" class="p-2 bg-slate-800 text-slate-400 hover:text-white rounded-lg transition-colors">
              <Twitter class="h-4 w-4" />
            </a>
            <a href="#" class="p-2 bg-slate-800 text-slate-400 hover:text-white rounded-lg transition-colors">
              <Instagram class="h-4 w-4" />
            </a>
            <a href="#" class="p-2 bg-slate-800 text-slate-400 hover:text-white rounded-lg transition-colors">
              <Github class="h-4 w-4" />
            </a>
          </div>
        </div>

        <!-- Links Grid - Column 1 -->
        <div>
          <h4 class="text-xs font-bold tracking-wider uppercase text-slate-300 mb-4">Products</h4>
          <ul class="space-y-2 text-xs text-slate-400 font-medium">
            <li><router-link to="/" class="hover:text-white transition-colors">New Arrivals</router-link></li>
            <li><router-link to="/" class="hover:text-white transition-colors">Best Sellers</router-link></li>
            <li><router-link to="/" class="hover:text-white transition-colors">Sale & Offers</router-link></li>
            <li><router-link to="/" class="hover:text-white transition-colors">Gift Cards</router-link></li>
          </ul>
        </div>

        <!-- Links Grid - Column 2 -->
        <div>
          <h4 class="text-xs font-bold tracking-wider uppercase text-slate-300 mb-4">Customer Care</h4>
          <ul class="space-y-2 text-xs text-slate-400 font-medium">
            <li><a href="#" class="hover:text-white transition-colors">Shipping & Returns</a></li>
            <li><a href="#" class="hover:text-white transition-colors">Orders & Tracking</a></li>
            <li><a href="#" class="hover:text-white transition-colors">FAQ & Support</a></li>
            <li><a href="#" class="hover:text-white transition-colors">Warranty & Claims</a></li>
          </ul>
        </div>

        <!-- Newsletter field -->
        <div class="space-y-4">
          <h4 class="text-xs font-bold tracking-wider uppercase text-slate-300 mb-4">Stay Connected</h4>
          <p class="text-xs text-slate-400 leading-relaxed">
            Subscribe to receive critical product launches, community projects, and exclusive discount codes.
          </p>
          <form class="flex gap-2" @submit.prevent>
            <input 
              type="email" 
              placeholder="e.g. name@example.com" 
              class="bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-xs w-full text-white placeholder-slate-500 focus:outline-none focus:border-slate-500 transition-colors"
            />
            <button class="bg-white text-slate-900 rounded-xl font-bold px-4 py-2 text-xs hover:bg-slate-100 transition-colors cursor-pointer">
              Join
            </button>
          </form>
        </div>

      </div>
    </div>

    <!-- Bottom Copyright Info -->
    <div class="border-t border-slate-800/80 bg-slate-950/40">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-[10px] text-slate-500 font-medium">
        <div>
          &copy; 2026 ShopVue Retail Inc. All rights reserved.
        </div>
        <div class="flex items-center gap-6">
          <a href="#" class="hover:text-slate-400 transition-colors">Privacy Policy</a>
          <a href="#" class="hover:text-slate-400 transition-colors">Terms of Service</a>
          <a href="#" class="hover:text-slate-400 transition-colors">Sitemap</a>
        </div>
      </div>
    </div>
  </footer>
</template>
