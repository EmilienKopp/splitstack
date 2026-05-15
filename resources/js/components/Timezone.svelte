<script lang="ts">
    import { Globe } from 'lucide-svelte';
    import { listTimezones, getTimezone } from '@/lib/core/support/timezone';
    import { Form } from '@inertiajs/svelte';
    import { shared } from '@/lib/inertia';
    import Combobox from '@/components/DataInput/Combobox.svelte';
    import Button from '@/components/Actions/Button.svelte';
    import { CheckIcon } from 'lucide-svelte';
    import { ChevronsUpDownIcon } from 'lucide-svelte';
    import { tick } from 'svelte';
    import * as Command from '@/components/ui/command/index.js';
    import * as Popover from '@/components/ui/popover/index.js';
    import Tip from './Tip.svelte';
    let timezones: string[] = $state(listTimezones());
    let selectedTimezone: string = $state(shared('timezone') ?? getTimezone());
</script>

<Button variant="ghost" size="icon" class="mx-1 rounded-full pt-1">
    <Form method="post" action={''}>
        {#snippet children({ processing }: any)}
            <Popover.Root>
                <Popover.Trigger>
                    <Tip text={selectedTimezone}>
                        <Globe class="size-5 opacity-80 group-hover:opacity-100" />
                    </Tip>
                </Popover.Trigger>
                <Popover.Content class="w-80">
                    <Combobox
                        options={timezones.map((tz) => ({ value: tz, label: tz }))}
                        bind:value={selectedTimezone} />
                    <Button type="submit" class="w-full mt-2" disabled={processing}>Save</Button>
                </Popover.Content>
            </Popover.Root>
        {/snippet}
    </Form>
</Button>
