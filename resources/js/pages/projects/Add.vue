<script setup lang="ts">
import NewProject from '@/components/project/NewProject.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Client, Proposal } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Projects', href: '/projects' }];
const {clients, languages, users, proposal} = defineProps<{
  clients: Client[];
  languages: any,
  users: {id: string, name:string}[],
  proposal?: Proposal}>();

console.log(proposal)
</script>
<template>
  <Head title="Project" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-8">
      <NewProject :clients :languages :users :proposal />
    </div>
  </AppLayout>
</template>
