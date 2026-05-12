import type { DataAction, DataHeader } from '@/types/core/dataDisplay';

import type { OrganizationEntity as Organization } from '@/types';
import { Perspective } from '@/lib/core/perspective';

type OrganizationTableConfig = {
    headers: DataHeader<Organization>[];
    actions: DataAction<Organization>[];
};

export default new Perspective<OrganizationTableConfig>({}, () => ({
    headers: [
        { label: 'Status', key: 'status' },
        { label: 'Name', key: 'name' },
        { label: 'Type', key: 'type' },
    ],
    actions: [],
}));
