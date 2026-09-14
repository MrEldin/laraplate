<?php

namespace Laraplate\AI\Tools;

use Laravel\Ai\Concerns\InteractsWithApprovals;
use Laravel\Ai\Contracts\Approvable;
use Laravel\Ai\Contracts\Tool;

/**
 * Base class for tools that change something.
 *
 * A run that calls one of these pauses instead of executing, and returns the
 * pending approvals to the caller. Nothing happens until a human decides:
 *
 *     $response = $user->ai()->ask('Suspend them if this looks like fraud.');
 *
 *     foreach ($response->pendingApprovals as $approval) {
 *         // show $approval->tool and $approval->arguments to a person
 *     }
 *
 *     $user->ai()->resume([$approval->id => true]);
 *
 * Read-only tools should extend nothing and simply implement Tool; the pause is
 * only worth its cost where the model can cause damage.
 */
abstract class ApprovableTool implements Approvable, Tool
{
    use InteractsWithApprovals;
}
