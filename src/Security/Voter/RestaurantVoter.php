<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class RestaurantVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['RESTAURANT_EDIT', 'RESTAURANT_VIEW', 'RESTAURANT_CREATE'])
            && $subject instanceof \App\Entity\Restaurant;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'RESTAURANT_EDIT':
                if($user === $subject->getOwner()){
                    return true;
                }
            case 'RESTAURANT_VIEW':
                if($user == $subject->getOwner()){
                    return true;
                }
            case 'RESTAURANT_CREATE':
                if($user == $subject->getOwner()){
                    return true;
                }
                
        }

        return false;
    }
}
