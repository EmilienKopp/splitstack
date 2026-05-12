<script lang="ts">
  import { twMerge } from 'tailwind-merge';
  import clsx from 'clsx';
  import InputLabel from './InputLabel.svelte';
  import InputError from './InputError.svelte';
  import { type Snippet } from 'svelte';
  import { readable } from '$lib/utils/formatting';
  import { usePrecog } from '$lib/inertia/hooks.svelte';

  interface Props {
    label?: string;
    options?: Option[];
    value?: any;
    placeholder?: string;
    error?: string | null;
    errors?: string | string[] | null;
    required?: boolean;
    class?: string;
    precog?: boolean;
    items?: any[];
    mapping?: { valueColumn: string; labelColumn: string };
    onchange?: (e: Event) => void;
    children?: Snippet;
    hidden?: boolean;
    [key: string]: any;
  }

  let {
    label,
    options = [],
    items,
    mapping,
    value = $bindable(),
    placeholder = 'Select something',
    error,
    errors,
    required = false,
    class: className = '',
    precog,
    onchange,
    children,
    hidden = false,
    ...rest
  }: Props = $props();

  interface Option {
    value: any;
    name: string;
  }

  // Normalize error handling - support both 'error' and 'errors' props
  const normalizedError = $derived(
    error || (typeof errors === 'string' ? errors : Array.isArray(errors) ? errors[0] : null)
  );

  let classes = $derived(
    clsx('select select-bordered w-full text-xs', normalizedError && 'select-error', className)
  );

  if (!options?.length && items && mapping) {
    options = items.map((item) => ({
      value: item[mapping.valueColumn],
      name: item[mapping.labelColumn],
    }));
  }

  if (typeof options === 'object' && !Array.isArray(options)) {
    console.log('options as object', options);
    options = Object.entries(options).map(([key, val]: [string, any]) => ({
      value: val.toString(),
      name: readable(key),
    }));
  }

  const { formContext, handleChange } = usePrecog({
    active: precog,
    handler: onchange,
    name: rest.name,
  });
</script>

<fieldset class="fieldset w-full" data-error={normalizedError ? 'true' : 'false'}>
  {#if label && !hidden}
    <InputLabel for={rest.id} {required}>
      {label}
    </InputLabel>
  {/if}
  <select
    class={classes}
    name={rest.name}
    class:hidden
    {...rest}
    bind:value
    onchange={(e) => handleChange?.(e)}
  >
    {#if placeholder}
      <option disabled selected value="">{placeholder}</option>
    {/if}

    {#if !children}
      {#each options as option, index}
        <option value={option.value} id={`${rest.name}-option-${index}`}>{option.name}</option>
      {/each}
    {:else}
      {@render children()}
    {/if}
  </select>
  {#if precog && rest.name && formContext?.invalid(rest.name)}
    <InputError message={formContext.errors[rest.name]} />
  {:else}
    <InputError message={normalizedError} />
  {/if}

  {#if hidden}
    <input type="hidden" name={rest.name} {value} />
  {/if}
</fieldset>
