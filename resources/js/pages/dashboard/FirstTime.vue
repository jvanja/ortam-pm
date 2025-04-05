<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
// Removed AppLayout and Heading as this is a setup step outside the main dashboard layout
// import Heading from '@/components/Heading.vue';
// import AppLayout from '@/layouts/AppLayout.vue';
// import { type BreadcrumbItem } from '@/types';

// const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard', href: '/dashboard' }];

const form = useForm({
  name: '',
});

const submit = () => {
  form.post(route('organizations.store'), {
    // Optional: Add callbacks for success/error handling
    // onSuccess: () => console.log('Organization created!'),
    // onError: () => console.error('Failed to create organization'),
  });
};
</script>

<template>
  <Head title="Create Organization" />

  <div class="flex min-h-screen items-center justify-center bg-background p-4">
    <Card class="w-full max-w-md">
      <form @submit.prevent="submit">
        <CardHeader>
          <CardTitle>Create Your Organization</CardTitle>
          <CardDescription>Let's start by setting up your organization.</CardDescription>
        </CardHeader>
        <CardContent class="space-y-4">
          <div class="space-y-2">
            <Label for="name">Organization Name</Label>
            <Input
              id="name"
              v-model="form.name"
              placeholder="Enter your organization's name"
              required
              autofocus
              :disabled="form.processing"
            />
            <!-- Display validation errors if they exist -->
            <p v-if="form.errors.name" class="text-sm text-destructive">
              {{ form.errors.name }}
            </p>
          </div>
        </CardContent>
        <CardFooter>
          <Button type="submit" class="w-full" :disabled="form.processing">
            <span v-if="form.processing">Creating...</span>
            <span v-else>Create Organization</span>
          </Button>
        </CardFooter>
      </form>
    </Card>
  </div>
</template>
