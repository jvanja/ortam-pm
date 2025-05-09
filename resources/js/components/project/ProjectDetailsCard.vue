<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import type { Client, Project } from '@/types';
import type { InertiaForm } from '@inertiajs/vue3';
import currencies from 'currency-codes';
import { ChevronRight } from 'lucide-vue-next';

const props = defineProps<{
  project: Project;
  client: Client;
  form: InertiaForm<Project>;
  submit: () => void;
}>();
</script>

<template>
  <Card>
    <CardHeader>
      <CardTitle>{{ project.type }}</CardTitle>
      <CardDescription>
        <strong>
          <a :href="'/clients/' + client.id" class="inline-flex items-center justify-center gap-2">
            {{ client.company_name }} <ChevronRight class="h-4 w-4" />
          </a>
        </strong>
        <div>{{ project.address }}</div>
      </CardDescription>
    </CardHeader>
    <CardContent>
      <form @submit.prevent="submit" class="space-y-6">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div class="flex flex-col space-y-1.5">
            <Label htmlFor="type">Project Type</Label>
            <Input id="type" :placeholder="form.type" v-model="form.type" />
          </div>
          <div class="flex flex-col space-y-1.5">
            <Label htmlFor="department">Departament</Label>
            <Input id="department" :placeholder="form.department" v-model="form.department" />
          </div>
          <div class="flex flex-col space-y-1.5">
            <Label htmlFor="rep">Representative Name</Label>
            <Input id="rep" placeholder="Representative name" v-model="form.sales_representative_name" />
          </div>
          <div class="flex flex-col space-y-1.5">
            <Label htmlFor="pm">Project Manager</Label>
            <Input id="pm" :placeholder="project.manager" v-model="form.manager" />
          </div>
          <div class="flex flex-col space-y-1.5">
            <Label htmlFor="status">Status</Label>
            <Select :defaultValue="project.status" v-model="form.status">
              <SelectTrigger id="status">
                <SelectValue />
              </SelectTrigger>
              <SelectContent position="popper">
                <SelectItem value="ongoing">Ongoing</SelectItem>
                <SelectItem value="completed">Completed</SelectItem>
                <SelectItem value="canceled">Canceled</SelectItem>
              </SelectContent>
            </Select>
          </div>
          <div class="flex flex-col space-y-1.5">
            <Label htmlFor="budget">Budget</Label>
            <div class="flex gap-2">
              <Input id="budget" v-model="form.budget" />
              <Select id="currency" :defaultValue="project.currency" v-model="form.currency">
                <SelectTrigger id="currency" class="mt-0">
                  <SelectValue />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem v-for="code in currencies.codes()" :key="code" :value="code">{{ code }}</SelectItem>
                </SelectContent>
              </Select>
            </div>
          </div>
        </div>
        <div class="flex w-full justify-end">
          <Button :disabled="form.processing">Save</Button>
        </div>
      </form>
    </CardContent>
  </Card>
</template>
