<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { BreadcrumbItem, Client, Project, Proposal } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

type ProposalWithProject = Proposal & { project: Project; client: Client; };
const breadcrumbs: BreadcrumbItem[] = [{ title: 'Proposals', href: '/proposals' }]; // Corrected breadcrumb title and href
defineProps<{
  proposals: ProposalWithProject[];
}>();

</script>

<template>
  <Head title="Proposals" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-8 space-y-4">
      <div class="flex justify-between items-center">
        <Heading title="Proposals" description="These are your latest proposals" />
        <Link :href="route('proposals.create')">
          <Button>Add New Proposal</Button>
        </Link>
      </div>

      <Table>
        <TableHeader>
          <TableRow>
            <TableHead>ID</TableHead>
            <TableHead>Client</TableHead>
            <TableHead>Project</TableHead>
            <TableHead>Status</TableHead>
            <TableHead>Amount</TableHead>
            <TableHead>Due Date</TableHead>
            <TableHead class="text-right">Actions</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="proposal in proposals" :key="proposal.id">
            <TableCell>{{ proposal.id }}</TableCell>
            <TableCell>{{ proposal.client?.company_name }}</TableCell>
            <TableCell>{{ proposal.project?.type }}</TableCell>
            <TableCell>{{ proposal.state }}</TableCell>
            <TableCell>{{proposal.currency}} {{ proposal.total_amount }}</TableCell>
            <TableCell>{{ proposal.expires_at }}</TableCell>
            <TableCell class="text-right">
              <!-- @vue-expect-error type conversion is fine -->
              <Link :href="route('proposals.show', proposal.id)">
                <Button variant="outline" size="sm">View</Button>
              </Link>
            </TableCell>
          </TableRow>
          <TableRow v-if="proposals.length === 0">
            <TableCell colspan="7" class="text-center py-4">No proposals found.</TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>
  </AppLayout>
</template>
