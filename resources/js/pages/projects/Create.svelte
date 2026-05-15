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
    import Button from '@/components/Actions/Button.svelte';
    import Input from '@/components/DataInput/Input.svelte';
    import Textarea from '@/components/DataInput/Textarea.svelte';
    import Select from '@/components/DataInput/Select.svelte';

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
            <Input
                label="Name"
                name="name"
                required
                placeholder="Project name"
                error={errors.name}
            />

            <Textarea
                label="Description"
                name="description"
                rows={3}
                placeholder="Optional description"
                error={errors.description}
            />

            <div class="grid grid-cols-2 gap-4">
                <Select
                    label="Type"
                    name="type"
                    options={typeOptions}
                    mapping={{ valueColumn: 'value', labelColumn: 'label' }}
                    error={errors.type}
                />

                <Select
                    label="Status"
                    name="status"
                    options={statusOptions}
                    mapping={{ valueColumn: 'value', labelColumn: 'label' }}
                    error={errors.status}
                />
            </div>

            <Input
                label="Start date"
                name="start_date"
                type="date"
                error={errors.start_date}
            />

            <div class="flex items-center gap-4">
                <Button type="submit" disabled={processing}>Create project</Button>
            </div>
        {/snippet}
    </Form>
</div>
