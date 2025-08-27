<?php

namespace Tests\Feature\Api;

use PHPUnit\Framework\Attributes\Test;



use Tests\TestCase;
use App\Models\MasterUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthenticationApiTest extends TestCase
{
    use RefreshDatabase;
    #[Test]
    public function it_can_register_new_user()
    {
        $userData = [
            'username' => 'newuser',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'nama' => 'New User',
            'email' => 'newuser@test.com',
            'level' => 'User',
            'kodedivisi' => 'TEST'
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'message',
                    'user' => [
                        'username',
                        'nama',
                        'email',
                        'level'
                    ],
                    'token'
                ]);

        $this->assertDatabaseHas('dbo.master_user', [
            'username' => 'newuser',
            'email' => 'newuser@test.com'
        ]);
    }
    #[Test]
    public function it_validates_registration_data()
    {
        $invalidData = [
            'username' => '', // Required
            'password' => '123', // Too short
            'password_confirmation' => '456', // Doesn't match
            'nama' => '', // Required
            'email' => 'invalid-email', // Invalid format
            'level' => 'InvalidLevel', // Invalid value
            'kodedivisi' => '' // Required
        ];

        $response = $this->postJson('/api/auth/register', $invalidData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors([
                    'username',
                    'password',
                    'nama',
                    'email',
                    'level',
                    'kodedivisi'
                ]);
    }
    #[Test]
    public function it_prevents_duplicate_username()
    {
        // Create existing user
        MasterUser::create([
            'username' => 'existinguser',
            'password' => 'password123',
            'nama' => 'Existing User',
            'email' => 'existing@test.com',
            'level' => 'User',
            'status' => 'Active',
            'kodedivisi' => 'TEST'
        ]);

        $userData = [
            'username' => 'existinguser', // Duplicate
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'nama' => 'Another User',
            'email' => 'another@test.com',
            'level' => 'User',
            'kodedivisi' => 'TEST'
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['username']);
    }
    #[Test]
    public function it_can_login_with_valid_credentials()
    {
        $user = MasterUser::create([
            'username' => 'testuser',
            'password' => 'password123',
            'nama' => 'Test User',
            'email' => 'testuser@test.com',
            'level' => 'User',
            'status' => 'Active',
            'kodedivisi' => 'TEST'
        ]);

        $loginData = [
            'username' => 'testuser',
            'password' => 'password123'
        ];

        $response = $this->postJson('/api/auth/login', $loginData);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'message',
                    'user' => [
                        'username',
                        'nama',
                        'email',
                        'level'
                    ],
                    'token'
                ]);
    }
    #[Test]
    public function it_rejects_invalid_credentials()
    {
        $user = MasterUser::create([
            'username' => 'testuser',
            'password' => 'password123',
            'nama' => 'Test User',
            'email' => 'testuser@test.com',
            'level' => 'User',
            'status' => 'Active',
            'kodedivisi' => 'TEST'
        ]);

        $loginData = [
            'username' => 'testuser',
            'password' => 'wrongpassword'
        ];

        $response = $this->postJson('/api/auth/login', $loginData);

        $response->assertStatus(401)
                ->assertJson(['message' => 'Invalid credentials']);
    }
    #[Test]
    public function it_rejects_inactive_user_login()
    {
        $user = MasterUser::create([
            'username' => 'inactiveuser',
            'password' => 'password123',
            'nama' => 'Inactive User',
            'email' => 'inactive@test.com',
            'level' => 'User',
            'status' => 'Inactive', // Inactive status
            'kodedivisi' => 'TEST'
        ]);

        $loginData = [
            'username' => 'inactiveuser',
            'password' => 'password123'
        ];

        $response = $this->postJson('/api/auth/login', $loginData);

        $response->assertStatus(401)
                ->assertJson(['message' => 'Account is inactive']);
    }
    #[Test]
    public function it_validates_login_data()
    {
        $invalidData = [
            'username' => '', // Required
            'password' => ''  // Required
        ];

        $response = $this->postJson('/api/auth/login', $invalidData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['username', 'password']);
    }
    #[Test]
    public function it_can_logout_authenticated_user()
    {
        $user = $this->createAuthenticatedUser();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->postJson('/api/auth/logout');

        $response->assertStatus(200)
                ->assertJson(['message' => 'Successfully logged out']);

        // Token should be revoked
        $this->assertCount(0, $user->tokens);
    }
    #[Test]
    public function it_requires_authentication_for_logout()
    {
        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(401);
    }
    #[Test]
    public function it_can_get_authenticated_user_profile()
    {
        $user = $this->createAuthenticatedUser();
        
        $response = $this->authenticateAs($user)->getJson('/api/auth/me');

        $response->assertStatus(200)
                ->assertJson([
                    'username' => $user->username,
                    'nama' => $user->nama,
                    'email' => $user->email,
                    'level' => $user->level
                ]);
    }
    #[Test]
    public function it_requires_authentication_for_profile()
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401);
    }
    #[Test]
    public function it_can_change_password()
    {
        $user = $this->createAuthenticatedUser();
        
        $passwordData = [
            'current_password' => 'password123',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123'
        ];

        $response = $this->authenticateAs($user)
                        ->postJson('/api/auth/change-password', $passwordData);

        $response->assertStatus(200)
                ->assertJson(['message' => 'Password changed successfully']);

        // Verify password was changed
        $user->refresh();
        $this->assertNotEquals('password123', $user->password);
    }
    #[Test]
    public function it_validates_password_change_data()
    {
        $user = $this->createAuthenticatedUser();
        
        $invalidData = [
            'current_password' => 'wrongpassword',
            'new_password' => '123', // Too short
            'new_password_confirmation' => '456' // Doesn't match
        ];

        $response = $this->authenticateAs($user)
                        ->postJson('/api/auth/change-password', $invalidData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors([
                    'current_password',
                    'new_password'
                ]);
    }
    #[Test]
    public function it_logs_authentication_attempts()
    {
        // This test would verify that authentication attempts are logged
        // Implementation depends on your logging setup
        
        $loginData = [
            'username' => 'nonexistent',
            'password' => 'password123'
        ];

        $response = $this->postJson('/api/auth/login', $loginData);

        $response->assertStatus(401);
        
        // Assert that failed login was logged
        // You would check your log files or database here
    }
    #[Test]
    public function it_handles_concurrent_logins()
    {
        $user = MasterUser::create([
            'username' => 'multiuser',
            'password' => 'password123',
            'nama' => 'Multi Session User',
            'email' => 'multi@test.com',
            'level' => 'User',
            'status' => 'Active',
            'kodedivisi' => 'TEST'
        ]);

        $loginData = [
            'username' => 'multiuser',
            'password' => 'password123'
        ];

        // First login
        $response1 = $this->postJson('/api/auth/login', $loginData);
        $response1->assertStatus(200);

        // Second login (should work - multiple sessions allowed)
        $response2 = $this->postJson('/api/auth/login', $loginData);
        $response2->assertStatus(200);

        // Both tokens should be different
        $this->assertNotEquals(
            $response1->json('token'),
            $response2->json('token')
        );
    }
    #[Test]
    public function it_expires_old_tokens()
    {
        $user = $this->createAuthenticatedUser();
        $token = $user->createToken('test-token')->plainTextToken;

        // Simulate token expiration by manipulating the created_at timestamp
        $user->tokens()->update(['created_at' => now()->subDays(31)]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->getJson('/api/auth/me');

        // Should be unauthorized due to expired token
        $response->assertStatus(401);
    }
}
