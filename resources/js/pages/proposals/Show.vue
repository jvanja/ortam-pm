<script setup lang="ts">
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem, Client, Project, Proposal } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { defineProps } from 'vue';
type ProposalWithProject = Proposal & { project: Project; client: Client };
import { is, can } from 'laravel-permission-to-vuejs'
import { toast } from 'vue-sonner';


const breadcrumbs: BreadcrumbItem[] = [{ title: 'Proposal', href: '/proposals' }];
const props = defineProps<{
  proposal: ProposalWithProject;
  proposal_view: string;
}>();

const form = useForm({ id: props.proposal.id, state: props.proposal.state });

const printProposal = () => window.print();

const sendProposal = () => {
  form.post(route('proposals.send', [props.proposal.id]), {
    onSuccess: () => {
      toast.success(`Proposal sent successfully!`);
    },
    onError: (errors) => {
      toast.error('Error sending proposal:', errors);
      if (errors && errors.error) {
        alert(errors.error);
      } else {
        alert('Failed to send proposal.');
      }
    },
  });
};

const acceptProposal = () => {
  form.state = 'accepted';
  form.patch(route('proposals.accept', [props.proposal.id]), {
    onSuccess: () => {
      toast.success(`Proposal accepted. The manger has been notified`, {
        style: { background: '#6ee7b7', color: '#000' },
      });
    },
    onError: (errors) => {
      toast.error('Error sending proposal:', errors);
      if (errors && errors.error) {
        alert(errors.error);
      } else {
        alert('Failed to send proposal.');
      }
    },
  });
}
</script>

<template>
  <Head title="Proposal" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-8">
      <div class="mx-auto mb-[60px] max-w-3xl" v-html="proposal_view"></div>
      <div class="no-print mb-4 flex justify-center gap-4">
        <div v-if="can('proposal.edit')">
        <AlertDialog>
          <AlertDialogTrigger as-child>
            <Button :disabled="!proposal.client.email">Send Proposal</Button>
          </AlertDialogTrigger>
          <AlertDialogContent>
            <AlertDialogHeader>
              <AlertDialogTitle>Are you sure you want to send this proposal?</AlertDialogTitle>
              <AlertDialogDescription> You are about to send this proposal to {{ proposal.client.email }}. </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
              <AlertDialogCancel :disabled="form.processing">Cancel</AlertDialogCancel>
              <AlertDialogAction @click="sendProposal" :disabled="form.processing">Yes, send the proposal</AlertDialogAction>
            </AlertDialogFooter>
          </AlertDialogContent>
        </AlertDialog>
        </div>
        <div v-if="is('client')">
          <Button @click="acceptProposal" :disabled="!(true || proposal.state !== 'accepted')">Accept Proposal</Button>
        </div>
        <Button @click="printProposal">Print Proposal</Button>
      </div>
    </div>
  </AppLayout>
</template>
<style scoped>
:deep(ul), :deep(ol) {
  padding-left: 2rem;
  list-style: auto;
}
@media print {
  .no-print {
    display: none !important;
  }
}
</style>
