import { Page, xPost } from "$lib/inertia";

class VoiceAssistant {
  // Private reactive state (read-only from outside, exposed via getters)
  #isRecording = $state(false);
  #isProcessing = $state(false);
  #transcript = $state('');
  #error = $state('');
  #isListening = $state(false);
  #currentVolume = $state(0);
  #assistantResponse = $state('');

  // Public reactive state (can be read and written directly)
  voiceActivationEnabled = $state(false);
  volumeThreshold = $state(15); // 0-100 scale

  // Private non-reactive state
  #mediaRecorder: MediaRecorder | null = null;
  #audioChunks: Blob[] = [];
  #audioContext: AudioContext | null = null;
  #analyser: AnalyserNode | null = null;
  #microphone: MediaStreamAudioSourceNode | null = null;
  #monitorIntervalId: number | null = null;
  #listeningStream: MediaStream | null = null;
  #secondsBelowThresholdToStop = 2;
  #belowThresholdCounter = 0;
  #belowThresholdIntervalId: number | null = null;

  // Callbacks
  private onSuccess: (event: Page) => any = () => {};
  private onError: (errors: any) => any = () => {};
  private onFinish: (event: any) => any = () => {};

  constructor() {}

  // Arrow function methods to preserve 'this' context when used as event handlers
  startRecording = async () => {
    try {
      this.clearTranscript();
      this.#audioChunks = [];

      const stream =
        this.#listeningStream ||
        (await navigator.mediaDevices.getUserMedia({ audio: true }));

      this.#mediaRecorder = new MediaRecorder(stream);

      if (!this.#mediaRecorder) {
        this.#error = 'MediaRecorder is not supported in your browser.';
        return;
      }

      this.#mediaRecorder.ondataavailable = (event) => {
        if (event.data.size > 0) {
          this.#audioChunks.push(event.data);
        }
      };

      this.#mediaRecorder.onstop = async () => {
        const audioBlob = new Blob(this.#audioChunks, { type: 'audio/webm' });
        this.sendAudioToServer(audioBlob);

        // Only stop tracks if not in voice activation mode
        if (!this.voiceActivationEnabled) {
          stream.getTracks().forEach((track) => track.stop());
        }
      };

      this.#mediaRecorder.start();
      this.#isRecording = true;
    } catch (err) {
      this.#error = 'Failed to start recording: ' + (err as Error).message;
    }
  };

  stopRecording = () => {
    if (this.#mediaRecorder && this.#isRecording) {
      this.#mediaRecorder.stop();
      this.#isRecording = false;
    }
  };

  startListening = async () => {
    try {
      this.#error = '';
      const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
      this.#listeningStream = stream;

      // Create audio context and analyser for volume detection
      this.#audioContext = new AudioContext();
      this.#analyser = this.#audioContext.createAnalyser();
      this.#analyser.fftSize = 256;

      this.#microphone = this.#audioContext.createMediaStreamSource(stream);
      this.#microphone.connect(this.#analyser);

      this.#isListening = true;
      this.monitorAudioLevel();
    } catch (err) {
      this.#error = `Failed to access microphone: ${err instanceof Error ? err.message : 'Unknown error'}`;
      console.error('Listening error:', err);
    }
  };

  stopListening = () => {
     this.#isListening = false;

     if (this.#monitorIntervalId !== null) {
       clearInterval(this.#monitorIntervalId);
       this.#monitorIntervalId = null;
     }

     if (this.#belowThresholdIntervalId !== null) {
       clearInterval(this.#belowThresholdIntervalId);
       this.#belowThresholdIntervalId = null;
     }

     if (this.#microphone) {
       this.#microphone.disconnect();
       this.#microphone = null;
     }

     if (this.#analyser) {
       this.#analyser.disconnect();
       this.#analyser = null;
     }

     if (this.#audioContext && this.#audioContext.state !== 'closed') {
       this.#audioContext.close();
       this.#audioContext = null;
     }

     if (this.#listeningStream) {
       this.#listeningStream.getTracks().forEach((track) => track.stop());
       this.#listeningStream = null;
     }

     this.#currentVolume = 0;
     this.#belowThresholdCounter = 0;
  };

  monitorAudioLevel = () => {
    if (!this.#analyser || !this.#isListening) return;

    const bufferLength = this.#analyser.frequencyBinCount;
    const dataArray = new Uint8Array(bufferLength);

    // Use setInterval instead of requestAnimationFrame so it works when window is not focused
    // Check every 100ms for responsive voice activation
    this.#monitorIntervalId = setInterval(() => {
      if (!this.#isListening || !this.#analyser) {
        if (this.#monitorIntervalId !== null) {
          clearInterval(this.#monitorIntervalId);
          this.#monitorIntervalId = null;
        }
        return;
      }

      this.#analyser.getByteFrequencyData(dataArray);

      // Calculate average volume (0-255 scale)
      const average =
        dataArray.reduce((sum, value) => sum + value, 0) / bufferLength;

      // Convert to 0-100 scale
      this.#currentVolume = Math.min(100, Math.round((average / 255) * 100));

      // Start recording if volume exceeds threshold and not already recording
      if (
        this.#currentVolume >= this.volumeThreshold &&
        !this.#isRecording &&
        !this.#isProcessing &&
        this.voiceActivationEnabled
      ) {
        this.#belowThresholdCounter = 0;
        if (this.#belowThresholdIntervalId !== null) {
          clearInterval(this.#belowThresholdIntervalId);
          this.#belowThresholdIntervalId = null;
        }
        this.startRecording();
      }

      // Start counter if volume drops below threshold while recording
      if (
        this.#currentVolume < this.volumeThreshold &&
        this.#isRecording &&
        this.voiceActivationEnabled &&
        this.#belowThresholdIntervalId === null
      ) {
        this.#belowThresholdCounter = 0;
        this.#belowThresholdIntervalId = setInterval(() => {
          this.#belowThresholdCounter++;
          if (this.#belowThresholdCounter >= this.#secondsBelowThresholdToStop) {
            this.stopRecording();
            if (this.#belowThresholdIntervalId !== null) {
              clearInterval(this.#belowThresholdIntervalId);
              this.#belowThresholdIntervalId = null;
            }
            this.#belowThresholdCounter = 0;
          }
        }, 1000);
      }

      // Reset counter if volume goes back above threshold
      if (this.#currentVolume >= this.volumeThreshold && this.#belowThresholdIntervalId !== null) {
        clearInterval(this.#belowThresholdIntervalId);
        this.#belowThresholdIntervalId = null;
        this.#belowThresholdCounter = 0;
      }
    }, 100); // Check every 100ms
  };

  sendAudioToServer = (audioBlob: Blob) => {
    try {
      this.#isProcessing = true;
      this.#error = '';

      const formData = new FormData();
      formData.append('audio', audioBlob, 'recording.webm');

      xPost(route('audio.assistant'), formData, {
        forceFormData: true,
        onSuccess: (event: Page) => {
          this.#isProcessing = false;
          const data = event.props.flash?.data;
          if (data?.assistantResponse) {
            this.#assistantResponse = data.assistantResponse;
          }
          if (data && data.transcript) {
            this.#transcript = data.transcript;
          }
          if (data?.audioError) {
            this.#error = data.audioError;
          }
          this.onSuccess(event);
        },
        onError: (errors: any) => {
          this.#isProcessing = false;
          this.#error = 'Error from server: ' + JSON.stringify(errors);
          this.onError(errors);
        },
        onFinish: (event: any) => {
          this.#isProcessing = false;
          this.onFinish(event);
        },
      });
    } catch (err) {
      this.#error = 'Failed to send audio to server: ' + (err as Error).message;
    }
  };

  // Read-only getters for reactive state (used in component templates)
  get isRecording() {
    return this.#isRecording;
  }

  get isProcessing() {
    return this.#isProcessing;
  }

  get transcript() {
    return this.#transcript;
  }

  get assistantResponse() {
    return this.#assistantResponse;
  }

  get error() {
    return this.#error;
  }

  get isListening() {
    return this.#isListening;
  }

  get currentVolume() {
    return this.#currentVolume;
  }

  // Action methods (arrow functions to preserve 'this' context)
  clearTranscript = () => {
    this.#transcript = '';
    this.#error = '';
  };

  setError = (message: string) => {
    this.#error = message;
  };

  [Symbol.dispose]() {
    this.stopListening();
    this.stopRecording();
    this.#audioContext?.close();
    if (this.#monitorIntervalId !== null) {
      clearInterval(this.#monitorIntervalId);
    }
    if (this.#belowThresholdIntervalId !== null) {
      clearInterval(this.#belowThresholdIntervalId);
    }
    this.#listeningStream?.getTracks().forEach((track) => track.stop());
  }
}


export const voiceAssistant = new VoiceAssistant();
export default voiceAssistant;