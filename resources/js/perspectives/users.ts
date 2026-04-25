import type { DataAction, DataHeader } from "@/types/core/dataDisplay";

import { Perspective } from "@/lib/core/perspective";
import type { User } from "@/types/auth";

type UserTableConfig = {
  headers: DataHeader<User>[];
  actions: DataAction<User>[];
};

export default new Perspective<UserTableConfig>(
    {
        user: () => ({
            headers: [
                { label: 'ID', key: 'id' },
                { label: 'Name', key: 'name' },
                { label: 'Email', key: 'email' },
            ],
            actions: [
                { label: 'Edit' },
            ],
        }),
        admin() {
            return {
                headers: [
                    { label: 'ID', key: 'id' },
                    { label: 'Name', key: 'name' },
                    { label: 'Email', key: 'email' },
                    { label: 'Role', key: 'role' },
                ],
                actions: [
                    { label: 'Edit' },
                    { label: 'Delete' },
                ],
            };
        },
    },
    () => ({
        headers: [
            { label: 'ID', key: 'id' },
            { label: 'Name', key: 'name' },
        ],
        actions: [],
    })
);