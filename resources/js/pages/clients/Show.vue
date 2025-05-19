<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Client, Invoice } from '@/types';
import ObjectList from '@/components/ObjectList.vue';
import InvoicesList from '@/components/invoice/List.vue';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { computed } from 'vue';

// Define props including projects
const props = defineProps<{
  client: Client;
  invoices: Invoice[];
  projects: { id: string; name: string }[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Clients', href: '/clients' },
  { title: props.client.company_name, href: '' },
];
const client = { ...props.client };
const form = useForm(client);
const submit = () => {
  form.patch(route('clients.update', [props.client.id]), {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Report has been updated successfully!', { style: { background: '#6ee7b7', color: '#000' } });
    },
  });
};
const clientAddress = computed(() => JSON.parse(props.client.address))
</script>
<template>
  <Head title="Client" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="flex flex-col gap-4 p-4">
        <Card>
          <CardHeader>
            <CardTitle>{{ client.company_name }}</CardTitle>
            <CardDescription>
              {{ clientAddress.street }}<br>
              {{ clientAddress.city }}<br>
              {{ clientAddress.state }} {{ clientAddress.postal_code }}<br>
              {{ clientAddress.country }}
            </CardDescription>
          </CardHeader>
          <CardContent>
            <form @submit.prevent="submit" class="space-y-6">
              <div class="grid w-full items-center gap-4">
                <div class="flex flex-col space-y-1.5">
                  <Label htmlFor="contact">Contact</Label>
                  <Input id="contact" v-model="form.contact_person" />
                </div>
                <div class="flex flex-col space-y-1.5">
                  <Label htmlFor="email">Email address</Label>
                  <Input id="email" v-model="form.email" />
                </div>
              </div>
              <div class="flex w-full justify-end">
                <Button :disabled="form.processing">Save</Button>
              </div>
            </form>
          </CardContent>
        </Card>

        <!-- Projects Card -->
        <Card v-if="props.projects && props.projects.length > 0">
          <CardHeader>
            <CardTitle>Associated Projects</CardTitle>
          </CardHeader>
          <CardContent>
            <ObjectList :objects="props.projects" type="projects" :can-delete="false" />
          </CardContent>
        </Card>
        <!-- End Projects Card -->

        <!-- Invoices -->
        <InvoicesList :invoices currency="EUR"></InvoicesList>
      </div>
    </div>
  </AppLayout>
</template>
