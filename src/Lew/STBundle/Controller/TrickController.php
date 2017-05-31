<?php

namespace Lew\STBundle\Controller;

use Lew\STBundle\Entity\Image;
use Lew\STBundle\Entity\Post;
use Lew\STBundle\Entity\Trick;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Trick controller.
 *
 */
class TrickController extends Controller
{
    /**
     * Lists all trick entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $tricks = $em->getRepository('LewSTBundle:Trick')->findAll();

        $delete_forms = array_map(
            function ($element) {
                return $this->createDeleteForm($element);
            }
            , $tricks
        );


        return $this->render('LewSTBundle:Trick:index.html.twig', array(
            'tricks' => $tricks,
            'delete_forms' => $delete_forms
        ));
    }

    /**
     * Creates a new trick entity.
     *
     */
    public function newAction(Request $request)
    {
        $trick = new Trick();
        $form = $this->createForm('Lew\STBundle\Form\TrickType', $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if($trick->getImages() != null) {
                foreach ($trick->getImages() as $image) {
                    $image->setTrick($trick);
                }
            }
            if($trick->getVideos() != null){
                foreach ($trick->getVideos() as $video) {
                    $video->setTrick($trick);
                }
            }

        try{
            $em->persist($trick);
            $em->flush();
            $request->getSession()->getFlashBag()->add('info', 'Annonce bien enregistrée.');
        }catch (\Exception $e){
                $erreur = $e->getPrevious()->getPrevious()->errorInfo[2];

            $request->getSession()->getFlashBag()->add('notice', 'Erreur lors de l\'enregistrement : ' . $erreur);
            return $this->render('LewSTBundle:Trick:new.html.twig', array(
                'trick' => $trick,
                'form' => $form->createView(),
            ));
        }

            return $this->redirectToRoute('trick_index');
        }

        return $this->render('LewSTBundle:Trick:new.html.twig', array(
            'trick' => $trick,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a trick entity.
     *
     */
    public function showAction(Request $request, Trick $trick, $page)
    {
        $deleteForm = $this->createDeleteForm($trick);
        $post = new Post();
        $form = $this->createForm('Lew\STBundle\Form\PostType', $post);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $nbParPage = 10;

        $posts = $em->getRepository('LewSTBundle:Post')->findPostsTrick($trick->getSlug());
        $count = count($posts);
        $totalPage = ceil($count/$nbParPage);

        $posts = $em->getRepository('LewSTBundle:Post')->findPostsTrick($trick->getSlug(), $page, $nbParPage);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setDate(new \DateTime());
            $post->setTrick($trick);
            $user = $this->get('security.token_storage')->getToken()->getUser();
            $post->setUser($user);

            $em->persist($post);
            $em->flush();
            $request->getSession()->getFlashBag()->add('info', 'Le Commentaire a bien été ajouté.');

            return $this->redirectToRoute('trick_show', array('slug' => $trick->getSlug()));
        }

        return $this->render('LewSTBundle:Trick:show.html.twig', array(
            'trick' => $trick,
            'delete_form' => $deleteForm->createView(),
            'form' => $form->createView(),
            'posts' => $posts,
            'pagination' => $totalPage,
            'page' => $page
        ));
    }

    /**
     * Displays a form to edit an existing trick entity.
     *
     */
    public function editAction(Request $request, Trick $trick)
    {
        $deleteForm = $this->createDeleteForm($trick);
        $editForm = $this->createForm('Lew\STBundle\Form\TrickType', $trick);
        $editForm->handleRequest($request);



        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $images = $em->getRepository('LewSTBundle:Image')->findBy(array('trick' => $trick));

            foreach ($images as $image) {
                if(!in_array($image->getImageName(),$_POST['imageSup'])){
                    $em->remove($image);
                }
            }

            foreach ($trick->getImages() as $image) {
                $image->setTrick($trick);
            }

            if($trick->getVideos() != null){
                foreach ($trick->getVideos() as $video) {
                    $video->setTrick($trick);
                }
            }

            if(empty($_POST['videos'])) {
                $videos = $em->getRepository('LewSTBundle:Video')->findBy(array('trick' => $trick));
                foreach ($videos as $video) {
                    $em->remove($video);
                }
            }

            $em->persist($trick);
            $em->flush();
            $request->getSession()->getFlashBag()->add('info', 'Le Trick a bien été modifié.');

            return $this->redirectToRoute('trick_index');
        }
        return $this->render('LewSTBundle:Trick:edit.html.twig', array(
            'trick' => $trick,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a trick entity.
     *
     */
    public function deleteAction(Request $request, Trick $trick)
    {
        $form = $this->createDeleteForm($trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($trick);
            $em->flush();
            $request->getSession()->getFlashBag()->add('info', 'Le Trick a bien été supprimé.');
        }

        return $this->redirectToRoute('trick_index');
    }

    /**
     * Creates a form to delete a trick entity.
     *
     * @param Trick $trick The trick entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Trick $trick)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('trick_delete', array('slug' => $trick->getSlug())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    public function confirmationDeleteAction(Request $request, Trick $trick)
    {
        $form = $this->createDeleteForm($trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($trick);
            $em->flush();

            return $this->redirectToRoute('trick_index');
        }

        return $this->render('LewSTBundle:Trick:confirmation.html.twig', array(
            'trick' => $trick,
            'delete_form' => $form->createView(),
        ));
    }
}
