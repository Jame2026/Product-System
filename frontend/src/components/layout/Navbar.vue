<script setup lang="ts">
import { ref, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useCartStore } from '@/stores/cartStore';
import { useAuthStore } from '@/stores/authStore';
import { useProductStore } from '@/stores/productStore';
import { 
  ShoppingBag, 
  Heart, 
  User, 
  Search, 
  Menu, 
  X,
  Compass,
  History,
  PackageCheck
} from 'lucide-vue-next';

const router = useRouter();
const route = useRoute();
const cartStore = useCartStore();
const authStore = useAuthStore();
const productStore = useProductStore();

const isMobileMenuOpen = ref(false);
const showProfileDropdown = ref(false);

const navItems = [
  { name: 'Shop All', to: '/', icon: Compass },
  { name: 'Favorites', to: '/favorites', icon: Heart },
  { name: 'Order History', to: '/order-history', icon: History }
];

const totalCartItems = computed(() => cartStore.totalItemsCount);
const totalFavItems = computed(() => productStore.favoriteProducts.length);

const handleSearchInput = (e: Event) => {
  const target = e.target as HTMLInputElement;
  productStore.searchQuery = target.value;
  // If we aren't on the Home page, redirect there to display search results
  if (route.name !== 'Home' && route.name !== 'Category') {
    router.push('/');
  }
};

const handleSearchSubmit = (e: Event) => {
  e.preventDefault();
  router.push('/');
};

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

const handleLogout = () => {
  authStore.logout();
  showProfileDropdown.value = false;
  router.push('/');
};
</script>

