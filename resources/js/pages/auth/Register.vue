<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps<{
  invitation_token?: string | null;
  email?: string | null; // Email passed from controller for pre-filling
}>();

const form = useForm({
  name: '',
  email: props.email ?? '', // Pre-fill email from prop
  password: '',
  password_confirmation: '',
  invitation_token: props.invitation_token, // Include token in form data
});

// Determine if the email field should be read-only
const isEmailReadonly = computed(() => !!props.invitation_token);

const submit = () => {
  form.post(route('register'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  });
};
</script>

<template>
  <AuthBase title="Create an account" description="Enter your details below to create your account">
    <Head title="Register" />

    <form @submit.prevent="submit" class="flex flex-col gap-6">
      <div class="grid gap-6">
        <div class="grid gap-2">
          <Label for="name">Name</Label>
          <Input id="name" type="text" required autofocus :tabindex="1" autocomplete="name" v-model="form.name" placeholder="Full name" />
          <InputError :message="form.errors.name" />
        </div>

        <div class="grid gap-2">
          <Label for="email">Email address</Label>
          <Input
            id="email"
            type="email"
            required
            :tabindex="2"
            autocomplete="email"
            v-model="form.email"
            placeholder="email@example.com"
            :readonly="isEmailReadonly"
            :class="{ 'cursor-not-allowed bg-gray-100': isEmailReadonly }"
          />
          <InputError :message="form.errors.email" />
          <InputError :message="form.errors.invitation_token" />
          <!-- Display token errors -->
        </div>

        <!-- Hidden input to submit the token -->
        <input type="hidden" name="invitation_token" v-model="form.invitation_token" />

        <div class="grid gap-2">
          <Label for="password">Password</Label>
          <Input id="password" type="password" required :tabindex="3" autocomplete="new-password" v-model="form.password" placeholder="Password" />
          <InputError :message="form.errors.password" />
        </div>

        <div class="grid gap-2">
          <Label for="password_confirmation">Confirm password</Label>
          <Input
            id="password_confirmation"
            type="password"
            required
            :tabindex="4"
            autocomplete="new-password"
            v-model="form.password_confirmation"
            placeholder="Confirm password"
          />
          <InputError :message="form.errors.password_confirmation" />
        </div>

        <Button type="submit" class="mt-2 w-full" tabindex="5" :disabled="form.processing">
          <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
          Create account
        </Button>
      </div>

      <div class="text-center text-sm text-muted-foreground">
        Already have an account?
        <TextLink :href="route('login')" class="underline underline-offset-4" :tabindex="6">Log in</TextLink>
      </div>
    </form>
  </AuthBase>
</template>
