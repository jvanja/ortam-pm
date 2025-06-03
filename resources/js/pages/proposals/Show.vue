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
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <h2 class="text-2xl font-semibold mb-4">Proposal Details</h2>
            <p><strong>ID:</strong> {{ proposal.id }}</p>
            <p><strong>Title:</strong> {{ proposal.title }}</p>
            <p><strong>Status:</strong> {{ proposal.status }}</p>
            <p><strong>Project:</strong> {{ proposal.project.type }}</p>
            <p><strong>Client:</strong> {{ proposal.client.company_name }}</p>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
