<script setup>
const posts = ref([])

const loadingStore = useLoadingStore()
const { $axios } = useNuxtApp()
const page = ref(1)
const getAllPost = async () => {
  try {
    loadingStore.setLoading(true)
    const response = await $axios.get('/posts/all')

    posts.value = response.data
  } catch (error) {
  } finally {
    loadingStore.setLoading(false)
  }
}

onMounted(() => {
  getAllPost()
})
</script>
<template>
  <div class="page-content mt-10">
    <div class="container">
      <ul class="nav-filters filter-underline blog-filters mb-4">
        <li>
          <a href="#" class="nav-filter active">
            Tất cả bài viết
            <span>{{ posts?.length }}</span>
          </a>
        </li>
      </ul>

      <div class="row grid cols-lg-3 cols-md-2 mb-2">
        <div class="grid-item" v-for="post in posts" :key="post.id">
          <article class="post post-mask overlay-zoom br-sm">
            <figure class="post-media">
              <NuxtLink :to="`/post/${post.canonical}`">
                <v-img
                  :src="resizeImage(post.image, 600, 240)"
                  width="600"
                  height="240"
                  :alt="post.name"
                />
              </NuxtLink>
            </figure>
            <div class="post-details">
              <div class="post-details-visible">
                <h4 class="post-title text-white">
                  <NuxtLink :to="`/post/${post.canonical}`">{{
                    post.name
                  }}</NuxtLink>
                </h4>
              </div>
              <div class="post-meta">
                Tác giả
                <a href="#" class="post-author">{{ post.user_name }}</a> -
                <a href="#" class="post-date">{{ post.created_at }}</a>
              </div>
            </div>
          </article>
        </div>
      </div>
      <div class="my-4" v-if="page > 1">
        <v-pagination
          v-model="page"
          :length="20"
          rounded="circle"
        ></v-pagination>
      </div>
    </div>
  </div>
</template>
