<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Algolia\SearchBundle\Entity\Aggregator;

/**
* @ORM\Entity
*/
class Algolia extends Aggregator{
    /**
     * Returns the entities class names that should be aggregated.
     *
     * @return string[]
     */
    public static function getEntities()
    {
        return [
            Incident::class,
            Comment::class,
            User::class,
        ];
    }
}