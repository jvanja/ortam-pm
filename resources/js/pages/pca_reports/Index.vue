<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { debounce } from 'lodash-es';
import Heading from '@/components/Heading.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import ObjectList from '@/components/ObjectList.vue';
import Pagination from '@/components/Pagination.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, PCA_Report } from '@/types';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps<{
  pca_reports: {
    data: PCA_Report[];
    meta: {
      current_page: number;
      from: number;
      last_page: number;
      links: Array<any>;
      path: string;
      per_page: number;
      to: number;
      total: number;
    };
  };
  filters: { search: string | null };
}>();
const breadcrumbs: BreadcrumbItem[] = [{ title: 'PCA Reports', href: '/pca-reports' }];
const searchQuery = ref(props.filters.search || '');

// const objects = props.pca_reports.data.map((pca_report) => {
//   return { id: pca_report.id!, name: pca_report.name };
// });
const objects = computed(() =>
  props.pca_reports.data
);
// Watch for changes in searchQuery
watch(
  searchQuery,
  debounce((newValue: string) => {
    router.get(
      '/pca-reports',
      { search: newValue },
      {
        preserveState: true,
        preserveScroll: true, // Keep scroll position after update
        replace: true, // Avoids adding duplicate history entries
      },
    );
  }, 300),
); // Debounce requests by 300ms

const currentPage = ref(props.pca_reports.meta.current_page || 1)
const onPageChange = (page: number) => {
  currentPage.value = page
  router.get('pca-report', {page: page}, { preserveScroll: true });
}
</script>

<template>
  <Head title="PCA Reports" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="flex flex-col gap-4 p-4">
        <Heading title="PCA Reports" description="These are your latest PCA Reports" />
        <div class="grid gap-1">
          <Label for="search">Search PCA reports</Label>
          <Input id="search" v-model="searchQuery" class="mt-1 block w-full" placeholder="Search by report name..." />
        </div>
        <ObjectList :objects="objects" type="pca-reports" :can-delete="true" />
        <Pagination :currentPage="currentPage" :pagesMeta="pca_reports.meta" :onPageChange="onPageChange" />
      </div>
    </div>
  </AppLayout>
</template>
