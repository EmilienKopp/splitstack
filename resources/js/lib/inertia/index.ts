import { router, useForm } from '@inertiajs/svelte';
import { get, Readable } from 'svelte/store';
import type { Context, InertiaForm, Page, RequestPayload, VisitHelperOptions } from './types';
import { page } from '@inertiajs/svelte';

export type * from './types';


export function setComponentHeader(event: any, component: string) {
  event.detail.visit.headers['X-Inertia-Component'] = component;
}

export function setDetachedHeader(event: any) {
  event.detail.visit.headers['X-Inertia-Detached'] = 'true';
}

// Wrap ziggy's route() to add "account" parameter automatically
export function xRoute(
  name: string,
  params?: Record<string, any>,
  absolute?: boolean
): string {
  const context = shared('context');
  const mergedParams = { ...(params || {}), account: context?.host };
  // @ts-ignore
  return route(name, mergedParams, absolute);
}

export function superUseForm<T extends object>(
  obj?: Partial<T>
): InertiaForm<T> {
  return useForm(obj as any);
}

export function hookSuccess<T extends object>(
  form: InertiaForm<T>,
  callback: () => void
): void {
  form.recentlySuccessful && callback();
}

export function getPage(path?: keyof Page): any {
  const p = page;
  if (path) {
    return path.split('.').reduce((obj: any, key: string) => obj && obj[key], p);
  }
  return p;
}

export function shared(path?: string): any {
  const context = page?.props;
  if (path) {
    return path
      .toString()
      .split('.')
      .reduce((obj: any, key: string) => obj && obj[key], context);
  }
  return context;
}

export function enums(path?: string): any {
  const enums = page?.props?.enums;
  if (path) {
    return path
      .toString()
      .split('.')
      .reduce((obj: any, key: string) => obj && obj[key], enums);
  }
  return enums;
}

export function getSharedContext(): Context {
  return page?.props?.context;
}


/**
 * Wrapper around inertia router post with preserved state and scroll
 */
export function xPost(route:string, data?: RequestPayload, options?: VisitHelperOptions) {
  return router.post(route, data ?? {}, {
    preserveScroll: true,
    preserveState: true,
    onStart: options?.onStart,
    onProgress: options?.onProgress,
    onFinish: options?.onFinish,
    onSuccess: options?.onSuccess,
    onError: options?.onError,
  });
}

/**
 * @returns The first role of the user
 */
export function getUserRoleName(): string {
  const pageStore = page;
  if(!pageStore?.props.auth.user)
    return 'guest';
  const roles = pageStore?.props.auth.user.roles;
  if(!roles || roles.length === 0) {
    return 'guest';
  }

  if(roles.filter((r) => r.name !== 'user').length > 0) {
    return roles.filter((r) => r.name !== 'user')[0].name;
  }

  return 'user';
}

/**
 * @returns Array of user roles except the default role
 */
export function getAllUserRoles(): string[] {
  const pageStore = page;
  if(!pageStore?.props.auth.user)
    return [];
  return (
    pageStore
      ?.props.auth.user.roles?.map((role) => role.name)
      .filter((r: string) => r != 'user') ?? []
  );
}
