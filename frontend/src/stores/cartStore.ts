import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { useProductStore } from './productStore';

export interface CartItem {
  productId: string;
  quantity: number;
  selectedColor?: string;
  selectedSize?: string;
}

export const useCartStore = defineStore('cart', () => {
  const items = ref<CartItem[]>([]);
  const couponCode = ref<string>('');
  const couponDiscount = ref<number>(0); // as a fraction (e.g. 0.10 for 10%)
  const isCouponValid = ref<boolean | null>(null);

  const productStore = useProductStore();

  function init() {
    const saved = localStorage.getItem('vue_shop_cart');
    if (saved) {
      try {
        const parsed = JSON.parse(saved);
        items.value = parsed.items || [];
        couponCode.value = parsed.couponCode || '';
        couponDiscount.value = parsed.couponDiscount || 0;
        isCouponValid.value = parsed.isCouponValid ?? null;
      } catch (e) {}
    }
  }

  function save() {
    localStorage.setItem('vue_shop_cart', JSON.stringify({
      items: items.value,
      couponCode: couponCode.value,
      couponDiscount: couponDiscount.value,
      isCouponValid: isCouponValid.value,
    }));
  }

  const cartDetails = computed(() => {
    return items.value.map(item => {
      const product = productStore.getProductById(item.productId);
      return {
        ...item,
        product,
      };
    }).filter(item => item.product !== undefined);
  });

  const subtotal = computed(() => {
    return cartDetails.value.reduce((sum, item) => {
      if (item.product) {
        return sum + (item.product.price * item.quantity);
      }
      return sum;
    }, 0);
  });

  const discountAmount = computed(() => {
    return subtotal.value * couponDiscount.value;
  });

  const shippingFee = computed(() => {
    if (subtotal.value === 0) return 0;
    return subtotal.value >= 150 ? 0 : 9.99; // Free shipping above $150
  });

  const taxAmount = computed(() => {
    return (subtotal.value - discountAmount.value) * 0.0825; // 8.25% Tax
  });

  const total = computed(() => {
    const value = subtotal.value - discountAmount.value + shippingFee.value + taxAmount.value;
    return Math.max(0, value);
  });

  const totalItemsCount = computed(() => {
    return items.value.reduce((sum, item) => sum + item.quantity, 0);
  });

  function addToCart(productId: string, quantity = 1, color = '', size = '') {
    const existingIndex = items.value.findIndex(
      item => item.productId === productId && 
              item.selectedColor === color && 
              item.selectedSize === size
    );

    if (existingIndex > -1) {
      items.value[existingIndex].quantity += quantity;
    } else {
      items.value.push({
        productId,
        quantity,
        selectedColor: color || undefined,
        selectedSize: size || undefined,
      });
    }
    save();
  }

  function removeFromCart(productId: string, color = '', size = '') {
    const index = items.value.findIndex(
      item => item.productId === productId && 
              item.selectedColor === (color || undefined) && 
              item.selectedSize === (size || undefined)
    );
    if (index > -1) {
      items.value.splice(index, 1);
      save();
    }
  }

  function updateQuantity(productId: string, quantity: number, color = '', size = '') {
    const index = items.value.findIndex(
      item => item.productId === productId && 
              item.selectedColor === (color || undefined) && 
              item.selectedSize === (size || undefined)
    );
    if (index > -1) {
      if (quantity <= 0) {
        items.value.splice(index, 1);
      } else {
        const product = productStore.getProductById(productId);
        if (product && quantity > product.stock) {
          items.value[index].quantity = product.stock; // Bound by stock limit
        } else {
          items.value[index].quantity = quantity;
        }
      }
      save();
    }
  }

  function applyCoupon(code: string): boolean {
    const formattedCode = code.toUpperCase().trim();
    if (formattedCode === 'WELCOME10') {
      couponCode.value = formattedCode;
      couponDiscount.value = 0.10; // 10%
      isCouponValid.value = true;
      save();
      return true;
    } else if (formattedCode === 'SAVE20') {
      couponCode.value = formattedCode;
      couponDiscount.value = 0.20; // 20%
      isCouponValid.value = true;
      save();
      return true;
    } else {
      couponCode.value = '';
      couponDiscount.value = 0;
      isCouponValid.value = false;
      save();
      return false;
    }
  }

  function removeCoupon() {
    couponCode.value = '';
    couponDiscount.value = 0;
    isCouponValid.value = null;
    save();
  }

  function clearCart() {
    items.value = [];
    couponCode.value = '';
    couponDiscount.value = 0;
    isCouponValid.value = null;
    save();
  }

  init();

  return {
    items,
    couponCode,
    couponDiscount,
    isCouponValid,
    cartDetails,
    subtotal,
    discountAmount,
    shippingFee,
    taxAmount,
    total,
    totalItemsCount,
    addToCart,
    removeFromCart,
    updateQuantity,
    applyCoupon,
    removeCoupon,
    clearCart,
  };
});
