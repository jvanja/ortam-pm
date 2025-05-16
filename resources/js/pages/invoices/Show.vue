<script setup lang="ts">
import { Client, Invoice, InvoiceItem, Organization, Project } from '@/types'; // Assuming you have a type definition for Invoice
import { useForm } from '@inertiajs/vue3';
import { useDateFormat } from '@vueuse/core';
import { defineProps } from 'vue';

type InvoiceWithProject = Invoice & { project: Project; client: Client; organization: Organization; invoice_items: InvoiceItem[] };

// Define the props expected by this component
const props = defineProps<{
  invoice: InvoiceWithProject;
}>();

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
        // Handle success, maybe show a flash message (handled by Inertia redirects)
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
  <div class="invoice-container">
    <div class="invoice-header">
      <h1>Invoice #{{ invoice.serial_number }}</h1>
      <p>Invoice date {{ useDateFormat(invoice.created_at, 'YYYY-MM-DD') }}</p>
    </div>

    <div class="invoice-details">
      <div class="organization-details">
        <h2>From:</h2>
        <p>
          <strong>{{ invoice.organization?.name }}</strong>
        </p>
        <p>{{ invoice.project?.manager }}</p>
        <p>{{ invoice.organization?.address }}</p>
      </div>

      <div class="client-details">
        <h2>To:</h2>
        <p>{{ invoice.client?.contact_person }}</p>
        <p>
          <strong>{{ invoice.client?.company_name }}</strong>
        </p>
        <p>{{ invoice.client?.address }}</p>
        <p>{{ invoice.client?.email }}</p>
        <!-- Display client email -->
      </div>
    </div>

    <div class="invoice-items">
      <table>
        <thead>
          <tr>
            <th>Description</th>
            <th class="text-right">Quantity</th>
            <th class="text-right">Amount</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in invoice.invoice_items" :key="item.id">
            <td>{{ item.description }}</td>
            <td>{{ item.quantity }}</td>
            <td class="text-right">{{ formatCurrency(Number(item.unit_price)) }}</td>
          </tr>
          <!-- Add more line items here if your invoice structure supports it -->
        </tbody>
        <tfoot>
          <tr>
            <td colspan="2" class="text-right"><strong>Total:</strong></td>
            <td class="text-right">
              <strong>{{ formatCurrency(total_amount) }}</strong>
            </td>
          </tr>
        </tfoot>
      </table>
    </div>

    <!-- Add sections for notes, terms, etc. if needed -->
    <div class="invoice-notes">
      <h2>Notes:</h2>
      <p>{{ invoice.description }}</p>
    </div>

    <div class="invoice-footer">
      <!-- Add footer details like bank info, etc. -->
      How to pay this invoice: ---
      <p><strong>Thank you for your business!</strong></p>
    </div>

    <div class="print-button-container no-print">
      <!-- Add the Send Invoice button -->
      <button @click="sendInvoice" :disabled="!invoice.client?.email">Send Invoice</button>
      <button @click="printInvoice">Print Invoice</button>
    </div>
  </div>
</template>

<style scoped>
/* Basic styling for the invoice layout */
.invoice-container {
  font-family: sans-serif;
  max-width: 800px;
  margin: 20px auto;
  padding: 30px;
  border: 1px solid #eee;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
  line-height: 1.6;
  color: #555;
  background-color: #fff;
}

.invoice-header {
  text-align: center;
  margin-bottom: 40px;
}

.invoice-header h1 {
  color: #333;
  margin-bottom: 5px;
}

.invoice-details {
  display: flex;
  justify-content: space-between;
  margin-bottom: 40px;
}

.organization-details,
.client-details {
  width: 48%;
}

.invoice-items table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 40px;
}

.invoice-items th,
.invoice-items td {
  border-bottom: 1px solid #eee;
  padding: 12px 8px;
  text-align: left;
}

.invoice-items th {
  background-color: #f8f8f8;
  font-weight: bold;
}

.invoice-items tfoot td {
  border-top: 2px solid #333;
  font-size: 1.2em;
}

.text-right {
  text-align: right;
}

.invoice-notes,
.invoice-footer {
  margin-top: 20px;
  padding-top: 20px;
  border-top: 1px solid #eee;
}

.print-button-container {
  text-align: center;
  margin-top: 30px;
  /* Add some spacing between buttons */
  & button {
    margin: 0 5px;
  }
}

.print-button-container button {
  padding: 10px 20px;
  font-size: 1em;
  cursor: pointer;
}

/* Print specific styles */
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
