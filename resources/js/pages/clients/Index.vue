<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import ObjectList from '@/components/ObjectList.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Client } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { debounce } from 'lodash-es';
import { computed, ref, watch } from 'vue';
import Pagination from '@/components/Pagination.vue';

const props = defineProps<{
  clients: {
    data: Client[];
    current_page: number;
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: Array<any>;
    next_page_url: string;
    path: string;
    per_page: number;
    prev_page_url: string;
    to: number;
    total: number;
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
const breadcrumbs: BreadcrumbItem[] = [{ title: 'Clients', href: '/clients' }];
const searchQuery = ref(props.filters.search || '');

const objects = computed(() =>
  props.clients.data.map((client) => {
    return { id: client.id!, name: client.company_name };
  }),
);

// Watch for changes in searchQuery
watch(
  searchQuery,
  debounce((newValue: string) => {
    router.get(
      '/clients',
      { search: newValue },
      {
        preserveState: true,
        preserveScroll: true, // Keep scroll position after update
        replace: true, // Avoids adding duplicate history entries
      },
    );
  }, 300),
); // Debounce requests by 300ms

const currentPage = ref(props.clients.meta.current_page || 1)
const onPageChange = (page: number) => {
  currentPage.value = page
  router.get('clients', {page: page}, { preserveScroll: true });
}
</script>

<template>
  <Head title="Clients" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="flex flex-col gap-4 p-4">
        <Heading title="Clients" description="These are your latest clients" />
        <div class="grid gap-1">
          <Label for="search">Search clients</Label>
          <Input id="search" v-model="searchQuery" class="mt-1 block w-full" placeholder="Search by client name or contact person" />
        </div>
        <ObjectList :objects="objects" type="clients" />
        <Pagination :currentPage="currentPage" :pagesMeta="clients.meta" :onPageChange="onPageChange" />
      </div>
    </div>
  </AppLayout>
</template>
