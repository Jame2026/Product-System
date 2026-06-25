import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { apiGet, getApiAssetUrl } from '@/services/api';

export interface Product {
  id: string;
  name: string;
  price: number;
  category: string;
  rating: number;
  reviewsCount: number;
  image: string;
  secondaryImages: string[];
  description: string;
  features: string[];
  specifications: Record<string, string>;
  stock: number;
  isPopular?: boolean;
}

interface BackendCategory {
  id?: number | string;
  name?: string;
  dec?: string;
}

interface BackendProduct {
  id: number | string;
  name: string;
  price: number | string;
  category?: BackendCategory | null;
  category_id?: number | string;
  image?: string | null;
  desc?: string | null;
  description?: string | null;
  qty?: number | string | null;
  stock?: number | string | null;
  is_active?: boolean | number;
  rating?: number | string | null;
  reviews_count?: number | string | null;
  reviewsCount?: number | string | null;
}

interface ProductCachePayload {
  cachedAt: number;
  products: Product[];
}

const PRODUCT_CACHE_KEY = 'vue_shop_products_cache';
const PRODUCT_CACHE_TTL_MS = 5 * 60 * 1000;

const SAMPLE_PRODUCTS: Product[] = [
  {
    id: 'prod-1',
    name: 'AeroGlide ANC Headphones',
    price: 299.00,
    category: 'Electronics',
    rating: 4.8,
    reviewsCount: 142,
    image: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&q=80&w=600',
    secondaryImages: [
      'https://images.unsplash.com/photo-1546435770-a3e426bf472b?auto=format&fit=crop&q=80&w=600',
      'https://images.unsplash.com/photo-1583394838336-acd977736f90?auto=format&fit=crop&q=80&w=600'
    ],
    description: 'Experience pure sonic bliss with the AeroGlide ANC Headphones. Features military-grade active noise cancellation, custom-tuned 40mm beryllium drivers, and a luxurious memory foam headband wrapped in premium high-grain leather. Designed for jet-setters and audiophiles alike.',
    features: [
      'Up to 45 hours of continuous battery life with fast charge support',
      'Hybrid Active Noise Cancellation with Ambient Transparency Mode',
      'Studio-engineered high-resolution audio with custom equalizer presets',
      'Multi-point Bluetooth® 5.2 connectivity to stream from two devices effortlessly'
    ],
    specifications: {
      'Driver Size': '40 mm Dynamic',
      'Frequency Response': '4Hz - 40kHz',
      'Impedance': '32 Ohm',
      'Connection': 'USB-C & Bluetooth 5.2',
      'Weight': '260g'
    },
    stock: 12,
    isPopular: true
  },
  {
    id: 'prod-2',
    name: 'Vanguard Waterproof Backpack',
    price: 129.00,
    category: 'Accessories',
    rating: 4.6,
    reviewsCount: 98,
    image: 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&q=80&w=600',
    secondaryImages: [
      'https://images.unsplash.com/photo-1622560480605-d83c853bc5c3?auto=format&fit=crop&q=80&w=600',
      'https://images.unsplash.com/photo-1575844261186-dc398adbe487?auto=format&fit=crop&q=80&w=600'
    ],
    description: 'Engineered for the modern urban commuter, the Vanguard Waterproof Backpack combines ballistic nylon structure with ultra-modern architectural ergonomics. Features an integrated water-resistant laptop sleeve, magnetic Fidlock buckles, and high-density padded air-mesh straps.',
    features: [
      '100% waterproof TPU-laminated ballistic nylon canvas core',
      'Suspended padded sleeve protects laptops up to 16 inches',
      'Concealed RFID-safe tech pocket for cards and passport safety',
      'Breathable, ergonomic molded back panel with rolling suitcase pass-through'
    ],
    specifications: {
      'Capacity': '24 Liters',
      'Material': '1680D Ballistic Nylon',
      'Laptop Pocket': 'Up to 16" MacBook Pro',
      'Water Resistance': 'IPX5 Rainproof',
      'Dimensions': '18" x 12.5" x 6.5"'
    },
    stock: 25,
    isPopular: true
  },
  {
    id: 'prod-3',
    name: 'Apollo Chronograph Wrist Watch',
    price: 189.00,
    category: 'Accessories',
    rating: 4.9,
    reviewsCount: 64,
    image: 'https://images.unsplash.com/photo-1522312346375-d1a52e2b99b3?auto=format&fit=crop&q=80&w=600',
    secondaryImages: [
      'https://images.unsplash.com/photo-1524592094714-0f0654e20314?auto=format&fit=crop&q=80&w=600',
      'https://images.unsplash.com/photo-1547996160-81dfa63595aa?auto=format&fit=crop&q=80&w=600'
    ],
    description: 'A tribute to classical luxury-sport watch architecture. The Apollo Chronograph is powered by an ultra-precise Japanese hybrid quartz movement housed in a surgical-grade brushed 316L stainless steel case. Features a fully custom leather strap sourced from artisanal Tuscan tanneries.',
    features: [
      'Domed scratch-resistant sapphire crystal lens with AR-reflective coating',
      'Stopwatch sub-dials with split-second timing resolution',
      'Authentic full-grain Italian Tuscan leather strap with buckle swap tool',
      'Super-LumiNova® high-performance luminescent hands and dial indices'
    ],
    specifications: {
      'Case Diameter': '41 mm',
      'Case Thickness': '11.5 mm',
      'Movement': 'Miyota Hybrid Chronograph',
      'Water Resistance': '50 meters (5 ATM)',
      'Strap Width': '20 mm'
    },
    stock: 6,
    isPopular: true
  },
  {
    id: 'prod-4',
    name: 'Apex Mechanical Keyboard',
    price: 149.00,
    category: 'Electronics',
    rating: 4.7,
    reviewsCount: 110,
    image: 'https://images.unsplash.com/photo-1618384887929-16ec33fab9ef?auto=format&fit=crop&q=80&w=600',
    secondaryImages: [
      'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&q=80&w=600',
      'https://images.unsplash.com/photo-1595225476474-87563907a212?auto=format&fit=crop&q=80&w=600'
    ],
    description: 'Unleash ultimate tactile productivity with the Apex Mechanical Keyboard. Configured with hot-swappable linear yellow switches pre-lubricated for smooth, near-silent keystrokes. Features premium doubleshot PBT keycaps in an iconic retro-modern color palette and dynamic south-facing custom RGB lighting.',
    features: [
      '75% space-saving layout with solid aircraft-grade aluminum top case',
      'Hot-swappable sockets support both 3-pin and 5-pin mechanical switches',
      'Triple-mode connectivity: Bluetooth® 5.0, low-latency 2.4Ghz, and USB-C',
      'Integrated sound-dampening silicon layers and custom gasket mount layout'
    ],
    specifications: {
      'Layout': '75% (82 Keys) ANSI',
      'Switches': 'Linear Custom Lubed Yellow (45g actuate)',
      'Keycaps': 'Doubleshot PBT Cherry Profile',
      'Battery': '4000mAh Lithium-Polymer',
      'Hot-swap': 'Yes, Full hot-swap'
    },
    stock: 14,
    isPopular: false
  },
  {
    id: 'prod-5',
    name: 'Nomad Premium Leather Wallet',
    price: 59.00,
    category: 'Lifestyle',
    rating: 4.5,
    reviewsCount: 79,
    image: 'https://images.unsplash.com/photo-1627123424574-724758594e93?auto=format&fit=crop&q=80&w=600',
    secondaryImages: [
      'https://images.unsplash.com/photo-1588444839799-eaa43425786a?auto=format&fit=crop&q=80&w=600'
    ],
    description: 'A minimalist bi-fold purse redesigned for thinness and ultimate security. Handcrafted from certified gold-rated Horween leather that ages exquisitely into a rich patina. Features space-saving card pockets and an invisible, integrated aluminum core that blocks RFID signals completely.',
    features: [
      'Fits up to 10 cards comfortably alongside flat bill pocketing',
      'Full-grain Horween Leather imports a beautiful custom patina profile',
      'Ultra-slender architectural styling avoids pocket bulging',
      'Complete RFID blocking safety system across all internal compartments'
    ],
    specifications: {
      'Material': 'Horween Full-Grain Leather',
      'Sizing': '3.2" x 4.1" x 0.35"',
      'Card Slots': '6 Custom Card pockets',
      'Hardware': 'Premium stitched German thread',
      'Weight': '42g'
    },
    stock: 30,
    isPopular: false
  },
  {
    id: 'prod-6',
    name: 'SoloPro Wireless Earbuds',
    price: 159.00,
    category: 'Electronics',
    rating: 4.5,
    reviewsCount: 165,
    image: 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?auto=format&fit=crop&q=80&w=600',
    secondaryImages: [
      'https://images.unsplash.com/photo-1608156639585-b3a032ef9689?auto=format&fit=crop&q=80&w=600'
    ],
    description: 'Incredible acoustic depth combined with lightweight, secure sports fit. The SoloPro buds are constructed with sweat-resistant composite casings and high-friction ear gels. Generates heart-thumping response with 10mm graphene drivers for energetic workout backing.',
    features: [
      'Up to 8 hours audio playback on a single charge (+30 hours via case)',
      'IP67 ratings provide robust waterproof, dust-free sports reliability',
      'Dual-beamforming microphones optimize voice pick performance',
      'Smart touch-panel allows navigation of tracks, volume, and smart assistants'
    ],
    specifications: {
      'Driver Spec': '10mm Graphene Composite',
      'Water rating': 'IP67 Waterproof',
      'BT Protocol': 'Bluetooth 5.3',
      'Codec Type': 'AAC, SBC, aptX Adaptive',
      'Charge speed': '10 mins charge = 2 hours play'
    },
    stock: 19,
    isPopular: true
  },
  {
    id: 'prod-7',
    name: 'ErgoSupport Executive Desk Chair',
    price: 449.00,
    category: 'Office',
    rating: 4.8,
    reviewsCount: 52,
    image: 'https://images.unsplash.com/photo-1505797149-43b0069ec26b?auto=format&fit=crop&q=80&w=600',
    secondaryImages: [
      'https://images.unsplash.com/photo-1580481072645-022f9a6dbf27?auto=format&fit=crop&q=80&w=600'
    ],
    description: 'Transform your work environment with ergonomic seating precision. Designed alongside certified spinal therapists, the ErgoSupport Desk Chair features highly adjustable Lumbar back support, dynamic mesh backing for cool ventilation, and responsive multi-direction arm cushions.',
    features: [
      'Fully adaptive orthopedic lumbar pressure contour profiling',
      'Highly breathable woven elastic mesh for cool thermal ventilation',
      'Sturdy heavy-duty class 4 pneumatic lift supports up to 350 lbs',
      'Configurable 3D multi-angle padded armrests adjust in 3 planes'
    ],
    specifications: {
      'Frame construction': 'Reinforced Premium Nylon Steel',
      'Back Panel': 'High-tensile Korean Elastic Mesh',
      'Tilt Range': '90° to 135° Recline',
      'Base Spec': 'Heavy-duty 60mm PU Castor wheels',
      'Warranty': '5-Year Structural Guarantee'
    },
    stock: 5,
    isPopular: true
  },
  {
    id: 'prod-8',
    name: 'Portis High-Speed SSD 1TB',
    price: 119.00,
    category: 'Electronics',
    rating: 4.9,
    reviewsCount: 221,
    image: 'https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&q=80&w=600',
    secondaryImages: [
      'https://images.unsplash.com/photo-1601524909162-be87252be298?auto=format&fit=crop&q=80&w=600'
    ],
    description: 'Pocket-sized powerhouse. The Portis Portable External SSD delivers blistering transfer rates of up to 1050MB/s utilizing USB 3.2 Gen 2 specs. Formatted in robust IP55 rubberized protection to cushion sudden drops, rainy shoots, or rough desert expeditions with ease.',
    features: [
      'Breathtaking read/write transfers landing up to 1050 MB/s speeds',
      'Engineered with internal thermal protection to sustain high workloads',
      'Encased in durable IP55 water-safe and dust-resistant casing shield',
      'Supports hardware-level AES-256 bit file lock passcodes'
    ],
    specifications: {
      'Storage Volume': '1 Terabyte (TB)',
      'Protocol Type': 'USB 3.2 Gen 2 (10Gbps)',
      'Internal Type': 'NVMe PCIe Storage core',
      'Durability': 'Undergoes up to 3-meter drop tests',
      'Included Leads': 'Type-C to Type-C & Type-C to Type-A wires'
    },
    stock: 18,
    isPopular: false
  }
];

