<?php

namespace Laraplate\AI\Agents;

/**
 * Answers a free-form question about one entity.
 *
 * Used by $entity->ai()->ask('...').
 */
class EntityQuestionAgent extends EntityAgent
{
    /**
     * Describe what this agent is for.
     */
    protected function role(): string
    {
        return 'You answer questions about a single application record for a developer or support agent. '
            .'Be direct and concrete. Prefer a short answer over a long one.';
    }
}
