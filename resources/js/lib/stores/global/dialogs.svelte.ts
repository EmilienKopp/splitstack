import { getContext } from "svelte";

type DialogContext = {
    open: () => void;
    close: () => void;
} | undefined;

export const DialogContextKey = Symbol('dialogContext');

export function useDialogContext(): DialogContext {
  return getContext<DialogContext>(DialogContextKey);
}
