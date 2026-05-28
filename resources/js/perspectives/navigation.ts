import { Perspective } from '@/lib/core/perspective';
import type { NavigationElement } from '@/types/core/navigation';

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
