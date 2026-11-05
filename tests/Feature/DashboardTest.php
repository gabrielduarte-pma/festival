<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page()
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_admin_is_sent_to_the_panel()
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->get(route('dashboard'))->assertRedirect('/painel');
    }

    public function test_exhibitor_is_sent_to_my_stand()
    {
        $this->actingAs(User::factory()->create(['role' => 'expositor']));

        $this->get(route('dashboard'))->assertRedirect('/meu-stand');
    }
}
