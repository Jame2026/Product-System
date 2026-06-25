<script setup lang="ts">
import type { UserProfile } from '@/stores/authStore';

defineProps<{
  user: UserProfile;
}>();
</script>

<template>
  <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm space-y-4">
    <h3 class="font-sans font-bold text-sm uppercase tracking-wider text-slate-400 border-b border-slate-50 pb-3 mb-2">Saved Payment Cards</h3>

    <div class="flex items-center gap-4 bg-slate-50 p-4 rounded-xl border border-slate-150/40 font-sans">
      <span class="p-2.5 bg-slate-900 text-white rounded-lg text-xs font-bold leading-none uppercase font-mono tracking-wider">Visa</span>
      <div class="flex-1 text-xs">
        <p class="font-bold text-slate-850">{{ user.cardName }}</p>
        <p class="text-slate-450 mt-0.5">ending in {{ user.cardNumber.slice(-4) }} - Expires {{ user.cardExpiry }}</p>
      </div>
      <span class="inline-block px-2.5 py-1 bg-emerald-50 border border-emerald-100 text-emerald-600 text-[10px] font-bold rounded-lg uppercase">Primary</span>
    </div>

    <p class="text-[10.5px] text-slate-450 leading-relaxed font-semibold">
      Credit cards are processed using sandboxed token encryption. We do not store secondary numbers.
    </p>
  </div>
</template>
