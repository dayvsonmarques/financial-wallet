<?php

namespace Tests\Unit\Models;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_be_reversed_returns_true_for_completed_transaction(): void
    {
        // Arrange
        $fromUser = User::factory()->create(['balance' => 1000.00]);
        $toUser = User::factory()->create(['balance' => 500.00]);
        
        $transaction = Transaction::create([
            'from_user_id' => $fromUser->id,
            'to_user_id' => $toUser->id,
            'type' => 'transfer',
            'amount' => 100.00,
            'status' => 'completed',
            'reversed_by_transaction_id' => null,
        ]);

        // Act & Assert
        $this->assertTrue($transaction->canBeReversed());
    }

    public function test_can_be_reversed_returns_false_for_reversed_transaction(): void
    {
        // Arrange
        $fromUser = User::factory()->create(['balance' => 1000.00]);
        $toUser = User::factory()->create(['balance' => 500.00]);
        
        $transaction = Transaction::create([
            'from_user_id' => $fromUser->id,
            'to_user_id' => $toUser->id,
            'type' => 'transfer',
            'amount' => 100.00,
            'status' => 'reversed',
            'reversed_by_transaction_id' => null,
        ]);

        // Act & Assert
        $this->assertFalse($transaction->canBeReversed());
    }

    public function test_can_be_reversed_returns_false_when_already_reversed(): void
    {
        // Arrange
        $fromUser = User::factory()->create(['balance' => 1000.00]);
        $toUser = User::factory()->create(['balance' => 500.00]);
        
        $originalTransaction = Transaction::create([
            'from_user_id' => $fromUser->id,
            'to_user_id' => $toUser->id,
            'type' => 'transfer',
            'amount' => 100.00,
            'status' => 'completed',
        ]);

        $reversalTransaction = Transaction::create([
            'from_user_id' => $toUser->id,
            'to_user_id' => $fromUser->id,
            'type' => 'reversal',
            'amount' => 100.00,
            'status' => 'completed',
            'reversed_by_transaction_id' => $originalTransaction->id,
        ]);

        $originalTransaction->update(['status' => 'reversed']);

        // Act & Assert
        $this->assertFalse($originalTransaction->canBeReversed());
    }

    public function test_is_reversed_returns_true_when_status_is_reversed(): void
    {
        // Arrange
        $transaction = Transaction::create([
            'type' => 'transfer',
            'amount' => 100.00,
            'status' => 'reversed',
        ]);

        // Act & Assert
        $this->assertTrue($transaction->isReversed());
    }

    public function test_is_reversed_returns_false_when_status_is_completed(): void
    {
        // Arrange
        $transaction = Transaction::create([
            'type' => 'transfer',
            'amount' => 100.00,
            'status' => 'completed',
        ]);

        // Act & Assert
        $this->assertFalse($transaction->isReversed());
    }

    public function test_transaction_has_from_user_relationship(): void
    {
        // Arrange
        $fromUser = User::factory()->create();
        $toUser = User::factory()->create();
        
        $transaction = Transaction::create([
            'from_user_id' => $fromUser->id,
            'to_user_id' => $toUser->id,
            'type' => 'transfer',
            'amount' => 100.00,
            'status' => 'completed',
        ]);

        // Act
        $transaction->load('fromUser');

        // Assert
        $this->assertInstanceOf(User::class, $transaction->fromUser);
        $this->assertEquals($fromUser->id, $transaction->fromUser->id);
    }

    public function test_transaction_has_to_user_relationship(): void
    {
        // Arrange
        $fromUser = User::factory()->create();
        $toUser = User::factory()->create();
        
        $transaction = Transaction::create([
            'from_user_id' => $fromUser->id,
            'to_user_id' => $toUser->id,
            'type' => 'transfer',
            'amount' => 100.00,
            'status' => 'completed',
        ]);

        // Act
        $transaction->load('toUser');

        // Assert
        $this->assertInstanceOf(User::class, $transaction->toUser);
        $this->assertEquals($toUser->id, $transaction->toUser->id);
    }
}

