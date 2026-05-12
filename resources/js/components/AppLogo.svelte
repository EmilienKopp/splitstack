<script lang="ts">
    import AppLogoIcon from '@/components/AppLogoIcon.svelte';
    import Clock from '@/components/Clock.svelte';
</script>

<div class="flex aspect-square size-8 items-center justify-center rounded-md">
    <AppLogoIcon class="size-5" />
</div>
<div class="ml-1 grid flex-1 text-left text-sm">
    <!-- <span class="mb-0.5 truncate leading-tight font-semibold"> Qadran </span> -->
    <Clock />
</div>
