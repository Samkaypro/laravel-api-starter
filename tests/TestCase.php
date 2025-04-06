<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, DatabaseTransactions;
    
    /**
     * Indicates whether the default seeder should run before each test.
     *
     * @var bool
     */
    protected $seed = false;
    
    /**
     * Flag to track if migrations have been run.
     */
    protected static $migrated = false;
    
    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Run migrations only once
        if (!static::$migrated) {
            Artisan::call('migrate:fresh', ['--seed' => true]);
            static::$migrated = true;
        }
    }
}
