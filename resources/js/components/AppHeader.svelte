<script lang="ts">
    import { Link, page, router } from '@inertiajs/svelte';
    import BookOpen from 'lucide-svelte/icons/book-open';
    import Folder from 'lucide-svelte/icons/folder';
    import LayoutGrid from 'lucide-svelte/icons/layout-grid';
    import Menu from 'lucide-svelte/icons/menu';
    import Search from 'lucide-svelte/icons/search';
    import AppLogo from '@/components/AppLogo.svelte';
    import AppLogoIcon from '@/components/AppLogoIcon.svelte';
    import Breadcrumbs from '@/components/Breadcrumbs.svelte';
    import TeamSwitcher from '@/components/TeamSwitcher.svelte';
    import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
    import { Button } from '@/components/ui/button';
    import {
        DropdownMenu,
        DropdownMenuContent,
        DropdownMenuTrigger,
    } from '@/components/ui/dropdown-menu';
    import {
        NavigationMenu,
        NavigationMenuItem,
        NavigationMenuList,
        navigationMenuTriggerStyle,
    } from '@/components/ui/navigation-menu';
    import {
        Sheet,
        SheetContent,
        SheetHeader,
        SheetTitle,
        SheetTrigger,
    } from '@/components/ui/sheet';
    import {
        Tooltip,
        TooltipContent,
        TooltipProvider,
        TooltipTrigger,
    } from '@/components/ui/tooltip';
    import * as KBD from '@/components/ui/kbd';
    import UserMenuContent from '@/components/UserMenuContent.svelte';
    import { currentUrlState } from '@/lib/currentUrl.svelte';
    import { getInitials } from '@/lib/initials';
    import { toUrl } from '@/lib/utils';
    import { dashboard } from '@/routes';
    import type { BreadcrumbItem, NavItem, Team } from '@/types';
    import FolderGit2 from 'lucide-svelte/icons/folder-git-2';
    import Building2 from 'lucide-svelte/icons/building-2';
    import Space from 'lucide-svelte/icons/space';
    import { Keyboard } from 'lucide-svelte';
    import { onMount } from 'svelte';
    import Timezone from './Timezone.svelte';
    import Tip from './Tip.svelte';

    let {
        breadcrumbs = [],
    }: {
        breadcrumbs?: BreadcrumbItem[];
    } = $props();

    const CMD_KEY = '⇧';

    const auth = $derived(page.props.auth);
    const currentTeam = $derived(page.props.currentTeam as Team | null);
    const dashboardUrl = $derived(currentTeam ? dashboard(currentTeam.slug) : '/dashboard');

    const url = currentUrlState();

    const activeItemStyles = 'text-neutral-900 dark:bg-neutral-800 dark:text-neutral-100';

    //#region Main Items
    // Map icons to backend-provided nav
    const icons = {
        Dashboard: LayoutGrid,
        Projects: FolderGit2,
        Organizations: Building2,
    };

    const shortcuts = {
        Dashboard: 'd',
        Projects: 'p',
        Organizations: 'o',
        Stats: 's',
    };

    const commands = {
        'Clock In/Out': '␣ / ↵',
    };

    const mainNavItems = $derived<NavItem[]>(
        page.props.nav.map((item: NavItem) => ({
            ...item,
            icon: icons[item.title as keyof typeof icons],
            kbd: shortcuts[item.title as keyof typeof shortcuts],
        })),
    );
    // #endregion

    const rightNavItems: NavItem[] = [];

    const navigateTo = (href: string) => {
        router.visit(href);
    };

    const handleShortcut = (event: KeyboardEvent) => {
        const isInput =
            (event.target as HTMLElement).tagName === 'INPUT' ||
            (event.target as HTMLElement).tagName === 'TEXTAREA' ||
            (event.target as HTMLElement).isContentEditable;
        if (!event.shiftKey || isInput) return;
        switch (event.key.toLowerCase()) {
            case 'd':
                navigateTo(toUrl(dashboardUrl));
                break;
            case 'p':
                const projectsItem = mainNavItems.find((item) => item.title === 'Projects');
                if (projectsItem) navigateTo(toUrl(projectsItem.href));
                break;
            case 'o':
                const orgsItem = mainNavItems.find((item) => item.title === 'Organizations');
                if (orgsItem) navigateTo(toUrl(orgsItem.href));
                break;
            case 's':
                const statsItem = mainNavItems.find((item) => item.title === 'Stats');
                if (statsItem) navigateTo(toUrl(statsItem.href));
                break;
        }
    };

    onMount(() => {
        document.addEventListener('keydown', handleShortcut);

        return () => {
            document.removeEventListener('keydown', handleShortcut);
        };
    });
