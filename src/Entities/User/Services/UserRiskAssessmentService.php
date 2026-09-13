<?php

namespace Laraplate\Entities\User\Services;

use Laraplate\AI\Facades\Intelligence;
use Laraplate\AI\Tools\EntityLookupTool;
use Laraplate\Entities\Role\Models\Role;
use Laraplate\Entities\User\Models\User;

/**
 * A worked example of using the AI layer from an ordinary service.
 *
 * The service never builds a prompt or names a model: it says which entity it
 * cares about and what decision it needs, and the AI layer supplies the
 * redacted context, the guardrails and the structured output schema.
 */
class UserRiskAssessmentService
{
    /**
     * The classifications this service accepts back from the model.
     *
     * @var list<string>
     */
    public const LABELS = ['healthy', 'dormant', 'over_privileged', 'suspicious'];

    /**
     * Assess a single user and return the structured verdict.
     *
     * @return array{label: string, confidence: float, reason: string}
     */
    public function handle(User $user): array
    {
        $assessment = Intelligence::for($user)
            // The lookup tool lets the model compare against other roles when
            // the user's own roles are not enough to reach a verdict.
            ->withTools(new EntityLookupTool(Role::class))
            ->classify(
                self::LABELS,
                'Assess this account for access risk. Weigh how privileged the roles are '
                .'against how the account is actually being used.',
            );

        return [
            'label' => $assessment['label'],
            'confidence' => (float) $assessment['confidence'],
            'reason' => $assessment['reason'],
        ];
    }
}
