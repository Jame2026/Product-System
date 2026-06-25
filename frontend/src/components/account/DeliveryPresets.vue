<script setup lang="ts">
import { reactive, ref, watch } from 'vue';
import type { UserProfile } from '@/stores/authStore';
import { Save } from 'lucide-vue-next';

const props = defineProps<{
  user: UserProfile;
}>();

const emit = defineEmits<{
  save: [profile: Partial<UserProfile>];
}>();

const isEditing = ref(false);
const form = reactive({
  name: '',
  email: '',
  phone: '',
  address: '',
  city: '',
  zipCode: '',
  country: '',
});

function syncForm() {
  form.name = props.user.name;
  form.email = props.user.email;
  form.phone = props.user.phone;
  form.address = props.user.address;
  form.city = props.user.city;
  form.zipCode = props.user.zipCode;
  form.country = props.user.country;
}

function discard() {
  syncForm();
  isEditing.value = false;
}

function save() {
  emit('save', { ...form });
  isEditing.value = false;
}

watch(() => props.user, syncForm, { immediate: true });
</script>

<template>
  <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm space-y-4">
    <div class="flex items-center justify-between pb-3 border-b border-slate-50 mb-2">
      <h3 class="font-sans font-bold text-sm uppercase tracking-wider text-slate-400">Delivery Presets</h3>

      <button
        @click="isEditing ? discard() : isEditing = true"
        class="px-3 py-1.5 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold rounded-lg cursor-pointer"
      >
        {{ isEditing ? 'Cancel' : 'Edit profile' }}
      </button>
    </div>

    <div v-if="!isEditing" class="grid grid-cols-1 sm:grid-cols-2 gap-4 pb-2">
      <div class="space-y-0.5 bg-slate-50/40 p-3.5 rounded-xl border border-slate-100">
        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Full Name</span>
        <span class="text-xs font-bold text-slate-800">{{ user.name }}</span>
      </div>

      <div class="space-y-0.5 bg-slate-50/40 p-3.5 rounded-xl border border-slate-100">
        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Contact Phone</span>
        <span class="text-xs font-bold text-slate-800 font-mono">{{ user.phone }}</span>
      </div>

      <div class="sm:col-span-2 space-y-1 bg-slate-50/40 p-3.5 rounded-xl border border-slate-100 leading-relaxed">
        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Default Address</span>
        <div class="text-xs font-bold text-slate-800">
          <p>{{ user.address }}</p>
          <p>{{ user.city }}, {{ user.zipCode }}</p>
          <p>{{ user.country }}</p>
        </div>
      </div>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div class="space-y-1">
        <label class="text-[10px] font-bold text-slate-400 uppercase">Contact Name</label>
        <input v-model="form.name" type="text" class="w-full bg-slate-50 border border-slate-150 rounded-xl px-3 py-2 text-xs text-slate-850" />
      </div>
      <div class="space-y-1">
        <label class="text-[10px] font-bold text-slate-400 uppercase">Email</label>
        <input v-model="form.email" type="email" class="w-full bg-slate-50 border border-slate-150 rounded-xl px-3 py-2 text-xs text-slate-850" />
      </div>
      <div class="space-y-1">
        <label class="text-[10px] font-bold text-slate-400 uppercase">Phone Number</label>
        <input v-model="form.phone" type="text" class="w-full bg-slate-50 border border-slate-150 rounded-xl px-3 py-2 text-xs text-slate-850" />
      </div>
      <div class="space-y-1">
        <label class="text-[10px] font-bold text-slate-400 uppercase">Country</label>
        <input v-model="form.country" type="text" class="w-full bg-slate-50 border border-slate-150 rounded-xl px-3 py-2 text-xs text-slate-850" />
      </div>
      <div class="sm:col-span-2 space-y-1">
        <label class="text-[10px] font-bold text-slate-400 uppercase">Default Address</label>
        <input v-model="form.address" type="text" class="w-full bg-slate-50 border border-slate-150 rounded-xl px-3 py-2 text-xs text-slate-850" />
      </div>
      <div class="space-y-1">
        <label class="text-[10px] font-bold text-slate-400 uppercase">City</label>
        <input v-model="form.city" type="text" class="w-full bg-slate-50 border border-slate-150 rounded-xl px-3 py-2 text-xs text-slate-850" />
      </div>
      <div class="space-y-1">
        <label class="text-[10px] font-bold text-slate-400 uppercase">ZIP code</label>
        <input v-model="form.zipCode" type="text" class="w-full bg-slate-50 border border-slate-150 rounded-xl px-3 py-2 text-xs text-slate-850" />
      </div>

      <div class="sm:col-span-2 pt-2 flex justify-end gap-2">
        <button @click="discard" class="px-3 py-2 border rounded-xl text-xs font-semibold hover:bg-slate-50 cursor-pointer">
          Discard
        </button>
        <button @click="save" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-slate-800 flex items-center gap-1.5 cursor-pointer">
          <Save class="h-3.5 w-3.5" />
          Save Changes
        </button>
      </div>
    </div>
  </div>
</template>
