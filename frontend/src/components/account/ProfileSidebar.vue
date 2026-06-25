<script setup lang="ts">
import type { UserProfile } from '@/stores/authStore';
import { CheckCircle, LogOut } from 'lucide-vue-next';

defineProps<{
  user: UserProfile;
  showSuccess?: boolean;
}>();

const emit = defineEmits<{
  logout: [];
}>();
</script>

<template>
  <div class="bg-white border border-slate-100 rounded-2xl p-6 text-center space-y-4 shadow-sm">
    <div
      v-if="showSuccess"
      class="bg-emerald-50 text-emerald-700 text-[11px] font-bold py-2 px-3 rounded-xl border border-emerald-100 flex items-center gap-1.5 justify-center leading-none"
    >
      <CheckCircle class="h-4.5 w-4.5 text-emerald-500" />
      Profile updated successfully!
    </div>

    <div class="space-y-3">
      <img
        :src="user.avatar"
        alt="User avatar image"
        referrerpolicy="no-referrer"
        class="h-20 w-20 rounded-full border border-slate-150 object-cover mx-auto"
      />
      <div>
        <h4 class="font-bold text-slate-900 text-base leading-tight">{{ user.name }}</h4>
        <span class="text-xs text-slate-405 font-mono">{{ user.email }}</span>
      </div>
    </div>

    <div class="pt-4 border-t border-slate-100 flex flex-col gap-2">
      <router-link
        to="/order-history"
        class="w-full py-2 bg-slate-50 hover:bg-slate-100 rounded-xl font-bold text-xs text-slate-700 block"
      >
        Review Purchases
      </router-link>

      <button
        @click="emit('logout')"
        class="w-full py-2.5 bg-rose-50/50 hover:bg-rose-100 text-rose-500 font-bold text-xs rounded-xl flex items-center justify-center gap-1.5 cursor-pointer"
      >
        <LogOut class="h-3.5 w-3.5" />
        Sign Out Session
      </button>
    </div>
  </div>
</template>
