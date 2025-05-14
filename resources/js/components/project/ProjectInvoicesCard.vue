<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { PlusCircle } from 'lucide-vue-next';
import type { Invoice } from '@/types';

const props = defineProps<{
  invoices: Invoice[];
  currency: string;
}>();

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
      <div v-if="invoices.length === 0" class="text-muted-foreground">No invoices found for this project.</div>
      <Table v-else>
        <TableHeader>
          <TableRow>
            <TableHead>Invoice #</TableHead>
            <TableHead>Amount</TableHead>
            <TableHead>Status</TableHead>
            <TableHead class="text-right">Created At</TableHead>
            <TableHead>Actions</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="invoice in invoices" :key="invoice.id">
            <TableCell class="font-medium">{{ invoice.invoice_number }}</TableCell>
            <TableCell>{{ formatCurrency(invoice.amount) }}</TableCell>
            <TableCell>{{ invoice.status }}</TableCell>
            <TableCell class="text-right">{{ new Date(invoice.created_at).toLocaleDateString() }}</TableCell>
            <TableCell>
              <div class="flex items-center gap-2">
                <div class="text-sm text-gray-900">
                  <a :href="`/invoices/${invoice.id}`">View</a>
                </div>
                <div class="text-sm text-gray-900">
                  <a :href="`/invoices/${invoice.id}/edit`">Edit</a>
                </div>
              </div>
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
        <div class="rounded-lg border-2 border-dashed p-1 px-4 py-2 bg-teal-50 dark:bg-neutral-800">
          <a href="/invoices/create/" class="flex flex-1 items-center gap-2 text-sm text-green-700">
            <PlusCircle class="text-green-700" />
            Add invoice</a>
        </div>
    </CardContent>
  </Card>
</template>
