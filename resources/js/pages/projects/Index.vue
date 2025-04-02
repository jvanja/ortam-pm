<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import ObjectList from '@/components/ObjectList.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Project } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { debounce } from 'lodash-es'; // Using lodash for debouncing
import { computed, ref, watch } from 'vue';

const props = defineProps<{ projects: Project[]; filters: { search: string | null } }>();
const breadcrumbs: BreadcrumbItem[] = [{ title: 'Projects', href: '/projects' }];
const searchQuery = ref(props.filters.search || '');

const objects = computed(() =>
  props.projects.map((project) => {
    return { id: project.id!, name: project.type };
  }),
);

// Watch for changes in searchQuery
watch(
  searchQuery,
  debounce((newValue: string) => {
    router.get(
      '/projects',
      { search: newValue },
      {
        preserveState: true,
        preserveScroll: true, // Keep scroll position after update
        replace: true, // Avoids adding duplicate history entries
      },
    );
  }, 300),
); // Debounce requests by 300ms
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="flex flex-col gap-4 p-4">
        <Heading title="Projects" description="Search or browse your projects" />
        <div class="grid gap-1">
          <Label for="search">Search projects</Label>
          <Input id="search" v-model="searchQuery" class="mt-1 block w-full" placeholder="Search by type, department, address..." />
        </div>
        <ObjectList :objects="objects" type="projects" />
      </div>
    </div>
  </AppLayout>
</template>
