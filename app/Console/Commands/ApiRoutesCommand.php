<?php

namespace App\Console\Commands;

use Dingo\Api\Console\Command\Routes;
use Symfony\Component\Console\Attribute\AsCommand;

/**
 * Dingo's route listing command extends Laravel's RouteListCommand. Laravel has
 * since moved that command over to a `$signature` string, which subclasses
 * inherit -- so Dingo's command silently registers itself as "route:list" with
 * Laravel's option set, shadowing the framework's own command and discarding the
 * Dingo-specific filters its handler still reads.
 *
 * Declaring the signature here restores the intended "api:routes" name and adds
 * back the options Dingo defines in getOptions(). AppServiceProvider re-registers
 * Laravel's RouteListCommand so that "route:list" keeps working as well.
 */
#[AsCommand(name: 'api:routes')]
class ApiRoutesCommand extends Routes
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:routes
                    {--json : Output the route list as JSON}
                    {--method= : Filter the routes by method}
                    {--action= : Filter the routes by action}
                    {--name= : Filter the routes by name}
                    {--domain= : Filter the routes by domain}
                    {--middleware= : Filter the routes by middleware}
                    {--path= : Only show routes matching the given path pattern}
                    {--except-path= : Do not display the routes matching the given path pattern}
                    {--r|reverse : Reverse the ordering of the routes}
                    {--sort=uri : The column (domain, method, uri, name, action) to sort by}
                    {--except-vendor : Do not display routes defined by vendor packages}
                    {--only-vendor : Only display routes defined by vendor packages}
                    {--versions=* : Filter the routes by version}
                    {--S|scopes=* : Filter the routes by scopes}
                    {--protected : Filter the protected routes}
                    {--unprotected : Filter the unprotected routes}
                    {--short : Get an abridged version of the routes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all registered API routes';
}
