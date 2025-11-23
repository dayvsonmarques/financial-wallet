<?php

namespace Tests\Unit\Services;

use App\Exceptions\TransactionException;
use App\Models\Transaction;
use App\Models\User;
use App\Services\ReversalService;
use App\Services\TransferService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReversalServiceTest extends TestCase
{
    use RefreshDatabase;

    private ReversalService $service;
    private TransferService $transferService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ReversalService();
        $this->transferService = new TransferService();
    }

    public function test_reverse_successfully_reverses_transfer_transaction(): void
    {
        // Arrange
        $fromUser = User::factory()->create(['balance' => 1000.00]);
        $toUser = User::factory()->create(['balance' => 500.00]);
        $amount = 200.00;

        $originalTransaction = $this->transferService->transfer($fromUser, $toUser, $amount);
        $description = 'Estorno solicitado';

        // Act
        $reversalTransaction = $this->service->reverse($originalTransaction, $description);

        // Assert
        $this->assertInstanceOf(Transaction::class, $reversalTransaction);
        $this->assertEquals('reversal', $reversalTransaction->type);
        $this->assertEquals($amount, $reversalTransaction->amount);
        $this->assertEquals('completed', $reversalTransaction->status);
        $this->assertEquals($description, $reversalTransaction->description);
        $this->assertEquals($originalTransaction->id, $reversalTransaction->reversed_by_transaction_id);

        $originalTransaction->refresh();
        $this->assertEquals('reversed', $originalTransaction->status);

        $fromUser->refresh();
        $toUser->refresh();

        $this->assertEquals(1000.00, $fromUser->balance);
        $this->assertEquals(500.00, $toUser->balance);
    }

    public function test_reverse_throws_exception_when_transaction_cannot_be_reversed(): void
    {
        // Arrange
        $fromUser = User::factory()->create(['balance' => 1000.00]);
        $toUser = User::factory()->create(['balance' => 500.00]);
        $amount = 200.00;

        $transaction = $this->transferService->transfer($fromUser, $toUser, $amount);
        $this->service->reverse($transaction);

        // Act & Assert
        $transaction->refresh();
        $this->expectException(TransactionException::class);
        $this->expectExceptionMessage('Esta transação não pode ser estornada');

        $this->service->reverse($transaction);
    }

    public function test_reverse_uses_default_description_when_not_provided(): void
    {
        // Arrange
        $fromUser = User::factory()->create(['balance' => 1000.00]);
        $toUser = User::factory()->create(['balance' => 500.00]);
        $transaction = $this->transferService->transfer($fromUser, $toUser, 100.00);

        // Act
        $reversalTransaction = $this->service->reverse($transaction);

        // Assert
        $this->assertStringContainsString('Estorno da transação #', $reversalTransaction->description);
    }

    public function test_reverse_reverses_deposit_transaction(): void
    {
        // Arrange
        $user = User::factory()->create(['balance' => 500.00]);
        $depositService = new \App\Services\DepositService();
        $depositTransaction = $depositService->deposit($user, 200.00);

        // Act
        $reversalTransaction = $this->service->reverse($depositTransaction);

        // Assert
        $this->assertEquals('reversal', $reversalTransaction->type);
        
        $user->refresh();
        $this->assertEquals(500.00, $user->balance);
    }
}

