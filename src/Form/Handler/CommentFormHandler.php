<?php
/**
 * Created by PhpStorm.
 * User: dbrusorio
 * Date: 25-3-19
 * Time: 17:22
 */
namespace App\Form\Handler;

use App\Entity\Comment;
use App\Form\CommentFormType;

use Doctrine\ORM\EntityManagerInterface;
use Hostnet\Component\FormHandler\HandlerConfigInterface;
use Hostnet\Component\FormHandler\HandlerTypeInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class CommentFormHandler implements HandlerTypeInterface
{

    public function __construct(
        EntityManagerInterface $em,
        TokenStorageInterface  $token_storage
    )
    {
        $this->em            = $em;
        $this->token_storage = $token_storage;
    }
    public function configure(HandlerConfigInterface $config)
    {
        $config->setType(CommentFormType::class);

        $config->onSuccess(function (?Comment $comment) {
            $date = new \DateTime('@'.strtotime('now'));
            $comment->setCreatedOn($date);
            $comment->setOwner($this->token_storage->getToken()->getUser());
            $this->em->persist($comment);
            $this->em->flush();
            return true;
        });
    }
}
