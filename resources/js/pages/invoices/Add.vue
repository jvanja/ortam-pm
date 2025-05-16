<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Invoices', href: '/invoices' }];
</script>
<template>
  <Head title="Invoices" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-8">
      New Invoice
    </div>
  </AppLayout>
</template>
