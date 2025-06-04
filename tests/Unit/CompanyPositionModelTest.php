<?php

use Tests\TestCase;
use App\Models\CompanyPosition;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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

it('can connect to the database', function () {
    expect(DB::connection()->getPdo())->toBeInstanceOf(PDO::class);
});

it('allows mass assignment', function () {
    $data = [
        'name' => 'Manager',
        'description' => 'Responsible for managing the team',
        'is_published' => true,
    ];

    $position = CompanyPosition::create($data);

    expect($position->name)->toBe('Manager');
    expect($position->description)->toBe('Responsible for managing the team');
    expect($position->is_published)->toBeTrue();
});

it('casts boolean attributes correctly', function () {
    $position = CompanyPosition::create([
        'name' => 'Engineer',
        'description' => 'Handles technical tasks',
        'is_published' => true,
    ]);

    expect($position->is_published)->toBeTrue();
});

it('throws validation error if name is missing', function () {
    CompanyPosition::create([
        'description' => 'No name provided',
        'is_published' => false,
    ]);
})->throws(Exception::class);

it('can update attributes', function () {
    $position = CompanyPosition::create([
        'name' => 'Developer',
        'description' => 'Writes code',
        'is_published' => false,
    ]);

    $position->update([
        'name' => 'Senior Developer',
        'is_published' => true,
    ]);

    expect($position->fresh())
        ->name->toBe('Senior Developer')
        ->description->toBe('Writes code')
        ->is_published->toBeTrue();
});
