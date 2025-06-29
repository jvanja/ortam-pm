<script setup lang="ts">
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
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem, Client, Invoice, InvoiceItem, Organization, Project } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { defineProps } from 'vue';
import { addAlpha } from '@/lib/utils';
import { toast } from 'vue-sonner';
type InvoiceWithProject = Invoice & { project: Project; client: Client; organization: Organization; invoice_items: InvoiceItem[] };

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Invoices', href: '/invoices' }];
const props = defineProps<{
  invoice: InvoiceWithProject;
  invoice_view: string;
}>();

const page = usePage();
const parser = new DOMParser();
const invoiceHTML = parser.parseFromString(props.invoice_view, 'text/html');
const invoicePaymentInstructions = invoiceHTML.querySelector('#payment-instructions > div') as HTMLDivElement;
const brandColor = (page.props.organization as Organization).brand_color;

if (invoicePaymentInstructions && brandColor) {
  invoicePaymentInstructions.style.backgroundColor = addAlpha(brandColor, 0.1);
}

const printInvoice = () => window.print();

const form = useForm({ id: props.invoice.id });

const sendInvoice = () => {
  form.post(route('invoices.send', [props.invoice.id]), {
    onSuccess: () => {
      toast.success('Invoice sent successfully!', {
        style: { background: '#6ee7b7', color: '#000' },
      });
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
};
</script>

<template>
  <Head title="View Invoice" class="no-print" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-8">
      <div class="mx-auto mb-[60px] max-w-3xl" v-html="invoiceHTML.body.innerHTML"></div>
      <div class="no-print mb-4 flex justify-center gap-4">
        <AlertDialog>
          <AlertDialogTrigger as-child>
            <Button :disabled="!invoice.buyer_information.email">Send Invoice</Button>
          </AlertDialogTrigger>
          <AlertDialogContent>
            <AlertDialogHeader>
              <AlertDialogTitle>Are you sure you want to send this invoice?</AlertDialogTitle>
              <AlertDialogDescription> You are about to send an invoice to {{ invoice.buyer_information.email }}. </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
              <AlertDialogCancel :disabled="form.processing">Cancel</AlertDialogCancel>
              <AlertDialogAction @click="sendInvoice" :disabled="form.processing">Yes, send the invoice</AlertDialogAction>
            </AlertDialogFooter>
          </AlertDialogContent>
        </AlertDialog>
        <Button @click="printInvoice">Print Invoice</Button>
      </div>
      <div v-if="!invoice.buyer_information.email" class="no-print text-sm text-gray-500">
        To send this invoice, please fill out the buyer information.
      </div>
    </div>
  </AppLayout>
</template>
<style>
@media print {
  .no-print {
    display: none !important;
  }
  #payment-instructions {
    margin-top: 2rem;
  }
}
</style>
