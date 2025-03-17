import { ref } from 'vue';
import { type NavItem } from '@/types';
import { Contact, Folder, Clipboard, LayoutGrid } from 'lucide-vue-next';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'PCA Reports',
        href: '/pca-reports',
        icon: Clipboard,
    },
    {
        title: 'Projects',
        href: '/projects',
        icon: Folder,
    },
    {
        title: 'Clients',
        href: '/clients',
        icon: Contact,
    },
];
const sidebarMenu = ref(mainNavItems);

export default function useSidebar() {
  const setSidebarMenu = (newMenu?: NavItem[]) => {
    if(newMenu) sidebarMenu.value = newMenu;
    else sidebarMenu.value = mainNavItems;
  };

  return {
    sidebarMenu,
    setSidebarMenu,
  };
}
