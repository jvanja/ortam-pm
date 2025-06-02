<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import countries from '@/lib/countries';
import { useForm } from '@inertiajs/vue3';
import { reactive } from 'vue';
import { toast } from 'vue-sonner';

const emit = defineEmits(['client-created', 'cancel']);

const { backRoute } = defineProps<{ backRoute: string | boolean }>();
const orgAddress = reactive({
  street: '',
  city: '',
  state: '',
  postal_code: '',
  country: '',
});

const form = useForm({
  backRoute,
  company_name: '',
  contact_person: '',
  address: orgAddress,
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
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
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
        <HeadingSmall title="Client address" class="mb-2" />
        <div class="grid grid-cols-4 items-center gap-2">
          <Label for="street">Street</Label>
          <Input
            id="street"
            type="text"
            class="col-span-3 mt-1 block w-full"
            v-model="form.address.street"
            required
            autocomplete="address-line1"
            placeholder="Company address"
          />
        </div>
        <div class="grid grid-cols-4 items-center gap-2">
          <Label for="city">City</Label>
          <Input
            id="city"
            type="text"
            class="col-span-3 mt-1 block w-full"
            v-model="form.address.city"
            required
            autocomplete="address-city"
            placeholder="City"
          />
        </div>
        <div class="grid grid-cols-4 items-center gap-2">
          <Label for="state">State</Label>
          <Input
            id="state"
            type="text"
            class="col-span-3 mt-1 block w-full"
            v-model="form.address.state"
            autocomplete="address-state"
            placeholder="State"
          />
        </div>
        <div class="grid grid-cols-4 items-center gap-2">
          <Label for="code">Postal Code</Label>
          <Input
            id="code"
            type="text"
            class="col-span-3 mt-1 block w-full"
            v-model="form.address.postal_code"
            required
            autocomplete="address-code"
            placeholder="Postal code"
          />
        </div>
        <div class="grid grid-cols-4 items-center gap-2">
          <Label for="country">Country</Label>
          <Select id="country" v-model="form.address.country" required>
            <SelectTrigger>
              <SelectValue placeholder="Country" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem v-for="country in countries" :key="country.countryShortCode" :value="country.countryName">
                {{ country.countryName }}
              </SelectItem>
            </SelectContent>
          </Select>
        </div>
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
