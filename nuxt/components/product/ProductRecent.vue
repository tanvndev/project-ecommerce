<script setup>
const props = defineProps({
  product: {
    type: [Object, Array],
    default: () => [],
  },
})
</script>
<template>
  <!-- <section class="vendor-product-section">
    <div class="title-link-wrapper mb-4">
      <h4 class="title text-left">More Products From This Vendor</h4>
      <a href="#" class="btn btn-dark btn-link btn-slide-right btn-icon-right"
        >More Products<i class="w-icon-long-arrow-right"></i
      ></a>
    </div>
    <div class="swiper-theme">
      <div class="row cols-lg-3 cols-md-4 cols-sm-3 cols-2">
        <div class="swiper-slide product">
          <figure class="product-media">
            <a href="product-default.html">
              <img
                src="assets/images/products/default/1-1.jpg"
                alt="Product"
                width="300"
                height="338"
              />
              <img
                src="assets/images/products/default/1-2.jpg"
                alt="Product"
                width="300"
                height="338"
              />
            </a>
            <div class="product-action-vertical">
              <a
                href="#"
                class="btn-product-icon btn-cart w-icon-cart"
                title="Add to cart"
              ></a>
              <a
                href="#"
                class="btn-product-icon btn-wishlist w-icon-heart"
                title="Add to wishlist"
              ></a>
              <a
                href="#"
                class="btn-product-icon btn-compare w-icon-compare"
                title="Add to Compare"
              ></a>
            </div>
            <div class="product-action">
              <a href="#" class="btn-product btn-quickview" title="Quick View"
                >Quick View</a
              >
            </div>
          </figure>
          <div class="product-details">
            <div class="product-cat">
              <a href="shop-banner-sidebar.html">Accessories</a>
            </div>
            <h4 class="product-name">
              <a href="product-default.html">Sticky Pencil</a>
            </h4>
            <div class="ratings-container">
              <div class="ratings-full">
                <span class="ratings" style="width: 100%"></span>
                <span class="tooltiptext tooltip-top"></span>
              </div>
              <a href="product-default.html" class="rating-reviews"
                >(3 reviews)</a
              >
            </div>
            <div class="product-pa-wrapper">
              <div class="product-price">$20.00</div>
            </div>
          </div>
        </div>
        <div class="swiper-slide product">
          <figure class="product-media">
            <a href="product-default.html">
              <img
                src="assets/images/products/default/2.jpg"
                alt="Product"
                width="300"
                height="338"
              />
            </a>
            <div class="product-action-vertical">
              <a
                href="#"
                class="btn-product-icon btn-cart w-icon-cart"
                title="Add to cart"
              ></a>
              <a
                href="#"
                class="btn-product-icon btn-wishlist w-icon-heart"
                title="Add to wishlist"
              ></a>
              <a
                href="#"
                class="btn-product-icon btn-compare w-icon-compare"
                title="Add to Compare"
              ></a>
            </div>
            <div class="product-action">
              <a href="#" class="btn-product btn-quickview" title="Quick View"
                >Quick View</a
              >
            </div>
          </figure>
          <div class="product-details">
            <div class="product-cat">
              <a href="shop-banner-sidebar.html">Electronics</a>
            </div>
            <h4 class="product-name">
              <a href="product-default.html">Mini Multi-Functional Cooker</a>
            </h4>
            <div class="ratings-container">
              <div class="ratings-full">
                <span class="ratings" style="width: 80%"></span>
                <span class="tooltiptext tooltip-top"></span>
              </div>
              <a href="product-default.html" class="rating-reviews"
                >(5 reviews)</a
              >
            </div>
            <div class="product-pa-wrapper">
              <div class="product-price">
                <ins class="new-price">$480.00</ins
                ><del class="old-price">$534.00</del>
              </div>
            </div>
          </div>
        </div>
        <div class="swiper-slide product">
          <figure class="product-media">
            <a href="product-default.html">
              <img
                src="assets/images/products/default/3.jpg"
                alt="Product"
                width="300"
                height="338"
              />
            </a>
            <div class="product-action-vertical">
              <a
                href="#"
                class="btn-product-icon btn-cart w-icon-cart"
                title="Add to cart"
              ></a>
              <a
                href="#"
                class="btn-product-icon btn-wishlist w-icon-heart"
                title="Add to wishlist"
              ></a>
              <a
                href="#"
                class="btn-product-icon btn-compare w-icon-compare"
                title="Add to Compare"
              ></a>
            </div>
            <div class="product-action">
              <a href="#" class="btn-product btn-quickview" title="Quick View"
                >Quick View</a
              >
            </div>
          </figure>
          <div class="product-details">
            <div class="product-cat">
              <a href="shop-banner-sidebar.html">Sports</a>
            </div>
            <h4 class="product-name">
              <a href="product-default.html">Skate Pan</a>
            </h4>
            <div class="ratings-container">
              <div class="ratings-full">
                <span class="ratings" style="width: 100%"></span>
                <span class="tooltiptext tooltip-top"></span>
              </div>
              <a href="product-default.html" class="rating-reviews"
                >(3 reviews)</a
              >
            </div>
            <div class="product-pa-wrapper">
              <div class="product-price">
                <ins class="new-price">$278.00</ins
                ><del class="old-price">$310.00</del>
              </div>
            </div>
          </div>
        </div>
        <div class="swiper-slide product">
          <figure class="product-media">
            <a href="product-default.html">
              <img
                src="assets/images/products/default/4-1.jpg"
                alt="Product"
                width="300"
                height="338"
              />
              <img
                src="assets/images/products/default/4-2.jpg"
                alt="Product"
                width="300"
                height="338"
              />
            </a>
            <div class="product-action-vertical">
              <a
                href="#"
                class="btn-product-icon btn-cart w-icon-cart"
                title="Add to cart"
              ></a>
              <a
                href="#"
                class="btn-product-icon btn-wishlist w-icon-heart"
                title="Add to wishlist"
              ></a>
              <a
                href="#"
                class="btn-product-icon btn-compare w-icon-compare"
                title="Add to Compare"
              ></a>
            </div>
            <div class="product-action">
              <a href="#" class="btn-product btn-quickview" title="Quick View"
                >Quick View</a
              >
            </div>
          </figure>
          <div class="product-details">
            <div class="product-cat">
              <a href="shop-banner-sidebar.html">Accessories</a>
            </div>
            <h4 class="product-name">
              <a href="product-default.html">Clip Attachment</a>
            </h4>
            <div class="ratings-container">
              <div class="ratings-full">
                <span class="ratings" style="width: 100%"></span>
                <span class="tooltiptext tooltip-top"></span>
              </div>
              <a href="product-default.html" class="rating-reviews"
                >(5 reviews)</a
              >
            </div>
            <div class="product-pa-wrapper">
              <div class="product-price">$40.00</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <section class="related-product-section">
    <div class="title-link-wrapper mb-4">
      <h4 class="title">Related Products</h4>
      <a href="#" class="btn btn-dark btn-link btn-slide-right btn-icon-right"
        >More Products<i class="w-icon-long-arrow-right"></i
      ></a>
    </div>
    <div class="swiper-theme">
      <div class="row cols-lg-3 cols-md-4 cols-sm-3 cols-2">
        <div class="swiper-slide product">
          <figure class="product-media">
            <a href="product-default.html">
              <img
                src="assets/images/products/default/5.jpg"
                alt="Product"
                width="300"
                height="338"
              />
            </a>
            <div class="product-action-vertical">
              <a
                href="#"
                class="btn-product-icon btn-cart w-icon-cart"
                title="Add to cart"
              ></a>
              <a
                href="#"
                class="btn-product-icon btn-wishlist w-icon-heart"
                title="Add to wishlist"
              ></a>
              <a
                href="#"
                class="btn-product-icon btn-compare w-icon-compare"
                title="Add to Compare"
              ></a>
            </div>
            <div class="product-action">
              <a href="#" class="btn-product btn-quickview" title="Quick View"
                >Quick View</a
              >
            </div>
          </figure>
          <div class="product-details">
            <h4 class="product-name">
              <a href="product-default.html">Drone</a>
            </h4>
            <div class="ratings-container">
              <div class="ratings-full">
                <span class="ratings" style="width: 100%"></span>
                <span class="tooltiptext tooltip-top"></span>
              </div>
              <a href="product-default.html" class="rating-reviews"
                >(3 reviews)</a
              >
            </div>
            <div class="product-pa-wrapper">
              <div class="product-price">$632.00</div>
            </div>
          </div>
        </div>
        <div class="swiper-slide product">
          <figure class="product-media">
            <a href="product-default.html">
              <img
                src="assets/images/products/default/6.jpg"
                alt="Product"
                width="300"
                height="338"
              />
            </a>
            <div class="product-action-vertical">
              <a
                href="#"
                class="btn-product-icon btn-cart w-icon-cart"
                title="Add to cart"
              ></a>
              <a
                href="#"
                class="btn-product-icon btn-wishlist w-icon-heart"
                title="Add to wishlist"
              ></a>
              <a
                href="#"
                class="btn-product-icon btn-compare w-icon-compare"
                title="Add to Compare"
              ></a>
            </div>
            <div class="product-action">
              <a href="#" class="btn-product btn-quickview" title="Quick View"
                >Quick View</a
              >
            </div>
          </figure>
          <div class="product-details">
            <h4 class="product-name">
              <a href="product-default.html">Official Camera</a>
            </h4>
            <div class="ratings-container">
              <div class="ratings-full">
                <span class="ratings" style="width: 100%"></span>
                <span class="tooltiptext tooltip-top"></span>
              </div>
              <a href="product-default.html" class="rating-reviews"
                >(3 reviews)</a
              >
            </div>
            <div class="product-pa-wrapper">
              <div class="product-price">
                <ins class="new-price">$263.00</ins
                ><del class="old-price">$300.00</del>
              </div>
            </div>
          </div>
        </div>
        <div class="swiper-slide product">
          <figure class="product-media">
            <a href="product-default.html">
              <img
                src="assets/images/products/default/7-1.jpg"
                alt="Product"
                width="300"
                height="338"
              />
              <img
                src="assets/images/products/default/7-2.jpg"
                alt="Product"
                width="300"
                height="338"
              />
            </a>
            <div class="product-action-vertical">
              <a
                href="#"
                class="btn-product-icon btn-cart w-icon-cart"
                title="Add to cart"
              ></a>
              <a
                href="#"
                class="btn-product-icon btn-wishlist w-icon-heart"
                title="Add to wishlist"
              ></a>
              <a
                href="#"
                class="btn-product-icon btn-compare w-icon-compare"
                title="Add to Compare"
              ></a>
            </div>
            <div class="product-action">
              <a href="#" class="btn-product btn-quickview" title="Quick View"
                >Quick View</a
              >
            </div>
          </figure>
          <div class="product-details">
            <h4 class="product-name">
              <a href="product-default.html">Phone Charge Pad</a>
            </h4>
            <div class="ratings-container">
              <div class="ratings-full">
                <span class="ratings" style="width: 80%"></span>
                <span class="tooltiptext tooltip-top"></span>
              </div>
              <a href="product-default.html" class="rating-reviews"
                >(8 reviews)</a
              >
            </div>
            <div class="product-pa-wrapper">
              <div class="product-price">$23.00</div>
            </div>
          </div>
        </div>
        <div class="swiper-slide product">
          <figure class="product-media">
            <a href="product-default.html">
              <img
                src="assets/images/products/default/8.jpg"
                alt="Product"
                width="300"
                height="338"
              />
            </a>
            <div class="product-action-vertical">
              <a
                href="#"
                class="btn-product-icon btn-cart w-icon-cart"
                title="Add to cart"
              ></a>
              <a
                href="#"
                class="btn-product-icon btn-wishlist w-icon-heart"
                title="Add to wishlist"
              ></a>
              <a
                href="#"
                class="btn-product-icon btn-compare w-icon-compare"
                title="Add to Compare"
              ></a>
            </div>
            <div class="product-action">
              <a href="#" class="btn-product btn-quickview" title="Quick View"
                >Quick View</a
              >
            </div>
          </figure>
          <div class="product-details">
            <h4 class="product-name">
              <a href="product-default.html">Fashionalble Pencil</a>
            </h4>
            <div class="ratings-container">
              <div class="ratings-full">
                <span class="ratings" style="width: 100%"></span>
                <span class="tooltiptext tooltip-top"></span>
              </div>
              <a href="product-default.html" class="rating-reviews"
                >(9 reviews)</a
              >
            </div>
            <div class="product-pa-wrapper">
              <div class="product-price">$50.00</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section> -->
</template>
