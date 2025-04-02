<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard', href: '/dashboard' }];
</script>

<template>
  <Head title="Client Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-4 p-8">
      <Heading title="Client Dashboard" description="These are your latest projects" />
      <div class="flex flex-col gap-2">

      </div>
    </div>
  </AppLayout>
</template>
