<template>
  <header class="box-shadow header-wrap z-[200] border-b bg-white">
    <div class="flex w-[100%] items-center justify-between px-10">
      <div class="flex items-center">
        <div class="leading-none">
          <button type="button" class="leading-none" @click="handleSidebar">
            <i
              class="fas fa-outdent text-[22px] text-gray-500 hover:text-gray-700"
              v-if="!isShowSidebar"
            ></i>
            <i class="fas fa-indent text-[22px] text-gray-500 hover:text-gray-700" v-else></i>
          </button>
        </div>
        <div class="input-search ml-7">
          <a-input
            placeholder="Tìm kiêm tại đây"
            size="large"
            class="w-[350px] border-none bg-[#f3f3f9] focus:shadow-none"
            allowClear
          >
            <template #prefix>
              <i class="fas fa-search pr-1 text-gray-500"></i>
            </template>
          </a-input>
        </div>
      </div>
      <ul class="mb-0 flex flex-shrink-0 items-center justify-end space-x-8">
        <li class="relative inline-block text-left">
          <button @click="handleFullscreen" type="button">
            <i
              class="fal fa-compress-wide text-[22px] text-gray-500 hover:text-gray-600"
              v-if="isFullscreen"
            ></i>
            <i class="fal fa-expand text-[22px] text-gray-500 hover:text-gray-600" v-else></i>
          </button>
        </li>
        <li class="relative inline-block text-left hidden">
          <a-dropdown>
            <button class="relative rounded-md align-middle focus:outline-none">
              <i class="fal fa-bell text-[22px] text-gray-500"></i>
              <span
                class="pulse absolute left-[22px] top-0 z-10 inline-flex h-5 w-5 -translate-x-1/2 -translate-y-1/2 transform items-center justify-center rounded-full bg-red-500 p-1 text-xs font-medium leading-none text-red-100"
                >5</span
              >
            </button>
            <template #overlay>
              <div class="w-[300px] rounded-[4px] bg-white shadow-lg md:w-[400px]">
                <div class="px-5">
                  <div class="flex items-center justify-between pb-2">
                    <h2 class="mb-0 text-lg">Thông báo</h2>
                    <a-badge
                      :number-style="{ backgroundColor: '#52c41a' }"
                      :count="'5 Thông báo'"
                    />
                  </div>
                  <ul class="border-t pt-3">
                    <li class="border-b pb-1 pt-1 last:border-b-0">
                      <h4>Bạn nhận được 1 đơn hàng từ hệ thống</h4>
                      <p class="mb-1 text-[13px]">Mã đơn: OR98723982323</p>
                      <p class="mb-1 text-[13px]">Địa chỉ: Dong anh ha noi</p>
                    </li>
                    <li class="border-b pb-1 pt-1 last:border-b-0">
                      <h4>Bạn nhận được 1 đơn hàng từ hệ thống</h4>
                      <p class="mb-1 text-[13px]">Mã đơn: OR98723982323</p>
                      <p class="mb-1 text-[13px]">Địa chỉ: Dong anh ha noi</p>
                    </li>
                    <li class="border-b pb-1 pt-1 last:border-b-0">
                      <h4>Bạn nhận được 1 đơn hàng từ hệ thống</h4>
                      <p class="mb-1 text-[13px]">Mã đơn: OR98723982323</p>
                      <p class="mb-1 text-[13px]">Địa chỉ: Dong anh ha noi</p>
                    </li>
                  </ul>
                </div>
              </div>
            </template>
          </a-dropdown>
        </li>

        <li class="relative inline-block pl-0 text-left">
          <a-dropdown>
            <div class="flex cursor-pointer items-center gap-x-2 bg-[#f3f3f9] px-8 py-4">
              <button
                class="mx-auto h-9 w-9 overflow-hidden rounded-full border-2 font-medium text-white focus:outline-none"
              >
                <a-avatar
                  class="h-full w-full object-cover"
                  :src="
                    (user && resizeImage(user.image, 100, 100)) || 'https://i.ibb.co/WpM5yZZ/9.png'
                  "
                  loading="lazy"
                ></a-avatar>
              </button>
              <div class="flex flex-col">
                <span class="mb-1 text-[13px] font-bold leading-none text-gray-700"
                  >{{ user && user.fullname }}
                </span>
                <span class="text-[12px] leading-none text-gray-500"
                  >{{ user && user.catalogue_name }}
                </span>
              </div>
            </div>
            <template #overlay>
              <a-menu>
                <a-menu-item>
                  <RouterLink :to="{ name: 'dashboard' }">
                    <i class="far fa-home mr-2"></i>
                    <span>Dashboard</span>
                  </RouterLink>
                </a-menu-item>
                <a-menu-item>
                  <RouterLink :to="{ name: 'user.update', params: { id: user?.id } }" v-if="user">
                    <i class="far fa-cog mr-2"></i>
                    <span>Chỉnh sửa hồ sơ</span>
                  </RouterLink>
                </a-menu-item>
                <a-menu-item @click="store.dispatch('authStore/logout')">
                  <i class="far fa-sign-out mr-2"></i>
                  <span>Đăng xuất</span>
                </a-menu-item>
              </a-menu>
            </template>
          </a-dropdown>
        </li>
      </ul>
    </div>
  </header>
</template>

<script setup>
import { resizeImage } from '@/utils/helpers';
import screenfull from 'screenfull';
import { computed, ref } from 'vue';
import { RouterLink } from 'vue-router';
import { useStore } from 'vuex';

const isFullscreen = ref(false);
const store = useStore();
const user = computed(() => store.getters['authStore/getUser']);
const isShowSidebar = computed(() => store.getters['sidebarStore/getIsShow']);

const handleFullscreen = () => {
  isFullscreen.value = !isFullscreen.value;
  screenfull.toggle();
};

const handleSidebar = () => {
  store.commit('sidebarStore/setIsShow', !isShowSidebar.value);
};
</script>
<style scoped>
.box-shadow {
  box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
}
</style>
