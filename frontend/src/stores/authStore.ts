import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import { apiPost, apiPut } from '@/services/api';

export interface UserProfile {
  id?: number | string;
  name: string;
  email: string;
  phone: string;
  address: string;
  city: string;
  zipCode: string;
  country: string;
  cardName: string;
  cardNumber: string;
  cardExpiry: string;
  cardCvv: string;
  avatar: string;
}

interface BackendUser {
  id?: number | string;
  name: string;
  email: string;
  avatar?: string | null;
}

interface AuthResponse {
  success: boolean;
  message?: string;
  token: string;
  user: BackendUser;
}

interface AuthCache {
  token: string;
  user: UserProfile;
}

const AUTH_CACHE_KEY = 'vue_shop_auth';

const DEFAULT_PROFILE: UserProfile = {
  name: '',
  email: '',
  phone: '+1 (555) 342-9980',
  address: '2486 Broad Street',
  city: 'San Francisco',
  zipCode: '94107',
  country: 'United States',
  cardName: 'CUSTOMER',
  cardNumber: '**** **** **** 4242',
  cardExpiry: '12/28',
  cardCvv: '***',
  avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=200',
};

function mapBackendUser(user: BackendUser, current?: UserProfile | null): UserProfile {
  return {
    ...DEFAULT_PROFILE,
    ...(current || {}),
    id: user.id,
    name: user.name,
    email: user.email,
    cardName: user.name.toUpperCase(),
    avatar: user.avatar || current?.avatar || DEFAULT_PROFILE.avatar,
  };
}

export const useAuthStore = defineStore('auth', () => {
  const user = ref<UserProfile | null>(null);
  const token = ref<string | null>(null);
  const isLoading = ref(false);
  const error = ref<string | null>(null);

  const isAuthenticated = computed(() => Boolean(token.value && user.value));

  function init() {
    const saved = localStorage.getItem(AUTH_CACHE_KEY);
    if (!saved) return;

    try {
      const parsed = JSON.parse(saved) as AuthCache;
      token.value = parsed.token || null;
      user.value = parsed.user || null;
    } catch (e) {
      localStorage.removeItem(AUTH_CACHE_KEY);
    }
  }

  function save() {
    if (!token.value || !user.value) {
      localStorage.removeItem(AUTH_CACHE_KEY);
      return;
    }

    localStorage.setItem(AUTH_CACHE_KEY, JSON.stringify({
      token: token.value,
      user: user.value,
    }));
  }

  async function login(email: string, password: string) {
    isLoading.value = true;
    error.value = null;

    try {
      const response = await apiPost<AuthResponse>('/login', { email, password });
      token.value = response.token;
      user.value = mapBackendUser(response.user, user.value);
      save();
      return user.value;
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Unable to sign in';
      throw err;
    } finally {
      isLoading.value = false;
    }
  }

  async function register(name: string, email: string, password: string, passwordConfirmation: string) {
    isLoading.value = true;
    error.value = null;

    try {
      const response = await apiPost<AuthResponse>('/register', {
        name,
        email,
        password,
        password_confirmation: passwordConfirmation,
      });

      token.value = response.token;
      user.value = mapBackendUser(response.user, user.value);
      save();
      return user.value;
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Unable to create account';
      throw err;
    } finally {
      isLoading.value = false;
    }
  }

  async function logout() {
    const currentToken = token.value;

    token.value = null;
    user.value = null;
    save();

    if (!currentToken) return;

    try {
      await apiPost('/logout', undefined, {
        headers: {
          Authorization: `Bearer ${currentToken}`,
        },
      });
    } catch (e) {
      // Local logout should still complete even if the token is already expired.
    }
  }

  async function updateProfile(newData: Partial<UserProfile>) {
    if (!user.value) return;

    user.value = { ...user.value, ...newData };
    save();

    if (!token.value) return;

    try {
      const response = await apiPut<{ success: boolean; user: BackendUser }>('/profile', {
        name: user.value.name,
        email: user.value.email,
      }, {
        headers: {
          Authorization: `Bearer ${token.value}`,
        },
      });

      user.value = mapBackendUser(response.user, user.value);
      save();
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Unable to update profile';
      throw err;
    }
  }

  init();

  return {
    user,
    token,
    isLoading,
    error,
    isAuthenticated,
    login,
    register,
    logout,
    updateProfile,
  };
});
