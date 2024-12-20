<template>
  <div v-if="!(systemConfigs?.snow_effect == 'off' || systemConfigs?.snow_effect == null)">
    <SnowEffect />
  </div>
  <!-- Loading status -->
  <!-- <LoadingIndicator v-if="isLoading" /> -->
  <div class="flex h-screen">
    <!-- Sidebar -->
    <SidebarComponent />
    <div class="flex w-full flex-1 flex-col">
      <!-- Header -->
      <HeaderComponent />
      <!-- Main -->
      <main class="h-full overflow-y-auto bg-[#f3f3f9]">
        <slot name="template"></slot>
      </main>
    </div>
  </div>
</template>

<script setup>
import { HeaderComponent, LoadingIndicator, SidebarComponent } from '@/components/backend';
import SnowEffect from '@/components/backend/includes/SnowCanvas.vue';
import axios from '@/configs/axios';
import { AuthService } from '@/services';
import { useAntToast } from '@/utils/antToast';
import Cookies from 'js-cookie';
import { computed, onMounted, ref, watchEffect } from 'vue';
import { useStore } from 'vuex';


const { showMessage } = useAntToast();
const store = useStore();
const isShowToast = computed(() => store.getters['antStore/getIsShow']);
const isLoading = computed(() => store.getters['loadingStore/getIsLoading']);
const token = Cookies.get('token') ?? null;
const systemConfigs = ref({});

const getSystemConfigs = async () => {
  try {
    const response = await axios.get('/system-configs');
    systemConfigs.value = response.data;
  } catch (error) {
    console.error('Error fetching system configs:', error);
  }
};

const setUserCurrent = async () => {
  if (token) {
    const user = await AuthService.me();
    store.commit('authStore/setUser', user.data);
    store.commit('authStore/setIsLoggedIn', token);
  }
};

watchEffect(() => {
  if (isShowToast.value) {
    const type = store.getters['antStore/getType'];
    const message = store.getters['antStore/getMessage'];
    showMessage(type, message);
    store.dispatch('antStore/removeMessage');
  }
});

onMounted(() => {
  setUserCurrent();
  getSystemConfigs();
});
</script>

<style scoped>
::-webkit-scrollbar {
  width: 5px;
}
::-webkit-scrollbar-track {
  background: #f1f1f1;
}
::-webkit-scrollbar-thumb {
  background: #acacac;
  border-radius: 10px;
}
::-webkit-scrollbar-thumb:hover {
  background: #797979;
}
</style>
