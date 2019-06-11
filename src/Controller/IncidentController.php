<?php
namespace App\Controller;

use App\Entity\Comment;
use App\Form\IncidentFormType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use App\Entity\Incident;
use App\Form\Model\IncidentFormModel as IncidentForm;
use App\Form\Model\CommentFormModel as CommentForm;



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
    public function incident_add(Request $request)
    {
        $incident = new Incident();
        $form = $this->createForm(IncidentFormType::class, $incident);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine();

            $formData = $form->getData();
            $incident->setTitle($formData->getTitle());
            $incident->setDescription($formData->getDescription());
            $incident->setPriority($formData->getPriority());

            $date = new \DateTime('@'.strtotime('now'));
            $incident->setCreatedOn($date);
            $incident->setCreatedBy($this->get('security.token_storage')->getToken()->getUser());
            $incident->setCategory($formData->getCategory());
            $incident->setStatus($formData->getStatus());
            $incident->setLocation($formData->getLocation());

            if($formData->getStatus()->getId() === 3){
                $incident->setOwner($this->get('security.token_storage')->getToken()->getUser());
            }


            $em->getManager()->persist($incident);
            $em->getManager()->flush();

            return $this->redirectToRoute('incident_show', ['id' => $incident->getId()]);
        }

        return $this->render('incidents/new.html.twig', ['form' => $form->createView()]);
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
    public function incident_edit(Request $request, $id)
    {
        // Get all the data from the current Incident
        $incident = $this->getDoctrine()->getRepository('App:Incident')->find($id);
        $form = $this->createForm(IncidentFormType::class, $incident);

        // Let form handle the request
        $form->handleRequest($request);

        // If the form is valid and submitted
        if($form->isSubmitted() && $form->isValid()) {
            /** @var IncidentForm $editModel */
            // Get the new data from the form
            $editModel = $form->getData();
            $incident->setCategory($form->getData()->getCategory());
            $incident->setStatus($form->getData()->getStatus());
            $incident->setLocation($form->getData()->getLocation());

            // Set the new title, description and priority
            $incident->setTitle($editModel->getTitle());
            $incident->setDescription($editModel->getDescription());
            $incident->setPriority($editModel->getPriority());

            // Save
            $this->getDoctrine()->getManager()->flush();

            // Redirect to the incident
            return $this->redirectToRoute('incident_show', ['id' => $id]);
        }

        return $this->render('incidents/update.html.twig', ['form' => $form->createView()]);
    }


    /**
     * @Route("/incidents/show/{id}", name="incident_show")
     */
    public function incident_show(Request $request, $id)
    {
        $incident = $this->getDoctrine()->getRepository('App:Incident')->find($id);
        $comments = $incident->getComment();
        $commentForm = new CommentForm();
        $form = $this->createFormBuilder($commentForm)
            ->add('title', TextType::class, array(
                'attr' => array('class' => 'form-control'),
                'data_class' => IncidentForm::class))
            ->add('message', TextareaType::class, array(
                'attr' => array('class' => 'form-control'),
                'data_class' => IncidentForm::class))
            ->add('save', SubmitType::class, array(
                'label' => 'Create comment',
                'attr' => array('class' => 'btn btn-primary mt-3')))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            /** @var IncidentForm $userModel */
            $em = $this->getDoctrine()->getManager();

            $formData = $form->getData();

            $comment = new Comment();
            $comment->setIncident($em->getRepository('App:Incident')->find($id));
            $date = new \DateTime('@'.strtotime('now'));
            $comment->setCreatedOn($date);
            $comment->setTitle($formData->getTitle());
            $comment->setMessage($formData->getMessage());
            $comment->setOwner($this->get('security.token_storage')->getToken()->getUser());

            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('incident_show', ['id' => $incident->getId()]);
        }
        return $this->render('incidents/show.html.twig', ['incident' => $incident, 'comments' => $comments, 'form' => $form->createView()]);
    }
}