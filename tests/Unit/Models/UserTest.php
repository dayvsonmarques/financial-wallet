<?php

namespace Tests\Unit\Models;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_has_sufficient_balance_returns_true_when_balance_is_greater_than_amount(): void
    {
        // Arrange
        $user = User::factory()->create(['balance' => 1000.00]);

        // Act & Assert
        $this->assertTrue($user->hasSufficientBalance(500.00));
    }

    public function test_has_sufficient_balance_returns_true_when_balance_equals_amount(): void
    {
        // Arrange
        $user = User::factory()->create(['balance' => 1000.00]);

        // Act & Assert
        $this->assertTrue($user->hasSufficientBalance(1000.00));
    }

    public function test_has_sufficient_balance_returns_false_when_balance_is_less_than_amount(): void
    {
        // Arrange
        $user = User::factory()->create(['balance' => 500.00]);

        // Act & Assert
        $this->assertFalse($user->hasSufficientBalance(1000.00));
    }

    public function test_has_sufficient_balance_returns_false_when_balance_is_negative(): void
    {
        // Arrange
        $user = User::factory()->create(['balance' => -100.00]);

        // Act & Assert
        $this->assertFalse($user->hasSufficientBalance(50.00));
    }

    public function test_user_has_sent_transactions_relationship(): void
    {
        // Arrange
        $user = User::factory()->create();
        $recipient = User::factory()->create();
        
        Transaction::create([
            'from_user_id' => $user->id,
            'to_user_id' => $recipient->id,
            'type' => 'transfer',
            'amount' => 100.00,
            'status' => 'completed',
        ]);

        // Act
        $sentTransactions = $user->sentTransactions;

        // Assert
        $this->assertCount(1, $sentTransactions);
        $this->assertEquals($user->id, $sentTransactions->first()->from_user_id);
    }

    public function test_user_has_received_transactions_relationship(): void
    {
        // Arrange
        $user = User::factory()->create();
        $sender = User::factory()->create();
        
        Transaction::create([
            'from_user_id' => $sender->id,
            'to_user_id' => $user->id,
            'type' => 'transfer',
            'amount' => 100.00,
            'status' => 'completed',
        ]);

        // Act
        $receivedTransactions = $user->receivedTransactions;

        // Assert
        $this->assertCount(1, $receivedTransactions);
        $this->assertEquals($user->id, $receivedTransactions->first()->to_user_id);
    }
}

