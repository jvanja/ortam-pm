<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

const emit = defineEmits(['client-created', 'cancel']);

const { backToProject  }= defineProps<{ backToProject: boolean }>();

const form = useForm({
  backToProject,
  company_name: '',
  contact_person: '',
  address: '',
  phone: '',
  email: '',
});

const submit = () => {
  form.post(route('clients.store'), {
    onSuccess: () => {
      const newClient = form;
      if (newClient) {
        toast.success(`New client created successfully!`);
        emit('client-created', newClient);
        form.reset();
      } else {
        toast.error('Client created but response data not received');
      }
    },
    onError: () => {
      toast.error('Failed to create client.');
    },
  });
};
</script>

<template>
  <form @submit.prevent="submit" class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="space-y-2">
        <Label for="new-client-company-name">Company Name <span class="text-red-500">*</span></Label>
        <Input v-model="form.company_name" id="new-client-company-name" placeholder="Enter company name" required />
        <div class="text-sm text-red-500" v-if="form.errors.company_name">{{ form.errors.company_name }}</div>
      </div>

      <div class="space-y-2">
        <Label for="new-client-contact-person">Contact Person <span class="text-red-500">*</span></Label>
        <Input v-model="form.contact_person" id="new-client-contact-person" placeholder="Enter contact person" required />
        <div class="text-sm text-red-500" v-if="form.errors.contact_person">{{ form.errors.contact_person }}</div>
      </div>

      <div class="space-y-2">
        <Label for="new-client-address">Address</Label>
        <Input v-model="form.address" id="new-client-address" placeholder="Enter address" />
        <div class="text-sm text-red-500" v-if="form.errors.address">{{ form.errors.address }}</div>
      </div>

      <div class="space-y-2">
        <Label for="new-client-phone">Phone</Label>
        <Input v-model="form.phone" id="new-client-phone" placeholder="Enter phone number" />
        <div class="text-sm text-red-500" v-if="form.errors.phone">{{ form.errors.phone }}</div>
      </div>

      <div class="space-y-2">
        <Label for="new-client-email">Email <span class="text-red-500">*</span></Label>
        <Input v-model="form.email" id="new-client-email" type="email" placeholder="Enter email address" required />
        <div class="text-sm text-red-500" v-if="form.errors.email">{{ form.errors.email }}</div>
      </div>
    </div>

    <div class="flex justify-end gap-4">
      <Button type="button" variant="outline" @click="emit('cancel')" :disabled="form.processing">Cancel</Button>
      <Button type="submit" :disabled="form.processing">Create Client</Button>
    </div>
  </form>
</template>
