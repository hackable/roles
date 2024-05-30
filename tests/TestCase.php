<?php

namespace HttpOz\Roles\Tests;

use HttpOz\Roles\Tests\Stubs\User;
use Orchestra\Testbench\TestCase as Orchestra;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TestCase extends Orchestra
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh'); // This will drop all tables and re-run all migrations
        $this->setUpDatabase($this->app);
        $this->withFactories(__DIR__ . '/../database/factories');
    }

    protected function getPackageProviders($app)
    {
        return [
            \Orchestra\Database\ConsoleServiceProvider::class
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'test_db'),
            'username' => env('DB_USERNAME', 'trustanchor'),
            'password' => env('DB_PASSWORD', 'Yai3hahMaepi9uyo3Joh'),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
            'sslmode' => 'prefer',
        ]);
        $app['config']->set('auth.providers.users.model', User::class);

        $app['config']->set('roles', [
            'connection' => null,
            'separator' => '.',
            'cache' => [
                'enabled' => false,
                'expiry' => 20160,
            ],
            'models' => [
                'role' => \HttpOz\Roles\Models\Role::class
            ],
            'pretend' => [
                'enabled' => false,
                'options' => [
                    'isRole' => true
                ],
            ],
        ]);
    }

    /**
     * Set up the database.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpDatabase($app)
    {
        // Include default Laravel migrations to ensure users table exists
        $this->artisan('migrate', ['--database' => 'testbench']);

        // Include custom migrations
        include_once __DIR__ . '/../database/migrations/create_user_table.php';
        include_once __DIR__ . '/../database/migrations/create_roles_table.php';
        include_once __DIR__ . '/../database/migrations/create_role_user_table.php';
        
        (new \CreateUsersTable())->up();
        (new \CreateRolesTable())->up();
        (new \CreateRoleUserTable())->up();
    }
}