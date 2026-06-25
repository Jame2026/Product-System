<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useProductStore } from '@/stores/productStore';
import { useCartStore } from '@/stores/cartStore';
import { formatPrice } from '@/utils/formatPrice';
import ProductCard from '@/components/product/ProductCard.vue';
import { 
  Star, 
  Heart, 
  ShoppingCart, 
  ChevronLeft, 
  ShieldCheck, 
  Truck,
  RotateCcw,
  Plus,
  Minus,
  Check,
  ChevronDown
} from 'lucide-vue-next';

const route = useRoute();
const router = useRouter();
const productStore = useProductStore();
const cartStore = useCartStore();

const productId = computed(() => {
  return route.params.id as string;
});

const product = computed(() => {
  return productStore.getProductById(productId.value);
});

// Image display state
const activeImage = ref('');

// Sync activeImage whenever product loads or switches
watch(product, (newVal) => {
  if (newVal) {
    activeImage.value = newVal.image;
  }
}, { immediate: true });

// Attributes options state
const selectedColor = ref('Anthracite');
const colorsList = ['Anthracite', 'Silver Oxide', 'Alpine Cream'];

const selectedSize = ref('Standard');
const sizesList = ['Standard', 'Extended XL'];

const buyQuantity = ref(1);
const activeTab = ref<'specs' | 'features'>('features');
const showSuccessToast = ref(false);

const relatedProducts = computed(() => {
  if (!product.value) return [];
  // Find products in the same category, exclusion current product ID
  return productStore.products
    .filter(p => p.category === product.value?.category && p.id !== product.value?.id)
    .slice(0, 3);
});

const handleGoBack = () => {
  router.back();
};

const handleAddToCart = () => {
  if (!product.value) return;
  cartStore.addToCart(
    product.value.id, 
    buyQuantity.value, 
    selectedColor.value, 
    selectedSize.value
  );
  
  // Show temporary feedback toast
  showSuccessToast.value = true;
  setTimeout(() => {
    showSuccessToast.value = false;
  }, 3500);
};

const toggleFavorite = () => {
  if (product.value) {
    productStore.toggleFavorite(product.value.id);
  }
};

const handleQuantityIncrement = () => {
  if (product.value && buyQuantity.value < product.value.stock) {
    buyQuantity.value++;
  }
};

const handleQuantityDecrement = () => {
  if (buyQuantity.value > 1) {
    buyQuantity.value--;
  }
};

const mockReviewsList = [
  { name: 'Sarah Jenkins', rating: 5, date: 'June 4, 2026', comment: 'Absolute masterpiece. The build quality exceeds anything I have owned before. Instant connection.' },
  { name: 'Michael Chen', rating: 4, date: 'May 18, 2026', comment: 'Excellent ergonomics. Sleek design details. Docked 1 star because shipping took 4 days instead of 2.' }
];
</script>

