<?php

use App\Http\Middleware\RequireReportAuthentication;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

function reportCredentials(User $user): array
{
    return ['login' => $user->email, 'password' => 'report-test-secret'];
}

function reportUser(): User
{
    return User::factory()->create(['password' => Hash::make('report-test-secret')]);
}

test('same account can confirm reports and retain the filtered intended URL', function () {
    $user = reportUser();
    $this->actingAs($user);
    $target = route('reports.visits', ['search' => 'Test', 'start_date' => '2020-01-01']);
    $this->get($target)->assertRedirect('/laporan/login');
    $this->get('/laporan/login')->assertOk()->assertSee('Login Ulang Laporan')->assertSee('name="_token"', false);
    $this->post('/laporan/login', reportCredentials($user))->assertRedirect($target);
    $this->withCookie(config('session.cookie'), session()->getId());
    $this->get($target)->assertOk();
    $this->withCookie(config('session.cookie'), session()->getId());
    $this->get(route('reports.index'))->assertOk();
    $this->assertAuthenticatedAs($user);
});

test('report login also authenticates a guest once using username', function () {
    $user = reportUser();
    $this->post('/laporan/login', ['login' => $user->name, 'password' => 'report-test-secret'])->assertRedirect(route('reports.index'));
    $this->assertAuthenticatedAs($user);
    $this->withCookie(config('session.cookie'), session()->getId());
    $this->get(route('reports.index'))->assertOk();
});

test('another valid account never replaces the operational account', function () {
    $current = reportUser();
    $other = reportUser();
    $this->actingAs($current);
    $this->post('/laporan/login', reportCredentials($other))->assertSessionHasErrors('login');
    $this->assertAuthenticatedAs($current);
    $this->get(route('reports.index'))->assertRedirect('/laporan/login');
});

test('invalid report credentials are rate limited without confirming access', function () {
    $user = reportUser();
    $this->actingAs($user);
    for ($i = 0; $i < 5; $i++) {
        $this->post('/laporan/login', ['login' => $user->email, 'password' => 'incorrect'])->assertSessionHasErrors('login');
    }
    $this->post('/laporan/login', reportCredentials($user))->assertSessionHasErrors('login');
    $this->get(route('reports.index'))->assertRedirect('/laporan/login');
    $this->travel(61)->seconds();
    $this->post('/laporan/login', reportCredentials($user))->assertRedirect();
    $this->withCookie(config('session.cookie'), session()->getId());
    $this->get(route('reports.index'))->assertOk();
});

test('operations and report URLs have independent areas and navigation', function () {
    expect(route('dashboard', absolute: false))->toBe('/pemeriksaan');
    expect(route('reports.index', absolute: false))->toBe('/laporan');
    expect(route('reports.visits', absolute: false))->toBe('/laporan/kunjungan');
    $user = reportUser();
    $this->post('/login', reportCredentials($user))->assertRedirect('/pemeriksaan');
    $this->withCookie(config('session.cookie'), session()->getId());
    $this->get(route('dashboard'))->assertOk()->assertSee('Pemeriksaan Klinik')->assertSee(route('reports.entry'));
    $this->get(route('reports.index'))->assertRedirect('/laporan/login');
});

test('report links stay in the gated read only area including clinical history and PDFs', function () {
    $patient = clinicPatient('ReadOnly');
    $visit = clinicVisit($patient, 'Poli Gigi');
    $user = reportUser();
    $this->actingAs($user)->post('/laporan/login', reportCredentials($user))->assertRedirect();
    $this->withCookie(config('session.cookie'), session()->getId());
    $this->get(route('reports.visits'))->assertOk()
        ->assertSee(route('reports.history.show', $patient))
        ->assertSee(route('reports.examinations.show', $visit))
        ->assertSee(route('reports.odontogram.show', $visit));
    foreach (['reports.history.show' => $patient, 'reports.examinations.show' => $visit, 'reports.odontogram.show' => $visit] as $name => $model) {
        $this->get(route($name, $model))->assertOk()->assertSee('Laporan Klinik · Baca Saja')
            ->assertDontSee('name="nomor_gigi"', false)->assertDontSee(route('visits.edit', $visit))
            ->assertDontSee(route('doctors.create'))->assertDontSee(route('profile.edit'));
    }
    foreach (['reports.examinations.pdf', 'reports.visits.pdf'] as $name) {
        $response = $this->get(route($name, $name === 'reports.visits.pdf' ? [] : $visit))->assertOk()->assertHeader('Content-Type', 'application/pdf');
        expect($response->getContent())->toStartWith('%PDF-');
    }
    $this->post(route('reports.odontogram.show', $visit), ['nomor_gigi' => 11, 'kondisi' => 'Karies'])->assertStatus(405);
    $this->assertDatabaseCount('visit_odontogram_snapshots', 0);
});

test('operational authentication alone cannot open clinic reports', function () {
    $this->actingAs(User::factory()->create());
    $this->get(route('reports.index'))->assertRedirect('/laporan/login');
    $this->get(route('dashboard'))->assertOk();
});

