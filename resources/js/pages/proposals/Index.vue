<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { BreadcrumbItem, Client, Project, Proposal } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { formatDate } from '@/lib/utils';

type ProposalWithProject = Proposal & { project: Project; client: Client; };
const breadcrumbs: BreadcrumbItem[] = [{ title: 'Proposals', href: '/proposals' }];
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
            <TableHead>Title</TableHead>
            <TableHead>Client</TableHead>
            <TableHead>Status</TableHead>
            <TableHead>Due Date</TableHead>
            <TableHead class="text-right">Actions</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="proposal in proposals" :key="proposal.id">
            <TableCell>{{ proposal.title }}</TableCell>
            <TableCell>{{ proposal.client?.company_name }}</TableCell>
            <TableCell>{{ proposal.state }}</TableCell>
            <TableCell>{{ formatDate(proposal.expires_at || '') }}</TableCell>
            <TableCell class="flex gap-2 justify-end">
              <Button variant="outline" size="sm"><a :href="route('proposals.show', [proposal.id])">View</a></Button>
              <Button :disabled="!(proposal.state === 'draft')" variant="outline" size="sm"><a :href="route('proposals.edit', [proposal.id])">Edit</a></Button>
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
