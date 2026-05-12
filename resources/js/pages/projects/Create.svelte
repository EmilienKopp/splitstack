<script module lang="ts">
    import { index } from '@/routes/projects';
    import { usePage } from '@inertiajs/svelte';
    const page = usePage();
    console.log('Page props:', page.props);
    export const layout = {
        breadcrumbs: [
            { title: 'Projects', href: index() },
            { title: 'New project', href: '#' },
        ],
    };
</script>

<script lang="ts">
    import { Form } from '@inertiajs/svelte';
    import ProjectController from '@/actions/App/Http/Controllers/TimeTracking/ProjectController';
    import AppHead from '@/components/AppHead.svelte';
    import Heading from '@/components/Heading.svelte';
    import InputError from '@/components/InputError.svelte';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';

    interface SelectOption {
        value: string;
        label: string;
    }

    interface Props {
        statusOptions: SelectOption[];
        typeOptions: SelectOption[];
    }

    let { statusOptions, typeOptions }: Props = $props();
</script>

<AppHead title="New project" />

<div class="flex flex-col space-y-6">
    <Heading
        title="New project"
        description="Create a new project to track time against"
    />

    <Form {...ProjectController.store.form()} class="space-y-6">
        {#snippet children({ errors, processing })}
            <div class="grid gap-2">
                <Label for="name">Name</Label>
                <Input
                    id="name"
                    name="name"
                    required
                    placeholder="Project name"
                />
                <InputError message={errors.name} />
            </div>

            <div class="grid gap-2">
                <Label for="description">Description</Label>
                <textarea
                    id="description"
                    name="description"
                    rows="3"
                    placeholder="Optional description"
                    class="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring flex w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                ></textarea>
                <InputError message={errors.description} />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="type">Type</Label>
                    <select
                        id="type"
                        name="type"
                        class="border-input bg-background ring-offset-background focus-visible:ring-ring flex h-10 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        {#each typeOptions as option (option.value)}
                            <option value={option.value}>{option.label}</option>
                        {/each}
                    </select>
                    <InputError message={errors.type} />
                </div>

                <div class="grid gap-2">
                    <Label for="status">Status</Label>
                    <select
                        id="status"
                        name="status"
                        class="border-input bg-background ring-offset-background focus-visible:ring-ring flex h-10 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        {#each statusOptions as option (option.value)}
                            <option value={option.value}>{option.label}</option>
                        {/each}
                    </select>
                    <InputError message={errors.status} />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="start_date">Start date</Label>
                <Input id="start_date" name="start_date" type="date" />
                <InputError message={errors.start_date} />
            </div>

            <div class="flex items-center gap-4">
                <Button type="submit" disabled={processing}
                    >Create project</Button
                >
            </div>
        {/snippet}
    </Form>
</div>
