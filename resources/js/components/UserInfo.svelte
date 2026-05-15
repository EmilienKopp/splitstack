<script lang="ts">
    import Avatar from '@/components/Display/Avatar.svelte';
    import { getInitials } from '@/lib/initials';
    import type { Team, User } from '@/types';

    let {
        user,
        showEmail = false,
        team = null,
    }: {
        user: User;
        showEmail?: boolean;
        team?: Team | null;
    } = $props();
</script>

<Avatar
    src={user.avatar ?? undefined}
    fallback={getInitials(user.name)}
    alt={user.name}
    class="h-8 w-8 overflow-hidden rounded-lg"
    fallbackClass="rounded-lg text-black dark:text-white"
/>

<div class="grid flex-1 text-left text-sm leading-tight">
    <span class="truncate font-medium">{user.name}</span>
    {#if team}
        <span class="truncate text-xs text-muted-foreground">{team.name}</span>
    {:else if showEmail}
        <span class="truncate text-xs text-muted-foreground">{user.email}</span>
    {/if}
</div>
