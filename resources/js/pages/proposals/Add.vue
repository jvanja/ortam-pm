<script setup lang="ts">
import NewClient from '@/components/client/NewClient.vue'; // Import the new component
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
import { Head, router, useForm } from '@inertiajs/vue3';
import { X as DeleteIcon, PlusCircle } from 'lucide-vue-next';
import { AcceptableValue } from 'reka-ui';
import { computed, onMounted, ref } from 'vue';

defineProps<{
  clients: { id: string; company_name: string }[];
  projects: { id: string; type: string }[];
  states: { name: string; value: string }[];
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Proposals', href: '/proposals' }];
const currentStep = ref<'select_client' | 'create_client'>('select_client'); // State for the two-step process
const selectedClientId = ref<string | null>(null);

const form = useForm({
  state: 'draft',
  project_id: getQuery().projectId || '',
  client_id: getQuery().clientId || '',
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
  console.log(form.data());
  return;
  form.total_amount = totalAmount.value;
  form.post(route('invoices.store'));
};

const deleteItem = (index: number) => {
  form.items = form.items.filter((item, i) => i !== index);
};

// Add a new item to the form
const addItem = () => {
  form.items.push({
    label: '',
    quantity: 1,
    unit_price: 0,
  });
};

const formatCurrency = (amount: number) => `${Number(amount).toFixed(2)} USD`; // Format to 2 decimal places

const getItemTotal = (item: { unit_price: number; quantity: number }) => {
  const quantity = Number(item.quantity) || 0;
  const unitPrice = Number(item.unit_price) || 0;
  return quantity * unitPrice;
};

// Computed property to calculate the total amount of all items
const totalAmount = computed(() => form.items.reduce((sum, item) => sum + getItemTotal(item), 0));
const modelChanged = (modelValue: AcceptableValue) => {
  if (typeof modelValue === 'string' && modelValue.split('|')[0] === 'new') {
    const model = modelValue.split('|')[1];
    router.get(route(`${model}.create`) + `?source=proposals.create`);
  }
};

// Handle client creation success from NewClientForm
const handleClientCreated = () => {
  const url = new URL(window.location.href);
  const newClientId = url.searchParams.get('clientId');
  selectedClientId.value = newClientId;
  currentStep.value = 'select_client';
};

// Handle cancel from NewClientForm
const handleCancelCreateClient = () => {
  currentStep.value = 'select_client';
};

onMounted(() => {
  const url = new URL(window.location.href);
  const newClientId = url.searchParams.get('clientId');
  selectedClientId.value = newClientId;
});
</script>

<template>
  <Head title="Create Proposal" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-8">
      <div v-if="currentStep === 'select_client'" class="space-y-6">
        <h1 class="mb-6 text-2xl font-semibold">Create New Proposal</h1>

        <form @submit.prevent="submit" class="space-y-6">
          <div class="mb-4">
            <Input id="description" v-model="form.state" type="textarea" class="hidden" disabled />
          </div>

          <!-- Client -->
          <div class="mb-4">
            <Label for="client_id">Client</Label>
            <Select id="client_id" v-model="selectedClientId" @update:modelValue="modelChanged" required>
              <SelectTrigger>
                <SelectValue placeholder="Proposal client" />
              </SelectTrigger>
              <SelectContent>
                <SelectGroup>
                  <SelectItem v-for="client in clients" :key="client.id" :value="client.id">
                    {{ client.company_name }}
                  </SelectItem>
                  <SelectItem value="new|clients" class="bg-teal-50 p-1 px-4 py-2 dark:bg-neutral-800">
                    <div class="flex items-center gap-2"><PlusCircle class="text-green-700" />Add new client</div>
                  </SelectItem>
                </SelectGroup>
              </SelectContent>
            </Select>
            <InputError class="mt-2" :message="form.errors.client_id" />
          </div>
          <div class="flex justify-center">
            <Button variant="outline" @click="currentStep = 'create_client'">Or Create New Client</Button>
          </div>

          <!-- Project -->
          <div class="mb-4">
            <Label for="project_id">Project</Label>
            <Select id="project_id" v-model="form.project_id" @update:modelValue="modelChanged" required>
              <SelectTrigger>
                <SelectValue placeholder="Proposal project" />
              </SelectTrigger>
              <SelectContent>
                <SelectGroup>
                  <SelectItem v-for="project in projects" :key="project.id" :value="project.id">
                    {{ project.type }}
                  </SelectItem>
                  <SelectItem value="new|projects" class="bg-teal-50 p-1 px-4 py-2 dark:bg-neutral-800">
                    <div class="flex items-center gap-2"><PlusCircle class="text-green-700" />Add new project</div>
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

          <!-- Proposal Items -->
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
                    <CurrencyInput v-model="item.unit_price" class="mt-1" :options="{ currency: 'USD' }" />
                  </TableCell>
                  <TableCell class="whitespace-nowrap px-2 py-4 text-right text-sm">{{ formatCurrency(getItemTotal(item)) }}</TableCell>
                  <TableCell class="whitespace-nowrap px-2 py-4 text-right text-sm">
                    <Button variant="ghost" class="text-red-500" @click="deleteItem(index)" :disabled="form.items.length === 1"
                      ><DeleteIcon width="12"
                    /></Button>
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
                    {{ formatCurrency(totalAmount) }}
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </div>

          <!-- Submit Button -->
          <div class="mt-6 flex items-center justify-end">
            <Button :class="{ 'opacity-25': form.processing }" :disabled="form.processing"> Create Proposal </Button>
          </div>
        </form>
      </div>
      <!-- Step 2: Create New Client -->
      <div v-if="currentStep === 'create_client'" class="space-y-6">
        <h2 class="text-xl font-semibold">Create New Client</h2>
        <NewClient @client-created="handleClientCreated" @cancel="handleCancelCreateClient" back-route="proposals.create" />
      </div>
    </div>
  </AppLayout>
</template>
