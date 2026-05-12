import { Page, xPost } from '$lib/inertia';

import { getTimezone } from '$lib/utils/timezone';
import { router } from '@inertiajs/svelte';
import { voiceAssistant } from './voiceAssistant.svelte';

// Type for Web Speech Recognition API
interface SpeechRecognitionEvent extends Event {
  results: SpeechRecognitionResultList;
  resultIndex: number;
}

interface SpeechRecognitionErrorEvent extends Event {
  error: string;
  message: string;
}

interface SpeechRecognition extends EventTarget {
  continuous: boolean;
  interimResults: boolean;
  lang: string;
  start(): void;
  stop(): void;
  abort(): void;
  onresult: ((event: SpeechRecognitionEvent) => void) | null;
  onerror: ((event: SpeechRecognitionErrorEvent) => void) | null;
  onend: (() => void) | null;
  onstart: (() => void) | null;
}

// Command type definition
type CommandAction = () => void | Promise<void>;

interface VoiceCommand {
  patterns: string[];
  action: CommandAction;
  description: string;
}

class VoiceCommandsService {
  // Reactive state
  #isListening = $state(false);
  #lastCommand = $state('');
  #error = $state('');
  #isSupported = $state(false);
  #continuousMode = $state(false);

  // Private state
  #recognition: SpeechRecognition | null = null;
  #commands: Map<string, VoiceCommand> = new Map();
  #localStorageKey = 'voiceCommands_continuousMode';
  #errorTimeout: number | null = null;

  constructor() {
    this.#isSupported = this.checkBrowserSupport();
    if (this.#isSupported) {
      this.initializeRecognition();
      this.registerDefaultCommands();
      this.restoreContinuousModeState();
    }
  }

