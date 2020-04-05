<?php

namespace Tests\Feature;

use App\DeploymentLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ViewDeploymentLogsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_view_existed_deployment_log()
    {
        $deploymentLog = factory(DeploymentLog::class)->create([
            'server_name' => 'Test server',
            'site_name' => 'Test site',
            'content' => 'Test deployment log.',
            'created_at' => Carbon::parse('2020-04-05 10:34:21'),
        ]);

        $response = $this->get(route('deployment-logs.show', [$deploymentLog]));

        $response->assertStatus(200);
        $response->assertViewIs('deployment-logs.show');
        $response->assertViewHas('deploymentLog', $deploymentLog);
        $response->assertSee('Test server');
        $response->assertSee('Test site');
        $response->assertSee('1586082861'); // Created_at as timestamp.
        $response->assertSee('Test deployment log.');
    }

    /** @test */
    public function cannot_view_not_existed_deployment_log()
    {
        $response = $this->get(route('deployment-logs.show', ['deploymentLog' => 'invalid-deployment-log-id']));

        $response->assertStatus(404);
    }
}
