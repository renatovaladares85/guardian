<?php

namespace App\Helpers;

use App\Services\SecurityContextService;
use Illuminate\Support\Facades\Auth;

class SecurityHelper
{
    /**
     * Check if current user has permission.
     */
    public static function can(string $permission): bool
    {
        return SecurityContextService::hasPermission($permission);
    }

    /**
     * Check if current user can access project.
     */
    public static function canAccessProject(int $projectId): bool
    {
        return SecurityContextService::canAccessProject($projectId);
    }

    /**
     * Get current user role.
     */
    public static function getUserRole(): ?string
    {
        return SecurityContextService::getUserRole();
    }

    /**
     * Check if user is admin.
     */
    public static function isAdmin(): bool
    {
        $role = self::getUserRole();
        return in_array($role, ['super_admin', 'project_manager']);
    }

    /**
     * Check if user is team lead or higher.
     */
    public static function isTeamLead(): bool
    {
        $role = self::getUserRole();
        return in_array($role, ['super_admin', 'project_manager', 'team_lead']);
    }

    /**
     * Get accessible project IDs for queries.
     */
    public static function getAccessibleProjectIds(): array
    {
        return SecurityContextService::getAccessibleProjectIds();
    }

    /**
     * Apply security filter to query.
     */
    public static function filterProjectQuery($query)
    {
        if (!self::isAdmin()) {
            $accessibleIds = self::getAccessibleProjectIds();
            $query->whereIn('id', $accessibleIds);
        }
        return $query;
    }

    /**
     * Apply security filter to task query.
     */
    public static function filterTaskQuery($query)
    {
        if (!self::isAdmin()) {
            $accessibleIds = self::getAccessibleProjectIds();
            $query->whereIn('project_id', $accessibleIds);
        }
        return $query;
    }

    /**
     * Get current user context.
     */
    public static function getContext(): ?array
    {
        return SecurityContextService::getSecurityContext();
    }
}
