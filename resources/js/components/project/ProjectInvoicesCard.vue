<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import type { Invoice } from '@/types';

const props = defineProps<{
  invoices: Invoice[];
}>();

// Helper function to format currency (basic example)
const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount);
};
</script>

<template>
  <Card>
    <CardHeader>
      <CardTitle>Invoices</CardTitle>
    </CardHeader>
    <CardContent>
      <div v-if="invoices.length === 0" class="text-center text-muted-foreground">No invoices found for this project.</div>
      <Table v-else>
        <TableCaption>A list of invoices for this project.</TableCaption>
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
    </CardContent>
  </Card>
</template>
