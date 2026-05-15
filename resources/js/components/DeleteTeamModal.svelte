<script lang="ts">
    import { Form } from '@inertiajs/svelte';
    import Button from '@/components/Actions/Button.svelte';
    import {
        Dialog,
        DialogClose,
        DialogContent,
        DialogDescription,
        DialogFooter,
        DialogTitle,
    } from '@/components/ui/dialog';
    import Input from '@/components/DataInput/Input.svelte';
    import { destroy } from '@/routes/teams';
    import type { Team } from '@/types';

    let {
        team,
        open = $bindable(),
    }: {
        team: Team;
        open: boolean;
    } = $props();

    let confirmationName = $state('');
    let formKey = $state(0);

    const canDeleteTeam = $derived(confirmationName === team.name);

    function handleOpenChange(value: boolean) {
        open = value;

        if (!value) {
            confirmationName = '';
            formKey++;
        }
    }
</script>

<Dialog {open} onOpenChange={handleOpenChange}>
    <DialogContent>
        {#key formKey}
            <Form
                {...destroy.form(team.slug)}
                class="space-y-6"
                onSuccess={() => (open = false)}
            >
                {#snippet children({ errors, processing })}
                    <div class="space-y-3">
                        <DialogTitle>Are you sure?</DialogTitle>
                        <DialogDescription>
                            This action cannot be undone. This will permanently
                            delete the team
                            <strong>"{team.name}"</strong>.
                        </DialogDescription>
                    </div>

                    <div class="space-y-4 py-4">
                        <div class="grid gap-2">
                            <Input
                                name="name"
                                value={confirmationName}
                                oninput={(event) =>
                                    (confirmationName = (
                                        event.currentTarget as HTMLInputElement
                                    ).value)}
                                placeholder="Enter team name"
                                autocomplete="off"
                                error={errors.name}
                                data-test="delete-team-name"
                            />
                        </div>
                    </div>

                    <DialogFooter class="gap-2">
                        <DialogClose>
                            <Button variant="secondary">Cancel</Button>
                        </DialogClose>

                        <Button
                            variant="destructive"
                            type="submit"
                            disabled={!canDeleteTeam || processing}
                            data-test="delete-team-confirm"
                        >
                            Delete team
                        </Button>
                    </DialogFooter>
                {/snippet}
            </Form>
        {/key}
    </DialogContent>
</Dialog>
