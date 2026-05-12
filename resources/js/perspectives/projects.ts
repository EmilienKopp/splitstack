import type { DataAction, DataHeader } from '@/types/core/dataDisplay';

import { Perspective } from '@/lib/core/perspective';
import type { ProjectEntity as Project } from '@/types';

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
    actions: [],
}));
