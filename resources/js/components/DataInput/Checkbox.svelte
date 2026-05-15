<script lang="ts">
  interface Props {
    checked?: boolean;
    onclick?: (e: MouseEvent) => void;
    onchange?: (e: Event) => void;
    onfocus?: (e: FocusEvent) => void;
    [key: string]: any;
  }

  let { checked = $bindable(false), onclick, onchange, onfocus, ...rest }: Props = $props();
</script>

<input
  type="checkbox"
  bind:checked
  class="du-checkbox du-checkbox-primary"
  {onclick}
  {onchange}
  {onfocus}
  {...rest}
/>
