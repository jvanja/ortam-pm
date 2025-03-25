<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { type clientsEntity } from '@/types/DatabaseModels';
import { Head } from '@inertiajs/vue3';

const props = defineProps<{
  clients: clientsEntity[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Clients',
    href: '/clients',
  },
];

console.log(props.clients);
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="flex flex-col gap-4 p-4">
        <Heading title="Clients" description="These are your latest clients" />
        <div class="flex flex-col gap-2">
          <div v-for="client in clients" :key="client.id" class="flex justify-between rounded-lg bg-neutral-100 p-1 dark:bg-neutral-800">
            <div class="px-4 py-2 text-sm font-medium">{{ client.company_name }}</div>
            <div class="flex gap-2">
              <Button variant="default"><a :href="`/clients/${client.id}`">Edit</a></Button>
              <Button variant="destructive">Delete</Button>
            </div>
          </div>
        </div>
        <div class="grid gap-1">
          <Label for="name">Search clients</Label>
          <Input id="search" class="mt-1 block w-full" placeholder="Client name" />
        </div>
        <Button>Add new</Button>
      </div>
    </div>
  </AppLayout>
</template>
