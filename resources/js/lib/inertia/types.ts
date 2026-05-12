export type { RequestPayload, VisitHelperOptions, Page } from '@inertiajs/core';

export type InertiaForm<T> = {
  data: T;
  errors: Partial<Record<keyof T, string>>;
  processing: boolean;
  progress: { percentage: number };
  wasSuccessful: boolean;
  recentlySuccessful: boolean;
  isDirty: boolean;
  setData(key: keyof T, value: any): void;
  get(url: string, options?: object): any;
  post(url: string, options?: object): void;
  put(url: string, options?: object): void;
  patch(url: string, options?: object): void;
  delete(url: string, options?: object): void;
  reset(...fields: (keyof T)[]): void;
  clearErrors(...fields: (keyof T)[]): void;
  submit(method: string, url: string, options?: object): void;
  transform(callback: (data: T) => object): void;
} & T;

export type RouterCallbacks = {
  onStart?: (event: any) => void;
  onProgress?: (event: any) => void;
  onFinish?: (event: any) => void;
  onSuccess?: (event: any) => void;
  onError?: (event: any) => void;
};

export type Context = {
  tenant: string;
  host: string;
  domain: string;
  executionContext: 'local' | 'web' | 'cli' | 'api' | 'desktop';

  availableTenants?: { 
    domain: string;
    github_user_id: string;
    google_user_id: string;
    host: string;
    name: string;
    tenant_id: string;
    user_id: string;
  }[];
};