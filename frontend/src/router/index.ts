import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';

// View Imports
import HomeView from '@/views/HomeView.vue';
import CategoryView from '@/views/CategoryView.vue';
import ProductDetailView from '@/views/ProductDetailView.vue';
import CartView from '@/views/CartView.vue';
import FavoritesView from '@/views/FavoritesView.vue';
import CheckoutView from '@/views/CheckoutView.vue';
import OrderHistoryView from '@/views/OrderHistoryView.vue';
import ProfileView from '@/views/ProfileView.vue';

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    name: 'Home',
    component: HomeView,
    meta: { title: 'Home' }
  },
  {
    path: '/category/:id',
    name: 'Category',
    component: CategoryView,
    meta: { title: 'Category' }
  },
  {
    path: '/product/:id',
    name: 'ProductDetail',
    component: ProductDetailView,
    meta: { title: 'Product Details' }
  },
  {
    path: '/cart',
    name: 'Cart',
    component: CartView,
    meta: { title: 'Shopping Cart' }
  },
  {
    path: '/favorites',
    name: 'Favorites',
    component: FavoritesView,
    meta: { title: 'My Favorites' }
  },
  {
    path: '/checkout',
    name: 'Checkout',
    component: CheckoutView,
    meta: { title: 'Secure Checkout', requiresAuth: true }
  },
  {
    path: '/order-history',
    name: 'OrderHistory',
    component: OrderHistoryView,
    meta: { title: 'Order History', requiresAuth: true }
  },
  {
    path: '/profile',
    name: 'Profile',
    component: ProfileView,
    meta: { title: 'My Account' }
  },
  // Fallback redirect
  {
    path: '/:pathMatch(.*)*',
    redirect: '/'
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition;
    } else {
      return { top: 0 };
    }
  }
});

// Authentication Guard implementation
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();
  
  // Set window title
  if (to.meta.title) {
    document.title = `${to.meta.title} | ShopVue`;
  }
  
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    // If route requires auth and not authenticated, redirect to /profile to log in first
    next({ name: 'Profile', query: { redirect: to.fullPath } });
  } else {
    next();
  }
});

export default router;
