<?php

namespace Tests\Unit\Services;

use App\Exceptions\TransactionException;
use App\Models\Transaction;
use App\Models\User;
use App\Services\DepositService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepositServiceTest extends TestCase
{
    use RefreshDatabase;

    private DepositService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DepositService();
    }

    public function test_deposit_successfully_adds_money_to_user_account(): void
    {
        // Arrange
        $user = User::factory()->create(['balance' => 500.00]);
        $amount = 200.00;
        $description = 'Depósito de teste';

        // Act
        $transaction = $this->service->deposit($user, $amount, $description);

        // Assert
        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertEquals('deposit', $transaction->type);
        $this->assertEquals($amount, $transaction->amount);
        $this->assertEquals('completed', $transaction->status);
        $this->assertEquals($description, $transaction->description);
        $this->assertEquals($user->id, $transaction->to_user_id);
        $this->assertNull($transaction->from_user_id);

        $user->refresh();
        $this->assertEquals(700.00, $user->balance);
    }

    public function test_deposit_throws_exception_when_amount_is_zero(): void
    {
        // Arrange
        $user = User::factory()->create(['balance' => 500.00]);

        // Act & Assert
        $this->expectException(TransactionException::class);
        $this->expectExceptionMessage('O valor deve ser maior que zero');

        $this->service->deposit($user, 0);
    }

    public function test_deposit_throws_exception_when_amount_is_negative(): void
    {
        // Arrange
        $user = User::factory()->create(['balance' => 500.00]);

        // Act & Assert
        $this->expectException(TransactionException::class);
        $this->expectExceptionMessage('O valor deve ser maior que zero');

        $this->service->deposit($user, -100);
    }

    public function test_deposit_uses_default_description_when_not_provided(): void
    {
        // Arrange
        $user = User::factory()->create(['balance' => 500.00]);

        // Act
        $transaction = $this->service->deposit($user, 100.00);

        // Assert
        $this->assertEquals('Depósito', $transaction->description);
    }

    public function test_deposit_works_with_negative_balance(): void
    {
        // Arrange
        $user = User::factory()->create(['balance' => -100.00]);
        $amount = 200.00;

        // Act
        $this->service->deposit($user, $amount);

        // Assert
        $user->refresh();
        $this->assertEquals(100.00, $user->balance);
    }

    public function test_deposit_updates_balance_correctly_with_decimals(): void
    {
        // Arrange
        $user = User::factory()->create(['balance' => 100.50]);
        $amount = 99.75;

        // Act
        $this->service->deposit($user, $amount);

        // Assert
        $user->refresh();
        $this->assertEquals(200.25, $user->balance);
    }
}

