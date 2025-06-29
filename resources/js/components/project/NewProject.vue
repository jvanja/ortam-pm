<script setup lang="ts">
import NewClient from '@/components/client/NewClient.vue'; // Import the new component
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { getQuery } from '@/lib/utils';
import { Client, Proposal } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import currencies from 'currency-codes';
import { upperFirst } from 'lodash-es';
import { computed, onMounted, ref, watch } from 'vue';
import { toast } from 'vue-sonner';

const page = usePage();
// @ts-expect-error The user is added in HandleInertiaRequest and always exists
const org_id = computed(() => page.props.auth.user.organization_id);
const {clients, languages, users, proposal} = defineProps<{
  clients: Client[];
  languages: any,
  users: {id: string, name:string}[],
  proposal?: Proposal}>();

// State for the two-step process
const currentStep = ref<'select_client' | 'create_client'>('select_client');
// const selectedClientId = ref(proposal?.client_id || '');
const clientId = proposal ? String(proposal.client_id) : '';
const form = useForm({
  type: proposal?.title || '',
  department: '',
  manager_id: '',
  language: 'english',
  address: '',
  status: '',
  opening_date: '',
  budget: proposal?.subtotal_amount || 0,
  currency: proposal?.currency || 'USD',
  sales_representative_name: '',
  deadline: '',
  client_id: clientId,
  organization_id: org_id.value,
});

// Watch for changes in selectedClientId and update the form
// watch(selectedClientId, (newVal) => {
//   form.client_id = newVal || '';
// });

const submit = () => {
  // Ensure a client is selected before submitting the project form
  // if (!selectedClientId.value) {
  if (!form.client_id) {
    toast.error('Please select or create a client.');
    return;
  }
  form.post(route('projects.store'), {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Project created successfully!', {
        style: { background: '#6ee7b7', color: '#000' },
      });
      // Optionally reset form and step after success
      form.reset();
      // selectedClientId.value = null;
      currentStep.value = 'select_client';
    },
    onError: () => {
      toast.error('Failed to create project.');
    },
  });
};

// Handle client creation success from NewClientForm
const handleClientCreated = () => {
  // selectedClientId.value = getQuery().clientId;
  form.client_id = getQuery().clientId;
  currentStep.value = 'select_client';
};

// Handle cancel from NewClientForm
const handleCancelCreateClient = () => {
  currentStep.value = 'select_client';
};

onMounted(() => {
  if (getQuery().clientId) {
    // selectedClientId.value = getQuery().clientId;
    form.client_id = getQuery().clientId;
  }
});
</script>