test('returning to operations invalidates report confirmation and entry always asks again', function () {
    $user = reportUser();
    $this->actingAs($user)->post('/laporan/login', reportCredentials($user));
    $this->withCookie(config('session.cookie'), session()->getId());
    $this->get(route('reports.index'))->assertOk();
    $this->get(route('dashboard'))->assertOk();
    $this->get(route('reports.index'))->assertRedirect('/laporan/login');
    $this->post('/laporan/login', reportCredentials($user));
    $this->withCookie(config('session.cookie'), session()->getId());
    $this->get(route('reports.entry'))->assertRedirect('/laporan/login');
    $this->get(route('reports.index'))->assertRedirect('/laporan/login');
});

test('confirmation expires after thirty minutes', function () {
    $user = reportUser();
    $this->actingAs($user)->post('/laporan/login', reportCredentials($user));
    $this->withCookie(config('session.cookie'), session()->getId());
    $this->travel(30)->minutes();
    $this->get(route('reports.index'))->assertRedirect('/laporan/login')->assertSessionMissing('reports.confirmation');
});

test('account switches and regenerated sessions cannot reuse confirmation', function () {
    $user = reportUser();
    $this->actingAs($user)->post('/laporan/login', reportCredentials($user));
    $this->withCookie(config('session.cookie'), session()->getId());
    $this->actingAs(reportUser())->get(route('reports.index'))->assertRedirect('/laporan/login');
    $this->actingAs($user)->post('/laporan/login', reportCredentials($user));
    session()->regenerate();
    $this->withCookie(config('session.cookie'), session()->getId());
    $this->get(route('reports.index'))->assertRedirect('/laporan/login');
});

test('logout destroys report confirmation', function () {
    $user = reportUser();
    $this->actingAs($user)->post('/laporan/login', reportCredentials($user));
    $this->withCookie(config('session.cookie'), session()->getId());
    $this->post(route('logout'))->assertRedirect('/')->assertSessionMissing('reports.confirmation');
    $this->withCookie(config('session.cookie'), session()->getId());
    $this->assertGuest();
    $this->get(route('reports.index'))->assertRedirect('/laporan/login');
});

test('every report endpoint including documents requires independent confirmation', function () {
    $patient = clinicPatient('Gate');
    $visit = clinicVisit($patient, 'Poli Gigi');
    $routes = collect(Route::getRoutes())->filter(fn ($r) => str_starts_with($r->getName() ?? '', 'reports.') && in_array(RequireReportAuthentication::class, $r->gatherMiddleware()));
    expect($routes->count())->toBe(16);
    foreach ($routes as $r) {
        $parameters = str_contains($r->uri(), '{patient}') ? [$patient] : (str_contains($r->uri(), '{visit}') ? [$visit] : []);
        $this->get(route($r->getName(), $parameters))->assertRedirect('/laporan/login');
    }
    $this->actingAs(reportUser());
    foreach ($routes as $r) {
        $parameters = str_contains($r->uri(), '{patient}') ? [$patient] : (str_contains($r->uri(), '{visit}') ? [$visit] : []);
        $this->get(route($r->getName(), $parameters))->assertRedirect('/laporan/login');
    }
});

test('confirmed sessions can read every canonical report endpoint without leaving reports', function () {
    $patient = clinicPatient('AllEndpoints');
    $visit = clinicVisit($patient, 'Poli Gigi');
    $user = reportUser();
    $this->actingAs($user)->post('/laporan/login', ['email' => $user->email, 'password' => 'report-test-secret'])->assertSessionHasNoErrors();
    $this->withCookie(config('session.cookie'), session()->getId());
    $routes = collect(Route::getRoutes())->filter(fn ($r) => str_starts_with($r->getName() ?? '', 'reports.') && in_array(RequireReportAuthentication::class, $r->gatherMiddleware()));
    foreach ($routes as $r) {
        $parameters = str_contains($r->uri(), '{patient}') ? [$patient] : (str_contains($r->uri(), '{visit}') ? [$visit] : []);
        $this->get(route($r->getName(), $parameters))->assertOk()->assertHeader('Cache-Control', 'no-store, private');
    }
    $this->get(route('reports.examinations.print', $visit))->assertOk()
        ->assertSee(route('reports.examinations.show', $visit))->assertDontSee(route('visits.show', $visit));
});

test('report login cannot redirect outside its own area', function () {
    $user = reportUser();
    foreach (['https://example.com', '//example.com', '/pemeriksaan/visits/create', '/laporan/login', '/laporan/masuk'] as $destination) {
        $this->withSession(['reports.intended' => $destination])->post('/laporan/login', reportCredentials($user))->assertRedirect('/laporan');
        $this->withCookie(config('session.cookie'), session()->getId());
    }
});

test('report login enforces CSRF protection', function () {
    $this->app->detectEnvironment(fn () => 'local');
    $this->post('/laporan/login', reportCredentials(reportUser()))->assertStatus(419);
});

test('legacy report URLs redirect within the gated report area retaining filters', function () {
    $this->actingAs(reportUser());
    $this->get('/laporan/reports/visits?search=Test')->assertRedirect('/laporan/login');
    $user = auth()->user();
    $this->post('/laporan/login', reportCredentials($user));
    $this->withCookie(config('session.cookie'), session()->getId());
    $this->get('/laporan/reports/visits?search=Test')->assertRedirect('/laporan/kunjungan?search=Test');
});

test('guests are sent to the independent report login', function () {
    $this->get(route('reports.index'))->assertRedirect('/laporan/login');
});
