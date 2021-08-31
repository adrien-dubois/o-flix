<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{

    private $security;

    public function __construct(Security $security){
        $this->security = $security;
    }


    const VIEW = 'view';
    const EDIT = 'edit';

    protected function supports(string $attribute, $subject): bool
    {

        return in_array($attribute, [self::VIEW, self::EDIT])
            && $subject instanceof User;
    }


    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        
        
        $user = $token->getUser();
        // if the user is not an instance of UserInterface, that means he's not logged, so he doesn't have any rights
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var User $post */
        $post = $subject;

        switch($attribute) {
            case self::VIEW:
                return $this->canView($post, $user);
            case self::EDIT:
                if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
                    return true;
                }
                // An admin has rights to edit his own 
                $roles = $subject->getRoles();
                if(count($roles) ==1 && $roles[0] == 'ROLE_USER'){
                    return true;
                }

                if($user == $subject){
                    return true;
                }
                // An admin can edit a simple user

        }
        return false;
    }

    private function canView($post, $user)
    {
        // The user can view himself
        if ($this->canEdit($post, $user)) {
            return true;
        }

    }

        
    private function canEdit($post, $user)
    {
        // The user can edit 
        return $user === $post;
    }
}

