<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create([
            'role' => 'team_member'
        ]);
        
        $this->admin = User::factory()->create([
            'role' => 'admin'
        ]);
    }

    public function test_admin_can_create_project(): void
    {
        $this->actingAs($this->admin);

        $projectData = [
            'name' => 'Test Project',
            'description' => 'This is a test project',
            'status' => 'planning',
            'priority' => 'medium',
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addDays(30)->format('Y-m-d'),
        ];

        $response = $this->post('/api/projects', $projectData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'uuid',
                        'code',
                        'name',
                        'description',
                        'status',
                        'priority',
                        'owner_id',
                        'progress'
                    ]
                ]);

        $this->assertDatabaseHas('projects', [
            'name' => 'Test Project',
            'owner_id' => $this->admin->id
        ]);
    }

    public function test_team_member_cannot_create_project(): void
    {
        $this->actingAs($this->user);

        $projectData = [
            'name' => 'Test Project',
            'description' => 'This is a test project',
        ];

        $response = $this->post('/api/projects', $projectData);

        $response->assertStatus(403);
    }

    public function test_project_progress_calculation(): void
    {
        $project = Project::factory()
            ->hasMembers(1, ['user_id' => $this->user->id])
            ->create(['owner_id' => $this->admin->id]);

        // Create tasks
        $project->tasks()->createMany([
            ['title' => 'Task 1', 'status' => 'done', 'assigned_to' => $this->user->id, 'created_by' => $this->admin->id],
            ['title' => 'Task 2', 'status' => 'done', 'assigned_to' => $this->user->id, 'created_by' => $this->admin->id],
            ['title' => 'Task 3', 'status' => 'in_progress', 'assigned_to' => $this->user->id, 'created_by' => $this->admin->id],
            ['title' => 'Task 4', 'status' => 'todo', 'assigned_to' => $this->user->id, 'created_by' => $this->admin->id],
        ]);

        $progress = $project->calculateProgress();

        $this->assertEquals(50, $progress); // 2 out of 4 tasks completed = 50%
    }

    public function test_user_can_only_access_assigned_projects(): void
    {
        $this->actingAs($this->user);

        // Create projects - one where user is member, one where they're not
        $accessibleProject = Project::factory()
            ->hasMembers(1, ['user_id' => $this->user->id])
            ->create(['owner_id' => $this->admin->id]);

        $inaccessibleProject = Project::factory()
            ->create(['owner_id' => $this->admin->id]);

        $response = $this->get('/api/projects');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonFragment(['id' => $accessibleProject->id])
                ->assertJsonMissing(['id' => $inaccessibleProject->id]);
    }

    public function test_project_code_generation(): void
    {
        $project = Project::factory()->create([
            'name' => 'Guardian System',
            'owner_id' => $this->admin->id
        ]);

        $this->assertMatchesRegularExpression(
            '/^GUA-\d{4}-\d{4}$/', 
            $project->code
        );
    }
}
