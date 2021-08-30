<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;


class UserVoter extends Voter
{

    const VIEW = 'view';
    const EDIT = 'edit';

    protected function supports(string $attribute, $subject): bool
    {

        return in_array($attribute, [self::VIEW, self::EDIT])
            && $subject instanceof User;
    }


    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        dd('coucou');
        $user = $token->getUser();
        // if the user is not an instance of UserInterface, that means he's not logged, so he doesn't have any rights
        if (!$user instanceof User) {
            return false;
        }

        /** @var User $post */
        $post = $subject;

        switch($attribute) {
            case self::VIEW:
                return $this->canView($post, $user);
            case self::EDIT:
                return $this->canEdit($post, $user);
        }
        throw new \LogicException('This code sould not be reached!');
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

