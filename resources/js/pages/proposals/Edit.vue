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
import type { BreadcrumbItem, Proposal } from '@/types';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import currencies from 'currency-codes';
import { PlusCircle } from 'lucide-vue-next';
import { AcceptableValue } from 'reka-ui';
import { computed, onMounted, ref } from 'vue';
import { toast } from 'vue-sonner';

const props = defineProps<{
  client: { id: string; company_name: string };
  project: { id: string; type: string };
  proposal: Proposal;
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Proposals', href: '/proposals' }];

const form = useForm({
  state: props.proposal.state,
  title: props.proposal.title,
  client_id: props.proposal.client_id,
  description: props.proposal.description,
  currency: props.proposal.currency,
  subtotal_amount: props.proposal.subtotal_amount || 1,
  tax_amount: props.proposal.tax_amount || 1,
  total_amount: props.proposal.total_amount,
  expires_at: props.proposal.expires_at,
});

// Handle form submission
const submit = () => {
  form.expires_at = expires_date.value!.toDate(getLocalTimeZone())
  form.total_amount = totalAmount.value;
  form.post(route('proposals.store'), {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Proposal updated successfully!', {
        style: { background: '#6ee7b7', color: '#000' },
      });
      form.reset();
    },
    onError: () => {
      toast.error('Failed to save proposal.');
    },
  });
};

/* ==========================================================================
 Amount calculations
 ========================================================================== */
const formatCurrency = (amount: number) => `${Number(amount).toFixed(2)} ${form.currency}`;
const totalAmount = computed(() => {
  const subtotal = parseFloat(form.subtotal_amount.toString());
  const tax = parseFloat(form.tax_amount.toString());
  return subtotal + (subtotal * tax) / 100;
  });

/* ==========================================================================
 Calendar
 ========================================================================== */
const df = new DateFormatter('en-US', {
  dateStyle: 'long',
})

const today = new Date()
const defaultExpiresDate = new CalendarDate(today.getFullYear(), today.getMonth() + 2, today.getDate())
const expires_date = ref<DateValue>(defaultExpiresDate)

</script>

<template>
  <Head :title="proposal.title" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-8">
      <div class="space-y-6">
        <div class="flex flex-col gap-y-0">
          <Input class="p-0 text-2xl md:text-xl font-semibold border-none" v-model="form.title" />
          <p class="text-sm text-muted-foreground">{{ client.company_name }}</p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
          <div class="mb-4">
            <Input id="state" v-model="form.state" type="textarea" class="hidden" disabled />
          </div>

          <div class="mb-4">
            <Label>Description</Label>
            <Tiptap :content="form.description" @update:model-value="form.description = $event" />
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
                  <Calendar v-model="expires_date" :default-value="defaultExpiresDate" initial-focus :min-value="new CalendarDate(today.getFullYear(), today.getMonth() + 1, today.getDate())" />
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
    </div>
  </AppLayout>
</template>