</script>

<div>
    <div class="border-b border-sidebar-border/80">
        <div class="mx-auto flex h-16 items-center px-4 md:max-w-7xl">
            <!-- Mobile Menu -->
            <div class="lg:hidden">
                <Sheet>
                    <SheetTrigger asChild>
                        {#snippet children(props)}
                            <Button
                                variant="ghost"
                                size="icon"
                                class="mr-2 h-9 w-9"
                                onclick={props.onclick}
                                aria-expanded={props['aria-expanded']}>
                                <Menu class="h-5 w-5" />
                            </Button>
                        {/snippet}
                    </SheetTrigger>
                    <SheetContent side="left" class="w-[300px] p-6">
                        <SheetTitle class="sr-only">Navigation menu</SheetTitle>
                        <SheetHeader class="flex justify-start text-left">
                            <AppLogoIcon class="size-6" />
                        </SheetHeader>
                        <div
                            class="flex h-full flex-1 flex-col justify-between space-y-4 pt-6 pb-10">
                            <nav class="-mx-3 space-y-1">
                                {#each mainNavItems as item (toUrl(item.href))}
                                    <Link
                                        href={toUrl(item.href)}
                                        class="flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-accent {url.whenCurrentUrl(
                                            item.href,
                                            url.currentUrl,
                                            activeItemStyles,
                                            '',
                                        ) ?? ''}">
                                        {#if item.icon}
                                            <item.icon class="h-5 w-5" />
                                        {/if}
                                        {item.title}
                                    </Link>
                                {/each}
                            </nav>
                            <div class="flex flex-col space-y-4">
                                {#each rightNavItems as item (toUrl(item.href))}
                                    <a
                                        href={toUrl(item.href)}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="flex items-center space-x-2 text-sm font-medium">
                                        {#if item.icon}
                                            <item.icon class="h-5 w-5" />
                                        {/if}
                                        <span>{item.title}</span>
                                    </a>
                                {/each}
                            </div>
                        </div>
                    </SheetContent>
                </Sheet>
            </div>

            <Link href={dashboardUrl} class="flex items-center gap-x-2">
                <AppLogo />
            </Link>

            <!-- Desktop Menu -->
            <div class="hidden h-full lg:flex lg:flex-1">
                <NavigationMenu class="ml-10 flex h-full items-stretch">
                    <NavigationMenuList class="flex h-full items-stretch space-x-0">
                        {#each mainNavItems as item (toUrl(item.href))}
                            <NavigationMenuItem class="relative flex h-full items-center">
                                <Link
                                    class="{navigationMenuTriggerStyle()} {url.whenCurrentUrl(
                                        item.href,
                                        url.currentUrl,
                                        activeItemStyles,
                                        '',
                                    ) ?? ''} h-9 cursor-pointer px-4"
                                    href={toUrl(item.href)}>
                                    {#if item.icon}
                                        <item.icon class="mr-2 h-4 w-4" />
                                    {/if}
                                    {item.title}
                                    {#if item.kbd}
                                        <KBD.Group class="ml-2">
                                            <KBD.Root>
                                                <span class="text-lg">{CMD_KEY}</span>
                                                {item.kbd.toUpperCase()}
                                            </KBD.Root>
                                        </KBD.Group>
                                    {/if}
                                </Link>
                                {#if url.isCurrentUrl(item.href, url.currentUrl)}
                                    <div
                                        class="absolute bottom-0 left-0 h-0.5 w-full translate-y-px bg-black dark:bg-white">
                                    </div>
                                {/if}
                            </NavigationMenuItem>
                        {/each}
                    </NavigationMenuList>
                </NavigationMenu>
            </div>

            <div class="ml-auto flex items-center space-x-2">
                <div class="relative flex items-center space-x-1">
                    <!-- <Button variant="ghost" size="icon" class="group h-9 w-9 cursor-pointer">
                        <Search class="size-5 opacity-80 group-hover:opacity-100" />
                    </Button> -->

                    <Timezone />

                    <Button
                        variant="ghost"
                        size="icon"
                        class="group h-9 w-9 cursor-pointer rounded-full">
                        <Tip>
                            <span class="sr-only">Shortcuts</span>
                            <Keyboard class="size-5" />
                            {#snippet content()}
                                {#each Object.entries(commands) as [title, key]}
                                    <p class="flex items-center justify-between text-xs min-w-44">
                                        <span class="mr-1">
                                            {key.toUpperCase()}
                                        </span>
                                        <span>{title}</span>
                                    </p>
                                {/each}
                                {#each Object.entries(shortcuts) as [title, key]}
                                    <p class="flex items-center justify-between text-xs min-w-44">
                                        <span class="mr-1">
                                            {CMD_KEY}
                                            {key.toUpperCase()}
                                        </span>
                                        <span>{title}</span>
                                    </p>
                                {/each}
                            {/snippet}
                        </Tip>
                    </Button>

                    <div class="hidden space-x-1 lg:flex">
                        {#each rightNavItems as item (toUrl(item.href))}
                            <TooltipProvider delayDuration={0}>
                                <Tooltip>
                                    <TooltipTrigger>
                                        {#snippet child({ props })}
                                            <a
                                                href={toUrl(item.href)}
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                {...props}
                                                class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground h-9 w-9 group cursor-pointer">
                                                <span class="sr-only">{item.title}</span>
                                                <item.icon
                                                    class="size-5 opacity-80 group-hover:opacity-100" />
                                            </a>
                                        {/snippet}
                                    </TooltipTrigger>
                                    <TooltipContent>
                                        <p>{item.title}</p>
                                    </TooltipContent>
                                </Tooltip>
                            </TooltipProvider>
                        {/each}
                    </div>
                </div>

                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        {#snippet children(props)}
                            <Button
                                variant="ghost"
                                size="icon"
                                class="relative size-10 w-auto rounded-full p-1 focus-within:ring-2 focus-within:ring-primary"
                                onclick={props.onclick}
                                aria-expanded={props['aria-expanded']}
                                data-state={props['data-state']}>
                                <Avatar class="size-8 overflow-hidden rounded-full">
                                    {#if auth.user.avatar}
                                        <AvatarImage src={auth.user.avatar} alt={auth.user.name} />
                                    {/if}
                                    <AvatarFallback
                                        class="rounded-lg bg-neutral-200 font-semibold text-black dark:bg-neutral-700 dark:text-white">
                                        {getInitials(auth.user?.name)}
                                    </AvatarFallback>
                                </Avatar>
                            </Button>
                        {/snippet}
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end" class="w-56">
                        <UserMenuContent user={auth.user} />
                    </DropdownMenuContent>
                </DropdownMenu>

                {#if page.props.config.usesTeams}
                    <TeamSwitcher inHeader={true} />
                {/if}
            </div>
        </div>
    </div>

    {#if breadcrumbs.length > 1}
        <div class="flex w-full border-b border-sidebar-border/70">
            <div
                class="mx-auto flex h-12 w-full items-center justify-start px-4 text-neutral-500 md:max-w-7xl">
                <Breadcrumbs {breadcrumbs} />
            </div>
        </div>
    {/if}
</div>
