<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import type { clientsEntity } from '@/types/DatabaseModels';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Toaster } from '@/components/ui/sonner';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps<{ client: clientsEntity }>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Clients', href: '/clients' },
  { title: props.client.company_name, href: '' },
];

const client = { ...props.client };
const form = useForm(client);
const submit = () => {
  console.log(form);
  // /* @ts-expect-error This is fine */
  /* form.patch(route('projects.update', props.project.id), { */
  /*   preserveScroll: true, */
  /*   onSuccess: () => { */
  /*     toast.success('Report has been updated successfully!', { style: { background: '#6ee7b7', color: '#000' } }); */
  /*   }, */
  /* }); */
};
</script>
<template>
  <Head title="Project" />

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
                  <Input id="contact" v-model="client.contact_person" />
                </div>
                <div class="flex flex-col space-y-1.5">
                  <Label htmlFor="email">Email address</Label>
                  <Input id="email" v-model="client.email" />
                </div>
              </div>
              <div class="flex w-full justify-end">
                <Button :disabled="form.processing">Save</Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </div>
    <Toaster />
  </AppLayout>
</template>
