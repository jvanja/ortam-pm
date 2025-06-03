<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem, Client, Project, Proposal } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { defineProps } from 'vue';
type ProposalWithProject = Proposal & { project: Project; client: Client; };

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Proposal', href: '/proposals' }];
const props = defineProps<{
  proposal: ProposalWithProject;
  proposal_view: string;
}>();

const form = useForm({ id: props.proposal.id });
const printProposal = () => window.print();

const sendProposal = () => {
  if (confirm('Are you sure you want to send this proposal?')) {
    form.post(route('proposals.send', [props.proposal.id]), {
      onSuccess: () => {
        console.log('Proposal sent successfully!');
      },
      onError: (errors) => {
        console.error('Error sending proposal:', errors);
        // You might want to display a user-friendly error message
        if (errors && errors.error) {
          alert(errors.error); // Display the error message from the backend
        } else {
          alert('Failed to send proposal.');
        }
      },
    });
  }
};
</script>

<template>
  <Head title="Proposal" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="mx-auto max-w-3xl p-8" v-html="proposal_view"></div>
    <div class="no-print flex gap-4 p-8">
      <Button @click="sendProposal" :disabled="!proposal.client?.email">Send Proposal</Button>
      <Button @click="printProposal">Print Proposal</Button>
    </div>
  </AppLayout>
</template>
