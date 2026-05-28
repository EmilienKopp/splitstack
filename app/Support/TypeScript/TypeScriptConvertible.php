<?php

declare(strict_types=1);

namespace App\Support\TypeScript;

interface TypeScriptConvertible
{
    /**
     * Return a map of property names to TypeScript type strings.
     *
     * @return array<string, string>|null
     */
    public static function getTypeScriptDefinition(): ?array;
}
