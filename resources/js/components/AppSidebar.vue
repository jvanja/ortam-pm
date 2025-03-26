<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import useSidebar from '@/composables/useSidebar';
import { Link } from '@inertiajs/vue3';
import AppLogo from './AppLogo.vue';

const { sidebarMenu } = useSidebar();
console.warn("DEBUGPRINT[10]: AppSidebar.vue:10: sidebarMenu=", sidebarMenu)

const mainNavItems = sidebarMenu;


const footerNavItems = [
  { title: 'Superadmin', href: 'admin/user/switch/start/1' },
  { title: 'Employee', href: 'admin/user/switch/start/2' },
  { title: 'Client', href: 'admin/user/switch/start/3' },
];
</script>

<template>
  <Sidebar collapsible="icon" variant="inset">
    <SidebarHeader>
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton size="lg" as-child>
            <Link :href="route('dashboard')">
              <AppLogo />
            </Link>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarHeader>

    <SidebarContent>
      <NavMain :items="mainNavItems" />
    </SidebarContent>

    <SidebarFooter>
      <NavFooter :items="footerNavItems" />
      <NavUser />
    </SidebarFooter>
  </Sidebar>
  <slot />
</template>
