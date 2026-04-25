type UTCDate = Date;
type JSTDate = Date;
type Timezone = string;
type JSTDateTime = Date;
type UTCDateTime = Date;
type Hours = number;
type Minutes = number;
type Seconds = number;
type Milliseconds = number;

type Average = number;
type Median = number;
type Mode = number;

type Currency = string;

type fn = (() => void) | (() => Promise<void>) | EventListener | undefined;

/**
 * Variants for buttons, badges, etc.
 */
type Variants =
    | 'primary'
    | 'secondary'
    | 'success'
    | 'danger'
    | 'warning'
    | 'info'
    | 'accent'
    | 'neutral'
    | 'ghost'
    | 'error';

/**
 * Toast feedback types
 */
type FeedbackType = 'success' | 'error' | 'info';

/**
 * Avoids annoying "Property 'name' does not exist on type 'EventTarget'" errors
 */
type AnyEvent =
    | Event
    | CustomEvent
    | KeyboardEvent
    | MouseEvent
    | TouchEvent
    | PointerEvent
    | WheelEvent
    | AnimationEvent
    | TransitionEvent
    | ClipboardEvent
    | CompositionEvent
    | DragEvent
    | FocusEvent
    | InputEvent
    | UIEvent
    | WheelEvent;

type Listener<T extends AnyEvent = AnyEvent> = (
    event: T,
) => void | Promise<void> | EventListener | undefined;

type Eventable = Document | HTMLElement | Window;

type HTTPMethod =
    | 'GET'
    | 'POST'
    | 'PUT'
    | 'PATCH'
    | 'DELETE'
    | 'get'
    | 'post'
    | 'put'
    | 'patch'
    | 'delete';

type DropdownAction = {
    text: string;
    href?: string;
    onclick?: fn;
    method?: HTTPMethod;
    as?: 'a' | 'button';
};

type SelectOption = {
    value: string | number;
    label?: string;
    name: string;
};
