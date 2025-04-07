<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, User } from '@/types';
import { Head } from '@inertiajs/vue3';
// Removed unused imports: useForm, toast

// Renamed prop from 'user' to 'employees' and changed type to User[]
const props = defineProps<{ employees: User[] }>();

// Updated breadcrumbs for the employee list view
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Employees', href: route('employees.index') }, // Assuming you have a named route 'employees.index'
];

</script>
<template>
  <!-- Updated Head title -->
  <Head title="Employees" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <!-- Added container and list to display employees -->
    <div class="container mx-auto px-4 py-8">
      <h1 class="mb-4 text-2xl font-semibold">Employee List</h1>
      <div v-if="props.employees.length > 0" class="overflow-x-auto rounded border bg-card text-card-foreground shadow">
        <table class="min-w-full divide-y divide-border">
          <thead class="bg-muted/50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">
                Name
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">
                Email
              </th>
              <!-- Add more columns as needed -->
            </tr>
          </thead>
          <tbody class="divide-y divide-border bg-background">
            <tr v-for="employee in props.employees" :key="employee.id">
              <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-foreground">
                {{ employee.name }}
              </td>
              <td class="whitespace-nowrap px-6 py-4 text-sm text-muted-foreground">
                {{ employee.email }}
              </td>
              <!-- Add more cells corresponding to columns -->
            </tr>
          </tbody>
        </table>
      </div>
      <div v-else class="mt-4 text-center text-muted-foreground">
        No employees found in this organization.
      </div>
    </div>
  </AppLayout>
</template>
