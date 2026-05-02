import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

if (typeof window !== 'undefined') {
    window.Pusher = Pusher;

    const isTLS = window.location.protocol === 'https:';
    const defaultPort = isTLS ? 443 : 80;

    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: window.location.hostname,
        wsPort: defaultPort,
        wssPort: defaultPort,
        forceTLS: isTLS,
        enabledTransports: ['ws', 'wss'],
    });

    window.Echo.private(`translucid.one-in-emilien`).listen(
        '.translucid.updated.users.3',
        (e) => console.log(e),
    );
}
