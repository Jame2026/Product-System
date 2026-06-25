import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export interface OrderItem {
  productId: string;
  name: string;
  price: number;
  quantity: number;
  image: string;
  category: string;
}

export interface Order {
  id: string;
  date: string;
  items: OrderItem[];
  subtotal: number;
  discount: number;
  shipping: number;
  tax: number;
  total: number;
  shippingAddress: {
    name: string;
    address: string;
    city: string;
    zipCode: string;
    country: string;
  };
  paymentMethod: {
    cardType: string;
    cardNumber: string; // last 4 digits
  };
  status: 'Processing' | 'Shipped' | 'Delivered' | 'Returned';
  trackingNumber?: string;
  estimatedDelivery?: string;
}

const PAST_ORDERS_SEED: Order[] = [
  {
    id: 'ORD-5481-92',
    date: 'June 10, 2026',
    items: [
      {
        productId: 'prod-5',
        name: 'Nomad Premium Leather Wallet',
        price: 59.00,
        quantity: 1,
        image: 'https://images.unsplash.com/photo-1627123424574-724758594e93?auto=format&fit=crop&q=80&w=200',
        category: 'Lifestyle'
      },
      {
        productId: 'prod-4',
        name: 'Apex Mechanical Keyboard',
        price: 149.00,
        quantity: 1,
        image: 'https://images.unsplash.com/photo-1618384887929-16ec33fab9ef?auto=format&fit=crop&q=80&w=200',
        category: 'Electronics'
      }
    ],
    subtotal: 208.00,
    discount: 20.80, // WELCOME10 applied
    shipping: 0.00,  // Over $150
    tax: 15.44,
    total: 202.64,
    shippingAddress: {
      name: 'Alex Mercer',
      address: '2486 Broad Street',
      city: 'San Francisco',
      zipCode: '94107',
      country: 'United States'
    },
    paymentMethod: {
      cardType: 'Visa',
      cardNumber: '4242'
    },
    status: 'Delivered',
    trackingNumber: 'USPS-9400111108239082341999',
    estimatedDelivery: 'June 14, 2026'
  },
  {
    id: 'ORD-1284-33',
    date: 'May 22, 2026',
    items: [
      {
        productId: 'prod-8',
        name: 'Portis High-Speed SSD 1TB',
        price: 119.00,
        quantity: 1,
        image: 'https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&q=80&w=200',
        category: 'Electronics'
      }
    ],
    subtotal: 119.00,
    discount: 0,
    shipping: 9.99,
    tax: 9.82,
    total: 138.81,
    shippingAddress: {
      name: 'Alex Mercer',
      address: '2486 Broad Street',
      city: 'San Francisco',
      zipCode: '94107',
      country: 'United States'
    },
    paymentMethod: {
      cardType: 'Mastercard',
      cardNumber: '9905'
    },
    status: 'Delivered',
    trackingNumber: 'FEDEX-983120489123',
    estimatedDelivery: 'May 25, 2026'
  }
];

export const useOrderStore = defineStore('orders', () => {
  const orders = ref<Order[]>([]);

  function init() {
    const saved = localStorage.getItem('vue_shop_orders');
    if (saved) {
      try {
        orders.value = JSON.parse(saved);
      } catch (e) {
        orders.value = [...PAST_ORDERS_SEED];
      }
    } else {
      orders.value = [...PAST_ORDERS_SEED];
      save();
    }
  }

  function save() {
    localStorage.setItem('vue_shop_orders', JSON.stringify(orders.value));
  }

  function createOrder(orderData: Omit<Order, 'id' | 'date' | 'status' | 'trackingNumber' | 'estimatedDelivery'>) {
    const randomIdSuffix = Math.floor(1000 + Math.random() * 9000);
    const randomIdMid = Math.floor(10 + Math.random() * 89);
    const orderId = `ORD-${randomIdSuffix}-${randomIdMid}`;
    
    // Set status to Processing, assign a randomized shipping date + carrier tracking code to make it real
    const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    const now = new Date();
    const formattedDate = `${months[now.getMonth()]} ${now.getDate()}, ${now.getFullYear()}`;
    
    const deliveryDate = new Date();
    deliveryDate.setDate(now.getDate() + 4);
    const formattedEstDelivery = `${months[deliveryDate.getMonth()]} ${deliveryDate.getDate()}, ${deliveryDate.getFullYear()}`;

    const trackingSuffix = Math.floor(100000000000 + Math.random() * 899999999999);
    const trackingNumber = `USPS-94001${trackingSuffix}`;

    const newOrder: Order = {
      ...orderData,
      id: orderId,
      date: formattedDate,
      status: 'Processing',
      trackingNumber,
      estimatedDelivery: formattedEstDelivery
    };

    orders.value.unshift(newOrder); // Add as first item in list
    save();
    return newOrder;
  }

  function getOrderById(id: string): Order | undefined {
    return orders.value.find(o => o.id === id);
  }

  init();

  return {
    orders,
    createOrder,
    getOrderById
  };
});
