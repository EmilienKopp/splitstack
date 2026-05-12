import { page, router, useFormContext, usePage } from '@inertiajs/svelte';
import { derived, get, Readable } from 'svelte/store';
import { RouterCallbacks } from './types';
import { openDrawer, type DrawerType } from '@/lib/stores/global/drawerState.svelte';

type InertiaFinishEvent = ReturnType<
    import('@inertiajs/core').GlobalEventCallback<
        'finish',
        import('@inertiajs/core').RequestPayload
    >
>;

export const getPage = (dotNotation?: string) => {
    const pageStore = page;
    const keys = dotNotation?.split('.') ?? [];

    let result: any = pageStore;

    for (const key of keys) {
        if (result && key in result) {
            result = result[key];
        } else {
            return undefined;
        }
    }

    return result;
};

const restProps = derived(page, ($page) => {
    const { props } = $page;
    const { flash, auth, enums, context, features, timezone, deferred, errors, ...restProps } =
        props;

    return {
        ...restProps,
    };
});

export function useRestProps() {
    return restProps;
}

const user = derived(page, ($page) => {
    return $page.props.auth?.user ?? null;
});

export function useUser() {
    const user = $derived(page.props.auth?.user);
    return {
        get value() {
            return user;
        },
    };
}

const enums = derived(page, ($page) => {
    return $page.props.enums ?? null;
});

export function useEnums() {
    return enums;
}

export const getProps = (dotNotation?: string) => {
    return getPage(dotNotation ? `props.${dotNotation}` : 'props');
};

export const getUser = (dotNotation?: string) => {
    return getPage(dotNotation ? `props.auth.user.${dotNotation}` : 'props.auth.user');
};

export const getEnums = (dotNotation?: string) => {
    return getPage(dotNotation ? `props.enums.${dotNotation}` : 'props.enums');
};

export const useReload = (
    route: any,
    data: Record<string, any> = {},
    { callbacks, drawer }: { callbacks?: RouterCallbacks; drawer?: DrawerType } = {},
): Promise<InertiaFinishEvent> => {
    const promise = new Promise<InertiaFinishEvent>((resolve) => {
        const finish = (event: any) => {
            resolve(event);
        };

        const mergedCallbacks = {
            ...callbacks,
            onFinish: (event: any) => {
                callbacks?.onFinish?.(event);
                finish(event);
            },
            onSuccess: (event: any) => {
                if (drawer) {
                    openDrawer(drawer);
                }
                callbacks?.onSuccess?.(event);
            },
        };

        router.get(route, data, {
            preserveUrl: true,
            preserveScroll: true,
            ...mergedCallbacks,
        });
    });

    return promise;
};

interface PrecogOptions {
    active?: boolean;
    handler?: (e?: any) => void;
    name?: string;
}

export const usePrecog = ({ active, handler, name }: PrecogOptions) => {
    if (!active) {
        return {};
    }

    const ctx = useFormContext();

    if (!ctx || !name?.length) {
        return {};
    }

    return {
        formContext: ctx,
        handleChange: (e?: any) => {
            ctx?.validate(name);
            if (handler) {
                handler(e);
            }
        },
    };
};

export const usePreferences = () => {
    const preferences = derived(page, ($page) => {
        return $page.props.preferences ?? {};
    });

    return preferences;
};
