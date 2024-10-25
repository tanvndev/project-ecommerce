<script setup>
import { ref, onMounted } from 'vue'
import MenuItem from '../MenuItem.vue'
import { useProductCatalogueStore } from '~/stores/productCatalogue'
import _ from 'lodash'

const { $axios, $pusher } = useNuxtApp()
const authStore = useAuthStore()
const cartStore = useCartStore()
const chatStore = useChatStore()
const notificationStore = useNotificationStore()
const wishlistStore = useWishlistStore()
const productCatalogueStore = useProductCatalogueStore()
const headerMain = ref(null)
const productCatalogues = ref([])
const cartCount = computed(() => cartStore.getCartCount)
const wishlistCount = computed(() => wishlistStore.getWishlistCount)
const notifications = computed(() => notificationStore.getNotifications)
const chatCount = computed(() => chatStore.getChatCount)
const isSignedIn = computed(() => authStore.isSignedIn)
const isShowBell = ref(false)
const config = useRuntimeConfig()

let lastScrollPosition = 0

const handleScroll = () => {
  if (!headerMain.value) return

  const headerTop = document.querySelector('.header-top')
  const headerMainHeight = headerMain.value.offsetHeight
  const currentScrollPosition = window.pageYOffset
  const threshold = headerTop.offsetHeight + headerMainHeight

  const isFixed = currentScrollPosition > threshold
  headerMain.value.classList.toggle('is-fixed', isFixed)

  lastScrollPosition = currentScrollPosition
}

const debouncedHandleScroll = debounce(handleScroll, 10)

const getProductCatalogues = async () => {
  const response = await $axios.get('/products/catalogues/list')
  productCatalogueStore.setProductCatalogues(response.data.data)
  productCatalogues.value = response.data.data.filter(
    (item) => item.parent_id === null
  )
}

const listenForVoucherNotifications = async () => {
  const channel = $pusher.subscribe(`voucher-created-channel`)

  channel.bind('voucher-created-event', (data) => {
    const message = `Chúc mừng bạn vừa nhận được mã giảm giá mới: ${data.voucher.name} Hãy áp dụng ngay!`
    toast(message)
    showNotification(data.voucher.name, message)
    notificationStore.getAllNotifications()
  })
}

const getChats = async () => {
  await chatStore.getAllChats()
}

watch(notifications, (newValue) => {
  isShowBell.value = newValue.some((item) => item.read_at == null)
})

