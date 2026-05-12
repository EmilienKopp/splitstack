import { readable } from '@/lib/core/support/formatting';

export const DrawerTypes = [
    'activity-daily',
    'activity-monthly',
    'projects',
    'organizations',
    'rates',
] as const;

export type DrawerType = (typeof DrawerTypes)[number];

export const DrawerTypeRegistry: Record<DrawerType, string> = DrawerTypes.reduce(
    (acc, drawer) => {
        acc[drawer] = readable(drawer);
        return acc;
    },
    {} as Record<DrawerType, string>,
);

interface DrawerState {
    activeDrawer: DrawerType | null;
    loading: boolean;
}

export const drawerState: DrawerState = $state({
    activeDrawer: null,
    loading: false,
});

export const useDetached = () => {
    return Boolean(drawerState.activeDrawer);
};

export function openDrawer(drawer: DrawerType) {
    drawerState.activeDrawer = drawer;
}

export function closeDrawer() {
    drawerState.activeDrawer = null;
    drawerState.loading = false;
}

export function setDrawerLoading(loading: boolean) {
    drawerState.loading = loading;
}
