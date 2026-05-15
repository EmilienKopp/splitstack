<script lang="ts">
    import Input from '$components/DataInput/Input.svelte';
    import Select from '$components/DataInput/Select.svelte';
    import Textarea from '$components/DataInput/Textarea.svelte';
    import type { ProjectEntity as Project } from '@/types/entities';
    import { Form } from '@inertiajs/svelte';
    import PrimaryButton from '$components/Actions/Button.svelte';
    import { useDialogContext } from '$lib/stores/global/dialogs.svelte';
    import ShowContainer from '@/components/ShowContainer.svelte';
    import { update } from '@/routes/projects';

    interface Props {
        project: Project;
        statusOptions: SelectOption[];
        onSuccess?: () => void;
    }

    let { project, statusOptions, onSuccess }: Props = $props();
</script>

<ShowContainer title={`Edit ${project.name}`} noActions>
    <Form action={update.url(project.id)} class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <Input label="Name" name="name" precog value={project.name} />
        <Select
            label="Status"
            name="status"
            options={statusOptions}
            value={project.status}
            precog />

        <Input type="date" label="Start Date" name="start_date" precog value={project.start_date} />
        <Input type="date" label="End Date" name="end_date" precog value={project.end_date} />
        <Textarea label="Description" name="description" class="col-span-2" precog>
            {project.description}
        </Textarea>
        <PrimaryButton type="submit" class="col-span-2 self-end">Save</PrimaryButton>
    </Form>
</ShowContainer>
