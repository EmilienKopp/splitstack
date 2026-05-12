<script lang="ts">
  import type { Paginated } from '$types/pagination';

  interface Props {
    paginatedData: Paginated<any>;
    pageIndex: number;
  }

  let { paginatedData, pageIndex = $bindable() }: Props = $props();
</script>

<div class="pagination-container w-full my-2 flex justify-center gap-2">
  {#if paginatedData}
    {#each paginatedData.links as link, index}
      <a
        href={link.url}
        class="btn btn-primary pagination-link"
        class:pagination-end={index === 0 ||
          index === paginatedData.links.length - 1}
        class:active={pageIndex === index &&
          index !== 0 &&
          index !== paginatedData.links.length - 1}
        onclick={() => {
          if (index !== 0 && index !== paginatedData.links.length - 1) {
            pageIndex = index;
          }
        }}
      >
        {@html link.label}
      </a>
    {/each}
  {/if}
</div>

<style>
  /* Hide middle pagination items on smaller screens */
  @media (max-width: 768px) {
    .pagination-link:not(.pagination-end) {
      display: none;
    }

    /* Show ellipsis after first item */
    .pagination-link:first-child::after {
      content: '...';
      margin-left: 0.5rem;
      color: #666;
    }
  }
</style>
