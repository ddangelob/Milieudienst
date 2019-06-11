<?php
/**
 * Created by PhpStorm.
 * User: dbrusorio
 * Date: 25-3-19
 * Time: 17:22
 */
namespace App\Form\Handler;

use App\Form\Model\IncidentFormModel as IncidentForm;

use Hostnet\Component\FormHandler\HandlerConfigInterface;
use Hostnet\Component\FormHandler\HandlerTypeInterface;

final class IncidentFormHandler implements HandlerTypeInterface
{

    public function __construct()
    {
    }
    public function configure(HandlerConfigInterface $config)
    {
        $config->setType(IncidentForm::class);

        $config->onSuccess(function (?Incident $incident) {
            // ...
        });
    }
}
