<script lang="ts">
  import { createBubbler } from 'svelte/legacy';
  import { twMerge } from 'tailwind-merge';
  import clsx from 'clsx';
  import InputLabel from './InputLabel.svelte';
  import InputError from './InputError.svelte';

  const bubble = createBubbler();
  
  interface Props {
    label?: string;
    required?: boolean;
    value?: string;
    error?: string | null;
    errors?: string | string[] | null;
    placeholder?: string;
    name?: string;
    class?: string;
    rows?: number;
    [key: string]: any;
  }

  let {
    label = '',
    required = false,
    value = $bindable(),
    class: className = '',
    placeholder = 'Enter text here',
    error,
    errors,
    name = '',
    rows = 4,
    ...rest
  }: Props = $props();

  // Normalize error handling - support both 'error' and 'errors' props
  const normalizedError = $derived(
    error || 
    (typeof errors === 'string' ? errors : Array.isArray(errors) ? errors[0] : null)
  );

  let classes = $derived(clsx(
    'textarea textarea-bordered w-full',
    normalizedError && 'textarea-error',
    className
  ))
</script>

<fieldset class="fieldset w-full" data-error={normalizedError ? 'true' : 'false'}>
  {#if label}
    <InputLabel {required}>{label}</InputLabel>
  {/if}

  <textarea
    class={classes}
    bind:value
    {name}
    {placeholder}
    {rows}
    onclick={bubble('click')}
    onchange={bubble('change')}
    oninput={bubble('input')}
    {...rest}
  ></textarea>

  <InputError message={normalizedError} />
</fieldset>
