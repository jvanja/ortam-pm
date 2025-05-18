<script setup lang="ts">
import { BreadcrumbItem, Client, Invoice, InvoiceItem, Organization, Project } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { useDateFormat } from '@vueuse/core';
import { Button } from '@/components/ui/button';
import { defineProps } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';

type InvoiceWithProject = Invoice & { project: Project; client: Client; organization: Organization; invoice_items: InvoiceItem[] };

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Invoices', href: '/invoices' }];
// Define the props expected by this component
const props = defineProps<{
  invoice: InvoiceWithProject;
  invoice_view: string;
}>();

console.log(props.invoice_view)
const printInvoice = () => {
  window.print();
};

// Form for sending the invoice
const form = useForm({
  id: props.invoice.id,
});

// Method to send the invoice
const sendInvoice = () => {
  if (confirm('Are you sure you want to send this invoice?')) {
    console.log(props.invoice.id);
    form.post(route('invoices.send', [props.invoice.id]), {
      onSuccess: () => {
        console.log('Invoice sent successfully!');
      },
      onError: (errors) => {
        // Handle errors, e.g., client email missing
        console.error('Error sending invoice:', errors);
        // You might want to display a user-friendly error message
        if (errors && errors.error) {
          alert(errors.error); // Display the error message from the backend
        } else {
          alert('Failed to send invoice.');
        }
      },
    });
  }
};

const formatCurrency = (amount: number) => `${amount / 100} ${props.invoice.currency}`;
const total_amount = props.invoice.invoice_items.reduce((acc, item) => acc + Number(item.unit_price) / 1, 0);
</script>

<template>
  <Head title="Create Invoice" class="no-print"/>

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-8 max-w-3xl mx-auto" v-html="props.invoice_view"></div>
    <div class="flex gap-4 p-8 no-print">
      <Button @click="sendInvoice" :disabled="!invoice.client?.email">Send Invoice</Button>
      <Button @click="printInvoice">Print Invoice</Button>
    </div>
  </AppLayout>
</template>
<style scoped>
@media print {
  .no-print {
    display: none !important;
  }

  .invoice-container {
    box-shadow: none;
    margin: 0;
    padding: 0;
    border: none;
  }
}
</style>
