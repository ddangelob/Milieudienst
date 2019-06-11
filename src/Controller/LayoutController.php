<?php
/**
 * Created by PhpStorm.
 * User: dbrusorio
 * Date: 23-3-19
 * Time: 10:04
 */
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LayoutController extends AbstractController {
    public function navbar()
    {
        if($user = $this->getUser()){
            // User is signed in
            $incidents = count($user->getLockedIncidents());
            return $this->render('inc/navbar.html.twig', ['lockedIncidents' => $incidents]);
        }

        // User is not signed in
        return $this->render('inc/navbar.html.twig');
    }
}