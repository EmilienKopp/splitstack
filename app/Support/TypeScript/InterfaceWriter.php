<?php

declare(strict_types=1);

namespace App\Support\TypeScript;

use Illuminate\Support\Facades\File;

final class InterfaceWriter
{
    /**
     * Write TypeScript interfaces to a file, with optional cross-imports from sibling files.
     *
     * @param  array<string, array<string, string>>  $interfaces  Map of interface name → property definitions
     * @param  array<string, string[]>  $availableImports  Map of relative path → exported type names
     *                                                     e.g. ['./entities' => ['UserEntity', 'OrderEntity']]
     */
    public function writeInterfaces(array $interfaces, string $outputPath, array $availableImports = []): void
    {
        File::ensureDirectoryExists(dirname($outputPath));
        File::put($outputPath, $this->buildContent($interfaces, $availableImports));
    }

    /**
     * Write a barrel index.ts that re-exports from each given output file.
     * Only includes files that exist and are non-empty.
     *
     * @param  array<string>  $outputPaths  Absolute paths to the files to barrel
     */
    public function writeBarrel(array $outputPaths, string $barrelPath): void
    {
        $exports = [];
        foreach ($outputPaths as $path) {
            if (File::exists($path) && File::size($path) > 0) {
                $basename = pathinfo($path, PATHINFO_FILENAME);
                $exports[] = sprintf("export * from './%s';", $basename);
            }
        }

        if ($exports === []) {
            return;
        }

        File::ensureDirectoryExists(dirname($barrelPath));
        $content = "// Auto-generated — do not edit\n".implode("\n", $exports)."\n";
        File::put($barrelPath, $content);
    }

    /**
     * Format a raw property string into a full interface declaration.
     */
    public function formatInterface(string $name, string $properties): string
    {
        return "export interface {$name} {\n{$properties}\n}";
    }

    private function buildContent(array $interfaces, array $availableImports): string
    {
        $content = "// Auto-generated — do not edit\n";
        $content .= '// Generated: '.now()->toIso8601String()."\n\n";

        // Add import statements for types referenced in these interfaces
        $importLines = $this->resolveImports($interfaces, $availableImports);
        if ($importLines !== []) {
            $content .= implode("\n", $importLines)."\n\n";
        }

        foreach ($interfaces as $name => $properties) {
            $content .= "export interface {$name} {\n";
            foreach ($properties as $prop => $type) {
                $content .= "  {$prop}: {$type};\n";
            }

            $content .= "}\n\n";
        }

        return mb_rtrim($content)."\n";
    }

    /**
     * Build import lines for any type referenced in $interfaces that exists in $availableImports.
     *
     * @param  array<string, array<string, string>>  $interfaces
     * @param  array<string, string[]>  $availableImports
     * @return array<string>
     */
    private function resolveImports(array $interfaces, array $availableImports): array
    {
        if ($availableImports === []) {
            return [];
        }

        // Collect all TypeScript type names mentioned in property values
        $referencedTypes = [];
        foreach ($interfaces as $properties) {
            foreach ($properties as $type) {
                preg_match_all('/\b([A-Z][a-zA-Z0-9]+)\b/', $type, $matches);
                foreach ($matches[1] as $match) {
                    $referencedTypes[$match] = true;
                }
            }
        }

        $importLines = [];
        foreach ($availableImports as $importPath => $exportedNames) {
            $needed = array_values(array_filter(
                $exportedNames,
                fn (string $name): bool => isset($referencedTypes[$name])
            ));
            sort($needed);

            if ($needed !== []) {
                $importLines[] = 'import type { '.implode(', ', $needed).sprintf(" } from '%s';", $importPath);
            }
        }

        return $importLines;
    }
}
