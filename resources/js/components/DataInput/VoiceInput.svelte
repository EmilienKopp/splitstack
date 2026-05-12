<script lang="ts">
  import { onMount, tick, onDestroy } from 'svelte';
  import { voiceAssistant } from '$lib/stores/global/voiceAssistant.svelte';
  import { voiceCommands } from '$lib/stores/global/voiceCommands.svelte';
  import VoiceAssistantPanel from './VoiceAssistantPanel.svelte';
  import { getPage } from '$lib/inertia';
  import { features } from '$lib/stores/global/features.svelte';

  let isMinimized = $state(true);

  // Get feature flag from Inertia props - true = voice assistant mode, false = voice commands mode
  let voiceAssistantMode = features.voiceAssistantMode;

  onMount(() => {
    // Check if browser supports MediaRecorder
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      voiceAssistant.setError('Your browser does not support audio recording');
    }

    // Trigger open and start recording on Ctrl/Cmd + Shift + V
    window.addEventListener('keydown', handleKeydown);
  });

  onDestroy(() => {
    voiceAssistant.stopListening();
    // Don't stop voiceCommands here - it's a global singleton that should persist
    // across component lifecycles. Continuous mode is controlled by user action only.
    window.removeEventListener('keydown', handleKeydown);
  });

  function toggleMinimize() {
    isMinimized = !isMinimized;
  }

  async function handleKeydown(event: KeyboardEvent) {
    // Ctrl/Cmd + Shift + V for voice assistant
    if (
      (event.ctrlKey || event.metaKey) &&
      event.shiftKey &&
      event.key.toLowerCase() === 'v'
    ) {
      event.preventDefault();
      if (isMinimized) {
        isMinimized = false;
      }
      await tick();
      if (!voiceAssistant.isRecording && !voiceAssistant.isProcessing) {
        voiceAssistant.startRecording();
      } else if (voiceAssistant.isRecording) {
        voiceAssistant.stopRecording();
      }
    }

    // Ctrl/Cmd + Shift + C for voice commands
    if (
      (event.ctrlKey || event.metaKey) &&
      event.shiftKey &&
      event.key.toLowerCase() === 'c'
    ) {
      event.preventDefault();
      if (isMinimized) {
        isMinimized = false;
      }
      await tick();
      if (voiceCommands.isSupported) {
        // Toggle continuous mode - if already in continuous mode, stop it
        if (voiceCommands.continuousMode) {
          voiceCommands.setContinuousMode(false);
        } else {
          // Enable continuous mode
          voiceCommands.setContinuousMode(true);
        }
      }
    }
  }
</script>

<div class="fixed bottom-4 left-4 z-50 w-80">
  <div class="card bg-base-200 shadow-xl border border-base-300">
    <!-- Header -->
    <div class="card-body p-3">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"
            />
          </svg>
          <h3 class="font-semibold text-sm">Voice Assistant</h3>
          {#if voiceAssistant.isRecording}
            <span class="badge badge-error badge-xs animate-pulse">REC</span>
          {:else if voiceAssistant.isListening}
            <span class="badge badge-info badge-xs">LISTENING</span>
          {/if}
          {#if isMinimized && voiceCommands.isListening}
            <span class="badge badge-success badge-xs animate-pulse">CMD</span>
          {/if}
        </div>
        <button
          onclick={toggleMinimize}
          class="btn btn-ghost btn-xs btn-circle"
          aria-label="Toggle minimize"
        >
          {#if isMinimized}
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M5 15l7-7 7 7"
              />
            </svg>
          {:else}
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 9l-7 7-7-7"
              />
            </svg>
          {/if}
        </button>
      </div>

      {#if !isMinimized}
        <div class="divider my-1"></div>
        <VoiceAssistantPanel {voiceAssistantMode} />
      {/if}
    </div>
  </div>
</div>
