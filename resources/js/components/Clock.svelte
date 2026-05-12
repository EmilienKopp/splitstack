<script lang="ts">
    import { clock } from '@/lib/stores/global/time.svelte';

    $effect(() => {
        const interval = setInterval(() => {
            clock.refresh();
        }, 1000);

        return () => clearInterval(interval);
    });
</script>

{#if clock.time}
    <div class="du-countdown font-mono text-2xl w-36">
        <span
            style="--du-value:{clock.h}; --du-digits: 2;"
            aria-live="polite"
            aria-label={clock.h.toString()}>{clock.h}</span
        >
        :
        <span
            style="--du-value:{clock.m}; --du-digits: 2;"
            aria-live="polite"
            aria-label={clock.m.toString()}>{clock.m}</span
        >
        :
        <span
            style="--du-value:{clock.s}; --du-digits: 2;"
            aria-live="polite"
            aria-label={clock.s.toString()}>{clock.s}</span
        >
    </div>
{/if}