function toNumber(value: number | string | null | undefined, fallback = 0): number {
  const parsed = Number(value);
  return Number.isFinite(parsed) ? parsed : fallback;
}

function readCachedProducts(): ProductCachePayload | null {
  const cached = localStorage.getItem(PRODUCT_CACHE_KEY);
  if (!cached) return null;

  try {
    const payload = JSON.parse(cached) as ProductCachePayload;
    if (!Array.isArray(payload.products)) return null;
    return payload;
  } catch (e) {
    return null;
  }
}

function writeCachedProducts(products: Product[]) {
  const payload: ProductCachePayload = {
    cachedAt: Date.now(),
    products,
  };

  localStorage.setItem(PRODUCT_CACHE_KEY, JSON.stringify(payload));
}

function getFallbackImage(index: number): string {
  return SAMPLE_PRODUCTS[index % SAMPLE_PRODUCTS.length]?.image || SAMPLE_PRODUCTS[0].image;
}

function normalizeBackendProduct(raw: BackendProduct, index: number): Product {
  const categoryName = raw.category?.name || raw.category?.dec || 'Uncategorized';
  const description = raw.desc || raw.description || 'No product description has been added yet.';
  const image = getApiAssetUrl(raw.image) || getFallbackImage(index);
  const stock = toNumber(raw.stock ?? raw.qty, 0);

  return {
    id: String(raw.id),
    name: raw.name,
    price: toNumber(raw.price),
    category: categoryName,
    rating: toNumber(raw.rating, 4.5),
    reviewsCount: toNumber(raw.reviewsCount ?? raw.reviews_count, 0),
    image,
    secondaryImages: [image],
    description,
    features: description ? [description] : [],
    specifications: {
      Category: categoryName,
      Stock: `${stock}`,
      Status: raw.is_active === false || raw.is_active === 0 ? 'Inactive' : 'Active',
    },
    stock,
    isPopular: index < 4 || stock > 10,
  };
}

