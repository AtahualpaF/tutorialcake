<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\User;
use Authorization\IdentityInterface;

/**
 * User policy
 */
class UserPolicy
{
    /**
     * Check if $user can add User
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\User $resource
     * @return bool
     */
    public function canAdd(IdentityInterface $user, User $resource)
    {
        // All logged in users can create articles.
        return true;
    }

    /**
     * Check if $user can edit User
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\User $resource
     * @return bool
     */
    public function canEdit(IdentityInterface $user, User $resource)
    {
        // logged in users can edit their own articles.
        return $this->isAuthor($user, $resource);
    }

    /**
     * Check if $user can delete User
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\User $resource
     * @return bool
     */
    public function canDelete(IdentityInterface $user, User $resource)
    {
        // logged in users can edit their own articles.
        //return $this->isAuthor($user, $resource);
        return $this->isAdmin2($user, $resource);
        //return false;
    }

    /**
     * Check if $user can view User
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\User $resource
     * @return bool
     */
    public function canView(IdentityInterface $user, User $resource)
    {
        // logged in users can edit their own articles.
        //return $this->isAuthor($user, $resource);
        // All logged in users can create articles.
        return true;
    }

    protected function isAuthor(IdentityInterface $user, User $resource)
    {
        return (($resource->id === $user->getIdentifier()) or $user->administrador);
    }

    protected function isAdmin(IdentityInterface $user)
    {
        return $user->administrador;
        
    }

    protected function isAdmin2(IdentityInterface $user)
    {
        if($user->administrador){
        return true;
        }
        else return false;
    }
}
