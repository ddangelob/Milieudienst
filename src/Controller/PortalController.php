<?php
namespace App\Controller;

use App\Service\Search;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


/**
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
class PortalController extends AbstractController{
    /**
     * @Route("/", name="home_page")
     */
    public function index(){
        $incident = $this->getDoctrine()->getRepository('App:Incident');
        $incidents = $incident->getRecent(5, 0);
        $incidentStats = $incident->getStatistics();
        $user = $this->getUser()->getRoles();
        return $this->render('portal/index.html.twig', ['incidents' => $incidents, 'user' => $user, 'stats' => $incidentStats]);
    }
    /**
     * @Route("/search", name="search_page")
     */
    public function search(Request $request, Search $search){
        $searchQuery = $request->query->get('search');
        $searchParam = substr($searchQuery, 0, 2);
        $searchString = substr($searchQuery, 2);
        if($searchParam == 'i '){
            $results = $search->doSearch(['incident'], ['incident' => array('title', 'description')] ,$searchString, []);
            return $this->render('search/search.html.twig', ['searchResult' => $results, 'searchQuery' => $searchString]);
        }
        if($searchParam == 'c '){
            $results = $search->doSearch(['comment'], ['comment' => array('title')] ,$searchString, []);
            return $this->render('search/search.html.twig', ['searchResult' => $results, 'searchQuery' => $searchString]);
        }
        $results = $search->doSearch(['incident', 'comment'], ['incident' => array('title', 'description'), 'comment' => array('title')] ,$searchQuery, []);
        return $this->render('search/search.html.twig', ['searchResult' => $results, 'searchParam' => $searchParam, 'searchQuery' => $searchQuery]);
    }
}