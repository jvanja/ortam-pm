<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import ObjectList from '@/components/ObjectList.vue';
import Pagination from '@/components/Pagination.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Project } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { debounce } from 'lodash-es';
import { computed, ref, watch } from 'vue';

const props = defineProps<{
  projects: {
    data: Project[];
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
  filters: { search: string | null; manager: string | null; status: string | null };
  managers: string[];
  statuses: string[];
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Projects', href: '/projects' }];

const searchQuery = ref(props.filters.search || '');
const selectedManager = ref(props.filters.manager || '');
const selectedStatus = ref(props.filters.status || '');

const objects = computed(() =>
  props.projects.data.map((project) => {
    return { id: project.id!, name: project.type };
  }),
);

// Combine all filters into a single reactive object
const currentFilters = computed(() => ({
  search: searchQuery.value,
  manager: selectedManager.value,
  status: selectedStatus.value,
}));

// Watch for changes in any filter
watch(
  currentFilters,
  debounce((newFilters) => {
    router.get('/projects', cleanFilters(newFilters), {
      preserveState: true,
      preserveScroll: true,
      replace: true, // Avoids adding duplicate history entries
    });
  }, 300), // Debounce requests by 300ms
  { deep: true },
);

const currentPage = ref(props.projects.meta.current_page || 1);

const onPageChange = (page: number) => {
  currentPage.value = page;
  // Include current filters when changing pages
  const filtersWithPage = {
    ...currentFilters.value,
    page: page,
  };

  router.get('projects', cleanFilters(filtersWithPage), { preserveScroll: true, preserveState: true });
};

// Remove null or empty string filters before sending
const cleanFilters = (filters: { manager: string; status: string }) => Object.fromEntries(Object.entries(filters).filter(([_, value]) => value !== null && value !== ''));
</script>

<template>
  <Head title="Projects" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="flex flex-col gap-4 p-4">
        <Heading title="Projects" description="Search or browse your projects" />
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
          <div class="grid gap-1">
            <Label for="search">Search projects</Label>
            <Input id="search" v-model="searchQuery" class="mt-1 block w-full" placeholder="Search by type, department, address..." />
          </div>
          <div class="grid gap-1">
            <Label for="manager">Filter by Manager</Label>
            <Select v-model="selectedManager">
              <SelectTrigger class="w-full">
                <SelectValue placeholder="Select a manager" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All</SelectItem>
                <SelectItem v-for="manager in managers" :key="manager" :value="manager">
                  {{ manager }}
                </SelectItem>
              </SelectContent>
            </Select>
          </div>
          <div class="grid gap-1">
            <Label for="status">Filter by Status</Label>
            <Select v-model="selectedStatus">
              <SelectTrigger class="w-full">
                <SelectValue placeholder="Select a status" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All</SelectItem>
                <SelectItem v-for="status in statuses" :key="status" :value="status">
                  {{ status }}
                </SelectItem>
              </SelectContent>
            </Select>
          </div>
        </div>
        <ObjectList :objects="objects" type="projects" />
        <Pagination :currentPage="currentPage" :pagesMeta="projects.meta" :onPageChange="onPageChange" />
      </div>
    </div>
  </AppLayout>
</template>