<template>
  <div v-if="product" class="space-y-12 pb-16 animate-fade-in" id="product-detail-view-container">
    
    <!-- Toast notification -->
    <div 
      v-if="showSuccessToast"
      class="fixed top-20 right-6 z-50 bg-slate-900 border border-slate-800 text-white rounded-2xl p-4 shadow-2xl flex items-center gap-3 animate-slide-in max-w-sm"
      id="add-to-cart-success-toast"
    >
      <span class="p-1 bg-emerald-500 rounded-full text-white">
        <Check class="h-4 w-4" />
      </span>
      <div class="space-y-0.5">
        <p class="text-xs font-bold">Successfully Added!</p>
        <p class="text-[11px] text-slate-350 leading-none">Added {{ buyQuantity }}x {{ product.name }} to your cart.</p>
      </div>
    </div>

    <!-- Navigation Breadcrumbs -->
    <div>
      <button 
        @click="handleGoBack"
        class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-900 cursor-pointer"
        id="btn-detail-back"
      >
        <ChevronLeft class="h-4 w-4" />
        Back to listings
      </button>
    </div>

    <!-- Main product details Layout: Two column grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">
      
      <!-- Column 1: Image Gallery Panel (Left side) -->
      <div class="lg:col-span-7 space-y-4">
        <div class="aspect-square bg-slate-100 rounded-2xl overflow-hidden border border-slate-100 shadow-sm relative">
          <img 
            :src="activeImage" 
            :alt="product.name" 
            referrerpolicy="no-referrer"
            class="w-full h-full object-cover object-center"
            id="main-product-image"
          />
        </div>

        <!-- Thumbnails Gallery Row -->
        <div class="flex items-center gap-3 overflow-x-auto pb-1">
          <!-- Primary Cover Thumbnail -->
          <button 
            @click="activeImage = product.image"
            class="h-18 w-18 flex-shrink-0 rounded-xl overflow-hidden border-2 cursor-pointer transition-all"
            :class="activeImage === product.image ? 'border-slate-905 scale-95 shadow-sm' : 'border-transparent opacity-60 hover:opacity-100'"
          >
            <img :src="product.image" class="h-full w-full object-cover" />
          </button>

          <!-- Secondary Image Thumbnails -->
          <button 
            v-for="(imgUrl, i) in product.secondaryImages" 
            :key="i"
            @click="activeImage = imgUrl"
            class="h-18 w-18 flex-shrink-0 rounded-xl overflow-hidden border-2 cursor-pointer transition-all"
            :class="activeImage === imgUrl ? 'border-slate-900 scale-95 shadow-sm' : 'border-transparent opacity-60 hover:opacity-100'"
          >
            <img :src="imgUrl" class="h-full w-full object-cover" />
          </button>
        </div>
      </div>

      <!-- Column 2: Buy & Description details (Right side) -->
      <div class="lg:col-span-5 space-y-6">
        
        <!-- Category and Title descriptors -->
        <div class="space-y-2">
          <span class="text-xs font-extrabold text-slate-400 uppercase tracking-widest">{{ product.category }}</span>
          <h1 class="font-sans font-extrabold text-2xl sm:text-3xl tracking-tight text-slate-900">{{ product.name }}</h1>
          
          <!-- Ratings bar summary -->
          <div class="flex items-center gap-2 pt-1">
            <div class="flex items-center gap-0.5">
              <Star 
                v-for="i in 5" 
                :key="i"
                class="h-4 w-4"
                :class="i <= Math.floor(product.rating) ? 'fill-yellow-400 text-yellow-400' : 'text-slate-200'"
              />
            </div>
            <span class="text-xs font-bold text-slate-800">{{ product.rating }}</span>
            <span class="text-xs text-slate-400">({{ product.reviewsCount }} verified reviews)</span>
          </div>
        </div>

        <!-- Pricing element -->
        <div class="py-4 border-y border-slate-100">
          <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Price</p>
          <div class="flex items-baseline gap-2">
            <span class="font-sans font-extrabold text-2xl sm:text-3xl text-slate-900">{{ formatPrice(product.price) }}</span>
            <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-100/40">In Stock</span>
          </div>
        </div>

        <!-- Attribute: Color select dots -->
        <div class="space-y-3">
          <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Select Color option</label>
          <div class="flex items-center gap-3">
            <button 
              v-for="color in colorsList" 
              :key="color"
              @click="selectedColor = color"
              class="px-4 py-2 bg-slate-50 border text-xs font-semibold rounded-xl cursor-pointer transition-all flex items-center gap-2"
              :class="selectedColor === color 
                ? 'border-slate-900 bg-white font-bold text-slate-900 ring-2 ring-slate-900/5' 
                : 'border-slate-100 text-slate-600 hover:border-slate-200'"
            >
              <!-- Color Dot Indicator -->
              <span 
                class="h-2.5 w-2.5 rounded-full" 
                :class="color === 'Anthracite' ? 'bg-slate-800' : color === 'Silver Oxide' ? 'bg-slate-300' : 'bg-orange-50'"
              ></span>
              {{ color }}
            </button>
          </div>
        </div>

        <!-- Attribute: Size select pill options -->
        <div class="space-y-3">
          <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Size Option</label>
          <div class="flex items-center gap-3 animate-fade-in">
            <button 
              v-for="sz in sizesList" 
              :key="sz"
              @click="selectedSize = sz"
              class="px-4 py-2 border text-xs font-semibold rounded-xl cursor-pointer transition-colors"
              :class="selectedSize === sz 
                ? 'border-slate-900 bg-slate-900 text-white font-bold' 
                : 'border-slate-100 text-slate-600 bg-slate-50 hover:bg-slate-100'"
            >
              {{ sz }}
            </button>
          </div>
        </div>

        <!-- Buy Quantity and core Add to Cart Controls -->
        <div class="space-y-3">
          <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Quantity Selector</label>
          <div class="flex items-center gap-4 flex-wrap">
            
            <!-- Quantity counter control -->
            <div class="flex items-center border border-slate-150 rounded-xl px-2.5 py-1.5 bg-slate-5"><button 
                @click="handleQuantityDecrement"
                class="p-1 text-slate-500 hover:text-slate-900 hover:bg-slate-100 rounded-lg active:scale-90 cursor-pointer"
                title="Decrease Qty"
              >
                <Minus class="h-4 w-4" />
              </button>
              
              <span class="px-5 text-sm font-bold text-slate-800 select-none min-w-[28px] text-center">
                {{ buyQuantity }}
              </span>
              
              <button 
                @click="handleQuantityIncrement"
                class="p-1 text-slate-500 hover:text-slate-900 hover:bg-slate-100 rounded-lg active:scale-90 cursor-pointer"
                :disabled="buyQuantity >= product.stock"
                :class="buyQuantity >= product.stock ? 'opacity-30 cursor-not-allowed' : ''"
                title="Increase Qty"
              >
                <Plus class="h-4 w-4" />
              </button>
            </div>

            <!-- Primary Cart CTA -->
            <button 
              @click="handleAddToCart"
              class="px-6 py-3.5 bg-slate-900 text-white font-bold text-sm rounded-xl shadow-md hover:bg-slate-800 active:scale-[0.98] transition-all flex items-center justify-center gap-2 flex-1 cursor-pointer"
              id="btn-detail-add-cart"
            >
              <ShoppingCart class="h-4 w-4" />
              Add to Shopping Cart
            </button>

            <!-- Favorite Toggle button -->
            <button 
              @click="toggleFavorite"
              class="p-3.5 bg-slate-50 text-slate-500 hover:text-rose-500 hover:bg-rose-50 border border-slate-100 rounded-xl transition-colors cursor-pointer"
              id="btn-detail-favorite-toggle"
              :title="productStore.isFavorite(product.id) ? 'Remove Favorite' : 'Save as Favorite'"
            >
              <Heart 
                class="h-5 w-5 transition-colors" 
                :class="productStore.isFavorite(product.id) ? 'fill-rose-500 text-rose-500' : 'text-slate-450'" 
              />
            </button>

          </div>
        </div>

        <!-- Trust factors columns list -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-slate-100 text-[11px] font-semibold text-slate-500">
          <div class="flex items-center gap-2">
            <Truck class="h-4 w-4 text-slate-400" />
            <span>Fast Dispatch Worldwide</span>
          </div>
          <div class="flex items-center gap-2">
            <RotateCcw class="h-4 w-4 text-slate-400" />
            <span>30-Day Pure Returns</span>
          </div>
          <div class="flex items-center gap-2">
            <ShieldCheck class="h-4 w-4 text-slate-400" />
            <span>2-Year Full Protection</span>
          </div>
        </div>

      </div>

    </div>

    <!-- Expanded Specs accordion panels and tabs -->
    <section class="border border-slate-100 bg-white rounded-2xl p-6 sm:p-8 shadow-sm">
      <div class="flex border-b border-slate-100 mb-6 gap-6">
        <button 
          @click="activeTab = 'features'"
          class="font-sans font-bold text-sm tracking-wider uppercase pb-3 border-b-2 cursor-pointer transition-colors"
          :class="activeTab === 'features' ? 'border-slate-950 text-slate-900' : 'border-transparent text-slate-450 hover:text-slate-950'"
        >
          Product Features
        </button>
        <button 
          @click="activeTab = 'specs'"
          class="font-sans font-bold text-sm tracking-wider uppercase pb-3 border-b-2 cursor-pointer transition-colors"
          :class="activeTab === 'specs' ? 'border-slate-950 text-slate-900' : 'border-transparent text-slate-450 hover:text-slate-950'"
        >
          Specifications
        </button>
      </div>

      <!-- Feature view list option -->
      <div v-if="activeTab === 'features'" class="space-y-4 animate-fade-in">
        <p class="text-sm text-slate-600 leading-relaxed font-semibold">
          {{ product.description }}
        </p>

        <ul class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
          <li 
            v-for="(feat, i) in product.features" 
            :key="i"
            class="text-xs text-slate-500 font-semibold leading-relaxed flex items-start gap-2"
          >
            <span class="mt-1 h-1.5 w-1.5 rounded-full bg-slate-900 flex-shrink-0"></span>
            {{ feat }}
          </li>
        </ul>
      </div>

      <!-- Specs descriptor key-values list -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-x-12 gap-y-3.5 text-xs animate-fade-in font-medium">
        <div 
          v-for="(val, name) in product.specifications" 
          :key="name"
          class="flex items-center justify-between pb-2 border-b border-slate-50"
        >
          <span class="text-slate-400 font-bold uppercase tracking-wider">{{ name }}</span>
          <span class="text-slate-800 font-bold">{{ val }}</span>
        </div>
      </div>
    </section>

    <!-- verified reviews block -->
    <section class="space-y-4">
      <h3 class="font-sans font-extrabold text-lg text-slate-900">Verified Consumer Reviews</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div 
          v-for="(rev, i) in mockReviewsList" 
          :key="i"
          class="p-5 bg-white border border-slate-100 rounded-2xl shadow-xs space-y-3"
        >
          <div class="flex items-center justify-between">
            <h4 class="font-bold text-xs sm:text-sm text-slate-850">{{ rev.name }}</h4>
            <span class="text-[10px] font-semibold text-slate-500">{{ rev.date }}</span>
          </div>
          <div class="flex items-center gap-0.5">
            <Star 
              v-for="star in 5" 
              :key="star"
              class="h-3 w-3"
              :class="star <= rev.rating ? 'fill-yellow-400 text-yellow-400' : 'text-slate-150'"
            />
          </div>
          <p class="text-xs text-slate-500 font-semibold leading-relaxed">{{ rev.comment }}</p>
        </div>
      </div>
    </section>

    <!-- Beautiful related products strip -->
    <section v-if="relatedProducts.length > 0" class="space-y-6 pt-6 border-t border-slate-100">
      <div>
        <h3 class="font-sans font-extrabold text-xl tracking-tight text-slate-900">You may also like</h3>
        <p class="text-[11px] text-slate-500 font-semibold mt-0.5">Handpicked premium creations sharing similar styles</p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 sm:gap-8">
        <div 
          v-for="relProd in relatedProducts" 
          :key="relProd.id"
        >
          <ProductCard :product="relProd" />
        </div>
      </div>
    </section>

  </div>

  <!-- Loading state safeguard -->
  <div v-else class="text-center py-20 animate-fade-in" id="product-detail-404">
    <h3 class="font-sans font-extrabold text-xl text-slate-900">Product not found</h3>
    <p class="text-xs text-slate-500 mt-2">The product ID is incorrect or may have been unlisted.</p>
    <router-link 
      to="/" 
      class="mt-6 inline-block px-5 py-2.5 bg-slate-900 text-white rounded-xl text-xs font-bold font-sans hover:bg-slate-800"
    >
      Return to catalog
    </router-link>
  </div>
</template>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.15s ease-out forwards;
}

.animate-slide-in {
  animation: slideIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
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

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateX(20px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}
</style>
