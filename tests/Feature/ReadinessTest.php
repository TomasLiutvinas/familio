<?php

namespace Tests\Feature;

use App\Models\User;
use Filament\Panel;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ReadinessTest extends TestCase
{
    public function test_readiness_checks_database_without_mutations(): void
    {
        DB::shouldReceive('selectOne')->once()->with('SELECT 1 AS ready')->andReturn((object) ['ready' => 1]);
        $this->get('/api/ready')->assertOk()->assertJson(['status' => 'ready']);
    }

    public function test_failure_response_does_not_expose_database_details(): void
    {
        DB::shouldReceive('selectOne')->once()->with('SELECT 1 AS ready')->andThrow(new \RuntimeException('private-token-secret'));
        $response = $this->get('/api/ready');
        $response->assertStatus(503)->assertExactJson(['status' => 'unavailable']);
        $this->assertStringNotContainsString('private-token-secret', $response->getContent());
    }

    public function test_owner_provisioned_accounts_can_access_only_admin_panel(): void
    {
        $user = new User;
        $this->assertTrue($user->canAccessPanel(Panel::make()->id('admin')));
        $this->assertFalse($user->canAccessPanel(Panel::make()->id('other')));
    }
}
