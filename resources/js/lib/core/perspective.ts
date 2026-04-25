import type { Config, ConfigGroup } from '@/types/core/config';

export class Perspective<T> {
    public variants: ConfigGroup<T>;
    public defaultVariant: Config<T>;

    constructor(variants: ConfigGroup<T>, defaultVariant: Config<T>) {
        this.variants = variants;
        this.defaultVariant = defaultVariant;
    }

    for(role: string): T {
        return (this.variants[role] ?? this.defaultVariant)();
    }
}
