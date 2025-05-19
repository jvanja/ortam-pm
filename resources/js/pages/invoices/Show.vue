<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem, Client, Invoice, InvoiceItem, Organization, Project } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { defineProps } from 'vue';
type InvoiceWithProject = Invoice & { project: Project; client: Client; organization: Organization; invoice_items: InvoiceItem[] };

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Invoices', href: '/invoices' }];
const props = defineProps<{
  invoice: InvoiceWithProject;
  invoice_view: string;
}>();

const printInvoice = () => window.print();

const form = useForm({ id: props.invoice.id });

const sendInvoice = () => {
  if (confirm('Are you sure you want to send this invoice?')) {
    console.log(props.invoice.id);
    form.post(route('invoices.send', [props.invoice.id]), {
      onSuccess: () => {
        console.log('Invoice sent successfully!');
      },
      onError: (errors) => {
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
</script>

<template>
  <Head title="Create Invoice" class="no-print" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="mx-auto max-w-3xl p-8" v-html="props.invoice_view"></div>
    <div class="no-print flex gap-4 p-8">
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