onMounted(() => {
  getProductCatalogues()
  listenForVoucherNotifications()
  setTimeout(() => {
    getChats()
  }, 4000)

  wishlistStore.getAllWishlists()
  cartStore.getAllCarts()
  window.addEventListener('scroll', debouncedHandleScroll)
})
onUnmounted(() => {
  $pusher.unsubscribe(`voucher-created-channel`)
})
</script>
<template>
  <!-- Start of Header -->
  <header class="header">
    <div class="header-top">
      <div class="container">
        <div class="header-left">
          <p class="welcome-msg">
            Chào mừng đến với Cửa hàng Wolmart hãy nhắn tin hoặc xóa nó!
          </p>
        </div>
        <div class="header-right">
          <!-- End of Dropdown Menu -->
          <span class="d-lg-show"></span>
          <div class="header-notification" v-if="isSignedIn">
            <NuxtLink to="/post/catalogue" class="d-lg-show notification-link">
              <i class="fas fa-bell fs-1" :class="{ shake: isShowBell }"></i>
              <span class="notification-text"> Thông báo </span>
            </NuxtLink>

            <div class="noti-dropdown-box" v-if="!_.isEmpty(notifications)">
              <h6>Thông báo mới nhận</h6>
              <ul>
                <li
                  v-for="notification in notifications"
                  :class="{ active: notification.read_at == null }"
                  :key="notification.id"
                >
                  <div class="noti-image">
                    <v-img
                      width="50"
                      height="50"
                      contain
                      src="https://cdn.tgdd.vn/Products/Images/54/315072/s16/tai-nghe-co-day-apple-mtjy3-thumb-13-650x650.png"
                    ></v-img>
                  </div>
                  <div class="noti-content">
                    <p class="noti-title">{{ notification?.data?.title }}</p>
                    <p class="noti-desc">
                      {{ notification?.data?.description }}
                    </p>
                  </div>
                </li>
              </ul>
              <h6 class="btn-all">Xem tất cả</h6>
            </div>
          </div>
          <NuxtLink to="/post/catalogue" class="d-lg-show">Bài viết</NuxtLink>
          <NuxtLink to="/contact" class="d-lg-show">Liên hệ</NuxtLink>
          <NuxtLink to="/user/profile" class="d-lg-show" v-if="isSignedIn">Tài khoản</NuxtLink>
          <a
            v-if="!authStore.isSignedIn"
            :href="`${config.public.VUE_APP_URL}/login`"
            class="d-lg-show login sign-in"
          >
            <i class="w-icon-account"></i>Đăng nhập</a
          >
          <span class="delimiter d-lg-show" v-if="!authStore.isSignedIn"
            >/</span
          >
          <a
            v-if="!authStore.isSignedIn"
            :href="`${config.public.VUE_APP_URL}/register`"
            class="ml-0 d-lg-show login register"
            >Đăng ký</a
          >
          <a
            href="/logout"
            @click.prevent="authStore.logout()"
            v-if="authStore.isSignedIn"
            >Đăng xuất</a
          >
        </div>
      </div>
    </div>
    <!-- End of Header Top -->

    <div class="header-main" ref="headerMain">
      <div class="header-middle">
        <div class="container">
          <div class="header-left mr-md-4">
            <a
              v-if="!authStore.isSignedIn"
              :href="`${config.public.VUE_APP_URL}/login`"
              class="mobile-menu-toggle w-icon-hamburger"
              aria-label="menu-toggle"
            >
            </a>
            <NuxtLink to="/" class="logo ml-lg-0">
              <img
                src="assets/images/logo.png"
                alt="logo"
                width="144"
                height="45"
              />
            </NuxtLink>
            <form
              method="get"
              action="#"
              class="header-search hs-expanded hs-round d-none d-md-flex input-wrapper"
            >
              <input
                type="text"
                class="form-control"
                name="search"
                id="search"
                placeholder="Search in..."
                required
              />
              <button class="btn btn-search" type="submit">
                <i class="w-icon-search"></i>
              </button>
            </form>
          </div>
          <div class="header-right ml-4">
            <div class="header-call d-xs-show d-lg-flex align-items-center">
              <a href="tel:#" class="w-icon-call"></a>
              <div class="call-info d-lg-show">
                <h4
                  class="chat font-weight-normal font-size-md text-normal ls-normal text-light mb-0"
                >
                  <a
                    href="https://portotheme.com/cdn-cgi/l/email-protection#b390"
                    class="text-capitalize"
                    >Gọi ngay</a
                  >
                  hoặc :
                </h4>
                <a href="tel:#" class="phone-number font-weight-bolder ls-50"
                  >0(800)123-456</a
                >
              </div>
            </div>

            <div class="dropdown cart-dropdown cart-offcanvas mr-0 mr-lg-2" v-if="isSignedIn">
              <div class="cart-overlay"></div>
              <NuxtLink to="/chat" class="cart-toggle label-down link">
                <i class="w-icon-chat">
                  <span class="cart-count" v-if="chatCount">{{ chatCount }}</span>
                </i>
                <span class="cart-label">Tin nhắn</span>
              </NuxtLink>
            </div>
            <div
              class="dropdown cart-dropdown cart-offcanvas mr-2 mr-lg-2"
              v-if="authStore.isSignedIn"
            >
              <div class="cart-overlay"></div>
              <NuxtLink to="/wishlist" class="cart-toggle label-down link">
                <i class="w-icon-heart">
                  <span class="cart-count" v-if="wishlistCount">{{
                    wishlistCount
                  }}</span>
                </i>
                <span class="cart-label">Ưa thích</span>
              </NuxtLink>
            </div>
            <div class="dropdown cart-dropdown cart-offcanvas mr-0 mr-lg-2">
              <div class="cart-overlay"></div>
              <NuxtLink to="/cart" class="cart-toggle label-down link">
                <i class="w-icon-cart">
                  <span class="cart-count" v-if="cartCount">{{
                    cartCount
                  }}</span>
                </i>
                <span class="cart-label">Giỏ hàng</span>
              </NuxtLink>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="header-menu">
      <div
        class="header-bottom sticky-content fix-top sticky-header has-dropdown"
      >
        <div class="container">
          <div class="inner-wrap">
            <div class="header-left">
              <nav class="main-nav">
                <ul class="menu-custom">
                  <MenuItem
                    v-for="productCatalogue in productCatalogues"
                    :key="productCatalogue.id"
                    :item="productCatalogue"
                  />
                </ul>
              </nav>
            </div>
            <div class="header-right">
              <NuxtLink to="/order-search" class="d-xl-show">
                <i class="w-icon-map-marker mr-1"></i>
                Theo dõi đơn hàng
              </NuxtLink>
              <NuxtLink to="/voucher">
                <i class="w-icon-sale"></i>
                Mã giảm giá
              </NuxtLink>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<style scoped>
.menu-custom {
  display: flex;
  list-style: none;
  padding: 0;
  margin: 0;
}

.menu-custom > .menu-item {
  margin-right: 20px;
}

.menu-custom > .menu-item:last-child {
  margin-right: 0;
}

.header-top {
  position: relative;
  z-index: 1001;
  transition: all 0.3s ease-in-out;
}

.header-main {
  position: relative;
  z-index: 1000;
  background-color: #fff;
  transition: all 0.3s ease-in-out;
}

.header-main.is-fixed {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  transition: top 0.3s ease-in-out;
}
</style>
