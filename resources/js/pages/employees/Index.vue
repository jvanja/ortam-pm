<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, User, Invitation } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { useDateFormat } from '@vueuse/core'

type UserRole = User & { role: string };

// Renamed prop from 'user' to 'employees' and changed type to User[]
const props = defineProps<{ employees: UserRole[]; invitees: Invitation[] }>();

// Updated breadcrumbs for the employee list view
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Employees', href: route('employees.index') }, // Assuming you have a named route 'employees.index'
];

// Form for inviting a new employee
const inviteForm = useForm({
  email: '',
});

// Function to handle the invitation submission
function submitInvite() {
  inviteForm.post(route('employees.invite'), {
    preserveScroll: true,
    onSuccess: (msg) => {
      console.log(msg);
      toast.success('Invitation sent successfully!');
      inviteForm.reset();
    },
    onError: (errors) => {
      // Display the first error message, or a generic one
      const firstError = Object.values(errors)[0];
      toast.error(firstError || 'Failed to send invitation. Please try again.');
    },
  });
}
</script>
<template>
  <!-- Updated Head title -->
  <Head title="Employees" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <!-- Employee List Section -->
    <div class="p-6">
      <h1 class="mb-4 text-2xl font-semibold">Employee List</h1>
      <div v-if="props.employees.length > 0" class="overflow-x-auto rounded border bg-card text-card-foreground shadow">
        <table class="min-w-full divide-y divide-border">
          <thead class="bg-muted/50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Name</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Email</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Role</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Action</th>
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
              <td class="whitespace-nowrap px-6 py-4 text-sm text-muted-foreground">
                {{ employee.role }}
              </td>
              <td class="whitespace-nowrap px-6 py-4 text-sm text-muted-foreground">
                <Button variant="default"><a :href="`/users/${employee.id}`" class="text-sm hover:text-primary">Edit</a></Button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-else class="mt-4 text-center text-muted-foreground">No employees found in this organization.</div>
    </div>
    <div class="p-6">
      <div v-if="props.invitees.length > 0" class="overflow-x-auto rounded border bg-card text-card-foreground shadow">
        <table class="min-w-full divide-y divide-border">
          <thead class="bg-muted/50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Invitations</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Expires</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border bg-background">
            <tr v-for="invitation in props.invitees" :key="invitation.id">
              <td class="whitespace-nowrap px-6 py-4 text-sm text-muted-foreground">{{ invitation.email }}</td>
              <td class="whitespace-nowrap px-6 py-4 text-sm text-muted-foreground">{{ useDateFormat(invitation.expires_at, 'YYYY-MM-DD') }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Invite Employee Section -->
    <div class="p-6">
      <h2 class="mb-4 text-xl font-semibold">Invite new employee</h2>
      <form @submit.prevent="submitInvite">
        <div class="space-y-4">
          <div>
            <label for="email" class="mb-2 block text-sm font-medium text-foreground">Email Address</label>
            <Input
              id="email"
              v-model="inviteForm.email"
              type="email"
              placeholder="employee@example.com"
              required
              :disabled="inviteForm.processing"
              aria-describedby="email-error"
            />
            <p v-if="inviteForm.errors.email" id="email-error" class="mt-1 text-sm text-destructive">
              {{ inviteForm.errors.email }}
            </p>
          </div>
          <Button type="submit" :disabled="inviteForm.processing" class="w-full">
            <span v-if="inviteForm.processing">Sending...</span>
            <span v-else>Send Invitation</span>
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
