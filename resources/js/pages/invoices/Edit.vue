<script setup lang="ts">
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Client, Invoice, Project } from '@/types'; // Assuming Invoice type is defined in types
import { Head, router, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

// Define props to receive invoice data from the controller
const props = defineProps<{ invoice: Invoice & { client: Client; project: Project } }>();

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
  form.put(route('invoices.update', [props.invoice.id]), {
    // Assuming route('invoices.update') exists
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
  router.delete(route('invoices.destroy', [props.invoice.id]), {
    // Assuming route('invoices.destroy') exists
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
  <Head :title="`Invoice number: ${invoice.invoice_number}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6">
      <Table class="mb-4 min-w-full divide-y divide-gray-200 border">
        <TableHeader>
          <TableRow>
            <TableHead scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">client</TableHead>
            <TableHead scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">project</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody class="divide-y divide-gray-200">
          <TableRow>
            <TableCell class="whitespace-nowrap px-6 py-4 text-sm">{{ invoice.client.company_name }}</TableCell>
            <TableCell class="whitespace-nowrap px-6 py-4 text-sm">{{ invoice.project.type }}</TableCell>
          </TableRow>
        </TableBody>
      </Table>

      <form @submit.prevent="updateInvoice" class="space-y-6 rounded border bg-card p-6 text-card-foreground shadow">
        <div>
          <Label for="invoice_number">Invoice Number</Label>
          <Input id="invoice_number" :model-value="invoice.invoice_number" disabled class="mt-1 bg-muted" />
        </div>

        <!-- Amount (Editable) -->
        <div>
          <Label for="amount">Amount</Label>

          <div class="flex w-full max-w-sm items-center gap-2">
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
            />{{ invoice.project.currency }}
          </div>
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
                <AlertDialogTitle>Are you sure?</AlertDialogTitle>
                <AlertDialogDescription>
                  This action cannot be undone. This will permanently delete the invoice #{{ invoice.invoice_number }}.
                </AlertDialogDescription>
              </AlertDialogHeader>
              <AlertDialogFooter>
                <AlertDialogCancel :disabled="form.processing">Cancel</AlertDialogCancel>
                <AlertDialogAction @click="deleteInvoice" :disabled="form.processing"> Yes, delete invoice </AlertDialogAction>
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
