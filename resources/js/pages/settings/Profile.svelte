<script module lang="ts">
    import { edit } from '@/routes/profile';

    export const layout = {
        breadcrumbs: [
            {
                title: 'Profile settings',
                href: edit(),
            },
        ],
    };
</script>

<script lang="ts">
    import { Form, page } from '@inertiajs/svelte';
    import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
    import AppHead from '@/components/AppHead.svelte';
    import DeleteUser from '@/components/DeleteUser.svelte';
    import Heading from '@/components/Heading.svelte';
    import Button from '@/components/Actions/Button.svelte';
    import Input from '@/components/DataInput/Input.svelte';

    const user = $derived(page.props.auth.user);
</script>

<AppHead title="Profile settings" />

<h1 class="sr-only">Profile settings</h1>

<div class="flex flex-col space-y-6">
    <Heading
        variant="small"
        title="Profile information"
        description="Update your name and email address"
    />

    <Form
        {...ProfileController.update.form()}
        class="space-y-6"
        options={{ preserveScroll: true }}
    >
        {#snippet children({ errors, processing })}
            <Input
                label="Name"
                name="name"
                value={user.name}
                required
                autocomplete="name"
                placeholder="Full name"
                error={errors.name}
            />

            <Input
                label="Email address"
                name="email"
                type="email"
                value={user.email}
                required
                autocomplete="username"
                placeholder="Email address"
                disabled
                error={errors.email}
            />

            <div class="flex items-center gap-4">
                <Button
                    type="submit"
                    disabled={processing}
                    data-test="update-profile-button">Save</Button
                >
            </div>
        {/snippet}
    </Form>
</div>

<DeleteUser />
