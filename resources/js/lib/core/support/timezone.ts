import { TIME_FORMAT } from '@/lib/core/support/formatting';
import dayjs from 'dayjs';
import { getProps } from '@/lib/inertia/hooks.svelte';

/**
 * Get the timezone set in the user's browser.
 */
export function getTimezone() {
    return Intl.DateTimeFormat().resolvedOptions().timeZone;
}

export interface TimezoneAwareResult {
    time: string;
    dayOffset: string;
    isBefore: boolean;
    isAfter: boolean;
}

export function timezoneAware(
    time: Date | string,
    tz?: string,
    options?: { complex?: false },
): string;
export function timezoneAware(
    time: Date | string,
    tz?: string,
    options?: { complex: true },
): TimezoneAwareResult;
export function timezoneAware(
    time: Date | string,
    tz?: string,
    { complex = false } = {},
): string | TimezoneAwareResult {
    const { preferences } = getProps();
    const browserTz = getTimezone();

    if (!time) return TIME_FORMAT.replace(/H/g, '-').replace(/m/g, '-').replace(/s/g, '-');

    let t = dayjs(time);

    // convert to a display timezone if configured
    if (preferences.displayInTimezone) {
        t = t.tz(preferences.displayInTimezone);
    } else if (preferences.overrideUsingLocalTimezone) {
        t = t.tz(browserTz);
    }

    // compute D+1 or D-1 due to tz changes
    const originalDate = (tz ? dayjs(time).tz(tz) : dayjs(time)).format('YYYY-MM-DD');
    const convertedDate = t.format('YYYY-MM-DD');
    const isBefore = convertedDate < originalDate;
    const isAfter = convertedDate > originalDate;

    if (complex) {
        return {
            time: t.format(TIME_FORMAT),
            dayOffset: isBefore ? 'D-1' : isAfter ? 'D+1' : '',
            isBefore,
            isAfter,
        };
    }

    return t.format(TIME_FORMAT);
}

export function listTimezones(): string[] {
    return Intl.supportedValuesOf('timeZone');
}
