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
  logo?: string;
  brandColor?: string;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Company branding',
    href: '/settings/branding',
  },
];

const form = useForm({
  logo: props.logo,
  brandColor: props.brandColor,
});

const page = usePage<SharedData>();
const user = page.props.auth.user as User;

const submit = () => {
  form.patch(route('organization.branding.update', [user.organization_id]), {
    preserveScroll: true,
    onSuccess: () => console.log('Organization created!'),
    onError: (msg) => console.error('Failed to create organization', msg),
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
            <Input type="file" @input="form.logo = $event.target.files[0]" />
            <progress v-if="form.progress" :value="form.progress.percentage" max="100">{{ form.progress.percentage }}%</progress>
            <InputError class="mt-2" :message="form.errors.logo" />
          </div>

          <div class="grid gap-2">
            <Label for="brandColor">Brand color</Label>
            <Input id="brandColor" type="text" class="mt-1 block w-full" v-model="form.brandColor" required placeholder="#ff00ff" />
            <InputError class="mt-2" :message="form.errors.brandColor" />
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
