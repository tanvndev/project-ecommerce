<script setup>
const props = defineProps({
  item: Object,
})
</script>
<!-- components/MenuItem.vue -->
<template>
  <li :class="['menu-item', { 'menu-item-has-children': item.childrens }]">
    <NuxtLink :to="`/category?catalogues=${item.id}`" class="ct-menu-link">
      {{ item.name }}
      <span v-if="item.childrens" class="ct-toggle-dropdown-desktop">
        <!-- v -->
        <svg
          class="ct-icon"
          width="8"
          height="8"
          viewBox="0 0 15 15"
          v-if="item.childrens?.length"
        >
          <path
            d="M2.1,3.2l5.4,5.4l5.4-5.4L15,4.3l-7.5,7.5L0,4.3L2.1,3.2z"
          ></path>
        </svg>
      </span>
    </NuxtLink>
    <ul v-if="item.childrens?.length" class="sub-menu">
      <MenuItem
        v-for="child in item.childrens"
        :key="child.id"
        :item="child"
        :parent="true"
      />
    </ul>
  </li>
</template>

<style scoped>
.menu-item {
  position: relative;
}

.ct-menu-link {
  display: block;
  padding: 10px;
  text-decoration: none;
  color: #333;
}

.ct-menu-link:first-child {
  padding-left: 0;
}

.sub-menu {
  border-radius: 2px;
  list-style: none;
  padding: 10px 0;
  margin: 5px;
  margin-top: -10px;
  display: none;
  position: absolute;
  left: 0;
  top: 55px;
  background: #fff;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  min-width: 250px;
  width: 100%;
  z-index: 2;
}

.sub-menu::before {
  content: '';
  position: absolute;
  top: -16px;
  left: 0;
  min-width: 250px;
  width: 100%;
  height: 30px;
}

.sub-menu::after {
  content: '';
  position: absolute;
  top: 0;
  right: -10px;
  width: 10px;
  height: 100%;
}

.menu-item-has-children:hover > .sub-menu {
  display: block;
}

.sub-menu .menu-item-has-children > .sub-menu {
  left: 100%;
  top: 0;
}

.sub-menu .menu-item-has-children::before {
  content: '';
  position: absolute;
  top: 0;
  right: -20px;
  padding: 10px 0;
  width: 30px;
  height: 100%;
  z-index: 10;
}

.ct-toggle-dropdown-desktop {
  margin-left: 8px;
}

.ct-icon {
  vertical-align: middle;
}

/* Thêm styles mới */
.sub-menu .menu-item {
  padding: 5px 20px;
}

.sub-menu .ct-menu-link {
  padding: 5px 0;
}
</style>
