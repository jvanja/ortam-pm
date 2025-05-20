<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, User } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { toast } from 'vue-sonner';

const props = defineProps<{ user: User }>();
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Users', href: '' },
  { title: props.user.name, href: '' },
];

const user = { ...props.user };
const form = useForm(user);
const submit = () => {
  form.patch(route('users.update', [props.user.id]), {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('User has been updated successfully!', { style: { background: '#6ee7b7', color: '#000' } });
    },
  });
};
</script>
<template>
  <Head title="User" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="flex flex-col gap-4 p-4">
        <Card>
          <CardHeader>
            <CardTitle>{{ user.name }}</CardTitle>
          </CardHeader>
          <CardContent>
            <form @submit.prevent="submit" class="space-y-6">
              <div class="grid w-full items-center gap-4">
                <div class="flex flex-col space-y-1.5">
                  <Label htmlFor="email">Email address</Label>
                  <Input id="email" v-model="form.email" />
                </div>
              </div>
              <div class="flex w-full justify-end">
                <Button :disabled="form.processing">Update</Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>
