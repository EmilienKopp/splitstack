<script lang="ts">
    import { Form, page } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import AppLogoIcon from '@/components/AppLogoIcon.svelte';
    import Button from '@/components/Actions/Button.svelte';
    import Input from '@/components/DataInput/Input.svelte';
    import Label from '@/components/DataInput/Label.svelte';
    import InputError from '@/components/DataInput/InputError.svelte';
    import registerOnTheFly from '@/routes/register-on-the-fly';

    const user = $derived(page.props.user);

    let slug = $state(page.props.suggested_org_slug);
    let name = $state(page.props.org.name);
</script>

<!-- <AppHead title="Create your organization" /> -->

<div
    class="flex min-h-screen flex-col items-center justify-center bg-[#FDFDFC] p-6 text-[#1b1b18] dark:bg-[#0a0a0a]"
>
    <div class="w-full max-w-sm">
        <div class="mb-8 flex items-center justify-center gap-2">
            <div
                class="flex size-9 items-center justify-center rounded-md bg-sidebar-primary text-sidebar-primary-foreground"
            >
                <AppLogoIcon
                    class="size-5 fill-current text-white dark:text-black"
                />
            </div>
        </div>

        <div
            class="rounded-lg bg-white p-8 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:bg-[#161615] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]"
        >
            <h1
                class="mb-1 text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC]"
            >
                Create your user and organization
            </h1>
            <p class="mb-6 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                Set up a workspace for you and your team.
            </p>

            <Form action={registerOnTheFly.submit()} class="space-y-5">
                {#snippet children({ errors, processing })}
                    <Input
                        label="Email"
                        name="email"
                        value={user.email}
                        disabled
                        readonly
                    />

                    <Input
                        label="Name"
                        name="name"
                        value={user.name}
                        disabled
                        readonly
                    />

                    <Input
                        label="Organization name"
                        name="org_name"
                        value={name}
                        placeholder="Acme Inc."
                        required
                        autofocus
                        error={errors.org_name}
                    />

                    <div class="grid gap-1">
                        <Label for="org_slug">URL slug</Label>
                        <div class="flex overflow-hidden rounded-md border focus-within:ring-2">
                            <input
                                id="org_slug"
                                name="org_slug"
                                value={slug}
                                placeholder="acme-inc"
                                required
                                class="du-input flex-1 rounded-none border-0 shadow-none focus:outline-none"
                            />
                            {#if page.props.config.app?.rootDomain}
                                <span class="select-none border-l bg-muted px-3 py-2 text-sm text-muted-foreground flex items-center">
                                    .{page.props.config.app.rootDomain}
                                </span>
                            {/if}
                        </div>
                        <InputError message={errors.org_slug} />
                    </div>

                    <input
                        type="hidden"
                        name="org_id"
                        value={page.props.org.id}
                    />
                    <input type="hidden" name="workos_id" value={user.id} />
                    <input type="hidden" name="avatar" value={user.avatar} />

                    <Button type="submit" class="w-full" disabled={processing}>
                        Create organization
                    </Button>
                {/snippet}
            </Form>
        </div>
    </div>
</div>
