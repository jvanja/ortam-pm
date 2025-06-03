<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import { BreadcrumbItem, Client, Invoice, Project, Proposal } from '@/types';
import { Head } from '@inertiajs/vue3';
import { defineProps } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Invoices', href: '/invoices' }];
const props = defineProps<{
  proposals: Proposal[];
}>();

</script>

<template>
  <Head title="Proposals" />

  <AppLayout :breadcrumbs="breadcrumbs">
  </AppLayout>
</template>
