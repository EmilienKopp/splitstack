<script lang="ts">
  import { voiceAssistant } from '$lib/stores/global/voiceAssistant.svelte';
  import { voiceCommands } from '$lib/stores/global/voiceCommands.svelte';

  interface Props {
    voiceAssistantMode?: boolean;
  }

  let { voiceAssistantMode = false }: Props = $props();
  let showSettings = $state(false);

  function toggleVoiceActivation() {
    voiceAssistant.voiceActivationEnabled =
      !voiceAssistant.voiceActivationEnabled;

    if (voiceAssistant.voiceActivationEnabled) {
      voiceAssistant.startListening();
    } else {
      voiceAssistant.stopListening();
    }
  }

  function toggleSettings() {
    showSettings = !showSettings;
  }

  function handleVoiceCommand() {
    voiceCommands.startListening();
  }
</script>


{#if true}
  <!-- Voice Assistant Mode (Audio Recording & Transcription) -->
  <!-- Voice Activation Toggle -->
  <div class="flex items-center justify-between mb-3">
    <div class="flex items-center gap-2">
      <span class="text-xs font-medium">Voice Activation</span>
      {#if voiceAssistant.isListening && voiceAssistant.currentVolume > 0}
        <div class="flex items-center gap-1">
          <div
            class="h-2 rounded-full bg-success transition-all duration-75"
            style="width: {Math.max(20, voiceAssistant.currentVolume)}px"
          ></div>
          <span class="text-xs opacity-60">{voiceAssistant.currentVolume}%</span
          >
        </div>
      {/if}
    </div>
    <div class="flex items-center gap-1">
      <button
        onclick={toggleSettings}
        class="btn btn-ghost btn-xs btn-circle"
        aria-label="Settings"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-3 w-3"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
          />
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
          />
        </svg>
      </button>
      <input
        type="checkbox"
        class="toggle toggle-sm toggle-success"
        checked={voiceAssistant.voiceActivationEnabled}
        onchange={toggleVoiceActivation}
      />
    </div>
  </div>

  <!-- Settings Panel -->
  {#if showSettings}
    <div class="bg-base-100 p-3 rounded-lg mb-3">
      <label class="form-control">
        <div class="label">
          <span class="label-text text-xs"
            >Volume Threshold: {voiceAssistant.volumeThreshold}%</span
          >
        </div>
        <input
          type="range"
          min="10"
          max="80"
          bind:value={voiceAssistant.volumeThreshold}
          class="range range-xs range-success"
        />
        <div class="label">
          <span class="label-text-alt text-xs opacity-60">
            Recording starts when volume exceeds this level
          </span>
        </div>
      </label>
    </div>
  {/if}

  <!-- Error Display -->
  {#if voiceAssistant.error}
    <div class="alert alert-error py-2 px-3 text-xs">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="stroke-current shrink-0 h-4 w-4"
        fill="none"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"
        />
      </svg>
      <span>{voiceAssistant.error}</span>
    </div>
  {/if}

  <!-- Transcript Display -->
  {#if voiceAssistant.transcript || voiceAssistant.assistantResponse}
    <div class="bg-base-100 p-3 rounded-lg text-sm">
      <div class="flex items-start justify-between gap-2">
        <p class="flex-1">{voiceAssistant.assistantResponse ?? voiceAssistant.transcript}</p>
        <button
          onclick={voiceAssistant.clearTranscript}
          class="btn btn-ghost btn-xs btn-circle"
          aria-label="Clear transcript"
        >
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
              d="M6 18L18 6M6 6l12 12"
            />
          </svg>
        </button>
      </div>
    </div>
  {/if}

  <!-- Controls -->
  <div class="flex gap-2 justify-center mt-2">
    {#if !voiceAssistant.isRecording && !voiceAssistant.isProcessing}
      <button
        onclick={voiceAssistant.startRecording}
        class="btn btn-primary btn-sm gap-2"
      >
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
            d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"
          />
        </svg>
        Start Recording
      </button>
    {:else if voiceAssistant.isRecording}
      <button
        onclick={voiceAssistant.stopRecording}
        class="btn btn-error btn-sm gap-2"
      >
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
            d="M6 18L18 6M6 6l12 12"
          />
        </svg>
        Stop Recording
      </button>
    {:else if voiceAssistant.isProcessing}
      <button class="btn btn-sm gap-2" disabled>
        <span class="loading loading-spinner loading-sm"></span>
        Processing...
      </button>
    {/if}
  </div>

  <!-- Voice Commands Section -->
  <div class="divider my-2 text-xs">Quick Commands</div>
<!-- {:else if voiceCommands.isSupported} -->
  <!-- Continuous Mode Toggle -->
  <div class="flex items-center justify-between mb-2">
    <div class="flex items-center gap-2">
      <span class="text-xs font-medium">Continuous Listening</span>
      {#if voiceCommands.continuousMode && voiceCommands.isListening}
        <span class="badge badge-success badge-xs animate-pulse">ACTIVE</span>
      {/if}
    </div>
    <input
      type="checkbox"
      class="toggle toggle-sm toggle-secondary"
      checked={voiceCommands.continuousMode}
      onchange={voiceCommands.toggleContinuousMode}
    />
  </div>

  <!-- Voice Command Status -->
  {#if voiceCommands.lastCommand}
    <div class="bg-base-100 p-2 rounded-lg text-xs mb-2">
      <span class="opacity-60">Last command:</span>
      <span class="font-medium ml-1">{voiceCommands.lastCommand}</span>
      <button
        onclick={voiceCommands.clearLastCommand}
        class="btn btn-ghost btn-xs btn-circle float-right"
        aria-label="Clear last command"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-3 w-3"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M6 18L18 6M6 6l12 12"
          />
        </svg>
      </button>
    </div>
  {/if}

  {#if voiceCommands.error}
    <div class="alert alert-warning py-2 px-3 text-xs mb-2">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="stroke-current shrink-0 h-4 w-4"
        fill="none"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
        />
      </svg>
      <span>{voiceCommands.error}</span>
      <button
        onclick={voiceCommands.clearError}
        class="btn btn-ghost btn-xs btn-circle"
        aria-label="Clear error"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-3 w-3"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M6 18L18 6M6 6l12 12"
          />
        </svg>
      </button>
    </div>
  {/if}

  <!-- Voice Command Button -->
  {#if !voiceCommands.continuousMode}
    <div class="flex gap-2 justify-center">
      <button
        onclick={handleVoiceCommand}
        class="btn btn-secondary btn-sm gap-2"
        disabled={voiceCommands.isListening}
      >
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
            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
          />
        </svg>
        {#if voiceCommands.isListening}
          <span class="animate-pulse">Listening...</span>
        {:else}
          Voice Command
        {/if}
      </button>
    </div>
  {/if}

  <div class="text-xs text-center mt-2 opacity-60">
    <div>Try: "go to projects", "scroll down", "help"</div>
    <div class="mt-1">
      <kbd class="kbd kbd-xs">Ctrl</kbd>+<kbd class="kbd kbd-xs">Shift</kbd
      >+<kbd class="kbd kbd-xs">C</kbd> to {voiceCommands.continuousMode
        ? 'stop'
        : 'start'}
    </div>
  </div>
{:else}
  <div class="alert alert-info py-2 px-3 text-xs">
    <svg
      xmlns="http://www.w3.org/2000/svg"
      class="stroke-current shrink-0 h-4 w-4"
      fill="none"
      viewBox="0 0 24 24"
    >
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
      />
    </svg>
    <span>Voice commands not supported in your browser</span>
  </div>
{/if}
