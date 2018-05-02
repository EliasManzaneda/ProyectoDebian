<?php
/**
 * Created by PhpStorm.
 * User: eliasmanzaneda
 * Date: 30/04/18
 * Time: 17:25
 */

namespace App\Controller;


use App\Entity\User;
use App\Entity\Question;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;




class QuestionController extends AbstractController
{
    /**
     * @Route("/news/{slug}", name="article_show")
     */
    public function show($slug)
    {
        $comments = [
            'I ate a normal rock once. It did NOT taste like bacon!',
            'Woohoo! I\'m going on an all-asteroid diet!',
            'I like bacon too! Buy some from my site! bakinsomebacon.com',
        ];
        return $this->render('question/show.html.twig', [
            'title' => ucwords(str_replace('-', ' ', $slug)),
            'comments' => $comments,
        ]);
    }


    /**
     * @Route("/locked", name="locked")
     */
    public function testguard()
    {

        return $this->render('security/testguard.html.twig', [

        ]);
    }


    /**
     * @Route("/", name="app_homepage")
     */
    public function homepage()
    {

        return $this->render('homepage.html.twig', [

        ]);
    }

    /**
     * @Route("/ask", name="new_question")
     */
    public function newQuestion(Request $request){
        if (!$this->container->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_homepage');
        }

        // creates a task and gives it some dummy data for this example
        $question = new Question();
        $form = $this->createFormBuilder($question)
            ->add('title', TextType::class)
            ->add('text', TextType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $question = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();

            $user = $this->getUser();

            $question->setUser($user);
            $question->setResolved(false);

            $entityManager->persist($question);
            $entityManager->flush();

            return $this->redirectToRoute('app_homepage');
        }


        return $this->render('question/askQuestion.html.twig', array(
            'form' => $form->createView(),
        ));

    }
}