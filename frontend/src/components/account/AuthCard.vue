<script setup lang="ts">
import { computed, ref } from 'vue';
import { Key, Mail, User, Lock, UserPlus, LogIn } from 'lucide-vue-next';

const props = defineProps<{
  isLoading?: boolean;
  error?: string | null;
}>();

const emit = defineEmits<{
  login: [email: string, password: string];
  register: [name: string, email: string, password: string, passwordConfirmation: string];
}>();

const mode = ref<'login' | 'register'>('login');
const name = ref('');
const email = ref('');
const password = ref('');
const passwordConfirmation = ref('');
const localError = ref('');

const title = computed(() => mode.value === 'login' ? 'Customer Login' : 'Create Customer Account');
const subtitle = computed(() => mode.value === 'login'
  ? 'Sign in with your customer account to continue checkout and view saved orders.'
  : 'Register a customer account using your Laravel backend API.');
const visibleError = computed(() => props.error || localError.value);

function resetFeedback() {
  localError.value = '';
}

function validate() {
  resetFeedback();

  if (!email.value.trim() || !email.value.includes('@')) {
    localError.value = 'Please enter a valid email address.';
    return false;
  }

  if (password.value.length < 8) {
    localError.value = 'Password must be at least 8 characters.';
    return false;
  }

  if (mode.value === 'register') {
    if (!name.value.trim()) {
      localError.value = 'Please enter your full name.';
      return false;
    }

    if (password.value !== passwordConfirmation.value) {
      localError.value = 'Password confirmation does not match.';
      return false;
    }
  }

  return true;
}

function submit() {
  if (!validate()) return;

  if (mode.value === 'login') {
    emit('login', email.value.trim(), password.value);
    return;
  }

  emit('register', name.value.trim(), email.value.trim(), password.value, passwordConfirmation.value);
}
</script>

<template>
  <div class="max-w-md mx-auto bg-white border border-slate-100 rounded-2xl p-6 sm:p-8 shadow-md space-y-6 my-8">
    <div class="text-center space-y-2">
      <span class="inline-block p-3.5 bg-slate-900 text-white rounded-2xl">
        <Key class="h-6 w-6" />
      </span>
      <h3 class="font-sans font-extrabold text-lg sm:text-xl text-slate-900">{{ title }}</h3>
      <p class="text-xs text-slate-400 font-medium leading-relaxed max-w-xs mx-auto">{{ subtitle }}</p>
    </div>

    <div class="grid grid-cols-2 gap-1 bg-slate-50 border border-slate-100 rounded-xl p-1">
      <button
        type="button"
        @click="mode = 'login'; resetFeedback()"
        class="py-2 rounded-lg text-xs font-bold flex items-center justify-center gap-1.5"
        :class="mode === 'login' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500'"
      >
        <LogIn class="h-3.5 w-3.5" />
        Login
      </button>
      <button
        type="button"
        @click="mode = 'register'; resetFeedback()"
        class="py-2 rounded-lg text-xs font-bold flex items-center justify-center gap-1.5"
        :class="mode === 'register' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500'"
      >
        <UserPlus class="h-3.5 w-3.5" />
        Register
      </button>
    </div>

    <form @submit.prevent="submit" class="space-y-4 font-sans">
      <div v-if="mode === 'register'" class="space-y-1.5">
        <label class="text-xs font-bold text-slate-500">Full Name</label>
        <div class="relative">
          <input
            v-model="name"
            type="text"
            placeholder="e.g. Alex Mercer"
            class="w-full bg-slate-50 border border-slate-150 rounded-xl pl-10 pr-3.5 py-2.5 text-xs text-slate-800 placeholder-slate-400 focus:outline-none"
          />
          <User class="absolute left-3.5 top-3 h-4.5 w-4.5 text-slate-400" />
        </div>
      </div>

      <div class="space-y-1.5">
        <label class="text-xs font-bold text-slate-500 block">Email Address</label>
        <div class="relative">
          <input
            v-model="email"
            type="email"
            placeholder="e.g. customer@example.com"
            class="w-full bg-slate-50 border border-slate-150 rounded-xl pl-10 pr-3.5 py-2.5 text-xs text-slate-800 placeholder-slate-400 focus:outline-none"
          />
          <Mail class="absolute left-3.5 top-3 h-4.5 w-4.5 text-slate-400" />
        </div>
      </div>

      <div class="space-y-1.5">
        <label class="text-xs font-bold text-slate-500 block">Password</label>
        <div class="relative">
          <input
            v-model="password"
            type="password"
            placeholder="Minimum 8 characters"
            class="w-full bg-slate-50 border border-slate-150 rounded-xl pl-10 pr-3.5 py-2.5 text-xs text-slate-800 placeholder-slate-400 focus:outline-none"
          />
          <Lock class="absolute left-3.5 top-3 h-4.5 w-4.5 text-slate-400" />
        </div>
      </div>

      <div v-if="mode === 'register'" class="space-y-1.5">
        <label class="text-xs font-bold text-slate-500 block">Confirm Password</label>
        <div class="relative">
          <input
            v-model="passwordConfirmation"
            type="password"
            placeholder="Repeat password"
            class="w-full bg-slate-50 border border-slate-150 rounded-xl pl-10 pr-3.5 py-2.5 text-xs text-slate-800 placeholder-slate-400 focus:outline-none"
          />
          <Lock class="absolute left-3.5 top-3 h-4.5 w-4.5 text-slate-400" />
        </div>
      </div>

      <p v-if="visibleError" class="text-[11px] font-semibold text-rose-500 leading-relaxed">
        {{ visibleError }}
      </p>

      <button
        type="submit"
        :disabled="isLoading"
        class="w-full bg-slate-900 text-white font-bold py-3.5 rounded-xl text-xs hover:bg-slate-800 transition-colors uppercase tracking-wider cursor-pointer shadow disabled:opacity-60 disabled:cursor-wait"
      >
        {{ isLoading ? 'Please wait...' : mode === 'login' ? 'Login' : 'Create Account' }}
      </button>
    </form>
  </div>
</template>
