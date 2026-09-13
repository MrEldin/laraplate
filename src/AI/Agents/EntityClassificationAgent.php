<?php

namespace Laraplate\AI\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laraplate\AI\Context\EntityContext;
use Laravel\Ai\Contracts\HasStructuredOutput;

/**
 * Assigns one entity to exactly one of a fixed set of labels.
 *
 * The labels are part of the schema, so the provider constrains the output to
 * them instead of the application having to re-validate free text.
 */
class EntityClassificationAgent extends EntityAgent implements HasStructuredOutput
{
    /**
     * @param  list<string>  $labels
     */
    public function __construct(EntityContext $context, public readonly array $labels)
    {
        parent::__construct($context);
    }

    /**
     * Describe what this agent is for.
     */
    protected function role(): string
    {
        return 'You classify a single application record into exactly one of the allowed labels: '
            .implode(', ', $this->labels).'.';
    }

    /**
     * Get the agent's structured output schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'label' => $schema->string()
                ->enum($this->labels)
                ->description('The single best matching label.')
                ->required(),

            'confidence' => $schema->number()
                ->min(0)
                ->max(1)
                ->description('How confident the classification is, from 0 to 1.')
                ->required(),

            'reason' => $schema->string()
                ->max(280)
                ->description('A short justification referring to the record.')
                ->required(),
        ];
    }
}
