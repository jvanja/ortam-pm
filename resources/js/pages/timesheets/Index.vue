<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import ObjectList from '@/components/ObjectList.vue';
import Pagination from '@/components/Pagination.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'; // Import Select components
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Project, TimeSheet, User } from '@/types'; // Import User type
import { Head, router } from '@inertiajs/vue3';
import { debounce } from 'lodash-es';
import { computed, ref, watch } from 'vue';
type TimesheetWithProject = TimeSheet & { project: Project };

const props = defineProps<{
  timesheets: {
    data: TimesheetWithProject[];
    meta: {
      current_page: number;
      from: number;
      last_page: number;
      links: Array<any>;
      path: string;
      per_page: number;
      to: number;
      total: number;
    };
  };
  filters: { search: string | null; user_id: string | null };
  users: User[]; // Add users prop
}>();
console.log(props)

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Timesheets', href: '/timesheets' }];
const searchQuery = ref(props.filters.search || '');
const selectedUser = ref(props.filters.user_id || '');
console.log(selectedUser)

const timesheets = props.timesheets;
const ts_created = timesheets.data.map((ts) => new Date(ts.created_at!).toLocaleDateString());
const ts_hours = timesheets.data.map((ts) => ts.worked_duration);
const ts_task = timesheets.data.map((ts) => ts.task_performed);
const ts_client = timesheets.data.map((ts) => ts.project.type);

const objects = computed(() =>
  props.timesheets.data.map((timesheet) => {
    return {
      id: timesheet.id!,
      name: `<strong>${timesheet.project.type}</strong> - ${timesheet.task_performed} - ${timesheet.worked_duration} hours`,
    };
  }),
);

const chartOptions = {
  chart: {
    id: 'timesheets',
    type: 'bar',
    width: '100%',
    height: 380,
    events: {
      click: (_event: string, _chartContext: unknown, opts: { dataPointIndex: number }) => console.log(opts.dataPointIndex),
    },
  },
  xaxis: {
    categories: ts_created,
  },
  tooltip: {
    enabled: true,
    x: {
      formatter: function (_value: string, { dataPointIndex }: { dataPointIndex: number }) {
        return ts_client[dataPointIndex];
      },
    },
    y: {
      formatter: function (_value: string, { dataPointIndex }: { dataPointIndex: number }) {
        return ts_task[dataPointIndex];
      },
    },
  },
};
const series = [{ name: 'Task', data: ts_hours }];

// Watch for changes in searchQuery
watch(
  searchQuery,
  debounce((newValue: string) => {
    router.get(
      '/timesheets',
      { search: newValue, user_id: selectedUser.value === '' ? null : selectedUser.value }, // Pass both filters
      {
        preserveState: true,
        preserveScroll: true, // Keep scroll position after update
        replace: true, // Avoids adding duplicate history entries
      },
    );
  }, 300),
); // Debounce requests by 300ms

// Watch for changes in selectedUser
watch(
  selectedUser,
  debounce((newValue: string) => {
    router.get(
      '/timesheets',
      { search: searchQuery.value, user_id: newValue === '' ? null : newValue }, // Pass both filters
      {
        preserveState: true,
        preserveScroll: true,
        replace: true,
      },
    );
  }, 300),
);

const currentPage = ref(props.timesheets.meta.current_page || 1);
const onPageChange = (page: number) => {
  currentPage.value = page;
  router.get(
    'timesheets',
    { page: page, search: searchQuery.value, user_id: selectedUser.value === '' ? null : selectedUser.value }, // Pass both filters
    { preserveScroll: true },
  );
};
</script>

<template>
  <Head title="Timesheets" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="flex flex-col gap-4 p-4">
        <Heading title="Timesheets" description="These are your latest timesheets" />
        <div id="chart">
          <apexchart :options="chartOptions" :series="series"></apexchart>
        </div>
        <div class="grid gap-1 md:grid-cols-2">
          <Input id="search" v-model="searchQuery" class="mt-1 block w-full" placeholder="Search the timesheets..." />
          <Select v-model="selectedUser">
            <SelectTrigger class="w-full mt-1">
              <SelectValue placeholder="Filter by user" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="all">All Users</SelectItem>
              <SelectItem v-for="user in users" :key="user.id" :value="String(user.id)">
                {{ user.name }}
              </SelectItem>
            </SelectContent>
          </Select>
        </div>
        <ObjectList :objects="objects" type="timesheets" />
        <Pagination :currentPage="currentPage" :pagesMeta="timesheets.meta" :onPageChange="onPageChange" />
        <Button>Add new</Button>
      </div>
    </div>
  </AppLayout>
</template>
<style scoped>
#chart {
  width: 760px;
  max-width: 100%;
  margin: 0 auto 3rem;
}
</style>