<template>
  <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-100 shadow-sm transition-all duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        
        <!-- Logo -->
        <div class="flex items-center">
          <router-link to="/" class="flex items-center gap-2 group" id="nav-logo">
            <span class="p-2 bg-slate-900 text-white rounded-xl group-hover:scale-105 transition-transform duration-200">
              <ShoppingBag class="h-5 w-5" />
            </span>
            <span class="font-sans font-bold text-xl tracking-tight text-slate-900">
              Shop<span class="text-slate-500 font-normal">Vue</span>
            </span>
          </router-link>
        </div>

        <!-- Search Bar (Desktop) -->
        <div class="hidden md:flex flex-1 max-w-md mx-8">
          <form @submit="handleSearchSubmit" class="relative w-full">
            <input 
              type="text" 
              placeholder="Search products, categories..." 
              :value="productStore.searchQuery"
              @input="handleSearchInput"
              class="w-full bg-slate-50 text-slate-850 placeholder-slate-400 text-sm pl-10 pr-4 py-2 rounded-xl border border-slate-100 focus:outline-none focus:border-slate-300 focus:bg-white transition-all duration-200"
              id="search-input-desktop"
            />
            <Search class="absolute left-3.5 top-2.5 h-4 w-4 text-slate-400" />
          </form>
        </div>

        <!-- Desktop Navigation Actions -->
        <div class="hidden md:flex items-center gap-6">
          <router-link 
            to="/" 
            class="text-sm font-medium transition-colors hover:text-slate-900" 
            :class="route.name === 'Home' ? 'text-slate-900 font-semibold' : 'text-slate-600'"
            id="nav-link-shop"
          >
            Shop All
          </router-link>

          <!-- Favorites Link -->
          <router-link 
            to="/favorites" 
            class="relative p-2 text-slate-600 hover:text-slate-900 rounded-full hover:bg-slate-50 transition-all duration-200"
            id="nav-link-favs"
          >
            <Heart class="h-5 w-5" />
            <span 
              v-if="totalFavItems > 0"
              class="absolute -top-1 -right-1 flex h-4 min-w-[16px] items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] font-bold text-white"
            >
              {{ totalFavItems }}
            </span>
          </router-link>

          <!-- Cart Link -->
          <router-link 
            to="/cart" 
            class="relative p-2 text-slate-600 hover:text-slate-900 rounded-full hover:bg-slate-50 transition-all duration-200"
            id="nav-link-cart"
          >
            <ShoppingBag class="h-5 w-5" />
            <span 
              v-if="totalCartItems > 0"
              class="absolute -top-1 -right-1 flex h-4 min-w-[16px] items-center justify-center rounded-full bg-slate-900 px-1 text-[10px] font-bold text-white animate-pulse"
            >
              {{ totalCartItems }}
            </span>
          </router-link>

          <!-- User dropdown area -->
          <div class="relative">
            <button 
              v-if="authStore.isAuthenticated"
              @click="showProfileDropdown = !showProfileDropdown"
              class="flex items-center gap-2 p-1 rounded-full hover:bg-slate-50 transition-all duration-200 cursor-pointer focus:outline-none"
              id="nav-user-menu-btn"
            >
              <img 
                :src="authStore.user?.avatar" 
                alt="Profile Avatar" 
                referrerpolicy="no-referrer"
                class="h-8 w-8 rounded-full border border-slate-200 object-cover"
              />
              <span class="text-xs font-semibold text-slate-700 hidden lg:inline-block max-w-[80px] truncate">
                {{ authStore.user?.name.split(' ')[0] }}
              </span>
            </button>

            <router-link 
              v-else 
              to="/profile" 
              class="flex items-center gap-2 px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-semibold hover:bg-slate-800 transition-colors duration-200"
              id="nav-login-btn"
            >
              <User class="h-4 w-4" />
              Sign In
            </router-link>

            <!-- Dropdown Menu -->
            <div 
              v-if="showProfileDropdown && authStore.isAuthenticated"
              v-on-click-outside="() => showProfileDropdown = false"
              class="absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-xl border border-slate-50 py-2 z-50 animate-fade-in"
            >
              <div class="px-4 py-2 border-b border-slate-50">
                <p class="text-xs text-slate-400">Signed in as</p>
                <p class="text-sm font-semibold text-slate-800 truncate">{{ authStore.user?.email }}</p>
              </div>

              <router-link 
                to="/profile" 
                @click="showProfileDropdown = false"
                class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors duration-200"
              >
                <User class="h-4 w-4" />
                My Profile
              </router-link>

              <router-link 
                to="/order-history" 
                @click="showProfileDropdown = false"
                class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors duration-200"
              >
                <PackageCheck class="h-4 w-4" />
                My Orders
              </router-link>

              <hr class="border-slate-50 my-1" />

              <button 
                @click="handleLogout"
                class="w-full text-left flex items-center gap-2.5 px-4 py-2.5 text-sm text-rose-500 hover:text-rose-600 hover:bg-rose-50/50 transition-colors duration-200 cursor-pointer"
              >
                <X class="h-4 w-4" />
                Sign Out
              </button>
            </div>
          </div>
        </div>

        <!-- Burger Icon (Mobile) -->
        <div class="flex items-center gap-4 md:hidden">
          <router-link to="/cart" class="relative p-2 text-slate-600" id="nav-mobile-cart">
            <ShoppingBag class="h-5 w-5" />
            <span 
              v-if="totalCartItems > 0"
              class="absolute 0 top-0 right-0 flex h-4 min-w-[16px] items-center justify-center rounded-full bg-slate-900 px-1 text-[9px] font-bold text-white"
            >
              {{ totalCartItems }}
            </span>
          </router-link>

          <button 
            @click="toggleMobileMenu" 
            class="p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-xl"
            id="nav-mobile-toggle"
          >
            <Menu v-if="!isMobileMenuOpen" class="h-6 w-6" />
            <X v-else class="h-6 w-6" />
          </button>
        </div>

      </div>
    </div>

    <!-- Mobile Drawer -->
    <div 
      v-if="isMobileMenuOpen"
      class="md:hidden border-t border-slate-100 bg-white px-4 py-4 space-y-4 shadow-inner"
    >
      <!-- Mobile Search -->
      <form @submit="handleSearchSubmit" class="relative w-full">
        <input 
          type="text" 
          placeholder="Search products..." 
          :value="productStore.searchQuery"
          @input="handleSearchInput"
          class="w-full bg-slate-50 text-slate-800 placeholder-slate-400 text-sm pl-10 pr-4 py-2.5 rounded-xl border border-slate-150 focus:outline-none"
          id="search-input-mobile"
        />
        <Search class="absolute left-3.5 top-3 h-4 w-4 text-slate-400" />
      </form>

      <!-- Mobile Links -->
      <nav class="flex flex-col gap-1.5">
        <router-link 
          v-for="item in navItems" 
          :key="item.name"
          :to="item.to"
          @click="isMobileMenuOpen = false"
          class="flex items-center gap-3 px-3 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-all"
        >
          <component :is="item.icon" class="h-5 w-5 text-slate-400" />
          <span class="text-sm font-medium">{{ item.name }}</span>
          
          <span 
            v-if="item.name === 'Favorites' && totalFavItems > 0"
            class="ml-auto bg-rose-100 text-rose-600 text-xs font-bold px-2 py-0.5 rounded-full"
          >
            {{ totalFavItems }}
          </span>
        </router-link>

        <router-link 
          v-if="authStore.isAuthenticated"
          to="/profile"
          @click="isMobileMenuOpen = false"
          class="flex items-center gap-3 px-3 py-3 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50"
        >
          <img 
            :src="authStore.user?.avatar" 
            alt="Profile Avatar"
            referrerpolicy="no-referrer"
            class="h-6 w-6 rounded-full border border-slate-200 object-cover"
          />
          <span class="text-sm font-medium">My Account ({{ authStore.user?.name.split(' ')[0] }})</span>
        </router-link>

        <router-link 
          v-else
          to="/profile"
          @click="isMobileMenuOpen = false"
          class="flex items-center gap-3 px-3 py-3 bg-slate-900 text-white rounded-xl"
        >
          <User class="h-5 w-5 text-slate-300" />
          <span class="text-sm font-medium">Sign In / Register</span>
        </router-link>

        <button 
          v-if="authStore.isAuthenticated"
          @click="handleLogout(); isMobileMenuOpen = false"
          class="flex items-center gap-3 px-3 py-3 rounded-xl text-rose-500 hover:bg-rose-50/50 text-left cursor-pointer w-full"
        >
          <X class="h-5 w-5 text-rose-400" />
          <span class="text-sm font-medium">Sign Out</span>
        </button>
      </nav>
    </div>
  </header>
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
