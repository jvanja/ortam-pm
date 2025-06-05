import type { PageProps } from '@inertiajs/core';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
  permissions: any;
  user: User;
}

export interface BreadcrumbItem {
  title: string;
  href: string;
}

export interface NavItem {
  title: string;
  href: string;
  icon?: LucideIcon;
  isActive?: boolean;
}

export interface SharedData extends PageProps {
  name: string;
  quote: { message: string; author: string };
  auth: Auth;
}

// Import base entity types
import type {
  clientsEntity,
  filesEntity,
  invitationsEntity,
  invoice_itemsEntity,
  invoicesEntity,
  organizationsEntity,
  p_c_a_reportsEntity,
  project_pipeline_stagesEntity,
  projectsEntity,
  proposalsEntity,
  time_sheetsEntity,
  usersEntity,
} from './DatabaseModels';

// Re-export or extend base types for application use
// Note: IDs are typically strings (UUIDs) in DatabaseModels.ts
// Dates are Date objects or null

export interface User extends Omit<usersEntity, 'id' | 'created_at' | 'updated_at' | 'email_verified_at'> {
  id: string;
  email_verified_at: Date | string | null; // Allow string for potential serialization differences
  created_at: Date | string;
  updated_at: Date | string;
  avatar?: string; // Keep avatar if used elsewhere
}

export interface Project extends Omit<projectsEntity, 'id' | 'created_at' | 'updated_at' | 'deadline' | 'opening_date'> {
  id: string;
  created_at: Date | string | null;
  updated_at: Date | string | null;
  deadline: Date | string; // Allow string
  opening_date: Date | string; // Allow string
}
export interface Client extends Omit<clientsEntity, 'id' | 'created_at' | 'updated_at'> {
  id: string;
  created_at: Date | string | null;
  updated_at: Date | string | null;
  // address?: {
  //   'street': string | null
  //   'city': string | null
  //   'postal_code': string | null
  //   'state': string | null
  //   'country': string | null
  // }
}

export interface Invoice extends Omit<invoicesEntity, 'id' | 'created_at' | 'updated_at'> {
  id: string; // Assuming ID is always present
  created_at: Date | string;
  updated_at: Date | string | null;
  buyer_information: {
    name?: string;
    email?: string;
    phone?: string;
    fields?: [];
    company?: string;
    tax_number?: string;
    shipping_address?: string;
    address?: {
      [key: string]: number;
    };
  };
}

export interface InvoiceItem extends Omit<invoice_itemsEntity, 'id' | 'created_at' | 'updated_at'> {
  id: string; // Assuming ID is always present
  created_at: Date | string;
  updated_at: Date | string | null;
}

export interface Organization extends Omit<organizationsEntity, 'id' | 'created_at' | 'updated_at'> {
  id: string; // Assuming ID is always present
  created_at: Date | string | null;
  updated_at: Date | string | null;
}

export interface PCA_Report extends Omit<p_c_a_reportsEntity, 'id' | 'created_at' | 'updated_at'> {
  id: string; // Assuming ID is always present
  created_at: Date | string | null;
  updated_at: Date | string | null;
}

export interface Invitation extends Omit<invitationsEntity, 'id' | 'created_at' | 'updated_at'> {
  id: string; // Assuming ID is always present
  created_at: Date | string | null;
  updated_at: Date | string | null;
}

// Define the possible values for task_performed based on the database enum
export type TaskPerformedEnum = 'visit' | 'research' | 'fieldwork' | 'report';

export interface TimeSheet extends Omit<time_sheetsEntity, 'id' | 'created_at' | 'updated_at' | 'task_performed'> {
  id: string;
  task_performed: TaskPerformedEnum;
  details?: string | null;
  created_at: Date | string;
  updated_at: Date | string | null;
  project?: Project;
  user?: User;
  organization?: Organization;
}

export interface Proposal extends Omit<proposalsEntity, 'id' | 'created_at' | 'updated_at'> {
  id: string;
  created_at: Date | string | null;
  updated_at: Date | string | null;
}

export interface ProjectPipelineStage extends Omit<project_pipeline_stagesEntity, 'id' | 'created_at' | 'updated_at'> {
  id: string;
  created_at: Date | string | null;
  updated_at: Date | string | null;
}

export interface ProjectFile extends Omit<filesEntity, 'id' | 'created_at' | 'updated_at' | 'size'> {
  id: string;
  lastModified: Date | string;
  size: number;
  created_at: Date | string | null;
  updated_at: Date | string | null;
}

export type BreadcrumbItemType = BreadcrumbItem;
