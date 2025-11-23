<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TransactionApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_deposit_money(): void
    {
        // Arrange
        $user = User::factory()->create(['balance' => 500.00]);
        Sanctum::actingAs($user);

        $depositData = [
            'amount' => 200.00,
            'description' => 'Test deposit',
        ];

        // Act
        $response = $this->postJson('/api/transactions/deposit', $depositData);

        // Assert
        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'transaction' => ['id', 'type', 'amount', 'status'],
            ]);

        $this->assertEquals('Deposit completed successfully', $response->json('message'));
        $this->assertEquals('deposit', $response->json('transaction.type'));
        $this->assertEquals(200.00, $response->json('transaction.amount'));

        $user->refresh();
        $this->assertEquals(700.00, $user->balance);
    }

    public function test_user_cannot_deposit_negative_amount(): void
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $depositData = [
            'amount' => -100.00,
        ];

        // Act
        $response = $this->postJson('/api/transactions/deposit', $depositData);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['amount']);
    }

    public function test_authenticated_user_can_transfer_money(): void
    {
        // Arrange
        $fromUser = User::factory()->create(['balance' => 1000.00]);
        $toUser = User::factory()->create(['balance' => 500.00]);
        Sanctum::actingAs($fromUser);

        $transferData = [
            'to_user_id' => $toUser->id,
            'amount' => 200.00,
            'description' => 'Test transfer',
        ];

        // Act
        $response = $this->postJson('/api/transactions/transfer', $transferData);

        // Assert
        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'transaction' => ['id', 'type', 'amount', 'status'],
            ]);

        $this->assertEquals('Transfer completed successfully', $response->json('message'));

        $fromUser->refresh();
        $toUser->refresh();

        $this->assertEquals(800.00, $fromUser->balance);
        $this->assertEquals(700.00, $toUser->balance);
    }

    public function test_user_cannot_transfer_with_insufficient_balance(): void
    {
        // Arrange
        $fromUser = User::factory()->create(['balance' => 100.00]);
        $toUser = User::factory()->create(['balance' => 500.00]);
        Sanctum::actingAs($fromUser);

        $transferData = [
            'to_user_id' => $toUser->id,
            'amount' => 200.00,
        ];

        // Act
        $response = $this->postJson('/api/transactions/transfer', $transferData);

        // Assert
        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Saldo insuficiente para esta transação',
            ]);
    }

    public function test_user_cannot_transfer_to_self(): void
    {
        // Arrange
        $user = User::factory()->create(['balance' => 1000.00]);
        Sanctum::actingAs($user);

        $transferData = [
            'to_user_id' => $user->id,
            'amount' => 200.00,
        ];

        // Act
        $response = $this->postJson('/api/transactions/transfer', $transferData);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['to_user_id']);
    }

    public function test_authenticated_user_can_view_own_transactions(): void
    {
        // Arrange
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Transaction::create([
            'from_user_id' => $user->id,
            'to_user_id' => $otherUser->id,
            'type' => 'transfer',
            'amount' => 100.00,
            'status' => 'completed',
        ]);

        Transaction::create([
            'from_user_id' => $otherUser->id,
            'to_user_id' => $user->id,
            'type' => 'transfer',
            'amount' => 50.00,
            'status' => 'completed',
        ]);

        Sanctum::actingAs($user);

        // Act
        $response = $this->getJson('/api/transactions');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'type', 'amount', 'status'],
                ],
            ]);

        $this->assertCount(2, $response->json('data'));
    }

    public function test_authenticated_user_can_view_specific_transaction(): void
    {
        // Arrange
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $transaction = Transaction::create([
            'from_user_id' => $user->id,
            'to_user_id' => $otherUser->id,
            'type' => 'transfer',
            'amount' => 100.00,
            'status' => 'completed',
        ]);

        Sanctum::actingAs($user);

        // Act
        $response = $this->getJson("/api/transactions/{$transaction->id}");

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'id' => $transaction->id,
                'type' => 'transfer',
                'amount' => 100.00,
            ]);
    }

    public function test_user_cannot_view_other_user_transaction(): void
    {
        // Arrange
        $user = User::factory()->create();
        $otherUser1 = User::factory()->create();
        $otherUser2 = User::factory()->create();

        $transaction = Transaction::create([
            'from_user_id' => $otherUser1->id,
            'to_user_id' => $otherUser2->id,
            'type' => 'transfer',
            'amount' => 100.00,
            'status' => 'completed',
        ]);

        Sanctum::actingAs($user);

        // Act
        $response = $this->getJson("/api/transactions/{$transaction->id}");

        // Assert
        $response->assertStatus(404)
            ->assertJson(['message' => 'Transaction not found']);
    }

    public function test_authenticated_user_can_reverse_own_transaction(): void
    {
        // Arrange
        $fromUser = User::factory()->create(['balance' => 800.00]);
        $toUser = User::factory()->create(['balance' => 700.00]);

        $transaction = Transaction::create([
            'from_user_id' => $fromUser->id,
            'to_user_id' => $toUser->id,
            'type' => 'transfer',
            'amount' => 200.00,
            'status' => 'completed',
        ]);

        Sanctum::actingAs($fromUser);

        // Act
        $response = $this->postJson("/api/transactions/{$transaction->id}/reverse", [
            'description' => 'Reversal test',
        ]);

        // Assert
        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'reversal_transaction' => ['id', 'type', 'amount'],
            ]);

        $this->assertEquals('Transaction reversed successfully', $response->json('message'));

        $transaction->refresh();
        $this->assertEquals('reversed', $transaction->status);

        $fromUser->refresh();
        $toUser->refresh();
        $this->assertEquals(1000.00, $fromUser->balance);
        $this->assertEquals(500.00, $toUser->balance);
    }

    public function test_user_cannot_reverse_other_user_transaction(): void
    {
        // Arrange
        $user = User::factory()->create();
        $otherUser1 = User::factory()->create();
        $otherUser2 = User::factory()->create();

        $transaction = Transaction::create([
            'from_user_id' => $otherUser1->id,
            'to_user_id' => $otherUser2->id,
            'type' => 'transfer',
            'amount' => 100.00,
            'status' => 'completed',
        ]);

        Sanctum::actingAs($user);

        // Act
        $response = $this->postJson("/api/transactions/{$transaction->id}/reverse");

        // Assert
        $response->assertStatus(403)
            ->assertJson(['message' => 'You can only reverse your own transactions']);
    }

    public function test_unauthenticated_user_cannot_access_transactions(): void
    {
        // Act
        $response = $this->getJson('/api/transactions');

        // Assert
        $response->assertStatus(401);
    }
}

