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

<fieldset class="fieldset w-full {className}" data-error={normalizedError ? 'true' : 'false'}>
  <div class="form-control">
    <label class="label cursor-pointer justify-start gap-4">
      <input 
        type="checkbox" 
        class="toggle toggle-primary" 
        bind:checked 
        {...rest}
      />
      <span class="label-text">
        {label}
        {#if required}
          <span class="text-error ml-1">*</span>
        {/if}
      </span>
    </label>
  </div>
  <InputError message={normalizedError} />
</fieldset>