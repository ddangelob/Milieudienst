<?php
namespace App\Controller;

// Entities
use App\Entity\Incident;
use App\Entity\Comment;

// Form Handlers
use App\Form\Handler\IncidentFormHandler;
use App\Form\Handler\CommentFormHandler;
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
    /**
     * @Route("/incidents", name="incident_page")
     */
    public function incident_page(){
        $incident = $this->getDoctrine()->getRepository('App:Incident');
        $incidents = $incident->getRecent(50, 0);
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
        $user = $this->getUser();
        $incidents = $user->getLockedIncidents();
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
        $em = $this->getDoctrine()->getManager();
        $incident = $em->getRepository('App:Incident')->find($id);
        $user = $this->getUser();

        if($lockarg == 0){
            // Ticket needs to be locked and the user isn't the owner of the incident
            $status = $em->getRepository('App:Status')->find(3);
            $incident->setOwner($user);
            $incident->setStatus($status);
            $em->flush();
        }
        if($user === $incident->getOwner()){
            // User is owner of ticket
            if($lockarg == 1){
                // Ticket needs to be unlocked
                $status = $em->getRepository('App:Status')->find(1);
                $incident->removeOwner($user);
                $incident->setStatus($status);
                $em->flush();
            }
        }
        return $this->redirectToRoute('incident_show', ['id' => $id]);
    }


    /**
     * @Route("/incidents/show/{id}/change_status/{status}", name="incident_change_status")
     */
    public function incident_close($id, $status){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $incident = $em->getRepository('App:Incident')->find($id);
        if($status == 1){
            $statusObj = $em->getRepository('App:Status')->find($status);
            $incident->setStatus($statusObj);
            $em->flush();
        }
        if($user === $incident->getOwner()){
            $status = $em->getRepository('App:Status')->find($status);
            $incident->setStatus($status);
            $incident->removeOwner($user);
            $em->flush();
        }
        return $this->redirectToRoute('incident_show', ['id' => $id]);
    }


    /**
     * @Route("/incidents/show/{id}/edit", name="incident_edit")
     */
    public function incident_edit(Request $request,HandlerFactoryInterface $handler, $id)
    {
        $handler = $handler->create(IncidentFormHandler::class);
        $incident = $this->getDoctrine()->getRepository('App:Incident')->find($id);
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
        $comment = new Comment();
        $comment->setIncident($this->getDoctrine()->getRepository('App:Incident')->find($id));
        if($handler->handle($request, $comment)){
            return $this->redirectToRoute('incident_show', ['id' => $id]);
        }

        $incident = $this->getDoctrine()->getRepository('App:Incident')->find($id);
        $comments = $incident->getComment();
        return $this->render('incidents/show.html.twig', ['incident' => $incident, 'comments' => $comments, 'form' => $handler->getForm()->createView()]);
    }
}