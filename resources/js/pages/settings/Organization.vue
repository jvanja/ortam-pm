<script setup lang="ts">
import { TransitionRoot } from '@headlessui/vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';

import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/CompanyLayout.vue';
import countries from '@/lib/countries';
import { SharedData, User, type BreadcrumbItem } from '@/types';
import { reactive, ref } from 'vue';

interface Props {
  name: string;
  email: string;
  phone_number: string;
  address: string;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Company settings',
    href: '/settings/company',
  },
];

// Safely parse props.address with a fallback
const parsedAddress = ref(
  (() => {
    try {
      return JSON.parse(props.address) || {};
    } catch (e) {
      console.error('Failed to parse address:', e);
      return {};
    }
  })(),
);

// Initialize a reactive address object
const orgAddress = reactive({
  street: parsedAddress.value.street || '',
  city: parsedAddress.value.city || '',
  state: parsedAddress.value.state || '',
  postal_code: parsedAddress.value.postal_code || '',
  country: parsedAddress.value.country || '',
});

const form = useForm({
  name: props.name,
  email: props.email,
  phone_number: props.phone_number,
  address: orgAddress,
});

const page = usePage<SharedData>();
const user = page.props.auth.user as User;

const submit = () => {
  console.log(form.data());
  form.patch(route('organization.update', [user.organization_id]), {
    preserveScroll: true,
    onSuccess: () => console.log('Organization created!'),
    onError: () => console.error('Failed to create organization'),
  });
};
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Company settings" />

    <SettingsLayout>
      <div class="flex flex-col space-y-6 py-6">
        <HeadingSmall title="Update your company's basic information" description="" />

        <form @submit.prevent="submit" class="space-y-6">
          <div class="grid gap-2">
            <Label for="name">Name</Label>
            <Input id="name" class="mt-1 block w-full" v-model="form.name" required autocomplete="name" placeholder="Company name" />
            <InputError class="mt-2" :message="form.errors.name" />
          </div>

          <div class="grid gap-2">
            <Label for="email">Email</Label>
            <Input id="email" class="mt-1 block w-full" v-model="form.email" required autocomplete="email" placeholder="Email address" />
            <InputError class="mt-2" :message="form.errors.email" />
          </div>

          <div class="grid gap-2">
            <Label for="phone_number">Phone number</Label>
            <Input id="phone_number" class="mt-1 block w-full" v-model="form.phone_number" required autocomplete="tel" placeholder="Phone number" />
            <InputError class="mt-2" :message="form.errors.phone_number" />
          </div>

          <div class="my-12 grid gap-2 py-4">
            <HeadingSmall title="Company address" description="These section will show on your documents like invoices and timesheets" class="mb-2" />
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
                required
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
              <Select v-model="form.address.country" required>
                <SelectTrigger>
                  <SelectValue placeholder="Country" />
                </SelectTrigger>
                <SelectContent>
                  <SelectGroup>
                    <SelectLabel>Task Types</SelectLabel>
                    <SelectItem v-for="country in countries" :key="country.countryShortCode" :value="country.countryName">
                      {{ country.countryName }}
                    </SelectItem>
                  </SelectGroup>
                </SelectContent>
              </Select>
            </div>
            <InputError class="mt-2" :message="form.errors.address" />
          </div>

          <div class="flex items-center gap-4">
            <Button :disabled="form.processing">Save</Button>

            <TransitionRoot
              :show="form.recentlySuccessful"
              enter="transition ease-in-out"
              enter-from="opacity-0"
              leave="transition ease-in-out"
              leave-to="opacity-0"
            >
              <p class="text-sm text-neutral-600">Saved.</p>
            </TransitionRoot>
          </div>
        </form>
      </div>
    </SettingsLayout>
  </AppLayout>
</template>
