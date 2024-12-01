<template>
  <a-col :span="12">
    <a-card class="p-0">
      <div>
        <div class="flex items-center justify-between">
          <div class="mb-3">
            <span class="border-b border-dashed">Top từ khóa tìm kiếm</span>
            <TooltipComponent title="Top từ khóa có lượng tìm kiếm nhiều nhất" />
          </div>
          <RouterLink :to="{ name: 'report.top.search.history' }" class="report-block-link">
            <i class="far fa-arrow-circle-right"></i>
          </RouterLink>
        </div>
      </div>

      <!-- Display keyword and search count only -->
      <div v-if="dataKeywords.length > 0">
        <div
          class="flex items-center justify-between border-b py-[14.5px] last:border-none"
          v-for="(item, index) in dataKeywords"
          :key="index"
        >
          <p class="w-[450px] truncate">{{ item.keyword }}</p>

          <div class="font-semibold text-gray-600">{{ item.total_count }} lần</div>
        </div>
      </div>

      <!-- <div v-if="dataKeywords.length > 0">
        <div
          class="flex items-center justify-between border-b py-[14.5px] last:border-none"
          v-for="(item, index) in dataKeywords"
          :key="index"
        >
          <p class="w-[450px] truncate">{{ item.keyword }}</p>

          <div class="font-semibold text-gray-600">{{ item.search_count }} lần</div>
        </div>
      </div> -->
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

const dataKeywords = ref([]);

const getData = async () => {
  try {
    const response = await axios.get('/statistics/search-history', {
      params: {
        date: props.date,
        start_date: props.startDate,
        end_date: props.endDate,
        pageSize: 5
      }
    });
    dataKeywords.value = response.data?.data;
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
