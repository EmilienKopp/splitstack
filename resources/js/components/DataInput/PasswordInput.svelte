<script lang="ts">
  import { twMerge } from 'tailwind-merge';
  import clsx from 'clsx';
  import InputLabel from './InputLabel.svelte';
  import InputError from './InputError.svelte';

  interface Props {
    label?: string;
    name: string;
    required?: boolean;
    value?: string | number | File | null;
    error?: string | null;
    errors?: string | string[] | Record<string, string> | null;
    class?: string;
    type?: 'password';
    placeholder?: string;
    oninput?: (e: Event) => void;
    onchange?: (e: Event) => void;
    [key: string]: any;
  }

  let {
    label = '',
    name,
    required = false,
    value = $bindable(''),
    error,
    errors,
    class: className = '',
    type = 'password',
    placeholder = 'Enter password',
    onchange,
    oninput,
    ...rest
  }: Props = $props();

  // Normalize error handling - support multiple error formats
  const normalizedError = $derived(
    error || 
    (typeof errors === 'string' ? errors : 
     Array.isArray(errors) ? errors[0] : 
     (errors && typeof errors === 'object' && errors[name]) ? errors[name] : null)
  );

  let classes = $derived(clsx(
    'input input-bordered w-full',
    normalizedError && 'input-error',
    className,
  ))
</script>

<fieldset class="fieldset w-full" data-error={normalizedError ? 'true' : 'false'}>
  {#if label}
    <InputLabel for={rest.id} {required}>{label}</InputLabel>
  {/if}
  <input 
    class={classes}
    {name}
    bind:value
    {onchange}
    {oninput}
    {placeholder}
    {...rest}
    {type}
  />
  <InputError message={normalizedError} />
</fieldset>
