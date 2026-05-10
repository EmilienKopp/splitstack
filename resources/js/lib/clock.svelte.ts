import dayjs from 'dayjs';
import duration from 'dayjs/plugin/duration';

dayjs.extend(duration);

class Clock {
    #datetime = $state(Date.now());

    get time() {
        return dayjs(this.#datetime).format('HH:mm:ss');
    }

    get h() {
        return dayjs(this.#datetime).hour();
    }

    get m() {
        return dayjs(this.#datetime).minute();
    }

    get s() {
        return dayjs(this.#datetime).second();
    }

    refresh() {
        this.#datetime = Date.now();
    }

    since(datetime: string | number | Date): string {
        const seconds = dayjs(this.#datetime).diff(dayjs(datetime), 'seconds');
        return dayjs.duration(seconds, 'seconds').format('HH:mm:ss');
    }
}

export const clock = new Clock();
