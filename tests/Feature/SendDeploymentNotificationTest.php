<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SendDeploymentNotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function dispatch_job_when_hash_is_valid()
    {
        Queue::partialMock();

        $response = $this->post(route('integrations.forge.webhook', ['hash' => '111']));

        $response->assertStatus(404);
    }
}
