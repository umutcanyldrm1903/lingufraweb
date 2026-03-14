<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create(['role' => 'student']);

        $response = $this
            ->actingAs($user)
            ->get('/student/setting');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create(['role' => 'student']);

        $response = $this
            ->actingAs($user)
            ->put('/student/setting/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'phone' => '5551112233',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/student/setting');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertSame('5551112233', $user->phone);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $originalVerifiedAt = $user->email_verified_at;

        $response = $this
            ->actingAs($user)
            ->put('/student/setting/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/student/setting');

        $this->assertEquals($originalVerifiedAt, $user->refresh()->email_verified_at);
    }

    public function test_profile_bio_can_be_updated(): void
    {
        $user = User::factory()->create(['role' => 'student']);

        $response = $this
            ->actingAs($user)
            ->put('/student/setting/bio', [
                'designation' => 'Language Learner',
                'short_bio' => 'Short bio',
                'bio' => 'Long bio content',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/student/setting');

        $user->refresh();
        $this->assertSame('Language Learner', $user->job_title);
        $this->assertSame('Short bio', $user->short_bio);
        $this->assertSame('Long bio content', $user->bio);
    }
}
