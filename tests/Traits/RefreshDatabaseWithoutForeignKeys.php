<?php

namespace Tests\Traits;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait RefreshDatabaseWithoutForeignKeys
{
    use RefreshDatabase;

    /**
     * Refresh the in-memory database.
     *
     * @return void
     */
    protected function refreshInMemoryDatabase()
    {
        // Disable foreign key checks for SQLite
        DB::statement('PRAGMA foreign_keys = OFF');

        parent::refreshInMemoryDatabase();

        // Re-enable foreign key checks after migrations
        DB::statement('PRAGMA foreign_keys = ON');
    }

    /**
     * Refresh a conventional database.
     *
     * @return void
     */
    protected function refreshTestDatabase()
    {
        if (! RefreshDatabaseState::$migrated) {
            // Disable foreign key checks for SQLite
            if (DB::connection()->getDriverName() === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = OFF');
            }

            $this->artisan('migrate:fresh', $this->migrateFreshUsing());

            // Re-enable foreign key checks after migrations
            if (DB::connection()->getDriverName() === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = ON');
            }

            RefreshDatabaseState::$migrated = true;
        }

        $this->beginDatabaseTransaction();
    }
}
