<?php
namespace App\Controller;

use App\Form\Model\CommentFormModel;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


/**
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
class CommentController extends AbstractController
{
    /**
     * @Route("/incidents/show/{id}/{comment_id}/edit", name="comment_edit")
     */
    public function comment_edit(Request $request, $id, $comment_id)
    {
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository('App:Comment')->find($comment_id);
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if ($comment->getOwner() == $user) {
            $commentForm = new CommentFormModel();
            $form = $this->createFormBuilder($commentForm)
                ->add('title', TextType::class, array(
                    'attr' => array('class' => 'form-control'),
                    'data' => $comment->getTitle()))
                ->add('message', TextareaType::class, array(
                    'attr' => array('class' => 'form-control'),
                    'data' => $comment->getMessage()))
                ->add('save', SubmitType::class, array(
                    'label' => 'Update comment',
                    'attr' => array('class' => 'btn btn-primary mt-3')))
                ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $formData = $form->getData();
                $comment->setTitle($formData->getTitle());
                $comment->setMessage($formData->getMessage());
                $em->flush();
                return $this->redirectToRoute('incident_show', ['id' => $id]);
            }
            return $this->render('incidents/update.html.twig', ['form' => $form->createView()]);
        }
        return $this->redirectToRoute('incident_show', ['id' => $id]);
    }

    /**
     * @Route("/incidents/show/{id}/{comment_id}", name="comment_remove", methods="DELETE")
     */
    public function comment_remove(Request $request, $id, $comment_id)
    {
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository('App:Comment')->find($comment_id);
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if ($comment->getOwner() == $user || $this->isGranted('ROLE_MGMT')) {
            $em->remove($comment);
            $em->flush();
        }
        $response = new Response();
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');
        return $response;
    }
}