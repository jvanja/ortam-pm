<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem, Client, Project, Proposal } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { defineProps } from 'vue';
type ProposalWithProject = Proposal & { project: Project; client: Client; };

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Proposal', href: '/proposals' }];
defineProps<{
  proposal: ProposalWithProject;
}>();

</script>

<template>
  <Head title="Proposal" />

  <AppLayout :breadcrumbs="breadcrumbs">
  </AppLayout>
</template>