export const useProductStore = defineStore('products', () => {
  const products = ref<Product[]>(SAMPLE_PRODUCTS);
  const isLoading = ref(false);
  const error = ref<string | null>(null);
  const lastFetchedAt = ref<number | null>(null);
  const selectedCategory = ref<string>('All');
  const searchQuery = ref<string>('');
  const sortBy = ref<string>('featured');
  
  // Favorites logic stored locally
  const favorites = ref<string[]>([]);

  function init() {
    const savedFavs = localStorage.getItem('vue_shop_favs');
    if (savedFavs) {
      try {
        favorites.value = JSON.parse(savedFavs);
      } catch (e) {}
    }

    const cached = readCachedProducts();
    if (cached) {
      products.value = cached.products;
      lastFetchedAt.value = cached.cachedAt;
    }

    void refreshProducts({ force: !cached || Date.now() - cached.cachedAt > PRODUCT_CACHE_TTL_MS });
  }

  async function refreshProducts(options: { force?: boolean } = {}) {
    const cached = readCachedProducts();
    const isFresh = cached && Date.now() - cached.cachedAt < PRODUCT_CACHE_TTL_MS;

    if (!options.force && isFresh) {
      products.value = cached.products;
      lastFetchedAt.value = cached.cachedAt;
      return products.value;
    }

    isLoading.value = true;
    error.value = null;

    try {
      const backendProducts = await apiGet<BackendProduct[]>('/products');
      const normalizedProducts = backendProducts.map(normalizeBackendProduct);

      if (normalizedProducts.length > 0) {
        products.value = normalizedProducts;
        lastFetchedAt.value = Date.now();
        writeCachedProducts(normalizedProducts);
      }

      return products.value;
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Unable to load products from API';

      if (cached) {
        products.value = cached.products;
        lastFetchedAt.value = cached.cachedAt;
      }

      return products.value;
    } finally {
      isLoading.value = false;
    }
  }

  const categories = computed(() => {
    return ['All', ...new Set(products.value.map(p => p.category))];
  });

  const filteredProducts = computed(() => {
    let result = [...products.value];

    // Filter by Category
    if (selectedCategory.value !== 'All') {
      result = result.filter(p => p.category === selectedCategory.value);
    }

    // Filter by Search Query
    if (searchQuery.value.trim() !== '') {
      const query = searchQuery.value.toLowerCase().trim();
      result = result.filter(p => 
        p.name.toLowerCase().includes(query) ||
        p.description.toLowerCase().includes(query) ||
        p.category.toLowerCase().includes(query)
      );
    }

    // Sort products
    if (sortBy.value === 'price-low-high') {
      result.sort((a, b) => a.price - b.price);
    } else if (sortBy.value === 'price-high-low') {
      result.sort((a, b) => b.price - a.price);
    } else if (sortBy.value === 'rating') {
      result.sort((a, b) => b.rating - a.rating);
    } // featured = default order

    return result;
  });

  const popularProducts = computed(() => {
    return products.value.filter(p => p.isPopular);
  });

  function getProductById(id: string): Product | undefined {
    return products.value.find(p => p.id === id);
  }

  function toggleFavorite(productId: string) {
    const index = favorites.value.indexOf(productId);
    if (index === -1) {
      favorites.value.push(productId);
    } else {
      favorites.value.splice(index, 1);
    }
    localStorage.setItem('vue_shop_favs', JSON.stringify(favorites.value));
  }

  function isFavorite(productId: string): boolean {
    return favorites.value.includes(productId);
  }

  const favoriteProducts = computed(() => {
    return products.value.filter(p => favorites.value.includes(p.id));
  });

  init();

  return {
    products,
    isLoading,
    error,
    lastFetchedAt,
    categories,
    selectedCategory,
    searchQuery,
    sortBy,
    filteredProducts,
    popularProducts,
    favoriteProducts,
    isFavorite,
    getProductById,
    toggleFavorite,
    refreshProducts,
  };
});
