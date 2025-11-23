<?php

namespace Tests\Unit\Services;

use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\TransactionException;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TransferService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransferServiceTest extends TestCase
{
    use RefreshDatabase;

    private TransferService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TransferService();
    }

    public function test_transfer_successfully_transfers_money_between_users(): void
    {
        // Arrange
        $fromUser = User::factory()->create(['balance' => 1000.00]);
        $toUser = User::factory()->create(['balance' => 500.00]);
        $amount = 200.00;
        $description = 'Transferência de teste';

        // Act
        $transaction = $this->service->transfer($fromUser, $toUser, $amount, $description);

        // Assert
        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertEquals('transfer', $transaction->type);
        $this->assertEquals($amount, $transaction->amount);
        $this->assertEquals('completed', $transaction->status);
        $this->assertEquals($description, $transaction->description);
        $this->assertEquals($fromUser->id, $transaction->from_user_id);
        $this->assertEquals($toUser->id, $transaction->to_user_id);

        $fromUser->refresh();
        $toUser->refresh();

        $this->assertEquals(800.00, $fromUser->balance);
        $this->assertEquals(700.00, $toUser->balance);
    }

    public function test_transfer_throws_exception_when_amount_is_zero(): void
    {
        // Arrange
        $fromUser = User::factory()->create(['balance' => 1000.00]);
        $toUser = User::factory()->create(['balance' => 500.00]);

        // Act & Assert
        $this->expectException(TransactionException::class);
        $this->expectExceptionMessage('O valor deve ser maior que zero');

        $this->service->transfer($fromUser, $toUser, 0);
    }

    public function test_transfer_throws_exception_when_amount_is_negative(): void
    {
        // Arrange
        $fromUser = User::factory()->create(['balance' => 1000.00]);
        $toUser = User::factory()->create(['balance' => 500.00]);

        // Act & Assert
        $this->expectException(TransactionException::class);
        $this->expectExceptionMessage('O valor deve ser maior que zero');

        $this->service->transfer($fromUser, $toUser, -100);
    }

    public function test_transfer_throws_exception_when_transferring_to_self(): void
    {
        // Arrange
        $user = User::factory()->create(['balance' => 1000.00]);

        // Act & Assert
        $this->expectException(TransactionException::class);
        $this->expectExceptionMessage('Não é possível transferir para você mesmo');

        $this->service->transfer($user, $user, 100);
    }

    public function test_transfer_throws_exception_when_insufficient_balance(): void
    {
        // Arrange
        $fromUser = User::factory()->create(['balance' => 100.00]);
        $toUser = User::factory()->create(['balance' => 500.00]);

        // Act & Assert
        $this->expectException(InsufficientBalanceException::class);

        $this->service->transfer($fromUser, $toUser, 200.00);
    }

    public function test_transfer_creates_transaction_without_description(): void
    {
        // Arrange
        $fromUser = User::factory()->create(['balance' => 1000.00]);
        $toUser = User::factory()->create(['balance' => 500.00]);

        // Act
        $transaction = $this->service->transfer($fromUser, $toUser, 100.00);

        // Assert
        $this->assertNull($transaction->description);
    }

    public function test_transfer_updates_balances_correctly(): void
    {
        // Arrange
        $fromUser = User::factory()->create(['balance' => 1000.50]);
        $toUser = User::factory()->create(['balance' => 250.75]);
        $amount = 150.25;

        // Act
        $this->service->transfer($fromUser, $toUser, $amount);

        // Assert
        $fromUser->refresh();
        $toUser->refresh();

        $this->assertEquals(850.25, $fromUser->balance);
        $this->assertEquals(401.00, $toUser->balance);
    }
}

