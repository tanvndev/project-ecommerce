<script setup>
import SIDEBAR_PROFILE from '~/static/sidebarProfile'
const route = useRoute()
const currentRoute = computed(() => route.path)
const authStore = useAuthStore()
const user = computed(() => authStore.getUser)
const handleChangeRoute = (route) => {
  if (route) {
    navigateTo(route)
  }
}
</script>
<template>
  <v-card class="mx-auto pa-2" max-width="300">
    <v-list>
      <v-list-item
        :prepend-avatar="user?.image"
        :subtitle="user?.email"
        title="user?.fullname"
      >
        <template v-slot:append>
          <v-btn icon="mdi-menu-down" size="small" variant="text"></v-btn>
        </template>
      </v-list-item>
    </v-list>

    <v-divider class="border-opacity-100"></v-divider>

    <v-list :lines="false" density="default" nav>
      <v-list-item
        v-for="(item, i) in SIDEBAR_PROFILE"
        :key="i"
        :value="item"
        color="primary"
        rounded="shaped"
        :active="item.route == currentRoute"
        @click="handleChangeRoute(item.route)"
      >
        <template v-slot:prepend>
          <v-icon :icon="item.icon"></v-icon>
        </template>

        <v-list-item-title v-text="item.text"></v-list-item-title>
      </v-list-item>
    </v-list>
  </v-card>
</template>
