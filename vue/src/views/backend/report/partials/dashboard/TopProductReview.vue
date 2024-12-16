<template>
    <a-col class="top-product" :span="12">
      <a-card class="p-0">
        <div class="">
          <div class="flex items-center justify-between">
            <div class="mb-3">
              <span class="border-b border-dashed">Top sản phẩm đánh giá</span>
              <TooltipComponent title="Top sản phẩm được đánh giá cao nhất" />
            </div>
            <RouterLink :to="{ name: 'report.top.product.review' }" class="report-block-link">
              <i class="far fa-arrow-circle-right"></i>
            </RouterLink>
          </div>
        </div>
  
        <div v-if="dataProductReview?.length">
          <div
            class="flex items-center justify-between border-b py-[14.5px] last:border-none"
            v-for="(item, index) in dataProductReview"
            :key="index"
          >
            <div>
              <p class="w-[450px] truncate">{{ item.product_name }}</p>
            </div>
            <div>
              <p>{{ item.review_count }} lần</p>
            </div>
            <div class=" items-center">
              <div>
                <span class="text-gray-400"> {{ item.avg_rating }} sao</span>
              </div>
            </div>
          </div>
        </div>
        
        <a-skeleton active v-else />
      </a-card>
    </a-col>
  </template>
  
  <script setup>
  import { TooltipComponent } from '@/components/backend';
  import axios from '@/configs/axios';
  import { debounce } from '@/utils/helpers';
  import { ref, watch } from 'vue';
  
  const props = defineProps({
    date: {
      type: String,
      default: 'last_30_days'
    },
    startDate: {
      type: String,
      default: ''
    },
    endDate: {
      type: String,
      default: ''
    }
  });
  
  const dataProductReview = ref([]);
  
  const getData = async () => {
    try {
      const response = await axios.get('/statistics/products?condition=product_review_top', {
        params: {
          date: props.date,
          start_date: props.startDate,
          end_date: props.endDate,
          pageSize: 5
        }
      });
  
      dataProductReview.value = response.data?.data;
    } catch (error) {
      console.log(error);
    }
  };
  
  const debounceGetData = debounce(getData, 500);
  
  watch(
    () => [props.date, props.startDate, props.endDate],
    () => {
      debounceGetData();
    },
    { immediate: true }
  );
  </script>
  