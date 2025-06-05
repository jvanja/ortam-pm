import { usePage } from '@inertiajs/vue3';
import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';
import { useDateFormat } from '@vueuse/core'

const locale = localStorage.getItem('locale') || 'en';

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs));
}

export function getQuery() {
  return (usePage().props.ziggy as any).query;
}

export function formatDate (date:string | Date) {
  if(date && date !== '') return useDateFormat(date, 'MMMM D, YYYY HH:mm A', { locales: locale })
  else return ''
}

export function formatCurrency (amount: number, currency: string) {
  return new Intl.NumberFormat('us-EN', { style: 'currency', currency }).format(amount);
}
