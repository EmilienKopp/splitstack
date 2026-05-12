<script lang="ts">
  import { stopPropagation } from 'svelte/legacy';
  import { createEventDispatcher } from 'svelte';
  import InputLabel from './InputLabel.svelte';
  import InputError from './InputError.svelte';
  
  interface Props {
    options?: Array<{ name: string, value: string | number }>;
    selected?: string[];
    placeholder?: string;
    disabled?: boolean;
    label: string;
    error?: string | null;
    errors?: string | string[] | null;
    required?: boolean;
    class?: string;
  }

  let {
    options = [],
    selected = $bindable([]),
    placeholder = 'Select items...',
    disabled = false,
    label,
    error,
    errors,
    required = false,
    class: className = ''
  }: Props = $props();

  let isOpen = $state(false);
  let searchText = $state('');
  let inputElement: HTMLDivElement = $state();
  
  const dispatch = createEventDispatcher();

  // Normalize error handling - support both 'error' and 'errors' props
  const normalizedError = $derived(
    error || 
    (typeof errors === 'string' ? errors : Array.isArray(errors) ? errors[0] : null)
  );
  
  let filteredOptions = $derived(searchText 
    ? options.filter(option => 
        option.name.toLowerCase().includes(searchText.toLowerCase())
      )
    : options);
  
  let selectedLabels = $derived(selected
    ?.map(value => options.find(opt => opt.value === value)?.name)
    ?.filter(Boolean));
  
  function toggleOption(value: string) {
    const index = selected.indexOf(value);
    if (index === -1) {
      selected = [...selected, value];
    } else {
      selected = selected.filter((_, i) => i !== index);
    }
    dispatch('change', { selected });
  }
  
  function removeOption(value: string) {
    selected = selected.filter(v => v !== value);
    dispatch('change', { selected });
  }
  
  function handleClickOutside(event: MouseEvent) {
    if (inputElement && !inputElement.contains(event.target as Node)) {
      isOpen = false;
    }
  }
  
  function handleKeydown(event: KeyboardEvent) {
    if (event.key === 'Escape') {
      isOpen = false;
    }
  }
</script>

<svelte:window onclick={handleClickOutside} onkeydown={handleKeydown} />

<fieldset class="fieldset w-full {className}" data-error={normalizedError ? 'true' : 'false'}>
  {#if label}
    <InputLabel {required}>{label}</InputLabel>
  {/if}
  
  <div class="dropdown dropdown-bottom w-full" bind:this={inputElement}>
    <div 
      tabindex="0" 
      role="button" 
      class="btn btn-outline w-full justify-between {normalizedError ? 'btn-error' : ''}"
      class:btn-disabled={disabled}
      onclick={() => !disabled && (isOpen = !isOpen)}
    >
      {#if selectedLabels.length > 0}
        <div class="flex flex-wrap gap-1 max-w-full overflow-hidden">
          {#each selectedLabels.slice(0, 3) as label, i}
            <span class="badge badge-primary badge-sm">
              {label}
              <button type="button"
                class="ml-1 text-xs"
                onclick={stopPropagation(() => removeOption(selected[i]))}
                disabled={disabled}
              >
                ×
              </button>
            </span>
          {/each}
          {#if selectedLabels.length > 3}
            <span class="badge badge-ghost badge-sm">+{selectedLabels.length - 3}</span>
          {/if}
        </div>
      {:else}
        <span class="text-base-content/60">{placeholder}</span>
      {/if}
      <svg class="h-4 w-4 transform transition-transform {isOpen ? 'rotate-180' : ''}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
      </svg>
    </div>
    
    {#if isOpen && !disabled}
      <div class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-full mt-1 border">
        <div class="form-control">
          <input
            type="text"
            class="input input-bordered input-sm mb-2"
            bind:value={searchText}
            placeholder="Search..."
          />
        </div>
        <div class="max-h-48 overflow-y-auto">
          {#each filteredOptions as option}
            <label class="label cursor-pointer justify-start gap-2 p-2 hover:bg-base-200 rounded">
              <input
                type="checkbox"
                class="checkbox checkbox-sm"
                checked={selected.includes(option.value.toString())}
                onchange={() => toggleOption(option.value.toString())}
              />
              <span class="label-text">{option.name}</span>
            </label>
          {/each}
          {#if filteredOptions.length === 0}
            <div class="text-center text-base-content/60 p-4">No options found</div>
          {/if}
        </div>
      </div>
    {/if}
  </div>
  
  <InputError message={normalizedError} />
</fieldset>