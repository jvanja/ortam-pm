<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import { TransitionRoot } from '@headlessui/vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';

import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/CompanyLayout.vue';
import { SharedData, User, type BreadcrumbItem } from '@/types';

interface Props {
  logo?: string;
  brand_color?: string;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Company branding',
    href: '/settings/branding',
  },
];

const form = useForm({
  logo: null,
  brand_color: props.brand_color,
});

const page = usePage<SharedData>();
const user = page.props.auth.user as User;

const submit = () => {
  // Corrected route name to match routes/settings.php
  form.post(route('organization.branding.update', [user.organization_id]), {
    preserveScroll: true,
    onSuccess: () => console.log('Organization branding updated!'),
    onError: (msg) => console.error('Failed to update organization branding', msg),
  });
};
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head :title="breadcrumbs[0].title" />

    <SettingsLayout>
      <div class="flex flex-col space-y-6">
        <HeadingSmall title="" description="Update your company's branding" />
        <form @submit.prevent="submit" class="space-y-6">
          <div class="grid gap-2">
            <Label for="logo">Logo</Label>

            <div class="flex gap-2">
              <!-- @vue-ignore -->
              <img v-if="logo" :src="logo" class="h-10 w-10 object-cover" @click="$refs.file.$el.click()" />
              <Input type="file" @input="form.logo = $event.target.files[0]" class="" ref="file" />
            </div>
            <progress v-if="form.progress" :value="form.progress.percentage" max="100">{{ form.progress.percentage }}%</progress>
            <InputError class="mt-2" :message="form.errors.logo" />
          </div>

          <div class="grid gap-2">
            <Label for="brand_color">Brand color</Label>
            <p class="text-sm text-neutral-400">Used for documents you sent out like invoices and emails.</p>
            <Input
              id="brand_color"
              type="color"
              class="mt-1 block size-[100px]"
              v-model="form.brand_color"
              :value="form.brand_color"
              required
              placeholder="#ff00ff"
            />
            <InputError class="mt-2" :message="form.errors.brand_color" />
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
