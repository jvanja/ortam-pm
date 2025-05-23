<script setup lang="ts">
import CurrencyInput from '@/components/invoice/CurrencyInput.vue';
import { PlusCircle, X as DeleteIcon } from 'lucide-vue-next';
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
import type { BreadcrumbItem, Client, Invoice, Project } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { toast } from 'vue-sonner';

type InvoiceItem = { id: number; label: string; quantity: number; unit_price: number; description: string };
const props = defineProps<{ invoice: Invoice; invoice_items: InvoiceItem[]; client: Client; project: Project; invoice_states: [] }>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Invoices', href: '' },
  { title: `Invoice #${props.invoice.serial_number}`, href: '' },
];

const items = ref([...props.invoice_items]);

const form = useForm({
  total_amount: props.invoice.total_amount,
  state: props.invoice.state,
  items: items.value,
});

const formatCurrency = (amount: number) => new Intl.NumberFormat('us-EN', { style: 'currency', currency: props.invoice.currency! }).format(amount);
const getItemTotal = (item: InvoiceItem) => formatCurrency(Number(item.unit_price) * (item.quantity ? item.quantity : 1));
const getInvoiceTotal = () => {
  const total = form.items.reduce((acc, item) => acc + Number(item.unit_price) * (item.quantity ? item.quantity : 1), 0);
  return total;
};

const invoiceStatuses = props.invoice_states;

function updateInvoice() {
  form.transform((data) => ({
    ...data,
    total_amount: 1
  })).put(route('invoices.update', [props.invoice.id]), {
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

const deleteItem = (id: number) => {
  items.value = items.value.filter((item) => item.id !== id);
  form.items = items.value.filter((item) => item.id !== id);
}

const addItem = () => {
  form.items.push({
    id: 0, // added this to silence the ts error; id is added in the backend
    label: '',
    description: '',
    quantity: 1,
    unit_price: 0,
  });
};
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
            <TableCell class="px-6 py-4 text-sm">{{ client.company_name }}</TableCell>
            <TableCell class="px-6 py-4 text-sm">{{ project.type }}</TableCell>
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
                <TableHead class="border-b p-2 text-left text-xs font-normal" style="width: 60%">Description</TableHead>
                <TableHead class="border-b p-2 text-left text-xs font-normal">Quantity</TableHead>
                <TableHead class="border-b p-2 text-left text-xs font-normal">Unit price</TableHead>
                <TableHead class="border-b p-2 text-right text-xs font-normal">Amount</TableHead>
                <TableHead class="border-b p-2 text-right text-xs font-normal"></TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="(item, index) in form.items" :key="item.id">
                <TableCell class="px-2 py-4 text-sm">
                  <Input v-model="form.items[index].label!" class="mt-1" />
                </TableCell>
                <TableCell class="px-2 py-4 text-sm">
                  <Input v-model="form.items[index].quantity!" class="mt-1" type="number" />
                </TableCell>
                <TableCell class="px-2 py-4 text-sm">
                  <CurrencyInput v-model="form.items[index].unit_price" class="mt-1" :options="{ currency: invoice.currency, precision: 2 }" />
                </TableCell>
                <TableCell class="whitespace-nowrap px-2 py-4 text-right text-sm">
                  {{ getItemTotal(form.items[index]) }}
                </TableCell>
                <TableCell class="whitespace-nowrap px-2 py-4 text-right text-sm">
                  <Button variant="ghost" class="text-red-500" @click="deleteItem(item.id)" :disabled="items.length === 1"><DeleteIcon width="12"/></Button>
                </TableCell>
              </TableRow>
              <TableRow>
                <TableCell colspan="5" class="px-2 py-4">
                  <div class="rounded-lg border-2 border-dashed border-teal-400 bg-teal-50 p-1 px-4 py-2 dark:bg-neutral-800">
                    <button type="button" @click="addItem" class="flex w-full flex-1 items-center gap-2 text-sm text-green-700">
                      <PlusCircle class="text-green-700" />
                      Add new item
                    </button>
                  </div>
                </TableCell>
              </TableRow>
              <TableRow>
                <TableCell colspan="4" class="font-semibold">Total</TableCell>
                <TableCell class="text-normal whitespace-nowrap px-2 py-4 font-semibold"> {{ formatCurrency(getInvoiceTotal()) }}</TableCell>
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
                  {{ (statusOption as string).charAt(0).toUpperCase() + (statusOption as string).slice(1) }}
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
