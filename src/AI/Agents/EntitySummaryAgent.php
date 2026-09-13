<?php

namespace Laraplate\AI\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\HasStructuredOutput;

/**
 * Produces a structured summary of one entity.
 *
 * Used by $entity->ai()->summarize(), which returns an array-accessible
 * structured response rather than prose.
 */
class EntitySummaryAgent extends EntityAgent implements HasStructuredOutput
{
    /**
     * Describe what this agent is for.
     */
    protected function role(): string
    {
        return 'You summarise a single application record so a colleague can understand it at a glance.';
    }

    /**
     * Get the agent's structured output schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'headline' => $schema->string()
                ->max(120)
                ->description('One sentence describing this record.')
                ->required(),

            'details' => $schema->array()
                ->items($schema->string())
                ->max(5)
                ->description('Up to five short factual bullet points drawn from the record.')
                ->required(),

            'concerns' => $schema->array()
                ->items($schema->string())
                ->max(5)
                ->description('Anything that looks wrong, missing, or risky. Empty when nothing stands out.')
                ->required(),
        ];
    }
}
