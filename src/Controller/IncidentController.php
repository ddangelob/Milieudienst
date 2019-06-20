<?php
namespace App\Controller;

// Entities
use App\Entity\Incident;
use App\Entity\Comment;

// Form Handlers
use App\Form\Handler\IncidentFormHandler;
use App\Form\Handler\CommentFormHandler;
use App\Repository\IncidentRepository;
use App\Repository\StatusRepository;
use Hostnet\Component\FormHandler\HandlerFactoryInterface;

// HTTP and routing
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

// Controller related
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;



/**
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
class IncidentController extends AbstractController{
    private $incident_repository;
    private $status_repository;
    public function __construct(IncidentRepository $incident_repository, StatusRepository $status_repository)
    {
        $this->incident_repository = $incident_repository;
        $this->status_repository = $status_repository;
    }

    /**
     * @Route("/incidents", name="incident_page")
     */
    public function incident_page(){
        $incidents = $this->incident_repository->getRecent(50, 0);
        return $this->render('portal/incident.html.twig', ['incidents' => $incidents]);
    }

    /**
     * @Route("/incidents/add", name="incident_add", methods={"GET","POST"})
     */
    public function incident_add(Request $request, HandlerFactoryInterface $handler)
    {
        $handler = $handler->create(IncidentFormHandler::class);
        $incident = new Incident();
        if($handler->handle($request, $incident)){
            return $this->redirectToRoute('incident_show', ['id' => $incident->getId()]);
        }

        return $this->render('incidents/new.html.twig', ['form' => $handler->getForm()->createView()]);
    }


    /**
     * @Route("/incidents/locked", name="locked_incident_page")
     */
    public function locked_incident_page(){
        $incidents = $this->getUser()->getLockedIncidents();
        return $this->render('portal/incident.html.twig', ['incidents' => $incidents]);
    }


    /**
     * @Route("/incidents/{page}", name="incident_pagination", requirements={"page"="\d+"})
     */
    public function incident_pagination($page){
        $incident = $this->getDoctrine()->getRepository('App:Incident');
        $incidents = $incident->getRecent(50, $page * 50);
        return $this->render('portal/incident.html.twig', ['incidents' => $incidents]);
    }


    /**
     * @Route("/incidents/show/{id}/change_lock/{lockarg}", name="incident_change_lock", requirements={"page"="\d+"})
     */
    public function incident_change_lock($id, $lockarg){
        // Get user and incident.
        $incident = $this->incident_repository->find($id);
        if($lockarg == 0){
            // Ticket needs to be locked and the user isn't the owner of the incident
            $incident->setOwner($this->getUser());
            $incident->setStatus($this->status_repository->find(3));
            $this->incident_repository->save($incident);
        }
        if($this->getUser() === $incident->getOwner()){
            // User is owner of ticket
            if($lockarg == 1){
                // Ticket needs to be unlocked
                $incident->removeOwner($this->getUser());
                $incident->setStatus($this->status_repository->find(1));
                $this->incident_repository->save($incident);
            }
        }
        return $this->redirectToRoute('incident_show', ['id' => $id]);
    }


    /**
     * @Route("/incidents/show/{id}/change_status/{status}", name="incident_change_status")
     */
    public function incident_close($id, $status){
        $incident = $this->incident_repository->find($id);
        if($status == 1){
            $statusObj = $this->status_repository->find($status);
            $incident->setStatus($statusObj);
            $this->incident_repository->save($incident);
        }
        if($this->getUser() === $incident->getOwner()){
            $statusObj = $this->status_repository->find($status);
            $incident->setStatus($statusObj);
            $incident->removeOwner($this->getUser());
            $this->incident_repository->save($incident);
        }
        return $this->redirectToRoute('incident_show', ['id' => $id]);
    }


    /**
     * @Route("/incidents/show/{id}/edit", name="incident_edit")
     */
    public function incident_edit(Request $request,HandlerFactoryInterface $handler, $id)
    {
        $handler = $handler->create(IncidentFormHandler::class);
        $incident = $this->incident_repository->find($id);
        if($handler->handle($request, $incident)){
            return $this->redirectToRoute('incident_show', ['id' => $incident->getId()]);
        }

        return $this->render('incidents/update.html.twig', ['form' => $handler->getForm()->createView()]);
    }


    /**
     * @Route("/incidents/show/{id}", name="incident_show")
     */
    public function incident_show(Request $request, HandlerFactoryInterface $handler, $id)
    {
        $handler = $handler->create(CommentFormHandler::class);
        $incident = $this->incident_repository->find($id);
        $comment = new Comment();
        $comment->setIncident($incident);
        if($handler->handle($request, $comment)){
            return $this->redirectToRoute('incident_show', ['id' => $id]);
        }
        $comments = $incident->getComment();
        return $this->render('incidents/show.html.twig', ['incident' => $incident, 'comments' => $comments, 'form' => $handler->getForm()->createView()]);
    }
}