<script setup lang="ts">
import NewClient from '@/components/client/NewClient.vue';
import InputError from '@/components/InputError.vue';
import Tiptap from '@/components/Tiptap.vue';
import { Button } from '@/components/ui/button';
import {
  CalendarDate,
  DateFormatter,
  type DateValue,
  getLocalTimeZone,
} from '@internationalized/date'
import { Calendar as CalendarIcon } from 'lucide-vue-next'
import { Calendar } from '@/components/ui/calendar';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { cn, getQuery } from '@/lib/utils';
import type { BreadcrumbItem } from '@/types';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import currencies from 'currency-codes';
import { PlusCircle } from 'lucide-vue-next';
import { AcceptableValue } from 'reka-ui';
import { computed, onMounted, ref } from 'vue';
import { toast } from 'vue-sonner';

defineProps<{
  clients: { id: string; company_name: string }[];
  projects: { id: string; type: string }[];
  states: { name: string; value: string }[];
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Proposals', href: '/proposals' }];
const currentStep = ref<'select_client' | 'create_client'>('select_client'); // State for the two-step process
const selectedClientId = ref<string>('');
const page = usePage();
// @ts-expect-error The user is added in HandleInertiaRequest and always exists
const org_id = computed(() => page.props.auth.user.organization_id);

const form = useForm({
  state: 'draft',
  title: '',
  client_id: '',
  description: `
<h1>Scope</h1>Below is a high-level breakdown of deliverables, their estimated times.
<h1>Deliverables</h1>Consequat culpa pariatur proident elit.
<ol>
<li>Lorem ipsum dolor sit amet</li>
<li>consectetur adipisicing elit</li>
<li>sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</li>
</ol>
<h1>Payment</h1>Consequat culpa pariatur proident elit. Deserunt eu sit cupidatat sint Lorem proident Lorem laboris dolore eiusmod proident laborum ea minim consequat. Laboris do ad aute non. Labore fugiat velit pariatur et culpa do adipisicing enim sit exercitation. Irure sunt aliqua et est qui ipsum cillum eu laborum dolor reprehenderit do sit laborum nulla. In fugiat esse proident eiusmod laborum laboris quis nostrud voluptate cillum ullamco magna excepteur.
`,
  currency: 'USD',
  subtotal_amount: 0,
  tax_amount: 0,
  total_amount: 0,
  expires_at: '',
  organization_id: org_id.value,
});

// Handle form submission
const submit = () => {
  form.expires_at = expires_date.value!.toDate(getLocalTimeZone()).toISOString()
  form.total_amount = totalAmount.value;
  form.client_id = selectedClientId.value;
  form.post(route('proposals.store'), {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Proposal created successfully!', {
        style: { background: '#6ee7b7', color: '#000' },
      });
      form.reset(); // Optionally reset form and step after success
    },
    onError: () => {
      toast.error('Failed to save proposal.');
    },
  });
};

/* ==========================================================================
 Amount calculations
 ========================================================================== */
const formatCurrency = (amount: number) => `${Number(amount).toFixed(2)} ${form.currency}`; // Format to 2 decimal places
const totalAmount = computed(() => form.subtotal_amount + (form.subtotal_amount * form.tax_amount) / 100);

/* ==========================================================================
 Client selection and creation
 ========================================================================== */
const modelChanged = (modelValue: AcceptableValue) => {
  if (typeof modelValue === 'string' && modelValue.split('|')[0] === 'new') {
    const model = modelValue.split('|')[1];
    router.get(route(`${model}.create`) + `?source=proposals.create`);
  }
};

// Handle client creation success from NewClientForm
const handleClientCreated = () => {
  const newClientId: string = getQuery().clientId || '';
  selectedClientId.value = newClientId;
  currentStep.value = 'select_client';
};

// Handle cancel from NewClientForm
const handleCancelCreateClient = () => {
  currentStep.value = 'select_client';
};

/* ==========================================================================
 Calendar
 ========================================================================== */
const df = new DateFormatter('en-US', {
  dateStyle: 'long',
})

const expires_date = ref<DateValue>()
const today = new Date()

/* ==========================================================================
 Lifecycle hooks
 ========================================================================== */
onMounted(() => {
  const newClientId: string = getQuery().clientId || '';
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
            <Input id="state" v-model="form.state" type="textarea" class="hidden" disabled />
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
            <Label for="title">Proposal title</Label>
            <Input id="title" v-model="form.title" required />
            <InputError class="mt-2" :message="form.errors.title" />
          </div>

          <!-- Description -->
          <div class="mb-4">
            <Label>Description</Label>
            <Tiptap v-model="form.description" />
            <InputError class="mt-2" :message="form.errors.description" />
          </div>

          <!-- Proposal Amount -->
          <div class="flex flex-col gap-4">
            <Table class="min-w-full divide-y divide-gray-200">
              <TableHeader>
                <TableRow class="text-gray-500">
                  <TableHead class="border-b p-2 text-left text-xs font-normal">Sub total</TableHead>
                  <TableHead class="border-b p-2 text-left text-xs font-normal" width="10%">Currency</TableHead>
                  <TableHead class="border-b p-2 text-left text-xs font-normal" width="80">Tax amount</TableHead>
                  <TableHead class="border-b p-2 text-right text-xs font-normal">Total</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow>
                  <TableCell class="px-2 py-4 text-sm">
                    <Input id="subtotal" v-model="form.subtotal_amount" class="mt-1" type="number" min="0" />
                  </TableCell>
                  <TableCell class="px-2 py-4 text-sm">
                    <Select id="currency" defaultValue="USD" v-model="form.currency">
                      <SelectTrigger id="currency" class="mt-0">
                        <SelectValue placeholder="Currency" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem v-for="code in currencies.codes()" :key="code" :value="code">{{ code }}</SelectItem>
                      </SelectContent>
                    </Select>
                    <div class="text-sm text-red-500" v-if="form.errors.currency">{{ form.errors.currency }}</div>
                  </TableCell>
                  <TableCell class="relative max-w-sm items-center px-2 py-4 text-sm">
                    <Input id="tax" v-model="form.tax_amount" class="mt-1" type="number" min="0" />
                    <span class="absolute inset-y-0 end-2 flex items-center justify-center px-2"> % </span>
                  </TableCell>
                  <TableCell class="whitespace-nowrap px-2 py-4 text-right text-sm">
                    {{ formatCurrency(totalAmount) }}
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </div>

          <!-- Expires -->
          <div class="mb-4">
            <div class="flex flex-col">
              <Label class="mb-2">Expires At</Label>
              <Popover>
                <PopoverTrigger as-child>
                  <Button variant="outline" :class="cn('w-[280px] justify-start text-left font-normal', !expires_date && 'text-muted-foreground')">
                    <CalendarIcon class="mr-2 h-4 w-4" />
                    {{ expires_date ? df.format(expires_date.toDate(getLocalTimeZone())) : 'Pick a date' }}
                  </Button>
                </PopoverTrigger>
                <PopoverContent class="w-auto p-0">
                  <Calendar v-model="expires_date" initial-focus :min-value="new CalendarDate(today.getFullYear(), today.getMonth() + 1, today.getDate())" />
                </PopoverContent>
              </Popover>
              <p class="text-sm text-muted-foreground">This proposal expiration date</p>
            </div>
            <InputError class="mt-2" :message="form.errors.expires_at" />
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
