<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import CurrencyInput from '@/components/invoice/CurrencyInput.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { getQuery } from '@/lib/utils';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { PlusCircle, X as DeleteIcon } from 'lucide-vue-next';
import { computed } from 'vue';
import { formatCurrency } from '@/lib/utils';
import { toast } from 'vue-sonner';

const props = defineProps<{
  clients: { id: string; company_name: string }[];
  projects: { id: string; type: string, currency: string }[];
  states: { name: string; value: string }[];
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Invoices', href: '/invoices' }];
const projectId = getQuery().projectId || null;
const clientId = getQuery().clientId || null;
const currency = props.projects.filter((project) => project.id === projectId)[0].currency;

const form = useForm({
  state: 'draft',
  project_id: projectId,
  client_id: clientId,
  description: '',
  total_amount: 0,
  items: [
    {
      label: '',
      quantity: 1,
      unit_price: 0,
    },
  ],
});

// Handle form submission
const submit = () => {
  form.total_amount = totalAmount.value;
  form.post(route('invoices.store'), {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Invoice created successfully!', {
        style: { background: '#6ee7b7', color: '#000' },
      });
      form.reset(); // Optionally reset form and step after success
    },
    onError: () => {
      toast.error('Failed to save invoice.');
    },
  });
};

const deleteItem = (index: number) => {
  form.items = form.items.filter((_, i) => i !== index);
}

// Add a new item to the form
const addItem = () => {
  form.items.push({
    label: '',
    quantity: 1,
    unit_price: 0,
  });
};

const getItemTotal = (item: { unit_price: number; quantity: number }) => {
  const quantity = Number(item.quantity) || 0;
  const unitPrice = Number(item.unit_price) || 0;
  return quantity * unitPrice;
};

// Computed property to calculate the total amount of all items
const totalAmount = computed(() => form.items.reduce((sum, item) => sum + getItemTotal(item), 0));
</script>

<template>
  <Head title="Create Invoice" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-8">
      <h1 class="mb-6 text-2xl font-semibold">Create New Invoice</h1>

      <form @submit.prevent="submit" class="space-y-6 rounded border bg-card p-6 text-card-foreground shadow">
        <!-- Invoice state -->
        <div class="mb-4">
          <Label for="state">Invoice state</Label>
          <Select v-model="form.state" required>
            <SelectTrigger>
              <SelectValue placeholder="Invoice state" />
            </SelectTrigger>
            <SelectContent>
              <SelectGroup>
                <SelectItem v-for="state in states" :key="state.value" :value="state">
                  {{ state }}
                </SelectItem>
              </SelectGroup>
            </SelectContent>
          </Select>
          <InputError class="mt-2" :message="form.errors.state" />
        </div>

        <!-- Client -->
        <div class="mb-4">
          <Label for="client_id">Client</Label>
          <Select id="client_id" v-model="form.client_id" required :disabled="clientId && !isNaN(clientId)">
            <SelectTrigger>
              <SelectValue placeholder="Invoice client" />
            </SelectTrigger>
            <SelectContent>
              <SelectGroup>
                <SelectItem v-for="client in clients" :key="client.id" :value="client.id">
                  {{ client.company_name }}
                </SelectItem>
              </SelectGroup>
            </SelectContent>
          </Select>
          <InputError class="mt-2" :message="form.errors.client_id" />
        </div>

        <!-- Project -->
        <div class="mb-4">
          <Label for="project_id">Project</Label>
          <Select id="project_id" v-model="form.project_id" required :disabled="projectId && !isNaN(projectId)">
            <SelectTrigger>
              <SelectValue placeholder="Invoice project" />
            </SelectTrigger>
            <SelectContent>
              <SelectGroup>
                <SelectItem v-for="project in projects" :key="project.id" :value="project.id">
                  {{ project.type }}
                </SelectItem>
              </SelectGroup>
            </SelectContent>
          </Select>
          <InputError class="mt-2" :message="form.errors.project_id" />
        </div>

        <!-- Description -->
        <div class="mb-4">
          <Label for="description">Description</Label>
          <Input id="description" v-model="form.description" type="textarea" required />
          <InputError class="mt-2" :message="form.errors.description" />
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
              <TableRow v-for="(item, index) in form.items" :key="index">
                <TableCell class="px-2 py-4 text-sm">
                  <Input v-model="item.label" class="mt-1" />
                </TableCell>
                <TableCell class="px-2 py-4 text-sm">
                  <Input v-model="item.quantity" class="mt-1" type="number" min="1" />
                </TableCell>
                <TableCell class="px-2 py-4 text-sm">
                  <CurrencyInput v-model="item.unit_price" class="mt-1" :options="{ currency }" />
                </TableCell>
                <TableCell class="whitespace-nowrap px-2 py-4 text-right text-sm">{{ formatCurrency(getItemTotal(item), currency) }}</TableCell>
                <TableCell class="whitespace-nowrap px-2 py-4 text-right text-sm">
                  <Button variant="ghost" class="text-red-500" @click="deleteItem(index)" :disabled="form.items.length === 1"><DeleteIcon width="12"/></Button>
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
                <TableCell colspan="3" class="font-semibold">Total</TableCell>
                <TableCell class="text-normal whitespace-nowrap px-2 py-4 text-right font-semibold">
                  {{ formatCurrency(totalAmount, currency) }}
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>

        <!-- Submit Button -->
        <div class="mt-6 flex items-center justify-end">
          <Button :class="{ 'opacity-25': form.processing }" :disabled="form.processing"> Create Invoice </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
