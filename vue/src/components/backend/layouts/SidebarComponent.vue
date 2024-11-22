<template>
  <aside
    class="z-30 w-64 flex-shrink-0 overflow-y-auto bg-[#001529] shadow-lg"
    :class="{ hidden: isShowSidebar }"
  >
    <div class="h-full">
      <RouterLink
        :to="{ name: 'dashboard' }"
        class="flex items-center justify-center border-b border-gray-700"
      >
        <div class="flex items-center gap-3 py-[18px]">
          <div>
            <img
              class="h-8 w-auto"
              src="https://adminlte.io/themes/v3/dist/img/AdminLTELogo.png"
              alt="logo"
            />
          </div>
          <div class="text-[16px] font-bold uppercase text-white">Trang quản trị</div>
        </div>
      </RouterLink>

      <a-menu
        class="mt-5"
        v-model:selectedKeys="selectedKeys"
        v-model:openKeys="openKeys"
        mode="inline"
        theme="dark"
      >
        <a-menu-item v-for="item in menuItems" :key="item.route">
          <RouterLink class="inline-flex cursor-pointer items-center" :to="{ name: item.route }">
            <i :class="item.icon" class="mr-3"></i>
            <span class="ml-1">{{ item.name }}</span>
          </RouterLink>
        </a-menu-item>

        <a-sub-menu v-for="item in subMenuItems" :key="item.id">
          <template #title>
            <span>
              <i :class="item.icon" class="mr-3"></i>
              <span class="capitalize">{{ item.name }}</span>
            </span>
          </template>
          <a-menu-item v-for="itemSub in item.subMenu" :key="itemSub.route">
            <RouterLink
              class="inline-flex cursor-pointer items-center"
              :to="{ name: itemSub.route }"
            >
              <i class="far fa-dot-circle text-[6px]"></i>
              <span class="ml-1 capitalize">{{ itemSub.name }}</span>
            </RouterLink>
          </a-menu-item>
        </a-sub-menu>

        <a-menu-item key="setting_sidebar">
          <RouterLink class="inline-flex cursor-pointer items-center" :to="{ name: 'setting' }">
            <i class="fas fa-cog mr-3 text-[16px]"></i>
            <span class="ml-1">Cấu hình</span>
          </RouterLink>
        </a-menu-item>
      </a-menu>
    </div>
  </aside>
</template>

<script setup>
import sidebar from '@/static/sidebar';
import _ from 'lodash';
import { computed, ref, watch } from 'vue';
import { RouterLink, useRoute } from 'vue-router';
import { useStore } from 'vuex';
const store = useStore();
const isShowSidebar = computed(() => store.getters['sidebarStore/getIsShow']);

const selectedKeys = ref([]);
const openKeys = ref([]);

const route = useRoute();

const menuItems = computed(() => {
  return sidebar.filter((item) => !item.subMenu || item.subMenu.length === 0);
});

const subMenuItems = computed(() => {
  return sidebar.filter((item) => item.subMenu && item.subMenu.length > 0);
});

const generateGeneralPattern = (route) => {
  if (!route) return '';
  const parts = route.split('.');

  if (parts.length > 1) {
    parts.pop();
  }

  return parts.join('.');
};

const isActiveRoute = (route, currentRoute) => {
  return generateGeneralPattern(route) === generateGeneralPattern(currentRoute);
};

const findSidebarItem = (currentRouteName, sidebar) => {
  return sidebar.find(
    (item) =>
      (item.subMenu.length && item.subMenu.some((sub) => sub.route === currentRouteName)) ||
      (item.route && item.route === currentRouteName) ||
      (_.isArray(item.active) && item.active.includes(currentRouteName.split('.')[0]))
  );
};

const updateMenuState = () => {
  const currentRouteName = route.name;
  if (!currentRouteName) return;

  const foundItem = findSidebarItem(currentRouteName, sidebar);
  if (!foundItem) {
    openKeys.value = [];
    selectedKeys.value = [];
    return;
  }

  openKeys.value = [foundItem.id];

  const subItem = foundItem.subMenu.find(
    (sub) => sub.route === currentRouteName || isActiveRoute(sub.route, currentRouteName)
  );

  selectedKeys.value = subItem ? [subItem.route] : [foundItem.route];
};

watch(
  () => route.name,
  () => {
    updateMenuState();
  },
  { immediate: true }
);
</script>

<style scoped>
.line-left {
  display: none;
}

.active .line-left {
  display: block;
}

.active .link-item {
  color: #10b981;
}
</style>
