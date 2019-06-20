<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class Search
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function doSearch($models, $fields, $searchQuery, $args){
        $foundItems = [];
        if(is_array($fields) && is_array($args) && is_array($models)){
            foreach($models as $model){
                // Turn array with fields in to string separated with a comma
                $implodedFields = implode(",", $fields[$model]);
                $sql = "SELECT *, MATCH (".$implodedFields.") AGAINST (:Search) as score FROM ".$model." WHERE MATCH (".$implodedFields.") AGAINST (:Search) > 0 ORDER BY score DESC;";
                // Prepare the SQL query
                $statement = $this->em->getConnection()->prepare($sql);
                // Add quotes to string and bind the search parameter to the SQL query
                $searchQuery = '"'.$searchQuery.'"';
                $statement->bindParam(':Search', $searchQuery);
                // Execute the SQL query
                $statement->execute();
                // Fetch all the results
                $res = $statement->fetchAll();
                if(!empty($res)){
                    $foundItems[$model] = [];
                    foreach($res as $resEntity){
                        $entity = $this->em->getRepository('App:'. ucfirst($model))->find($resEntity['id']);
                        array_push($foundItems[$model], $entity);
                    }
                }
            }
        }
        return $foundItems;
    }
}