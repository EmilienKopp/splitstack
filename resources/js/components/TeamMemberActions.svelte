<script lang="ts">
  import ChevronDown from 'lucide-svelte/icons/chevron-down';
  import X from 'lucide-svelte/icons/x';
  import Button from '@/components/Actions/Button.svelte';
  import Tip from '@/components/Tip.svelte';
  import Badge from '@/components/Display/Badge.svelte';
  import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
  } from '@/components/ui/dropdown-menu';
  import type { RoleOption, TeamMember, TeamPermissions } from '@/types';

  interface Props {
    member: TeamMember;
    permissions: TeamPermissions;
    availableRoles: RoleOption[];
    onRoleChange?: (newRole: string) => void;
    onRemove?: () => void;
  }

  let { member, permissions, availableRoles, onRoleChange, onRemove }: Props = $props();
</script>

<div class="flex items-center gap-2">
  {#if member.role !== 'owner' && permissions.canUpdateMember}
    <DropdownMenu>
      <DropdownMenuTrigger asChild>
        {#snippet children(props)}
          <Button
            variant="outline"
            size="sm"
            onclick={props.onclick}
            aria-expanded={props['aria-expanded']}
            data-state={props['data-state']}
            data-test="member-role-trigger"
          >
            {member.role_label}
            <ChevronDown class="ml-2 h-4 w-4 opacity-50" />
          </Button>
        {/snippet}
      </DropdownMenuTrigger>
      <DropdownMenuContent>
        {#each availableRoles as role (role.value)}
          <DropdownMenuItem asChild>
            {#snippet children(props)}
              <button
                type="button"
                class={props.class}
                data-test="member-role-option"
                onclick={() => {
                  props.onClick?.();
                  onRoleChange?.(role.value);
                }}
              >
                {role.label}
              </button>
            {/snippet}
          </DropdownMenuItem>
        {/each}
      </DropdownMenuContent>
    </DropdownMenu>
  {:else}
    <Badge variant="secondary">{member.role_label}</Badge>
  {/if}

  {#if member.role !== 'owner' && permissions.canRemoveMember}
    <Tip text="Remove member">
      <Button
        variant="ghost"
        size="sm"
        onclick={onRemove}
        data-test="member-remove-button"
      >
        <X class="h-4 w-4" />
      </Button>
    </Tip>
  {/if}
</div>
