<script lang="ts">
    import { CheckIcon } from 'lucide-svelte';
    import { ChevronsUpDownIcon } from 'lucide-svelte';
    import { tick } from 'svelte';
    import * as Command from '$components/ui/command/index.js';
    import * as Popover from '$components/ui/popover/index.js';
    import { Button } from '$components/ui/button/index.js';
    import { cn } from '$lib/utils.js';

    interface Props {
        options: {
            value: string;
            label: string;
        }[];
        value?: string;
        placeholder?: string;
        group?: string;
    }
    let {
        options = [],
        value = $bindable(''),
        placeholder = 'Select an option...',
        group = 'options',
    }: Props = $props();

    let open = $state(false);
    let triggerRef = $state<HTMLButtonElement>(null!);
    let search = $state(value ?? '');

    const selectedValue = $derived(options.find((f) => f.value === value)?.label);

    // We want to refocus the trigger button when the user selects
    // an item from the list so users can continue navigating the
    // rest of the form with the keyboard.
    function closeAndFocusTrigger() {
        open = false;
        tick().then(() => {
            triggerRef.focus();
        });
    }
</script>

<Popover.Root bind:open>
    <Popover.Trigger bind:ref={triggerRef}>
        {#snippet child({ props })}
            <Button
                {...props}
                variant="outline"
                class="w-[200px] justify-between"
                role="combobox"
                aria-expanded={open}
                onClick={() => (search = '')}>
                {selectedValue || placeholder}
                <ChevronsUpDownIcon class="opacity-50" />
            </Button>
        {/snippet}
    </Popover.Trigger>
    <Popover.Content class="w-[200px] p-0">
        <Command.Root>
            <Command.Input {placeholder} bind:value={search} />
            <Command.List>
                <Command.Empty>No options found.</Command.Empty>
                <Command.Group value={group}>
                    {#each options as option (option.value)}
                        <Command.Item
                            value={option.value}
                            onSelect={() => {
                                console.log('Selected value:', option.value);
                                value = option.value;
                                closeAndFocusTrigger();
                            }}>
                            <CheckIcon class={cn(value !== option.value && 'text-transparent')} />
                            {option.label}
                        </Command.Item>
                    {/each}
                </Command.Group>
            </Command.List>
        </Command.Root>
    </Popover.Content>
</Popover.Root>
