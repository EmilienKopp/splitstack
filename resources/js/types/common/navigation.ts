import type { RouteDefinition } from '@/wayfinder';

export type NavigationElement = {
  name: string;
  href: string | RouteDefinition<'get'>;
  active?: boolean;
}
