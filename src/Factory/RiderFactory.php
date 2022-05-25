<?php

namespace App\Factory;

use App\Entity\Rider;
use App\Entity\User;

class RiderFactory
{
    public static function createFromUser(User $user): Rider
    {
        $rider = new Rider();
        $rider->setFirstName($user->getFirstName());
        $rider->setSurname($user->getSurname());
        $rider->setPhone($user->getPhone());
        $rider->setUser($user);

        return $rider;
    }
}