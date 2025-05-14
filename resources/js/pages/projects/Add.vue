<script setup lang="ts">
import NewProject from '@/components/project/NewProject.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Client } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Projects', href: '/projects' }];
const {clients} = defineProps<{clients: Client[]}>();
</script>
<template>
  <Head title="Project" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-8">
      <NewProject :clients />
    </div>
  </AppLayout>
</template>
