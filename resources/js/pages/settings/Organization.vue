<script setup lang="ts">
import { TransitionRoot } from '@headlessui/vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';

import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/CompanyLayout.vue';
import { SharedData, User, type BreadcrumbItem } from '@/types';

interface Props {
  name?: string;
  address?: string;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Company settings',
    href: '/settings/company',
  },
];

const form = useForm({
  name: props.name,
  address: props.address,
});

const page = usePage<SharedData>();
const user = page.props.auth.user as User;

const submit = () => {
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
      <div class="flex flex-col space-y-6">
        <HeadingSmall title="" description="Update your company's basic information" />

        <form @submit.prevent="submit" class="space-y-6">
          <div class="grid gap-2">
            <Label for="name">Name</Label>
            <Input id="name" class="mt-1 block w-full" v-model="form.name" required autocomplete="name" placeholder="Company name" />
            <InputError class="mt-2" :message="form.errors.name" />
          </div>

          <div class="grid gap-2">
            <Label for="address">Company address</Label>
            <Input
              id="address"
              type="text"
              class="mt-1 block w-full"
              v-model="form.address"
              required
              autocomplete="address-line1"
              placeholder="Company address"
            />
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
