<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3'; // Import useForm

// Define props received from the controller
const props = defineProps<{
  clients: { id: string; company_name: string }[];
  projects: { id: string; type: string }[];
  statuses: { name: string; value: string }[]; // Assuming enum cases are passed as objects with name/value
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Invoices', href: '/invoices' }];

// Initialize the form using useForm
const form = useForm({
  amount: 0.0,
  status: 'draft', // Default status
  project_id: '',
  client_id: '',
});

// Handle form submission
const submit = () => {
  form.post(route('invoices.store')); // Assuming you have a route named 'invoices.store'
};

// Prepare options for select inputs
</script>

<template>
  <Head title="Create Invoice" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-8">
      <h1 class="mb-6 text-2xl font-semibold">Create New Invoice</h1>

      <form @submit.prevent="submit" class="max-w-lg rounded-md p-6 shadow-md">
        <!-- Amount -->
        <div class="mb-4">
          <Label for="amount" value="Amount" />
          <Input id="amount" type="number" step="0.01" class="mt-1 block w-full" v-model="form.amount" required autofocus />
          <InputError class="mt-2" :message="form.errors.amount" />
        </div>

        <!-- Status -->
        <div class="mb-4">
          <Label for="status" value="Status" />
          <Select v-model="form.status" required>
            <SelectTrigger>
              <SelectValue placeholder="Invoice status" />
            </SelectTrigger>
            <SelectContent>
              <SelectGroup>
                <SelectItem id="status" v-for="status in statuses" :key="status.value" :value="status">
                  {{ status }}
                </SelectItem>
              </SelectGroup>
            </SelectContent>
          </Select>
          <InputError class="mt-2" :message="form.errors.status" />
        </div>

        <!-- Client -->
        <div class="mb-4">
          <Label for="client_id" value="Client" />
          <Select id="client_id" v-model="form.client_id" required>
            <SelectTrigger>
              <SelectValue placeholder="Invoice client" />
            </SelectTrigger>
            <SelectContent>
              <SelectGroup>
                <SelectItem v-for="client in clients" :key="client.id" :value="client.company_name">
                  {{ client.company_name }}
                </SelectItem>
              </SelectGroup>
            </SelectContent>
          </Select>
          <InputError class="mt-2" :message="form.errors.client_id" />
        </div>

        <!-- Project -->
        <div class="mb-4">
          <Label for="project_id" value="Project" />
          <Select id="client_id" v-model="form.project_id" required>
            <SelectTrigger>
              <SelectValue placeholder="Invoice project" />
            </SelectTrigger>
            <SelectContent>
              <SelectGroup>
                <SelectItem v-for="project in projects" :key="project.id" :value="project.type">
                  {{ project.type }}
                </SelectItem>
              </SelectGroup>
            </SelectContent>
          </Select>
          <InputError class="mt-2" :message="form.errors.project_id" />
        </div>

        <!-- Submit Button -->
        <div class="mt-6 flex items-center justify-end">
          <Button :class="{ 'opacity-25': form.processing }" :disabled="form.processing"> Create Invoice </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
