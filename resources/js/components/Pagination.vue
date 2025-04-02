<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
  Pagination,
  PaginationEllipsis,
  PaginationFirst,
  PaginationLast,
  PaginationList,
  PaginationListItem,
  PaginationNext,
  PaginationPrev,
} from '@/components/ui/pagination';
const props = defineProps({
  currentPage: {
    type: Number,
    required: true,
  },
  pagesMeta: {
    type: Object,
    required: true,
  },
  onPageChange: {
    type: Function,
    required: true,
  }
});

const updatePage = (page: number) => {
  props.onPageChange(page);
};
</script>

<template>
  <!-- v-model:page="currentPage" -->
  <Pagination
    v-slot="{ page }"
    :items-per-page="pagesMeta.per_page"
    :total="pagesMeta.total"
    :sibling-count="1"
    show-edges
    :default-page="currentPage"
    @update:page="updatePage"
  >
    <PaginationList v-slot="{ items }" class="flex items-center gap-1">
      <PaginationFirst />
      <PaginationPrev />

      <template v-for="(item, index) in items">
        <PaginationListItem v-if="item.type === 'page'" :key="index" :value="item.value" as-child>
          <Button class="h-9 w-9 p-0" :variant="item.value === page ? 'default' : 'outline'">
            {{ item.value }}
          </Button>
        </PaginationListItem>
        <PaginationEllipsis v-else :key="item.type" :index="index" />
      </template>

      <PaginationNext />
      <PaginationLast />
    </PaginationList>
  </Pagination>
</template>
