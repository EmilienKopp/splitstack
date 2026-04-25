<script lang="ts">
    import { Form, page } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import AppLogoIcon from '@/components/AppLogoIcon.svelte';
    import InputError from '@/components/InputError.svelte';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';

    let name = $state('');
    let slug = $state('');
    let slugEdited = $state(false);

    const user = $derived(page.props.user);

    function slugify(value: string): string {
        return value
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }

    function onNameInput(e: Event) {
        name = (e.target as HTMLInputElement).value;
        if (!slugEdited) {
            slug = slugify(name);
        }
    }

    function onSlugInput(e: Event) {
        slug = (e.target as HTMLInputElement).value;
        slugEdited = true;
    }

    $inspect(user);
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
                <AppLogoIcon class="size-5 fill-current text-white dark:text-black" />
            </div>
        </div>

        <div
            class="rounded-lg bg-white p-8 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:bg-[#161615] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]"
        >
            <h1 class="mb-1 text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">
                Create your user and organization
            </h1>
            <p class="mb-6 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                Set up a workspace for you and your team.
            </p>

            <Form action="/orgs" method="post" class="space-y-5">
                {#snippet children({ errors, processing })}
                    <div class="grid gap-2">
                        <Label for="email">Email</Label>
                        <Input
                            id="email"
                            name="email"
                            value={user.email}
                            disabled
                            class="cursor-not-allowed bg-muted/50"
                        />

                        <div class="grid gap-2 col-span-full">
                            <Label for="first_name">First Name</Label>
                            <Input
                                id="first_name"
                                name="first_name"
                                value={user.firstName}
                                disabled
                                class="cursor-not-allowed bg-muted/50"
                            />
                            <Label for="last_name">Last Name</Label>
                            <Input
                                id="last_name"
                                name="last_name"
                                value={user.lastName}
                                disabled
                                class="cursor-not-allowed bg-muted/50"
                            />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="name">Organization name</Label>
                        <Input
                            id="name"
                            name="name"
                            value={name}
                            oninput={onNameInput}
                            placeholder="Acme Inc."
                            required
                            autofocus
                        />
                        <InputError message={errors.name} />
                    </div>

                    <div class="grid gap-2">
                        <Label for="slug">URL slug</Label>
                        <div
                            class="flex overflow-hidden rounded-md border focus-within:ring-2 focus-within:ring-ring"
                        >
                            <span
                                class="select-none border-r bg-muted px-3 py-2 text-sm text-muted-foreground"
                            >
                                app/
                            </span>
                            <Input
                                id="slug"
                                name="slug"
                                value={slug}
                                oninput={onSlugInput}
                                placeholder="acme-inc"
                                required
                                class="rounded-none border-0 shadow-none focus-visible:ring-0"
                            />
                        </div>
                        <InputError message={errors.slug} />
                    </div>

                    <Button type="submit" class="w-full" disabled={processing}>
                        Create organization
                    </Button>
                {/snippet}
            </Form>
        </div>
    </div>
</div>
