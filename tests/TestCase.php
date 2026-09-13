<?php

namespace Tests;

use Database\Seeders\Test\TestingDatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Traits\AuthenticatedUser;

abstract class TestCase extends BaseTestCase
{
    use AuthenticatedUser, DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(TestingDatabaseSeeder::class);

        $this->createAuthenticatedUser('super-admin');
    }
}
