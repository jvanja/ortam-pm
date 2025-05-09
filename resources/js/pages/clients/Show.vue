<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Client, Invoice } from '@/types';
import ObjectList from '@/components/ObjectList.vue'; // Import ObjectList

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { useDateFormat } from '@vueuse/core';

// Define props including projects
const props = defineProps<{
  client: Client;
  invoices: Invoice[];
  projects: { id: string; name: string }[]; // Add projects prop
}>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Clients', href: '/clients' },
  { title: props.client.company_name, href: '' },
];

console.log(props)
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
</script>
<template>
  <Head title="Client" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="flex flex-col gap-4 p-4">
        <Card>
          <CardHeader>
            <CardTitle>{{ client.company_name }}</CardTitle>
            <CardDescription>{{ client.address }}</CardDescription>
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

        <Card>
          <CardHeader>
            <CardTitle>Invoices</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th
                      scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Invoice Number
                    </th>
                    <th
                      scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Date
                    </th>
                    <th
                      scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Amount
                    </th>
                    <th
                      scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"/>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="invoice in props.invoices" :key="invoice.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">{{ invoice.invoice_number }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ useDateFormat(invoice.created_at, 'YYYY-MM-DD') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">{{ invoice.amount }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">{{ invoice.status ? 'Paid' : 'Not paid'}}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">
                        <a :href="`/invoices/${invoice.id}`">Edit</a>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>