<template>
  <div>
    <Head title="New project" />

    <h2 class="text-xl font-semibold mb-4">New project</h2>
    <!-- Step 1: Select or Create Client -->
    <div v-if="currentStep === 'select_client'" class="space-y-6">
      <form @submit.prevent="submit" class="space-y-6">
        <h2 v-if="!form.client_id" class="text-lg font-semibold">Step 1: Select Client</h2>
        <div class="space-y-2">
          <Label for="client_id">Client</Label>
          <Select v-model="form.client_id" id="client_id" :disabled="!!proposal?.client_id">
            <SelectTrigger>
              <SelectValue placeholder="Select a client" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem v-for="client in clients" :key="client.id" :value="client.id">
                {{ client.company_name }}
              </SelectItem>
            </SelectContent>
          </Select>
          <div class="text-sm text-red-500" v-if="form.errors.client_id">{{ form.errors.client_id }}</div>
        </div>

        <div v-if="!form.client_id" class="flex justify-center">
          <Button variant="outline" @click="currentStep = 'create_client'">Or Create New Client</Button>
        </div>

        <div class="grid grid-cols-2 gap-6">
          <div class="space-y-2">
            <Label for="type">Project Type</Label>
            <Input v-model="form.type" id="type" placeholder="Enter project type" />
            <div class="text-sm text-red-500" v-if="form.errors.type">{{ form.errors.type }}</div>
          </div>

          <div class="space-y-2">
            <Label for="department">Department</Label>
            <Input v-model="form.department" id="department" placeholder="Enter department" />
            <div class="text-sm text-red-500" v-if="form.errors.department">{{ form.errors.department }}</div>
          </div>

          <div class="space-y-2">
            <Label for="manager">Project Manager</Label>

            <Select id="manager" :defaultValue="form.manager_id" v-model="form.manager_id" required>
              <SelectTrigger id="manager" class="mt-0">
                <SelectValue placeholder="Select a manager" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem v-for="user in users" :key="user.id" :value="String(user.id)">{{ user.name }}</SelectItem>
              </SelectContent>
            </Select>
            <div class="text-sm text-red-500" v-if="form.errors.manager_id">{{ form.errors.manager_id }}</div>
          </div>

          <div class="space-y-2">
            <Label for="language">Project Language</Label>
            <Select id="language" :defaultValue="form.language" v-model="form.language">
              <SelectTrigger id="language" class="mt-0">
                <SelectValue placeholder="English" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem v-for="lang in languages" :key="lang" :value="lang">{{ upperFirst(lang) }}</SelectItem>
              </SelectContent>
            </Select>
            <div class="text-sm text-red-500" v-if="form.errors.language">{{ form.errors.language }}</div>
          </div>

          <div class="space-y-2">
            <Label for="address">Project Address</Label>
            <Input v-model="form.address" id="address" placeholder="Enter project address" />
            <div class="text-sm text-red-500" v-if="form.errors.address">{{ form.errors.address }}</div>
          </div>

          <div class="space-y-2">
            <Label for="opening_date">Opening Date</Label>
            <Input v-model="form.opening_date" type="date" id="opening_date" />
            <div class="text-sm text-red-500" v-if="form.errors.opening_date">{{ form.errors.opening_date }}</div>
          </div>

          <div class="space-y-2">
            <Label for="budget">Budget</Label>
            <div class="flex gap-2">
              <div>
                <Input v-model="form.budget" type="number" id="budget" placeholder="Enter budget" />
                <div class="text-sm text-red-500" v-if="form.errors.budget">{{ form.errors.budget }}</div>
              </div>
              <div>
                <Select id="currency" defaultValue="USD" v-model="form.currency">
                  <SelectTrigger id="currency" class="mt-0">
                    <SelectValue placeholder="Currency" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem v-for="code in currencies.codes()" :key="code" :value="code">{{ code }}</SelectItem>
                  </SelectContent>
                </Select>
                <div class="text-sm text-red-500" v-if="form.errors.currency">{{ form.errors.currency }}</div>
              </div>
            </div>
          </div>

          <div class="space-y-2">
            <Label for="sales_representative_name">Sales Representative</Label>
            <Input v-model="form.sales_representative_name" id="sales_representative_name" placeholder="Enter sales representative name" />
            <div class="text-sm text-red-500" v-if="form.errors.sales_representative_name">{{ form.errors.sales_representative_name }}</div>
          </div>

          <div class="space-y-2">
            <Label for="deadline">Deadline</Label>
            <Input v-model="form.deadline" type="date" id="deadline" />
            <div class="text-sm text-red-500" v-if="form.errors.deadline">{{ form.errors.deadline }}</div>
          </div>
        </div>

        <div class="flex w-full justify-end">
          <Button type="submit" :disabled="form.processing || !form.client_id">Save Project</Button>
        </div>
      </form>
    </div>

    <!-- Step 2: Create New Client -->
    <div v-if="currentStep === 'create_client'" class="space-y-6">
      <h2 class="text-xl font-semibold">Create New Client</h2>
      <NewClient @client-created="handleClientCreated" @cancel="handleCancelCreateClient" back-route="projects.create" />
    </div>
  </div>
</template>
