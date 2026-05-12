<script lang="ts">
    let {
        checked = $bindable(false),
        disabled = false,
        class: className = '',
        id,
        name,
        value,
        onchange,
        onclick,
        ...rest
    }: {
        checked?: boolean;
        disabled?: boolean;
        class?: string;
        id?: string;
        name?: string;
        value?: string;
        onchange?: (event: Event) => void;
        onclick?: (event: MouseEvent) => void;
        [key: string]: unknown;
    } = $props();
</script>

<input type="checkbox"
    bind:checked={checked} 
    {disabled} {id} {name} {value} 
    class="du-checkbox border border-neutral-300"
    {onchange}
    {onclick}
    {...rest}
/>