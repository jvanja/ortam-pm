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
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Client, Invoice, InvoiceItem, Project } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

const props = defineProps<{ invoice: Invoice; invoice_items: InvoiceItem[]; client: Client; project: Project }>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Invoices', href: route('invoices.index') }, // Assuming route('invoices.index') exists
  { title: `Invoice #${props.invoice.serial_number}`, href: '' },
];

const form = useForm({
  amount: props.invoice.total_amount!,
  state: props.invoice.state,
});

const invoiceStatuses = ['draft', 'sent', 'paid', 'cancelled', 'overdue'];

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

function deleteInvoice() {
  router.delete(route('invoices.destroy', [props.invoice.id]), {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Invoice deleted successfully!');
    },
    onError: (errors) => {
      const firstError = Object.values(errors)[0];
      toast.error(firstError || 'Failed to delete invoice. Please try again.');
    },
  });
}

const getItemTotal = (item: InvoiceItem) => {
  return formatCurrency(Number(item.unit_price) * (item.quantity ? item.quantity : 1));
}
const formatCurrency = (amount: number) => `${Number(amount) / 100} ${props.invoice.currency}`;
const getTotalAmount = props.invoice_items.reduce((acc, item) => acc + Number(item.unit_price) / 1, 0);
</script>

<template>
  <Head :title="`Invoice number: ${invoice.serial_number}`" />

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
            <TableCell class="whitespace-nowrap px-6 py-4 text-sm">{{ client.company_name }}</TableCell>
            <TableCell class="whitespace-nowrap px-6 py-4 text-sm">{{ project.type }}</TableCell>
          </TableRow>
        </TableBody>
      </Table>

      <form @submit.prevent="updateInvoice" class="space-y-6 rounded border bg-card p-6 text-card-foreground shadow">
        <div>
          <Label for="serial_number">Invoice Number</Label>
          <Input id="serial_number" :model-value="invoice.serial_number" disabled class="mt-1 bg-muted" />
        </div>

        <!-- Invoice Items -->
        <div class="flex flex-col gap-4">
          <Table class="min-w-full divide-y divide-gray-200">
            <TableHeader>
              <TableRow class="text-gray-500">
                <TableHead class="whitespace-nowrap border-b py-2 pr-2 text-left text-xs font-normal"> Description </TableHead>
                <TableHead class="whitespace-nowrap border-b p-2 text-left text-xs font-normal"> Quantity </TableHead>
                <TableHead class="whitespace-nowrap border-b p-2 text-left text-xs font-normal">Unit price</TableHead>
                <TableHead class="p-0"></TableHead>
                <TableHead class="whitespace-nowrap border-b py-2 pl-2 text-right text-xs font-normal"> Amount </TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="item in invoice_items" :key="item.id">
                <TableCell class="whitespace-nowrap px-6 py-4 text-sm">{{ item.label }}</TableCell>
                <TableCell class="whitespace-nowrap px-6 py-4 text-sm">{{ item.quantity }}</TableCell>
                <TableCell class="whitespace-nowrap px-6 py-4 text-sm">{{ formatCurrency(Number(item.unit_price!)) }}</TableCell>
                <TableCell class="whitespace-nowrap px-6 py-4 text-sm"></TableCell>
                <TableCell class="whitespace-nowrap px-6 py-4 text-sm">{{ getItemTotal(item) }}</TableCell>
              </TableRow>
              <TableRow>
                <TableCell colspan="4" class="font-semibold">Total</TableCell>
                <TableCell class="whitespace-nowrap px-6 py-4 text-normal font-semibold"> {{ formatCurrency(Number(invoice.total_amount)) }}</TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>

        <!-- state (Editable Dropdown) -->
        <div>
          <Label for="state">State</Label>
          <Select v-model="form.state" :disabled="form.processing">
            <SelectTrigger id="state" class="mt-1" aria-describedby="state-error">
              <SelectValue placeholder="Select state" />
            </SelectTrigger>
            <SelectContent>
              <SelectGroup>
                <SelectItem v-for="statusOption in invoiceStatuses" :key="statusOption" :value="statusOption">
                  {{ statusOption.charAt(0).toUpperCase() + statusOption.slice(1) }}
                </SelectItem>
              </SelectGroup>
            </SelectContent>
          </Select>
          <p v-if="form.errors.state" id="state-error" class="mt-1 text-sm text-destructive">
            {{ form.errors.state }}
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
                  This action cannot be undone. This will permanently delete the invoice #{{ invoice.serial_number }}.
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