  private restoreContinuousModeState() {
    try {
      const saved = localStorage.getItem(this.#localStorageKey);
      if (saved === 'true') {
        // Restore continuous mode and start listening
        this.#continuousMode = true;
        this.startListening();
      }
    } catch (err) {
      console.error('Failed to restore continuous mode state:', err);
    }
  }

  private saveContinuousModeState() {
    try {
      localStorage.setItem(this.#localStorageKey, String(this.#continuousMode));
    } catch (err) {
      console.error('Failed to save continuous mode state:', err);
    }
  }

  private setError(message: string, duration: number = 2000) {
    // Clear any existing timeout
    if (this.#errorTimeout) {
      clearTimeout(this.#errorTimeout);
    }

    // Set the error
    this.#error = message;

    // Auto-clear after duration
    this.#errorTimeout = setTimeout(() => {
      this.#error = '';
      this.#errorTimeout = null;
    }, duration) as unknown as number;
  }

  private clearErrorTimeout() {
    if (this.#errorTimeout) {
      clearTimeout(this.#errorTimeout);
      this.#errorTimeout = null;
    }
  }

  private checkBrowserSupport(): boolean {
    return 'webkitSpeechRecognition' in window || 'SpeechRecognition' in window;
  }

  private initializeRecognition() {
    const SpeechRecognitionConstructor =
      (window as any).SpeechRecognition || (window as any).webkitSpeechRecognition;

    if (!SpeechRecognitionConstructor) return;

    this.#recognition = new SpeechRecognitionConstructor() as SpeechRecognition;
    this.#recognition.continuous = false; // Will be set dynamically
    this.#recognition.interimResults = true; // Enable interim results to handle pauses better
    this.#recognition.lang = 'en-US';

    // These properties help with hesitation/pauses (browser-specific)
    if ('maxAlternatives' in this.#recognition) {
      (this.#recognition as any).maxAlternatives = 3; // Get multiple interpretation alternatives
    }
    if ('speechTimeout' in this.#recognition) {
      (this.#recognition as any).speechTimeout = 3000; // Allow 3 seconds of silence before stopping
    }
    if ('speechEndTimeout' in this.#recognition) {
      (this.#recognition as any).speechEndTimeout = 2000; // Wait 2 seconds after speech ends
    }

    this.#recognition.onresult = (event: SpeechRecognitionEvent) => {
      // Only process final results, ignore interim ones
      const result = event.results[event.resultIndex];
      if (!result.isFinal) {
        // Log interim results for debugging
        const interimTranscript = result[0].transcript;
        console.log('Interim result:', interimTranscript);
        return;
      }

      const transcript = result[0].transcript.toLowerCase().trim();
      console.log('Final voice command heard:', transcript);
      this.#lastCommand = transcript;
      this.processCommand(transcript);

      // Restart if in continuous mode
      if (this.#continuousMode && this.#recognition) {
        // Small delay to prevent rapid restarts
        setTimeout(() => {
          if (this.#continuousMode && !this.#isListening) {
            this.startListening();
          }
        }, 100);
      }
    };

    this.#recognition.onerror = (event: SpeechRecognitionErrorEvent) => {
      console.error('Speech recognition error:', event.error);
      this.#isListening = false;

      // Filter out transient errors that shouldn't be shown to user
      const transientErrors = ['no-speech', 'aborted', 'audio-capture'];

      if (!transientErrors.includes(event.error)) {
        // Only show persistent errors with debouncing
        this.setError(`Speech recognition error: ${event.error}`, 3000);
      }

      // Restart if in continuous mode and not a fatal error
      if (this.#continuousMode && event.error !== 'no-speech' && event.error !== 'aborted') {
        setTimeout(() => {
          if (this.#continuousMode) {
            this.startListening();
          }
        }, 500);
      }
    };

    this.#recognition.onend = () => {
      this.#isListening = false;

      // Restart if in continuous mode
      if (this.#continuousMode) {
        setTimeout(() => {
          if (this.#continuousMode) {
            this.startListening();
          }
        }, 100);
      }
    };

    this.#recognition.onstart = () => {
      this.#isListening = true;
      this.clearErrorTimeout();
      this.#error = '';
    };
  }

  private registerDefaultCommands() {
    // Navigation commands
    this.registerCommand('dashboard', {
      patterns: ['go to dashboard', 'open dashboard', 'show dashboard', 'home', 'go to home', 'dashboard'],
      action: () => {
        router.visit(route('dashboard'));
      },
      description: 'Navigate to dashboard'
    });

    this.registerCommand('projects', {
      patterns: ['go to projects', 'open projects', 'show projects', 'projects'],
      action: () => {
        router.visit(route('project.index'));
      },
      description: 'Navigate to projects'
    });

    this.registerCommand('organizations', {
      patterns: ['go to organizations', 'open organizations', 'show organizations', 'organizations'],
      action: () => {
        router.visit(route('organization.index'));
      },
      description: 'Navigate to organizations'
    });

    this.registerCommand('rates', {
      patterns: ['go to rates', 'open rates', 'show rates', 'rates'],
      action: () => {
        router.visit(route('rate.index'));
      },
      description: 'Navigate to rates'
    });

    this.registerCommand('reports', {
      patterns: ['go to reports', 'open reports', 'show reports', 'reports'],
      action: () => {
        router.visit(route('report.index'));
      },
      description: 'Navigate to reports'
    });

    this.registerCommand('settings', {
      patterns: ['go to settings', 'open settings', 'show settings', 'settings'],
      action: () => {
        router.visit(route('profile.edit'));
      },
      description: 'Navigate to settings'
    });

    this.registerCommand('profile', {
      patterns: ['go to profile', 'open profile', 'show profile', 'my profile'],
      action: () => {
        router.visit(route('profile.edit'));
      },
      description: 'Navigate to profile'
    });

    // Browser navigation commands
    this.registerCommand('back', {
      patterns: ['go back', 'back', 'previous page'],
      action: () => {
        window.history.back();
      },
      description: 'Go back to previous page'
    });

    this.registerCommand('forward', {
      patterns: ['go forward', 'forward', 'next page'],
      action: () => {
        window.history.forward();
      },
      description: 'Go forward'
    });

    this.registerCommand('refresh', {
      patterns: ['refresh', 'reload', 'refresh page', 'reload page'],
      action: () => {
        window.location.reload();
      },
      description: 'Refresh current page'
    });

    // Scroll commands
    this.registerCommand('scroll-down', {
      patterns: ['scroll down', 'page down', 'down'],
      action: () => {
        window.scrollBy({ top: 300, behavior: 'smooth' });
      },
      description: 'Scroll down'
    });

    this.registerCommand('scroll-up', {
      patterns: ['scroll up', 'page up', 'up'],
      action: () => {
        window.scrollBy({ top: -300, behavior: 'smooth' });
      },
      description: 'Scroll up'
    });

    this.registerCommand('scroll-top', {
      patterns: ['scroll to top', 'top of page', 'go to top'],
      action: () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
      },
      description: 'Scroll to top of page'
    });

    this.registerCommand('scroll-bottom', {
      patterns: ['scroll to bottom', 'bottom of page', 'go to bottom'],
      action: () => {
        window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
      },
      description: 'Scroll to bottom of page'
    });

    // Voice control commands
    this.registerCommand('start-voice', {
      patterns: ['start voice listening', 'enable voice', 'voice on', 'start listening'],
      action: async () => {
        if (!voiceAssistant.voiceActivationEnabled) {
          voiceAssistant.voiceActivationEnabled = true;
          await voiceAssistant.startListening();
        }
      },
      description: 'Enable voice activation for voice assistant'
    });

    this.registerCommand('stop-voice', {
      patterns: ['stop voice listening', 'disable voice', 'voice off', 'stop listening'],
      action: async () => {
        if (voiceAssistant.voiceActivationEnabled) {
          voiceAssistant.voiceActivationEnabled = false;
          voiceAssistant.stopListening();
        }
      },
      description: 'Disable voice activation for voice assistant'
    });

    // Help command
    this.registerCommand('help', {
      patterns: ['help', 'show commands', 'what can you do', 'available commands'],
      action: () => {
        this.showAvailableCommands();
      },
      description: 'Show available voice commands'
    });
  }

