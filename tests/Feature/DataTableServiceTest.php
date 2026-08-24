<?php

use App\Models\User;
use App\Services\DataTableService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = new DataTableService();
});

function dataTableRequest(array $params = []): Request
{
    return Request::create('/', 'GET', $params);
}

test('it paginates with the default page size', function () {
    User::factory()->count(15)->create();

    $result = $this->service->process(User::query(), dataTableRequest(), [
        'resource' => 'users',
    ]);

    expect($result['data']->perPage())
        ->toBe(DataTableService::DEFAULT_PAGE_SIZE)
        ->and($result['data']->total())
        ->toBe(15)
        ->and($result['data']->items())
        ->toHaveCount(10);
});

test('it accepts allowed per_page values and rejects others', function () {
    User::factory()->count(30)->create();

    $allowed = $this->service->process(User::query(), dataTableRequest(['per_page' => 25]), [
        'resource' => 'users',
    ]);
    expect($allowed['data']->perPage())->toBe(25);

    $rejected = $this->service->process(User::query(), dataTableRequest(['per_page' => 7]), [
        'resource' => 'users',
    ]);
    expect($rejected['data']->perPage())->toBe(DataTableService::DEFAULT_PAGE_SIZE);

    $nonsense = $this->service->process(User::query(), dataTableRequest(['per_page' => 'abc']), [
        'resource' => 'users',
    ]);
    expect($nonsense['data']->perPage())->toBe(DataTableService::DEFAULT_PAGE_SIZE);
});

test('per_page=all returns every filtered row up to the cap', function () {
    User::factory()->count(12)->create();

    $result = $this->service->process(User::query(), dataTableRequest(['per_page' => 'all']), [
        'resource' => 'users',
    ]);

    expect($result['data']->perPage())
        ->toBe(12)
        ->and($result['data']->items())
        ->toHaveCount(12);
});

test('it searches across the configured searchable columns', function () {
    User::factory()->create(['name' => 'Alice Wonders', 'email' => 'alice@example.com']);
    User::factory()->create(['name' => 'Bob Builder', 'email' => 'bob@example.com']);

    $byName = $this->service->process(User::query(), dataTableRequest(['search' => 'Alice']), [
        'searchable' => ['name', 'email'],
        'resource' => 'users',
    ]);
    expect($byName['data']->total())
        ->toBe(1)
        ->and($byName['data']->items()[0]->name)
        ->toBe('Alice Wonders');

    $byEmail = $this->service->process(User::query(), dataTableRequest(['search' => 'bob@example']), [
        'searchable' => ['name', 'email'],
        'resource' => 'users',
    ]);
    expect($byEmail['data']->total())
        ->toBe(1)
        ->and($byEmail['data']->items()[0]->name)
        ->toBe('Bob Builder');
});

test('it searches relationship columns with dot notation', function () {
    $user = User::factory()->create(['name' => 'History Owner']);
    $other = User::factory()->create(['name' => 'Someone Else']);

    $user->loginHistory()->create(['login_at' => now(), 'user_agent' => 'TestAgent']);
    $other->loginHistory()->create(['login_at' => now(), 'user_agent' => 'OtherAgent']);

    $result = $this->service->process(
        \App\Models\LoginHistory::query(),
        dataTableRequest(['search' => 'History Owner']),
        [
            'searchable' => ['user.name', 'user_agent'],
            'resource' => 'login_history',
        ],
    );

    expect($result['data']->total())
        ->toBe(1)
        ->and($result['data']->items()[0]->user_agent)
        ->toBe('TestAgent');
});

test('it sorts by allowlisted columns in both directions', function () {
    User::factory()->create(['name' => 'Zed']);
    User::factory()->create(['name' => 'Anna']);

    $asc = $this->service->process(User::query(), dataTableRequest(['sort_by' => 'name', 'sort_dir' => 'asc']), [
        'sortable' => ['name' => ['type' => 'simple']],
        'resource' => 'users',
    ]);
    expect($asc['data']->items()[0]->name)->toBe('Anna');

    $desc = $this->service->process(User::query(), dataTableRequest(['sort_by' => 'name', 'sort_dir' => 'desc']), [
        'sortable' => ['name' => ['type' => 'simple']],
        'resource' => 'users',
    ]);
    expect($desc['data']->items()[0]->name)->toBe('Zed');
});

test('it ignores sort columns that are not allowlisted', function () {
    User::factory()->count(3)->create();

    $result = $this->service->process(User::query(), dataTableRequest(['sort_by' => 'password', 'sort_dir' => 'asc']), [
        'sortable' => ['name' => ['type' => 'simple']],
        'resource' => 'users',
    ]);

    expect($result['data']->total())->toBe(3);
});

