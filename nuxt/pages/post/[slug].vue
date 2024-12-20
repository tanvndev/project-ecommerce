<script setup>
const date = ref(new Date())
const post = ref(null)
const loadingStore = useLoadingStore()
const { $axios } = useNuxtApp()

const route = useRoute()
const { slug } = route.params

const getPost = async () => {
  try {
    loadingStore.setLoading(true)
    const response = await $axios.get(`/posts/${slug}/detail`)

    post.value = response.data
  } catch (error) {
  } finally {
    loadingStore.setLoading(false)
  }
}

watch(
  post,
  (newVal) => {
    useSeoMeta({
      title: newVal?.meta_title,
      ogTitle: newVal?.meta_title,
      description: newVal?.meta_description,
      ogDescription: newVal?.meta_description,
    })
  },
  { immediate: true, deep: true }
)
onMounted(() => {
  getPost()
})
</script>
<template>
  <div class="page-content mb-8 mt-5" v-if="post">
    <div class="container">
      <div class="row gutter-lg">
        <div class="main-content post-single-content">
          <div class="post post-grid post-single">
            <figure class="post-media br-sm">
              <v-img
                :src="resizeImage(post?.image, 930, 500)"
                alt="Blog"
                width="930"
                height="500"
              />
            </figure>
            <div class="post-details">
              <div class="post-meta">
                Tác giả
                <a href="#" class="post-author">{{ post?.user_name }}</a> -
                <a href="#" class="post-date">{{ post?.created_at }}</a>
              </div>
              <h2 class="post-title">
                <a href="#">{{ post?.name }}</a>
              </h2>
              <div class="post-content" v-html="post?.content"></div>
            </div>
          </div>

          <div class="social-links-wrapper mb-7">
            <div class="social-links">
              <div
                class="social-icons social-no-color border-thin"
                style="border-color: transparent !important"
              >
                <a
                  href="https://www.facebook.com/"
                  class="social-icon social-facebook w-icon-facebook"
                  data-platform="facebook"
                  @click.prevent="handleSocialIconClick"
                ></a>
                <a
                  href="https://twitter.com/"
                  class="social-icon social-twitter w-icon-twitter"
                  data-platform="twitter"
                  @click.prevent="handleSocialIconClick"
                ></a>
                <a
                  href="https://www.pinterest.com/"
                  class="social-icon social-pinterest fab fa-pinterest-p"
                  data-platform="pinterest"
                  @click.prevent="handleSocialIconClick"
                ></a>
                <a
                  href="https://www.whatsapp.com/"
                  class="social-icon social-whatsapp fab fa-whatsapp"
                  data-platform="whatsapp"
                  @click.prevent="handleSocialIconClick"
                ></a>
                <a
                  href="https://www.linkedin.com/"
                  class="social-icon social-youtube fab fa-linkedin-in"
                  data-platform="linkedin"
                  @click.prevent="handleSocialIconClick"
                ></a>
              </div>
            </div>
          </div>
          <!-- End Social Links -->
          <div class="post-author-detail" style="border-radius: 4px">
            <figure class="author-media mr-4">
              <img src="" :alt="post?.user_name" width="105" height="105" />
            </figure>
            <div class="author-details">
              <div class="author-name-wrapper flex-wrap mb-2">
                <h4 class="author-name font-weight-bold mb-2 pr-4 mr-auto">
                  {{ post?.user_name }}
                  <span class="font-weight-normal text-default">(Tác giả)</span>
                </h4>
              </div>
              <p class="mb-0">
                Vestibulum volutpat, lacus a ultrices sagittis, mi neque
                euismoder eu pulvinar nunc sapien ornare nisl. Ped earcudaap
                ibuseu, fermentum et, dapibus sed, urna. Morbi interdum mollis
                sapien.
              </p>
            </div>
          </div>
        </div>
        <!-- End of Main Content -->
        <aside
          class="sidebar right-sidebar blog-sidebar sidebar-fixed sticky-sidebar-wrapper"
        >
          <div class="sidebar-overlay">
            <a href="#" class="sidebar-close">
              <i class="close-icon"></i>
            </a>
          </div>
          <a href="#" class="sidebar-toggle">
            <i class="fas fa-chevron-left"></i>
          </a>
          <div class="sidebar-content">
            <div class="sticky-sidebar">
              <div class="widget widget-categories d-none">
                <h3 class="widget-title bb-no mb-0">Nhóm bài viết</h3>
                <ul class="widget-body filter-items search-ul">
                  <li><a href="blog.html">Clothes</a></li>
                  <li><a href="blog.html">Entertainment</a></li>
                  <li><a href="blog.html">Fashion</a></li>
                  <li><a href="blog.html">Lifestyle</a></li>
                  <li><a href="blog.html">Others</a></li>
                  <li><a href="blog.html">Shoes</a></li>
                  <li><a href="blog.html">Technology</a></li>
                </ul>
              </div>

              <div class="widget widget-custom-block">
                <h3 class="widget-title bb-no">Mô tả</h3>
                <div class="widget-body">
                  <p class="text-default mb-0">
                    {{ post.description }}
                  </p>
                </div>
              </div>

              <div class="widget widget-calendar">
                <h3 class="widget-title bb-no">Lịch</h3>
                <div class="widget-body">
                  <div class="calendar-container">
                    <v-container>
                      <v-row justify="space-around">
                        <v-date-picker
                          :model-value="date"
                          show-adjacent-months
                          show-week
                          elevation="4"
                        ></v-date-picker>
                      </v-row>
                    </v-container>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </div>
</template>
