<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class WalletApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_get_balance(): void
    {
        // Arrange
        $user = User::factory()->create(['balance' => 1250.75]);
        Sanctum::actingAs($user);

        // Act
        $response = $this->getJson('/api/wallet/balance');

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'balance' => 1250.75,
            ]);
    }

    public function test_unauthenticated_user_cannot_get_balance(): void
    {
        // Act
        $response = $this->getJson('/api/wallet/balance');

        // Assert
        $response->assertStatus(401);
    }

    public function test_balance_reflects_after_deposit(): void
    {
        // Arrange
        $user = User::factory()->create(['balance' => 500.00]);
        Sanctum::actingAs($user);

        // Act - Deposit
        $this->postJson('/api/transactions/deposit', [
            'amount' => 200.00,
        ]);

        // Assert - Check balance
        $response = $this->getJson('/api/wallet/balance');
        $response->assertStatus(200)
            ->assertJson(['balance' => 700.00]);
    }

    public function test_balance_reflects_after_transfer(): void
    {
        // Arrange
        $fromUser = User::factory()->create(['balance' => 1000.00]);
        $toUser = User::factory()->create(['balance' => 500.00]);
        Sanctum::actingAs($fromUser);

        // Act - Transfer
        $this->postJson('/api/transactions/transfer', [
            'to_user_id' => $toUser->id,
            'amount' => 300.00,
        ]);

        // Assert - Check sender balance
        $response = $this->getJson('/api/wallet/balance');
        $response->assertStatus(200)
            ->assertJson(['balance' => 700.00]);
    }
}

