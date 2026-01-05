<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Collection;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('dashboard page is displayed', function () {
    $user = User::factory()->has(Wallet::factory()->richChillGuy())->create();
    $wallet = Wallet::factory()->richChillGuy()->for($user)->create();

    $response = actingAs($user)->get('/');

    $response
        ->assertOk()
        ->assertSeeTextInOrder([
            __('Balance'),
            Number::currencyCents($wallet->balance),
            'Transactions history',
            'Just a rich chill guy',
        ]);
});

test('send money to a friend', function () {
    $user = User::factory()->has(Wallet::factory()->richChillGuy())->create();
    $recipient = User::factory()->has(Wallet::factory())->create();

    $response = actingAs($user)->post('/send-money', [
        'recipient_email' => $recipient->email,
        'amount' => 10, // In euros, not cents
        'reason' => 'Just a chill guy gift',
    ]);

    $response
        ->assertRedirect('/')
        ->assertSessionHas('money-sent-status', 'success')
        ->assertSessionHas('money-sent-recipient-name', $recipient->name)
        ->assertSessionHas('money-sent-amount', 10_00);

    actingAs($user)->get('/')
        ->assertSeeTextInOrder([
            __('Balance'),
            Number::currencyCents(1_000_000 - 10_00),
            'Transactions history',
            'Just a chill guy gift',
            Number::currencyCents(-10_00),
            'Just a rich chill guy',
            Number::currencyCents(1_000_000),
        ]);
});

test('cannot send money to a friend with insufficient balance', function () {
    $user = User::factory()->has(Wallet::factory())->create();
    $recipient = User::factory()->has(Wallet::factory())->create();

    $response = actingAs($user)->post('/send-money', [
        'recipient_email' => $recipient->email,
        'amount' => 10, // In euros, not cents
        'reason' => 'Just a chill guy gift',
    ]);

    $response
        ->assertRedirect('/')
        ->assertSessionHas('money-sent-status', 'insufficient-balance')
        ->assertSessionHas('money-sent-recipient-name', $recipient->name)
        ->assertSessionHas('money-sent-amount', 10_00);
});

it('shows dashboard with empty transactions and zero balance when its new user', function () {
    $user = User::factory()->create();

    actingAs($user);

    $response = get(route('dashboard'));

    $response->assertOk()
        ->assertViewIs('dashboard')
        ->assertViewHas('transactions', fn ($transactions) => $transactions instanceof Collection && $transactions->isEmpty())
        ->assertViewHas('balance', 0);
});
