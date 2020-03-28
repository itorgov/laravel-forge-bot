<?php

namespace Tests\Feature;

use App\Facades\Hashids;
use App\Jobs\SendDeploymentNotificationJob;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SendDeploymentNotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function dispatch_job_when_hash_is_valid()
    {
        Queue::fake();
        $user = factory(User::class)->create([
            'telegram_chat_id' => '12345',
        ]);
        $hash = Hashids::encode($user->id);
        $requestParams = [
            'test_param_A' => 'Value_A',
            'test_param_B' => 'Value_B',
        ];

        $response = $this->post(route('integrations.forge.webhook', ['hash' => $hash]), $requestParams);

        $response->assertStatus(200);
        Queue::assertPushed(SendDeploymentNotificationJob::class, function ($job) use ($user, $requestParams) {
            $this->assertEquals($requestParams, $job->deploymentInfo);

            return $job->user->id === $user->id;
        });
    }

    /** @test */
    public function job_didnot_dispatch_when_hash_was_invalid()
    {
        Queue::fake();
        factory(User::class)->create([
            'telegram_chat_id' => '12345',
        ]);

        $response = $this->post(route('integrations.forge.webhook', ['hash' => 'invalid-hash']));

        $response->assertStatus(404);
        Queue::assertNothingPushed();
    }
}
