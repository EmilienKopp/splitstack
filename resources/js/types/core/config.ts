
export type Config<T> = () => T;
export type ConfigGroup<T> = Record<string, Config<T>>;