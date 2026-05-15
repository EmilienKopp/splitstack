<script lang="ts">
  import InputLabel from './InputLabel.svelte';
  import InputError from './InputError.svelte';
  
  interface Props {
    label: string;
    checked: boolean;
    error?: string | null;
    errors?: string | string[] | null;
    required?: boolean;
    class?: string;
    [key: string]: any;
  }

  let { 
    label, 
    checked = $bindable(), 
    error,
    errors,
    required = false,
    class: className = '',
    ...rest 
  } = $props();

  // Normalize error handling - support both 'error' and 'errors' props
  const normalizedError = $derived(
    error || 
    (typeof errors === 'string' ? errors : Array.isArray(errors) ? errors[0] : null)
  );
</script>

<fieldset class="du-fieldset w-full {className}" data-error={normalizedError ? 'true' : 'false'}>
  <div class="du-form-control">
    <label class="du-label cursor-pointer justify-start gap-4">
      <input
        type="checkbox"
        class="du-toggle du-toggle-primary"
        bind:checked
        {...rest}
      />
      <span class="du-label-text">
        {label}
        {#if required}
          <span class="text-error ml-1">*</span>
        {/if}
      </span>
    </label>
  </div>
  <InputError message={normalizedError} />
</fieldset>