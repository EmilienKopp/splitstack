import { getPage, type Page } from "$lib/inertia";

export class Features {

  #page: Page;

  constructor() {
    this.#page = getPage();
  }

  isEnabled(featureName: string): boolean {
    if(!this.#page) return false;
    return this.#page.props.features?.[featureName] ?? false;
  }

  get voiceAssistantMode(): boolean {
    return this.isEnabled('voiceAssistantMode');
  }

  get terminalAccess(): boolean {
    return this.isEnabled('terminalAccess');
  }

}

export const features = new Features();