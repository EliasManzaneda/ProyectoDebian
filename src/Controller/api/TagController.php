<?php


namespace App\Controller\api;

use App\Entity\Tag;


use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;

// use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;

class TagController extends FOSRestController
{
    /**
     * POST Route annotation.
     * @Post("/tags")
     */
    public function postTagAction(Request $request)
    {

        $entityManager = $this->getDoctrine()->getManager();



        $data = $request->request->get('name');

        $tag = new Tag();
        $tag->setName($request->get($data));
        // $this->articleRepository->save($tag);
        $entityManager->persist($tag);
        $entityManager->flush();
        // In case our POST was a success we need to return a 201 HTTP CREATED response
        return View::create($tag, Response::HTTP_CREATED);
    }


    /**
     * GET Route annotation.
     * @Get("/tags")
     */
    public function getTagAction()
    {

        $repository = $this->getDoctrine()->getRepository(Tag::class);
        /*
        // $repository = $this->getDoctrine()->getRepository(Tag::class);
        $repository = $this->getDoctrine()->getRepository(User::class);


        // $tags = $repository->findForAPI();
        // $tags = $repository->findForAPI();
        $users = $repository->findAll();
        */
        $tags = $repository->findAll();

        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $serializer = new Serializer(array($normalizer));

        $data = $serializer->normalize($tags, null, array('groups' => array('group1')));


        $response = new JsonResponse();
        $response->setData(array('tags' => $data));



        // return View::create(new JsonResponse(['tags' => $data]), Response::HTTP_OK , []);
        return View::create($data, Response::HTTP_OK , []);

        // return View::create($articles, Response::HTTP_OK);
    }
}