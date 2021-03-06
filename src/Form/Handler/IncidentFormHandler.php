<?php
/**
 * Created by PhpStorm.
 * User: dbrusorio
 * Date: 25-3-19
 * Time: 17:22
 */
namespace App\Form\Handler;

use App\Entity\Incident;
use App\Form\IncidentFormType;

use Doctrine\ORM\EntityManagerInterface;
use Hostnet\Component\FormHandler\HandlerConfigInterface;
use Hostnet\Component\FormHandler\HandlerTypeInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class IncidentFormHandler implements HandlerTypeInterface
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
        $config->setType(IncidentFormType::class);

        $config->onSuccess(function (?Incident $incident) {
            $date = new \DateTime('@'.strtotime('now'));
            $incident->setCreatedOn($date);
            $incident->setCreatedBy($this->token_storage->getToken()->getUser());
            $this->em->persist($incident);
            $this->em->flush();
            return true;
        });
    }
}
