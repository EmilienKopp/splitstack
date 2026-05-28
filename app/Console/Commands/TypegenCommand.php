<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Support\TypeScript\TypeScriptGenerator;
use Illuminate\Console\Command;

final class TypegenCommand extends Command
{
    protected $signature = 'split:typegen
        {--entities-only      : Only generate interfaces for Domain/Entities}
        {--value-objects-only : Only generate interfaces for Domain/ValueObjects}
        {--resources-only     : Only generate interfaces for resources and DTOs}
        {--no-barrel          : Skip writing the barrel index.ts}';

    protected $description = 'Generate TypeScript interfaces from domain entities, value objects, and resources';

    public function __construct(
        private readonly TypeScriptGenerator $generator,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $sources = $this->resolveSources();

        /** @var array<string, array{path: string, names: string[]}> $generated */
        $generated = [];

        foreach ($sources as $sourceKey) {
            $result = $this->generateSource($sourceKey, $generated);
            if ($result !== null) {
                $generated[$sourceKey] = $result;
            }
        }

        if (empty($generated)) {
            $this->warn('No TypeScriptConvertible classes found in any source directory.');

            return self::SUCCESS;
        }

        if (! $this->option('no-barrel')) {
            $this->writeBarrel($generated);
        }

        $this->info('Done.');

        return self::SUCCESS;
    }

    /**
     * Returns the list of source keys to process based on flags.
     *
     * @return array<string>
     */
    private function resolveSources(): array
    {
        $onlyFlags = [
            'entities-only' => 'entities',
            'value-objects-only' => 'value_objects',
            'resources-only' => 'resources',
        ];

        foreach ($onlyFlags as $flag => $key) {
            if ($this->option($flag)) {
                return [$key];
            }
        }

        // Default: all sources defined in config
        return array_keys(config('typegen.sources', []));
    }

    /**
     * Scan and write one source group.
     *
     * @param  array<string, array{path: string, names: string[]}>  $alreadyGenerated
     * @return array{path: string, names: string[]}|null
     */
    private function generateSource(string $sourceKey, array $alreadyGenerated): ?array
    {
        $outputPath = config('typegen.output.'.$sourceKey);

        if (! $outputPath) {
            $this->warn(sprintf("No output path configured for source '%s'. Skipping.", $sourceKey));

            return null;
        }

        $this->line(sprintf('Scanning <comment>%s</comment>...', $sourceKey));

        $interfaces = $this->generator->scanSource($sourceKey, function (string $name): void {
            $this->line('  <info>+</info> '.$name);
        });

        if ($interfaces === []) {
            $this->warn(sprintf("  No TypeScriptConvertible classes found for '%s'.", $sourceKey));

            return null;
        }

        // Build import map from previously generated sources so cross-references work
        $availableImports = [];
        foreach ($alreadyGenerated as $info) {
            $basename = pathinfo($info['path'], PATHINFO_FILENAME);
            $availableImports['./'.$basename] = $info['names'];
        }

        $this->generator->writeInterfaces($interfaces, $outputPath, $availableImports);
        $this->info('  Written '.count($interfaces).(' interface(s) → '.$outputPath));

        return [
            'path' => $outputPath,
            'names' => array_keys($interfaces),
        ];
    }

    /**
     * @param  array<string, array{path: string, names: string[]}>  $generated
     */
    private function writeBarrel(array $generated): void
    {
        $barrelPath = config('typegen.output.barrel', resource_path('js/types/index.ts'));
        $outputPaths = array_column($generated, 'path');

        $this->generator->writeBarrel($outputPaths, $barrelPath);
        $this->info('Barrel written → '.$barrelPath);
    }
}
