import type { DataAction, DataHeader } from '@/types/core/dataDisplay';
import { Eye, SquarePen } from 'lucide-svelte';
import { edit, show } from '@/routes/projects';

import { Perspective } from '@/lib/core/perspective';
import type { ProjectEntity as Project } from '@/types';
import { router } from '@inertiajs/svelte';

type ProjectTableConfig = {
    headers: DataHeader<Project>[];
    actions: DataAction<Project>[];
};

export default new Perspective<ProjectTableConfig>({}, () => ({
    headers: [
        { label: 'Status', key: 'status' },
        { label: 'Name', key: 'name' },
        { label: 'Type', key: 'type' },
    ],
    actions: [
        {
            label: 'View',
            href: (project) => (project?.id ? show.url(project.id) : undefined),
            listViewOnly: true,
            icon: () => Eye,
            iconOnly: true,
        },
        {
            label: 'Edit',
            href: (project) => (project?.id ? edit.url(project.id) : undefined),
            icon: () => SquarePen,
            iconOnly: true,
        },
    ],
}));
