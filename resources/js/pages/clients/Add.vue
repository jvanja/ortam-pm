<script setup lang="ts">
import NewClient from '@/components/client/NewClient.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Clients', href: '/clients' }];
</script>
<template>
  <Head title="New Client" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-8">
      <NewClient back-route="clients.create" />
    </div>
  </AppLayout>
</template>
