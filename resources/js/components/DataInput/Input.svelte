<script lang="ts">
  import { twMerge } from 'tailwind-merge';
  import clsx from 'clsx';
  import InputError from './InputError.svelte';
  import { usePrecog } from '$lib/inertia/hooks.svelte';

  interface Props {
    label?: string;
    name?: string;
    id?: string;
    required?: boolean;
    value?: string | number | File | null | Date;
    error?: string | null;
    errors?: string | string[] | null;
    class?: string;
    fieldsetClass?: string;
    hint?: string;
    precog?: boolean;
    type?:
      | 'text'
      | 'number'
      | 'file'
      | 'search'
      | 'tel'
      | 'url'
      | 'email'
      | 'password'
      | 'date'
      | 'time'
      | 'datetime-local'
      | 'month'
      | 'week'
      | 'color';
    placeholder?: string;
    oninput?: (e: Event) => void;
    onchange?: (e: Event) => void;
    pattern?: string;
    [key: string]: any;
  }

  let {
    label = '',
    name,
    id,
    required = false,
    value = $bindable(),
    error,
    errors,
    type = 'text',
    class: className = '',
    fieldsetClass = '',
    placeholder = 'Type here',
    hint = '',
    precog,
    pattern,
    onchange,
    oninput,
    ...rest
  }: Props = $props();

  if (!name && id) {
    name = id;
  }

  if (name && !id) {
    id = name;
  }

  // Normalize error handling - support both 'error' and 'errors' props
  const normalizedError = $derived(
    error || (typeof errors === 'string' ? errors : Array.isArray(errors) ? errors[0] : null)
  );

  let classes = $derived(
    clsx('du-input du-input-bordered w-full', normalizedError && 'du-input-error', className)
  );

  const fieldsetClasses = $derived(twMerge('du-fieldset w-full', fieldsetClass));

  const { formContext, handleChange } = usePrecog({
    active: precog,
    name,
    handler: onchange,
  });
</script>

<fieldset class={fieldsetClasses} data-error={normalizedError ? 'true' : 'false'}>
  {#if label}
    <legend class="du-fieldset-legend">
      {label}
      {#if required}
        <span class="text-error">*</span>
      {/if}
    </legend>
  {/if}
  <input
    class={classes}
    {name}
    {id}
    bind:value
    onchange={() => {
      handleChange?.();
      console.log('precog');
    }}
    {oninput}
    {placeholder}
    {...rest}
    {type}
  />
  {#if hint}
    <p class="optional">{hint}</p>
  {/if}
  {#if precog && name && formContext?.invalid(rest.name)}
    <InputError message={formContext.errors[rest.name]} />
  {:else}
    <InputError message={normalizedError} />
  {/if}
</fieldset>
