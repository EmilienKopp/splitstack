<script lang="ts">
  import { Tooltip, TooltipTrigger, TooltipContent, TooltipProvider } from '@/components/ui/tooltip';

  interface Props {
    content: string;
    children?: import('svelte').Snippet;
  }

  let { content, children }: Props = $props();
</script>

<TooltipProvider>
  <Tooltip>
    <TooltipTrigger>
      {@render children?.()}
    </TooltipTrigger>
    <TooltipContent>
      <p>{content}</p>
    </TooltipContent>
  </Tooltip>
</TooltipProvider>
