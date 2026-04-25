import type { NavigationElement } from '@/types/core/navigation';
import { Perspective } from '@/lib/core/perspective';

export default new Perspective<NavigationElement[]>(
    {
        user: () => [
            { name: 'Dashboard', href: '/dashboard' },
        ],
    },
    () => [
        { name: 'Home', href: '/dashboard' },
    ]
);
