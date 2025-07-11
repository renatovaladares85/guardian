<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SecurityContextService;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard with optimized queries using security context.
     */
    public function index(Request $request)
    {
        // Get security context once to avoid multiple database queries
        $securityContext = SecurityContextService::getSecurityContext();
        
        if (!$securityContext) {
            return redirect()->route('login');
        }

        // Get accessible project IDs from security context (no DB query)
        $accessibleProjectIds = $securityContext['accessible_projects'] ?? [];
        $userRole = $securityContext['role'];
        $userDepartment = $securityContext['department'];

        // Build optimized queries based on security context
        $projectsQuery = Project::query();
        $tasksQuery = Task::query();

        // Apply security filtering
        if (!in_array($userRole, ['super_admin', 'project_manager'])) {
            $projectsQuery->whereIn('id', $accessibleProjectIds);
            $tasksQuery->whereIn('project_id', $accessibleProjectIds);
        }

        // Get dashboard statistics with single queries
        $statistics = $this->getDashboardStatistics($projectsQuery, $tasksQuery, $securityContext);
        
        // Get recent activities efficiently
        $recentActivities = $this->getRecentActivities($accessibleProjectIds, $userRole);
        
        // Get user's tasks efficiently
        $userTasks = $this->getUserTasks($securityContext['user_id'], $accessibleProjectIds);
        
        // Get project progress data
        $projectProgress = $this->getProjectProgress($accessibleProjectIds, $userRole);

        // Legacy variables for compatibility
        $user = Auth::user();
        $stats = [
            'total_projects' => $statistics['projects']['total'],
            'active_projects' => $statistics['projects']['active'],
            'my_tasks' => $statistics['my_tasks']['total'],
            'overdue_tasks' => $statistics['my_tasks']['overdue'],
        ];
        $recentProjects = $recentActivities['recent_projects'];
        $myTasks = $userTasks['active_tasks'];
        $overdueTasks = $userTasks['overdue_tasks'];

        return view('dashboard', compact(
            'stats',
            'recentProjects', 
            'myTasks',
            'overdueTasks',
            'statistics',
            'recentActivities',
            'userTasks',
            'projectProgress',
            'securityContext'
        ));
    }

    /**
     * Get dashboard statistics with optimized queries.
     */
    private function getDashboardStatistics($projectsQuery, $tasksQuery, array $securityContext): array
    {
        $userId = $securityContext['user_id'];
        $userRole = $securityContext['role'];

        // Use single query with aggregations for better performance
        $projectStats = $projectsQuery->selectRaw('
            COUNT(*) as total_projects,
            SUM(CASE WHEN status = "active" THEN 1 ELSE 0 END) as active_projects,
            SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_projects,
            SUM(CASE WHEN status = "planning" THEN 1 ELSE 0 END) as planning_projects,
            SUM(CASE WHEN status = "on_hold" THEN 1 ELSE 0 END) as on_hold_projects,
            AVG(progress_percentage) as avg_progress,
            SUM(budget) as total_budget,
            SUM(spent_budget) as total_spent
        ')->first();

        $taskStats = $tasksQuery->selectRaw('
            COUNT(*) as total_tasks,
            SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_tasks,
            SUM(CASE WHEN status = "in_progress" THEN 1 ELSE 0 END) as in_progress_tasks,
            SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_tasks,
            SUM(CASE WHEN status = "testing" THEN 1 ELSE 0 END) as testing_tasks,
            SUM(estimated_hours) as total_estimated_hours,
            SUM(actual_hours) as total_actual_hours
        ')->first();

        // Get user-specific task statistics
        $userTaskStats = Task::where('assigned_to', $userId)
            ->selectRaw('
                COUNT(*) as my_total_tasks,
                SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as my_pending_tasks,
                SUM(CASE WHEN status = "in_progress" THEN 1 ELSE 0 END) as my_in_progress_tasks,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as my_completed_tasks,
                SUM(CASE WHEN due_date < NOW() AND status != "completed" THEN 1 ELSE 0 END) as my_overdue_tasks
            ')->first();

        // Get team statistics for managers
        $teamStats = [];
        if (in_array($userRole, ['super_admin', 'project_manager', 'team_lead'])) {
            $teamStats = User::where('department', $securityContext['department'])
                ->where('is_active', true)
                ->selectRaw('
                    COUNT(*) as team_members,
                    SUM(CASE WHEN last_login_at > DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as active_members
                ')->first();
        }

        return [
            'projects' => [
                'total' => $projectStats->total_projects ?? 0,
                'active' => $projectStats->active_projects ?? 0,
                'completed' => $projectStats->completed_projects ?? 0,
                'planning' => $projectStats->planning_projects ?? 0,
                'on_hold' => $projectStats->on_hold_projects ?? 0,
                'avg_progress' => round($projectStats->avg_progress ?? 0, 1),
                'total_budget' => $projectStats->total_budget ?? 0,
                'total_spent' => $projectStats->total_spent ?? 0,
                'budget_utilization' => $projectStats->total_budget > 0 
                    ? round(($projectStats->total_spent / $projectStats->total_budget) * 100, 1) 
                    : 0,
            ],
            'tasks' => [
                'total' => $taskStats->total_tasks ?? 0,
                'pending' => $taskStats->pending_tasks ?? 0,
                'in_progress' => $taskStats->in_progress_tasks ?? 0,
                'completed' => $taskStats->completed_tasks ?? 0,
                'testing' => $taskStats->testing_tasks ?? 0,
                'completion_rate' => $taskStats->total_tasks > 0 
                    ? round(($taskStats->completed_tasks / $taskStats->total_tasks) * 100, 1)
                    : 0,
                'estimated_hours' => $taskStats->total_estimated_hours ?? 0,
                'actual_hours' => $taskStats->total_actual_hours ?? 0,
                'efficiency' => $taskStats->total_estimated_hours > 0
                    ? round(($taskStats->total_estimated_hours / $taskStats->total_actual_hours) * 100, 1)
                    : 0,
            ],
            'my_tasks' => [
                'total' => $userTaskStats->my_total_tasks ?? 0,
                'pending' => $userTaskStats->my_pending_tasks ?? 0,
                'in_progress' => $userTaskStats->my_in_progress_tasks ?? 0,
                'completed' => $userTaskStats->my_completed_tasks ?? 0,
                'overdue' => $userTaskStats->my_overdue_tasks ?? 0,
            ],
            'team' => [
                'members' => $teamStats->team_members ?? 0,
                'active_members' => $teamStats->active_members ?? 0,
            ],
        ];
    }

    /**
     * Get recent activities efficiently.
     */
    private function getRecentActivities(array $accessibleProjectIds, string $userRole): array
    {
        // Get recent project updates
        $recentProjects = Project::whereIn('id', $accessibleProjectIds)
            ->where('updated_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get(['id', 'name', 'status', 'updated_at']);

        // Get recent task updates
        $recentTasks = Task::whereIn('project_id', $accessibleProjectIds)
            ->where('updated_at', '>=', Carbon::now()->subDays(7))
            ->with(['project:id,name', 'assignedTo:id,name'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get(['id', 'title', 'status', 'project_id', 'assigned_to', 'updated_at']);

        return [
            'recent_projects' => $recentProjects,
            'recent_tasks' => $recentTasks,
        ];
    }

    /**
     * Get user's tasks efficiently.
     */
    private function getUserTasks(int $userId, array $accessibleProjectIds): array
    {
        // Get user's active tasks
        $activeTasks = Task::where('assigned_to', $userId)
            ->whereIn('project_id', $accessibleProjectIds)
            ->whereIn('status', ['pending', 'in_progress'])
            ->with(['project:id,name'])
            ->orderBy('due_date', 'asc')
            ->limit(10)
            ->get(['id', 'title', 'status', 'priority', 'due_date', 'project_id']);

        // Get overdue tasks
        $overdueTasks = Task::where('assigned_to', $userId)
            ->whereIn('project_id', $accessibleProjectIds)
            ->where('due_date', '<', Carbon::now())
            ->where('status', '!=', 'completed')
            ->with(['project:id,name'])
            ->orderBy('due_date', 'asc')
            ->limit(5)
            ->get(['id', 'title', 'status', 'priority', 'due_date', 'project_id']);

        return [
            'active_tasks' => $activeTasks,
            'overdue_tasks' => $overdueTasks,
        ];
    }

    /**
     * Get project progress data for charts.
     */
    private function getProjectProgress(array $accessibleProjectIds, string $userRole): array
    {
        // Get project progress data
        $projectProgress = Project::whereIn('id', $accessibleProjectIds)
            ->where('status', 'active')
            ->orderBy('progress_percentage', 'desc')
            ->limit(10)
            ->get(['id', 'name', 'progress_percentage', 'status', 'end_date']);

        // Get monthly task completion trend
        $taskTrend = Task::whereIn('project_id', $accessibleProjectIds)
            ->where('completed_at', '>=', Carbon::now()->subMonths(6))
            ->selectRaw('
                DATE_FORMAT(completed_at, "%Y-%m") as month,
                COUNT(*) as completed_tasks
            ')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return [
            'project_progress' => $projectProgress,
            'task_trend' => $taskTrend,
        ];
    }

    /**
     * Get dashboard data via AJAX for real-time updates.
     */
    public function ajaxUpdate(Request $request)
    {
        $securityContext = SecurityContextService::getSecurityContext();
        
        if (!$securityContext) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $type = $request->get('type', 'statistics');
        $accessibleProjectIds = $securityContext['accessible_projects'] ?? [];

        switch ($type) {
            case 'statistics':
                $projectsQuery = Project::query();
                $tasksQuery = Task::query();
                
                if (!in_array($securityContext['role'], ['super_admin', 'project_manager'])) {
                    $projectsQuery->whereIn('id', $accessibleProjectIds);
                    $tasksQuery->whereIn('project_id', $accessibleProjectIds);
                }
                
                $statistics = $this->getDashboardStatistics($projectsQuery, $tasksQuery, $securityContext);
                return response()->json($statistics);

            case 'activities':
                $activities = $this->getRecentActivities($accessibleProjectIds, $securityContext['role']);
                return response()->json($activities);

            case 'tasks':
                $tasks = $this->getUserTasks($securityContext['user_id'], $accessibleProjectIds);
                return response()->json($tasks);

            default:
                return response()->json(['error' => 'Invalid type'], 400);
        }
    }
}
