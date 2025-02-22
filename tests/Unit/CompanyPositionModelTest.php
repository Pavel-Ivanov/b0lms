<?php

use Tests\TestCase;
use App\Models\CompanyPosition;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

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
