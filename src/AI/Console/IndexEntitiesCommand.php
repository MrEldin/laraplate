<?php

namespace Laraplate\AI\Console;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Laraplate\AI\Concerns\HasAiSearch;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\progress;

/**
 * Rebuilds the semantic search index for one entity type.
 */
#[AsCommand(name: 'ai:index')]
class IndexEntitiesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:index
                            {model : The fully qualified entity class to index}
                            {--chunk=100 : How many records to load at a time}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build the semantic search index for an entity type';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $model = (string) $this->argument('model');

        if (! class_exists($model) || ! is_subclass_of($model, Model::class)) {
            $this->components->error("[{$model}] is not an Eloquent model.");

            return self::FAILURE;
        }

        if (! in_array(HasAiSearch::class, class_uses_recursive($model), true)) {
            $this->components->error("[{$model}] does not use the ".HasAiSearch::class.' trait.');

            return self::FAILURE;
        }

        $total = $model::query()->count();

        if ($total === 0) {
            $this->components->info("No {$model} records to index.");

            return self::SUCCESS;
        }

        $indexed = 0;

        progress(
            label: 'Indexing '.class_basename($model),
            steps: $total,
            callback: function ($progress) use ($model, &$indexed): void {
                $model::query()->chunkById((int) $this->option('chunk'), function ($records) use ($progress, &$indexed): void {
                    foreach ($records as $record) {
                        $record->aiIndex();
                        $indexed++;
                        $progress->advance();
                    }
                });
            },
        );

        $this->components->info("Indexed {$indexed} ".class_basename($model).' records.');

        return self::SUCCESS;
    }
}
