<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import ObjectList from '@/components/ObjectList.vue';
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

const objects = props.clients.map((client) => {
  return { id: client.id!, name: client.company_name };
});

console.log(objects);
</script>

<template>
  <Head title="Clients" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="flex flex-col gap-4 p-4">
        <Heading title="Clients" description="These are your latest clients" />
        <ObjectList :objects="objects" type="clients" />
        <div class="grid gap-1">
          <Label for="name">Search clients</Label>
          <Input id="search" class="mt-1 block w-full" placeholder="client name" />
        </div>
        <Button>Add new</Button>
      </div>
    </div>
  </AppLayout>
</template>