test('it does not execute SQL injection attempts via sort parameters', function () {
    User::factory()->count(2)->create();

    $result = $this->service->process(
        User::query(),
        dataTableRequest([
            'sort_by' => 'name; DROP TABLE users;--',
            'sort_dir' => 'asc, (SELECT 1)',
        ]),
        [
            'sortable' => ['name' => ['type' => 'simple']],
            'resource' => 'users',
        ],
    );

    expect($result['data']->total())
        ->toBe(2)
        ->and(User::count())
        ->toBe(2);
});

test('it falls back to the default direction for invalid sort_dir', function () {
    User::factory()->create(['name' => 'Anna']);
    User::factory()->create(['name' => 'Zed']);

    $result = $this->service->process(
        User::query(),
        dataTableRequest(['sort_by' => 'name', 'sort_dir' => 'sideways']),
        [
            'sortable' => ['name' => ['type' => 'simple']],
            'resource' => 'users',
        ],
    );

    // Default direction is desc
    expect($result['data']->items()[0]->name)->toBe('Zed');
});

test('LIKE wildcards in search input are matched literally', function () {
    User::factory()->create(['name' => 'Normal Name']);
    User::factory()->create(['name' => '100% Legit']);
    User::factory()->create(['name' => 'under_score']);

    $percent = $this->service->process(User::query(), dataTableRequest(['search' => '100%']), [
        'searchable' => ['name'],
        'resource' => 'users',
    ]);
    expect($percent['data']->total())
        ->toBe(1)
        ->and($percent['data']->items()[0]->name)
        ->toBe('100% Legit');

    // '%' alone must not match every row
    $bare = $this->service->process(User::query(), dataTableRequest(['search' => '%%%%']), [
        'searchable' => ['name'],
        'resource' => 'users',
    ]);
    expect($bare['data']->total())->toBe(0);

    $underscore = $this->service->process(User::query(), dataTableRequest(['search' => 'under_s']), [
        'searchable' => ['name'],
        'resource' => 'users',
    ]);
    expect($underscore['data']->total())->toBe(1);
});

test('array search input is ignored instead of throwing', function () {
    User::factory()->count(3)->create();

    $result = $this->service->process(User::query(), dataTableRequest(['search' => ['x', 'y']]), [
        'searchable' => ['name', 'email'],
        'resource' => 'users',
    ]);

    expect($result['data']->total())->toBe(3);
});

test('buildFilters only echoes datatable keys back to the page', function () {
    $result = $this->service->process(
        User::query(),
        dataTableRequest(['search' => 'abc', 'per_page' => 10, 'utm_source' => 'evil', 'foo' => 'bar']),
        ['resource' => 'users'],
    );

    expect($result['filters'])
        ->toHaveKeys(['search', 'per_page'])
        ->and($result['filters'])
        ->not->toHaveKey('utm_source')
        ->and($result['filters'])
        ->not->toHaveKey('foo');
});

test('relationship sort preserves the controller select', function () {
    $userA = User::factory()->create(['name' => 'Anna']);
    $userZ = User::factory()->create(['name' => 'Zed']);

    foreach ([$userA, $userZ] as $user) {
        \App\Models\Session::create([
            'id' => 'session-' . $user->id,
            'user_id' => $user->id,
            'payload' => 'should-not-be-selected',
            'last_activity' => now()->timestamp,
        ]);
    }

    $result = $this->service->process(
        \App\Models\Session::query()->select(['id', 'user_id', 'last_activity']),
        dataTableRequest(['sort_by' => 'user.name', 'sort_dir' => 'asc']),
        [
            'sortable' => [
                'user.name' => [
                    'type' => 'relationship',
                    'table' => 'users',
                    'foreign_key' => 'sessions.user_id',
                    'local_key' => 'users.id',
                    'order_by' => 'users.name',
                ],
            ],
            'resource' => 'sessions',
        ],
    );

    $first = $result['data']->items()[0];

    expect($first->user_id)->toBe($userA->id)->and($first->getAttributes())->not->toHaveKey('payload');
});

test('it resolves sane page numbers from garbage input', function () {
    expect($this->service->resolvePage(dataTableRequest(['page' => -5])))
        ->toBe(1)
        ->and($this->service->resolvePage(dataTableRequest(['page' => 'abc'])))
        ->toBe(1)
        ->and($this->service->resolvePage(dataTableRequest(['page' => 3])))
        ->toBe(3);
});
