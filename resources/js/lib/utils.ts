import type { ClassValue } from 'clsx';
import type { LinkComponentBaseProps } from '@inertiajs/core';
import { clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function toUrl(
    href: NonNullable<LinkComponentBaseProps['href']>,
): string {
    return typeof href === 'string' ? href : href.url;
}

export function OS() {
    let os;
    if (navigator.userAgentData) {
        navigator.userAgentData
            .getHighEntropyValues(['platform', 'platformVersion'])
            .then((ua) => {
                os = ua.platform;
            });
    } else {
        os = navigator.platform;
    }
    return os;
}
