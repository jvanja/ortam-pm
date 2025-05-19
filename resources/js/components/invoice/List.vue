<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import type { Invoice } from '@/types';
import { useDateFormat } from '@vueuse/core';
import { PlusCircle } from 'lucide-vue-next';

const props = defineProps({
  invoices: Array<Invoice>,
  currency: {
    type: String,
    default: 'USD',
  },
});
const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: props.currency }).format(amount);
};
</script>
<template>
  <Card>
    <CardHeader>
      <CardTitle>Invoices</CardTitle>
    </CardHeader>
    <CardContent>
      <div class="overflow-x-auto">
        <Table class="min-w-full divide-y divide-gray-200">
          <TableHeader>
            <TableRow>
              <TableHead scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Invoice Number</TableHead>
              <TableHead scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Date</TableHead>
              <TableHead scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Amount</TableHead>
              <TableHead scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</TableHead>
              <TableHead scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500" />
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="invoice in props.invoices" :key="invoice.id">
              <TableCell class="whitespace-nowrap px-6 py-4">
                <div class="text-sm">{{ invoice.serial_number }}</div>
              </TableCell>
              <TableCell class="whitespace-nowrap px-6 py-4">
                <div class="text-sm">{{ useDateFormat(invoice.created_at, 'YYYY-MM-DD') }}</div>
              </TableCell>
              <TableCell class="whitespace-nowrap px-6 py-4">
                <div class="text-sm">{{ formatCurrency(Number(invoice.total_amount)) }}</div>
              </TableCell>
              <TableCell class="whitespace-nowrap px-6 py-4">
                <div class="text-sm">{{ invoice.state }}</div>
              </TableCell>
              <TableCell class="whitespace-nowrap px-6 py-4">
                <div class="flex items-center gap-2">
                  <div class="text-sm">
                    <a :href="`/invoices/${invoice.id}`">View</a>
                  </div>
                  <div class="text-sm">
                    <a :href="`/invoices/${invoice.id}/edit`">Edit</a>
                  </div>
                </div>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
        <div class="rounded-lg border-2 border-dashed border-teal-400 bg-teal-50 p-1 px-4 py-2 dark:bg-neutral-800">
          <a href="/invoices/create/" class="flex flex-1 items-center gap-2 text-sm text-green-700">
            <PlusCircle class="text-green-700" />
            Add invoice</a
          >
        </div>
      </div>
    </CardContent>
  </Card>
</template>
