<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use AppBundle\Form\PostType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/api")
 */
class PostController extends Controller
{
    /**
     * @Route("/posts")
     * @Method("GET")
     */
    public function getAllAction()
    {
        $posts = $this->getDoctrine()->getRepository(Post::class)->findAll();

        $serializer = $this->get('jms_serializer');

        $posts = $serializer->serialize($posts, 'json');

        $response = new Response($posts);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/posts")
     * @Method("POST")
     */
    public function postAction(Request $request)
    {
        $post = new Post();

        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        $serializer = $this->get('jms_serializer');

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $post->setDate(new \DateTime());
            $em->persist($post);
            $em->flush();

            $response = new Response($serializer->serialize($post, 'json'));
            $response->headers->set('Content-Type', 'application/json');

            return $response;

        }

        $response = new Response($serializer->serialize($form->getErrors(), 'json'));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}