import { capitalize } from './strings';
import dayjs from 'dayjs';
import duration from 'dayjs/plugin/duration';
import { getTimezone } from './timezone';
import timezone from 'dayjs/plugin/timezone';
import utc from 'dayjs/plugin/utc';

interface SelectOption {
    value: string | number;
    label: string;
    name: string;
}

dayjs.extend(utc);
dayjs.extend(timezone);
dayjs.extend(duration);

export const DATE_FORMAT = 'YYYY/MM/DD';
export const TIME_FORMAT = 'HH:mm';
export const DATETIME_FORMAT = `${DATE_FORMAT} ${TIME_FORMAT}`;
export const DURATION_FORMAT = 'HH[h]mm[mn]ss[s]';
export const DURATION_FORMAT_SHORT = 'HH[h]mm[mn]';

export function readable(str: string): string {
    return capitalize(str.replaceAll(/[_-]/g, ' '));
}

export function date(date: Date | string | null, format?: string) {
    if (!date) return '';
    return dayjs(date).format(format ?? DATE_FORMAT);
}

export function time(date: Date | string | null, format?: string) {
    if (!date) return '';
    return dayjs(date).format(format ?? TIME_FORMAT);
}

export function diffSeconds(far: Date | string, near: Date | string) {
    const seconds = dayjs(far).diff(near, 'seconds');
    return seconds;
}

export function datetime(date: Date | string | null, format?: string) {
    if (!date) return '';
    return dayjs(date).format(format ?? DATETIME_FORMAT);
}

export function secondsToHHMM(seconds: number | undefined): string {
    return dayjs.duration(seconds ?? 0, 'seconds').format('HH[h]mm[mn]');
}

export function secondsToHHMMSS(seconds: number | undefined): string {
    return dayjs.duration(seconds ?? 0, 'seconds').format(DURATION_FORMAT);
}

export function smartDuration(seconds: number | undefined): string {
    if (!seconds) return '';
    if (seconds < 3600) {
        return dayjs.duration(1000 * seconds).format('mm[m]');
    } else {
        return dayjs.duration(1000 * seconds).format('H[h] mm[m]');
    }
}

export function timespan(
    start: Date | string,
    end: Date | string,
    format: string = DURATION_FORMAT_SHORT,
) {
    const seconds = dayjs(end).diff(start, 'seconds');
    return dayjs.duration(seconds, 'seconds').format(format);
}

/**
 *
 * @param value amount to format
 * @param locale only 'ja-JP' and 'en-US' are supported
 * @returns a formatted string with the correct currency symbol
 */
export function currency(
    value: number | string | { amount: string | number; currency: string },
    locale: 'ja-JP' | 'en-US' = 'ja-JP',
): string {
    let currency = locale === 'ja-JP' ? 'JPY' : 'USD';
    if (typeof value === 'object' && value !== null && 'amount' in value) {
        currency = value.currency;
        value = value.amount;
    }

    value = Number(value);

    if (isNaN(value)) return '';
    return new Intl.NumberFormat(locale, {
        style: 'currency',
        currency: currency,
    }).format(value);
}

/**
 * Generate an array of objects with value, label (as is) and name (capitalized).
 * If `data` is an array of strings, it will be used as both value and label.
 * @param data Array of objects or strings
 * @param valueColumn (optional) key to use as value
 * @param nameColumn (optional) key to use as name
 * @returns an array of objects with value, label, and name keys
 */
export function asSelectOptions<T extends { [key: string]: any }>(
    data?: Record<string, number | string>[] | string[] | T[],
    valueColumn?: string,
    nameColumn?: string,
): SelectOption[] {
    if (!data?.length) return [];
    if (data.every((d: unknown) => typeof d === 'string')) {
        return (data as string[]).map((d: string) => ({
            value: d,
            label: d,
            name: capitalize(d),
        }));
    }
    if (!valueColumn || !nameColumn) {
        throw new Error('Value and name columns are required for object arrays');
    }
    return data.map((d: Record<string, number | string>) => ({
        value: d[valueColumn],
        name: capitalize(d[nameColumn] as string),
        label: d[nameColumn] as string,
    }));
}

/**
 * Parses a string range into a tuple of numbers
 * @param range a string of two numbers separated by a separator
 * @param separator the character(s) used to separate the two numbers
 * @returns a tuple with the two numbers
 */
export function formatStringRangeToNumbers(
    range: string,
    separator: string = '-',
): [number, number] {
    let first: number, last: number;
    range = range.replaceAll(/[\s.,]/g, ''); // remove spaces, commas, and dots
    const firstNumberMatch = range.match(/^(-?[0-9.,]+)/);
    const secondNumberMatch = range.match(/(-?[0-9.,]+)$/);
    if (!secondNumberMatch) {
        if (!firstNumberMatch) return [0, 0];
        return [Number(firstNumberMatch[0]), Number(firstNumberMatch[0])];
    } else if (!firstNumberMatch) {
        return [Number(secondNumberMatch[0]), Number(secondNumberMatch[0])];
    }

    first = Number(firstNumberMatch[0]);
    const separatorIndex = range.indexOf(separator, firstNumberMatch[0].length);
    last = Number(range.slice(separatorIndex + 1));

    const max = Math.max(first, last);
    const min = Math.min(first, last);
    return [min, max];
}
