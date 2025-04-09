<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from '@/components/ui/alert-dialog';
import type { BreadcrumbItem, Invoice } from '@/types'; // Assuming Invoice type is defined in types

// Define props to receive invoice data from the controller
const props = defineProps<{ invoice: Invoice }>();

// Breadcrumbs for navigation
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Invoices', href: route('invoices.index') }, // Assuming route('invoices.index') exists
  { title: `Invoice #${props.invoice.invoice_number}`, href: '' },
];

// Form helper for updating the invoice
const form = useForm({
  amount: props.invoice.amount,
  status: props.invoice.status,
});

// Available invoice statuses (adjust as needed)
const invoiceStatuses = ['draft', 'sent', 'paid', 'cancelled', 'overdue'];

// Function to handle saving changes
function updateInvoice() {
  form.put(route('invoices.update', [ props.invoice.id ]), { // Assuming route('invoices.update') exists
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Invoice updated successfully!');
    },
    onError: (errors) => {
      const firstError = Object.values(errors)[0];
      toast.error(firstError || 'Failed to update invoice. Please try again.');
    },
  });
}

// Function to handle deleting the invoice
function deleteInvoice() {
  router.delete(route('invoices.destroy', [ props.invoice.id ]), { // Assuming route('invoices.destroy') exists
    preserveScroll: true,
    onSuccess: () => {
      // Redirect or show success message. Redirecting to index is common.
      toast.success('Invoice deleted successfully!');
      // router.visit(route('invoices.index')); // Optional: redirect after delete
    },
    onError: (errors) => {
      const firstError = Object.values(errors)[0];
      toast.error(firstError || 'Failed to delete invoice. Please try again.');
    },
  });
}
</script>

<template>
  <Head :title="`Invoice #${invoice.invoice_number}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6">
      <h1 class="mb-4 text-2xl font-semibold">Invoice #{{ invoice.invoice_number }}</h1>

      <form @submit.prevent="updateInvoice" class="space-y-6 rounded border bg-card p-6 text-card-foreground shadow">
        <!-- Invoice Number (Read-only) -->
        <div>
          <Label for="invoice_number">Invoice Number</Label>
          <Input id="invoice_number" :model-value="invoice.invoice_number" disabled class="mt-1 bg-muted" />
        </div>

        <!-- Amount (Editable) -->
        <div>
          <Label for="amount">Amount</Label>
          <Input
            id="amount"
            v-model="form.amount"
            type="number"
            step="0.01"
            placeholder="Enter amount"
            required
            class="mt-1"
            :disabled="form.processing"
            aria-describedby="amount-error"
          />
          <p v-if="form.errors.amount" id="amount-error" class="mt-1 text-sm text-destructive">
            {{ form.errors.amount }}
          </p>
        </div>

        <!-- Status (Editable Dropdown) -->
        <div>
          <Label for="status">Status</Label>
          <Select v-model="form.status" :disabled="form.processing">
            <SelectTrigger id="status" class="mt-1" aria-describedby="status-error">
              <SelectValue placeholder="Select status" />
            </SelectTrigger>
            <SelectContent>
              <SelectGroup>
                <SelectItem v-for="statusOption in invoiceStatuses" :key="statusOption" :value="statusOption">
                  {{ statusOption.charAt(0).toUpperCase() + statusOption.slice(1) }}
                </SelectItem>
              </SelectGroup>
            </SelectContent>
          </Select>
           <p v-if="form.errors.status" id="status-error" class="mt-1 text-sm text-destructive">
            {{ form.errors.status }}
          </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between pt-4">
          <!-- Delete Button with Confirmation -->
          <AlertDialog>
            <AlertDialogTrigger as-child>
              <Button type="button" variant="destructive" :disabled="form.processing">Delete Invoice</Button>
            </AlertDialogTrigger>
            <AlertDialogContent>
              <AlertDialogHeader>
                <AlertDialogTitle>Are you absolutely sure?</AlertDialogTitle>
                <AlertDialogDescription>
                  This action cannot be undone. This will permanently delete the invoice #{{ invoice.invoice_number }}.
                </AlertDialogDescription>
              </AlertDialogHeader>
              <AlertDialogFooter>
                <AlertDialogCancel :disabled="form.processing">Cancel</AlertDialogCancel>
                <AlertDialogAction @click="deleteInvoice" :disabled="form.processing">
                  Yes, delete invoice
                </AlertDialogAction>
              </AlertDialogFooter>
            </AlertDialogContent>
          </AlertDialog>

          <!-- Save Button -->
          <Button type="submit" :disabled="form.processing || !form.isDirty">
            <span v-if="form.processing">Saving...</span>
            <span v-else>Save Changes</span>
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
