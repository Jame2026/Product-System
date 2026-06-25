<script setup lang="ts">
import { ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import AuthCard from '@/components/account/AuthCard.vue';
import BillingCard from '@/components/account/BillingCard.vue';
import DeliveryPresets from '@/components/account/DeliveryPresets.vue';
import ProfileSidebar from '@/components/account/ProfileSidebar.vue';
import { useAuthStore, type UserProfile } from '@/stores/authStore';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const showUpdateSuccess = ref(false);

function redirectAfterAuth() {
  const redirectPath = route.query.redirect as string | undefined;
  router.push(redirectPath || '/');
}

async function handleLogin(email: string, password: string) {
  try {
    await authStore.login(email, password);
    redirectAfterAuth();
  } catch (e) {
    // Store exposes the error for AuthCard.
  }
}

async function handleRegister(name: string, email: string, password: string, passwordConfirmation: string) {
  try {
    await authStore.register(name, email, password, passwordConfirmation);
    redirectAfterAuth();
  } catch (e) {
    // Store exposes the error for AuthCard.
  }
}

async function handleSaveProfile(profile: Partial<UserProfile>) {
  try {
    await authStore.updateProfile(profile);
    showUpdateSuccess.value = true;
    setTimeout(() => {
      showUpdateSuccess.value = false;
    }, 3500);
  } catch (e) {
    // Keep the current form visible; the store keeps the latest error.
  }
}

async function handleLogout() {
  await authStore.logout();
  router.push('/');
}
</script>

<template>
  <div class="space-y-6 pb-16 animate-fade-in" id="profile-view-container">
    <div>
      <h2 class="font-sans font-extrabold text-2xl tracking-tight text-slate-900">My Account Dashboard</h2>
      <p class="text-xs text-slate-500 font-semibold mt-1">Manage delivery address presets, billing preferences, and customer session.</p>
    </div>

    <AuthCard
      v-if="!authStore.isAuthenticated"
      :is-loading="authStore.isLoading"
      :error="authStore.error"
      @login="handleLogin"
      @register="handleRegister"
    />

    <div v-else-if="authStore.user" class="grid grid-cols-1 md:grid-cols-3 gap-8 items-start">
      <ProfileSidebar
        :user="authStore.user"
        :show-success="showUpdateSuccess"
        @logout="handleLogout"
      />

      <div class="md:col-span-2 space-y-6">
        <p v-if="authStore.error" class="text-[11px] font-semibold text-rose-500 bg-rose-50 border border-rose-100 rounded-xl px-3 py-2">
          {{ authStore.error }}
        </p>
        <DeliveryPresets :user="authStore.user" @save="handleSaveProfile" />
        <BillingCard :user="authStore.user" />
      </div>
    </div>
  </div>
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
