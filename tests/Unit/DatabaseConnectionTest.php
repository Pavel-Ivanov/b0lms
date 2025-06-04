<?php

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Traits\DatabaseTestTables;

uses(TestCase::class, DatabaseTransactions::class, DatabaseTestTables::class);

beforeEach(function () {
    // Make sure we're using SQLite in-memory database
    config(['database.default' => 'sqlite']);
    config(['database.connections.sqlite.database' => ':memory:']);

    // Enable foreign key support for SQLite
    DB::statement('PRAGMA foreign_keys = ON');

    // Create necessary tables for testing
    $this->createTestTables();
});

it('can connect to the SQLite database', function () {
    // Test basic connection
    expect(DB::connection()->getPdo())->toBeInstanceOf(PDO::class);

    // Verify we're using SQLite
    expect(DB::connection()->getDriverName())->toBe('sqlite');
});

it('can perform basic database operations', function () {
    // Create a test table
    if (Schema::hasTable('test_table')) {
        Schema::dropIfExists('test_table');
    }

    Schema::create('test_table', function ($table) {
        $table->id();
        $table->string('name');
        $table->timestamps();
    });

    // Insert data
    DB::table('test_table')->insert([
        'name' => 'Test Record',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Query data
    $record = DB::table('test_table')->where('name', 'Test Record')->first();

    // Verify data was inserted and can be retrieved
    expect($record)->not->toBeNull();
    expect($record->name)->toBe('Test Record');

    // Clean up
    Schema::dropIfExists('test_table');
});
