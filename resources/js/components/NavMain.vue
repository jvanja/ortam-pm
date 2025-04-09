<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { Clipboard, Contact, Folder, LayoutGrid, SheetIcon, User } from 'lucide-vue-next';

const mainNavItems = [
  {
    title: 'Dashboard',
    href: '/dashboard',
    icon: LayoutGrid,
    permission: 'dashboard.view',
  },
  {
    title: 'PCA Reports',
    href: '/pca-reports',
    icon: Clipboard,
    permission: 'report.view',
  },
  {
    title: 'Projects',
    href: '/projects',
    icon: Folder,
    permission: 'project.view',
  },
  {
    title: 'Timesheeets',
    href: '/timesheets',
    icon: SheetIcon,
    permission: 'timesheet.view',
  },
  {
    title: 'Clients',
    href: '/clients',
    icon: Contact,
    permission: 'client.view',
  },
  {
    title: 'Employees',
    href: '/employees',
    icon: User,
    permission: 'admin.view',
  },
];
const page = usePage<SharedData>();
const hasPermission = (permission: string) => {
  return permission === '' ? true : page.props.auth.permissions.includes(permission);
};
</script>

<template>
  <SidebarGroup class="px-2 py-0">
    <SidebarGroupLabel>Platform</SidebarGroupLabel>
    <SidebarMenu>
      <SidebarMenuItem v-for="item in mainNavItems" :key="item.title">
        <SidebarMenuButton as-child :is-active="item.href === page.url">
          <Link v-if="hasPermission(item.permission)" :href="item.href">
            <component :is="item.icon" />
            <span>{{ item.title }}</span>
          </Link>
        </SidebarMenuButton>
      </SidebarMenuItem>
    </SidebarMenu>
  </SidebarGroup>
</template>
