import type { Context } from "$lib/inertia";
import { get } from 'svelte/store';
import { page } from '@inertiajs/svelte';

class TenantContextClass {
  get available(): Context['availableTenants'] {
    const context = page?.props?.context;
    if(!context?.availableTenants) return [];
    return context.availableTenants;
  }

  get current(): Omit<Context, 'availableTenants'> {
    const context = page?.props?.context;
    if (!context) return {} as Omit<Context, 'availableTenants'>;
    const { availableTenants, ...rest } = context;
    return rest;
  }
}

export const TenantContext = new TenantContextClass();

export function useTenantContext() {
  return TenantContext;
}