  private processCommand(transcript: string) {
    let commandFound = false;

    // Try exact match first
    for (const [_, command] of this.#commands) {
      for (const pattern of command.patterns) {
        if (transcript === pattern) {
          command.action();
          commandFound = true;
          return;
        }
      }
    }

    // Try fuzzy matching (partial match)
    if (!commandFound) {
      for (const [_, command] of this.#commands) {
        for (const pattern of command.patterns) {
          if (transcript.includes(pattern) || pattern.includes(transcript)) {
            command.action();
            commandFound = true;
            return;
          }
        }
      }
    }

    // No command match - send to backend AI for interpretation
    if (!commandFound) {
      console.log('No matching command found, sending to AI:', transcript);

      // Restrict "key words" that trigger AI processing to avoid unnecessary calls
      const aiTriggerWords = ['create', 'add', 'set', 'clock', 'punch', 'update', 'delete', 'remove', 'generate', 'make', 'build', 'launch', 'start', 'stop', 'calculate', 'compute', 'analyze', 'show me', 'what is', 'how to', 'help me with'];
      const containsTriggerWord = aiTriggerWords.some(word => transcript.includes(word));

      if (!containsTriggerWord) {
        console.log('Transcript does not contain AI trigger words, ignoring:', transcript);
        this.#error = 'Command not recognized. Say "help" to hear available commands.';
        return;
      }

      this.sendToAI(transcript);
    }
  }

  private sendToAI(transcript: string) {
    this.#error = 'Processing with AI...';

    xPost(
      route('audio.assistant'),
      { text: transcript, timezone: getTimezone() },
      {
        onSuccess: (event: Page) => {
          this.#error = '';
          console.log('AI assistant executed successfully');
        },
        onError: (errors: any) => {
          this.#error = `AI processing failed: ${errors.text || Object.values(errors)[0] || 'Unknown error'}`;
          console.error('AI processing error:', errors);
        },
        onFinish: () => {
          // Clear processing message after a delay
          setTimeout(() => {
            if (this.#error === 'Processing with AI...') {
              this.#error = '';
            }
          }, 2000);
        },
      }
    );
  }

  private showAvailableCommands() {
    const commandList = Array.from(this.#commands.values())
      .map(cmd => `• ${cmd.patterns[0]}: ${cmd.description}`)
      .join('\n');
    
    console.log('Available voice commands:\n', commandList);
    // Store commands in error state to display in UI
    this.#error = 'Voice commands available. Check console for full list.';
  }

  // Public methods
  registerCommand(id: string, command: VoiceCommand) {
    this.#commands.set(id, command);
  }

  unregisterCommand(id: string) {
    this.#commands.delete(id);
  }

  startListening = () => {
    if (!this.#isSupported) {
      this.#error = 'Speech recognition is not supported in your browser';
      return;
    }

    if (!this.#recognition) {
      this.#error = 'Speech recognition not initialized';
      return;
    }

    if (this.#isListening) {
      console.log('Already listening');
      return;
    }

    try {
      this.#recognition.start();
    } catch (err) {
      console.error('Failed to start voice commands:', err);
      this.#error = 'Failed to start voice commands: ' + (err as Error).message;
    }
  };

  stopListening = () => {
    if (this.#recognition && this.#isListening) {
      // Only stop the recognition, don't change continuous mode
      // Continuous mode should only be changed by user action
      this.#recognition.stop();
    }
  };

  toggleContinuousMode = () => {
    this.#continuousMode = !this.#continuousMode;
    this.saveContinuousModeState();

    if (this.#continuousMode && !this.#isListening) {
      // Start listening if enabling continuous mode
      this.startListening();
    } else if (!this.#continuousMode) {
      // Disable continuous mode - this will prevent auto-restart
      // Stop recognition if currently listening
      if (this.#recognition && this.#isListening) {
        this.#recognition.stop();
      }
    }
  };

  setContinuousMode = (enabled: boolean) => {
    this.#continuousMode = enabled;
    this.saveContinuousModeState();

    if (enabled && !this.#isListening) {
      this.startListening();
    } else if (!enabled) {
      // Disable continuous mode - this will prevent auto-restart
      // Stop recognition if currently listening
      if (this.#recognition && this.#isListening) {
        this.#recognition.stop();
      }
    }
  };

  clearError = () => {
    this.clearErrorTimeout();
    this.#error = '';
  };

  clearLastCommand = () => {
    this.#lastCommand = '';
  };

  // Getters
  get isListening() {
    return this.#isListening;
  }

  get lastCommand() {
    return this.#lastCommand;
  }

  get error() {
    return this.#error;
  }

  get isSupported() {
    return this.#isSupported;
  }

  get continuousMode() {
    return this.#continuousMode;
  }

  get commands() {
    return Array.from(this.#commands.entries()).map(([id, cmd]) => ({
      id,
      ...cmd
    }));
  }
}

export const voiceCommands = new VoiceCommandsService();
export default voiceCommands;
