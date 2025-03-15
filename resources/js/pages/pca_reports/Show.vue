<script setup lang="ts">
import { type BreadcrumbItem } from '@/types';
import { type p_c_a_reportsEntity } from '@/types/DatabaseModels';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
// import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { route } from 'ziggy-js';

const props = defineProps<{
  pca_report: p_c_a_reportsEntity;
}>();
// console.log(props.pca_report);

const report = ref(props.pca_report);
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'PCA Reports',
    href: '/pca_reports',
  },
  {
    title: props.pca_report.name,
    href: props.pca_report.id!,
  },
];

const form = useForm({
  occupation_of_the_property: props.pca_report.occupation_of_the_property,
});

  // console.warn("DEBUGPRINT[3]: Show.vue:31: form=", form)

const submit = () => {
  form.patch(route('pca_reports.update',  props.pca_report.id), {
      preserveScroll: true,
  });
};
</script>
<template>
  <Head title="PCA Report" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="flex flex-col gap-4 p-4">
        <Card>
          <CardHeader>
            <CardTitle>{{ report.name }}</CardTitle>
            <CardDescription>{{ report.year_of_construction }}</CardDescription>
          </CardHeader>
          <CardContent>
            <form @submit.prevent="submit" class="space-y-6">
              <div class="grid w-full items-center gap-4">
                <div class="flex flex-col space-y-1.5">
                  <Label htmlFor="name">Occupation of the property</Label>
                  <Input v-model="form.occupation_of_the_property" id="name" :placeholder="report.occupation_of_the_property" />
                </div>
              </div>
              <div class="flex w-full justify-end">
                <Button :disabled="form.processing">Save</Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>
