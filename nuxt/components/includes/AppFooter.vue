<script setup>
const systemConfigStore = useSystemConfigStore()
const { $axios } = useNuxtApp()
const systemConfigs = computed(() => systemConfigStore.getSystemConfigs)

const getSystemConfigs = async () => {
  try {
    const response = await $axios.get('/system-configs')
    systemConfigStore.setSystemConfigs(response.data)
  } catch (error) {}
}

onMounted(() => {
  getSystemConfigs()
})
</script>
<template>
  <!-- Start of Footer -->
  <footer class="footer">
    <div class="footer-newsletter bg-primary">
      <div class="container">
        <div class="row justify-content-center align-items-center">
          <div class="col-xl-5 col-lg-6">
            <div class="icon-box icon-box-side text-white">
              <div class="icon-box-icon d-inline-flex">
                <i class="w-icon-envelop3"></i>
              </div>
              <div class="icon-box-content">
                <h4
                  class="icon-box-title text-white text-uppercase font-weight-bold"
                >
                  Đăng ký nhận bản tin của chúng tôi
                </h4>
                <p class="text-white">
                  Nhận tất cả thông tin mới nhất về Sự kiện, Khuyến mại và Ưu
                  đãi.
                </p>
              </div>
            </div>
          </div>
          <div class="col-xl-7 col-lg-6 col-md-9 mt-4 mt-lg-0">
            <form
              action="#"
              method="get"
              class="input-wrapper input-wrapper-inline input-wrapper-rounded"
            >
              <input
                type="email"
                class="form-control mr-2 bg-white"
                name="email"
                id="email"
                placeholder="Địa chỉ email của bạn"
              />
              <button class="btn btn-dark btn-rounded" type="button">
                Đăng ký<i class="w-icon-long-arrow-right"></i>
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div class="container">
      <div class="footer-top">
        <div class="row">
          <div class="col-lg-4 col-sm-6">
            <div class="widget widget-about">
              <NuxtLink to="/" class="logo-footer">
                <v-img
                  :src="resizeImage(systemConfigs?.logo)"
                  alt="logo-footer"
                  width="144"
                  height="45"
                />
              </NuxtLink>
              <div class="widget-body">
                <p class="widget-about-title">
                  Bạn có thắc mắc? Hãy gọi cho chúng tôi 24/7
                </p>
                <a href="tel:18005707777" class="widget-about-call">{{
                  systemConfigs?.hotline
                }}</a>
                <p class="widget-about-desc">
                  Đăng ký ngay để nhận thông tin cập nhật.
                </p>

                <div class="social-icons social-icons-colored">
                  <a
                    :href="systemConfigs?.facebook"
                    target="_blank"
                    class="social-icon social-facebook w-icon-facebook"
                  ></a>
                  <a
                    :href="systemConfigs?.twitter"
                    target="_blank"
                    class="social-icon social-twitter w-icon-twitter"
                  ></a>
                  <a
                    :href="systemConfigs?.instagram"
                    target="_blank"
                    class="social-icon social-instagram w-icon-instagram"
                  ></a>
                  <a
                    :href="systemConfigs?.youtube"
                    target="_blank"
                    class="social-icon social-youtube w-icon-youtube"
                  ></a>
                  <a
                    :href="systemConfigs?.pinterest"
                    target="_blank"
                    class="social-icon social-pinterest w-icon-pinterest"
                  ></a>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-sm-6">
            <div class="widget">
              <h3 class="widget-title">Đường dẫn nhanh</h3>
              <ul class="widget-body">
                <li><NuxtLink to="/about">Giới thiệu</NuxtLink></li>
                <li><NuxtLink to="/contact">Liên hệ chúng tôi</NuxtLink></li>
                <li><NuxtLink to="/user/profile">Tài khoản</NuxtLink></li>
                <li>
                  <NuxtLink to="/order-search">Tra cứu đơn hàng</NuxtLink>
                </li>
              </ul>
            </div>
          </div>
          <div class="col-lg-3 col-sm-6">
            <div class="widget">
              <h4 class="widget-title">tài khoản của tôi</h4>
              <ul class="widget-body">
                <li>
                  <NuxtLink to="/order-search">Tra cứu đơn hàng</NuxtLink>
                </li>
                <li><NuxtLink to="/cart">View Cart</NuxtLink></li>
                <li><NuxtLink to="/login">Đăng nhập</NuxtLink></li>
                <li><NuxtLink to="/post">Bài viết</NuxtLink></li>
                <li><NuxtLink to="#">Chính sách & bảo mật</NuxtLink></li>
              </ul>
            </div>
          </div>
          <div class="col-lg-3 col-sm-6">
            <div class="widget">
              <h4 class="widget-title">Dịch vụ khách hàng</h4>
              <ul class="widget-body">
                <li><a href="#">Phương thức thanh toán</a></li>
                <li><a href="#">Đảm bảo hoàn tiền!</a></li>
                <li><a href="#">Trả lại sản phẩm</a></li>
                <li><a href="#">Trung tâm hỗ trợ</a></li>
                <li><a href="#">Vận chuyển</a></li>
                <li><a href="#">Điều khoản và điều kiện</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="footer-middle d-none">
        <div class="widget widget-category">
          <div class="category-box">
            <h6 class="category-name">Consumer Electric:</h6>
            <a href="#">TV Television</a>
            <a href="#">Air Condition</a>
            <a href="#">Refrigerator</a>
            <a href="#">Washing Machine</a>
            <a href="#">Audio Speaker</a>
            <a href="#">Security Camera</a>
            <a href="#">View All</a>
          </div>
          <div class="category-box">
            <h6 class="category-name">Clothing & Apparel:</h6>
            <a href="#">Men's T-shirt</a>
            <a href="#">Dresses</a>
            <a href="#">Men's Sneacker</a>
            <a href="#">Leather Backpack</a>
            <a href="#">Watches</a>
            <a href="#">Jeans</a>
            <a href="#">Sunglasses</a>
            <a href="#">Boots</a>
            <a href="#">Rayban</a>
            <a href="#">Acccessories</a>
          </div>
          <div class="category-box">
            <h6 class="category-name">Home, Garden & Kitchen:</h6>
            <a href="#">Sofa</a>
            <a href="#">Chair</a>
            <a href="#">Bed Room</a>
            <a href="#">Living Room</a>
            <a href="#">Cookware</a>
            <a href="#">Utensil</a>
            <a href="#">Blender</a>
            <a href="#">Garden Equipments</a>
            <a href="#">Decor</a>
            <a href="#">Library</a>
          </div>
          <div class="category-box">
            <h6 class="category-name">Health & Beauty:</h6>
            <a href="#">Skin Care</a>
            <a href="#">Body Shower</a>
            <a href="#">Makeup</a>
            <a href="#">Hair Care</a>
            <a href="#">Lipstick</a>
            <a href="#">Perfume</a>
            <a href="#">View all</a>
          </div>
          <div class="category-box">
            <h6 class="category-name">Jewelry & Watches:</h6>
            <a href="#">Necklace</a>
            <a href="#">Pendant</a>
            <a href="#">Diamond Ring</a>
            <a href="#">Silver Earing</a>
            <a href="#">Leather Watcher</a>
            <a href="#">Rolex</a>
            <a href="#">Gucci</a>
            <a href="#">Australian Opal</a>
            <a href="#">Ammolite</a>
            <a href="#">Sun Pyrite</a>
          </div>
          <div class="category-box">
            <h6 class="category-name">Computer & Technologies:</h6>
            <a href="#">Laptop</a>
            <a href="#">iMac</a>
            <a href="#">Smartphone</a>
            <a href="#">Tablet</a>
            <a href="#">Apple</a>
            <a href="#">Asus</a>
            <a href="#">Drone</a>
            <a href="#">Wireless Speaker</a>
            <a href="#">Game Controller</a>
            <a href="#">View all</a>
          </div>
        </div>
      </div>
      <div class="footer-bottom">
        <div class="footer-left">
          <p class="copyright">
            {{ systemConfigs?.copyright }}
          </p>
        </div>
        <div class="footer-right d-none">
          <span class="payment-label mr-lg-8"
            >Chúng tôi đang sử dụng thanh toán an toàn cho</span
          >
          <figure class="payment">
            <img
              src="assets/images/payment.png"
              alt="payment"
              width="159"
              height="25"
            />
          </figure>
        </div>
      </div>
    </div>
  </footer>
  <!-- End of Footer -->
</template>
