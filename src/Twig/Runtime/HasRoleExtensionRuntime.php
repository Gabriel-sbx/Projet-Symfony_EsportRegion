<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

class HasRoleExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private RoleHierarchyInterface $roleHierarchy
        ) 
    {
        // Inject dependencies if needed
    }

    public function hasRole(UserInterface $user, string $role)
    {
        return in_array($role, $this->roleHierarchy->getReachableRoleNames($user->getRoles()));

    }
}
