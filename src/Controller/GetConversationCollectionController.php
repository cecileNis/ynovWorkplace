<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GetConversationCollectionController extends AbstractController
{
    public function __invoke(): array
    {
        $user = $this->getUser();
        /** @var User $user */
        $ownerConversations = $user->getOwnerConversations()->getValues();
        $guestConversations = $user->getGuestConversations()->getValues();
        return array_merge($ownerConversations, $guestConversations);
    }
}
