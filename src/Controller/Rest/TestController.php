<?php
/**
 * Created by PhpStorm.
 * User: eliasmanzaneda
 * Date: 26/05/18
 * Time: 15:44
 */

namespace App\Controller\Rest;

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
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;

class TestController extends FOSRestController
{
    /**
     * POST Route annotation.
     * @Post("/tags")
     */
    public function postTagAction(Request $request){
        $entityManager = $this->getDoctrine()->getManager();
        $data = $request->request->get('name');
        $tag = new Tag();
        $tag->setName($request->get($data));
        $entityManager->persist($tag);
        $entityManager->flush();
        return View::create($tag, Response::HTTP_CREATED);
    }


    /**
     * GET Route annotation.
     * @Get("/tags")
     */
    public function getTagAction(){
        $repository = $this->getDoctrine()->getRepository(Tag::class);
        $tags = $repository->findAll();
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $serializer = new Serializer(array($normalizer));
        $data = $serializer->normalize($tags, null, array('groups' => array('group1')));
        $response = new JsonResponse();
        $response->setData(array('tags' => $data));

        return View::create($data, Response::HTTP_OK , []);



    }
}