<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import AppFooter from '~/components/includes/AppFooter.vue'
import AppHeader from '~/components/includes/AppHeader.vue'
import Spinner from './components/includes/Spinner.vue'
import Cookies from 'js-cookie'

const authStore = useAuthStore()
const notificationStore = useNotificationStore()
const user = computed(() => authStore.getUser)
const token = computed(() => authStore.getToken ?? null)
const route = useRoute()
const { $authService, $pusher } = useNuxtApp()

const showScrollTop = ref(false)
const progressIndicator = ref(null)
const scrollTopRef = ref(null)

const setSessionId = () => {
  if (authStore.isSignedIn) return
  if (Cookies.get('session_id')) return

  const session_id = generateUUID()

  Cookies.set('session_id', session_id, {
    expires: parseInt(process.env.SESSION_ID_EXPIRES, 10),
  })
}

const setTokenAndSetCurrentUser = async () => {
  if (token.value) {
    const user = await $authService.me()
    authStore.setUser(user.data)
  }
}

const checkScroll = () => {
  const scrollTop = window.scrollY
  const documentHeight = document.documentElement.scrollHeight
  const windowHeight = window.innerHeight
  const scrollPercent = (scrollTop / (documentHeight - windowHeight)) * 100

  showScrollTop.value = scrollTop > 200

  if (progressIndicator.value) {
    const circumference = 2 * Math.PI * progressIndicator.value.r.baseVal.value
    const offset = circumference - (scrollPercent / 100) * circumference

    progressIndicator.value.style.strokeDasharray = `${circumference}px ${circumference}px`
    progressIndicator.value.style.strokeDashoffset = offset
  }
}

const scrollToTop = (event) => {
  event.preventDefault()
  window.scrollTo({
    top: 0,
    behavior: 'smooth',
  })
}

const listenForNotifications = async () => {
  const channel = $pusher.subscribe(`private-App.Models.User.${user.value?.id}`)

  channel.bind(
    'Illuminate\\Notifications\\Events\\BroadcastNotificationCreated',
    (data) => {
      console.log(data.title, data.message)
      toast(data.message)
      showNotification(data.title, data.message)
    }
  )
}

const debounceGetNotifications = debounce(async () => {
  if (authStore.isSignedIn) {
    await notificationStore.getAllNotifications()
    // await listenForNotifications()
  }
}, 2000)

watch(
  () => route.path,
  async () => {
    await setTokenAndSetCurrentUser()
    debounceGetNotifications()
  },
  { immediate: true }
)

onMounted(() => {
  setSessionId()
  window.addEventListener('scroll', checkScroll)
})

onUnmounted(() => {
  window.removeEventListener('scroll', checkScroll)
})
</script>
<template>
  <Spinner />
  <div class="page-wrapper">
    <!-- <NuxtLoadingBar :duration="1000" /> -->

    <h1 class="d-none">Wolmart - Responsive Marketplace HTML Template</h1>
    <AppHeader />

    <main class="main" style="min-height: 100vh">
      <NuxtPage />
    </main>

    <AppFooter />
  </div>

  <!-- Start of Scroll Top -->
  <a
    ref="scrollTopRef"
    id="scroll-top"
    class="scroll-top"
    href="#top"
    title="Top"
    role="button"
    @click="scrollToTop"
    :class="{ show: showScrollTop }"
  >
    <i class="w-icon-angle-up"></i>
    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 70 70">
      <circle
        ref="progressIndicator"
        fill="transparent"
        stroke="#2c67e7"
        stroke-width="4"
        stroke-linecap="round"
        cx="35"
        cy="35"
        r="34"
      ></circle>
    </svg>
  </a>
  <!-- End of Scroll Top -->
</template>

<style>
.layout-enter-active,
.layout-leave-active {
  transition: all 0.4s;
}
.layout-enter-from,
.layout-leave-to {
  filter: grayscale(1);
}
</style>
