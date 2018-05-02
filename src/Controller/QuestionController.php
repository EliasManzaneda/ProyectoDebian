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
use function Sodium\add;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Form\QuestionType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

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
        $repository = $this->getDoctrine()->getRepository(Question::class);
        $questions = $repository->findForHomepage();

        // $newQuestions = $questions;
        $newQuestions= array_slice($questions, -3);

        return $this->render('homepage.html.twig', [
            'questions' => $questions,
            'newQuestions' => $newQuestions
        ]);
    }


    /**
     * @Route("/myquestions", name="list_questions")
     */
    public function myQuestions()
    {
        if (!$this->container->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_homepage');
        }

        $repository = $this->getDoctrine()->getRepository(Question::class);

        $user = $this->getUser();

        /*
        $questions = $repository->findBy(
            ['user' => $user],
            ['creationDate' => 'ASC'],
            ['title' => 'ASC']
        );
        */
        $questions = $repository-> findForUserProfile($user);

        return $this->render('question/listMyQuestions.html.twig', [
            'questions' => $questions
        ]);
    }


    /**
     * @Route("/searchedquestions", name="searched_questions")
     */
    public function searchedQuestions()
    {
        $repository = $this->getDoctrine()->getRepository(Question::class);

        $user = $this->getUser();

        /*
        $questions = $repository->findBy(
            ['user' => $user],
            ['creationDate' => 'ASC'],
            ['title' => 'ASC']
        );
        */
        $questions = $repository-> findForUserProfile($user);

        return $this->render('question/listMyQuestions.html.twig', [
            'questions' => $questions
        ]);
    }


    /**
     * @Route("/searchquestion", name="search_question")
     */
    public function searchQuestion(Request $request){

        $request = $this->get('request_stack')->getMasterRequest();

        $defaultData = array('message' => 'Type your message here');
        /*
        $form = $this->createFormBuilder('form', 'searchbarform', $defaultData)
            ->add('searchbar', TextType::class)
            ->add('send', SubmitType::class)
            ->getForm();
        */
        $form = $this->createFormBuilder($defaultData)
            ->add('searchbar', TextType::class)
            ->add('send', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

                $data = $form->getData();

                $searchedquestion = $data["searchbar"];

                $repository = $this->getDoctrine()->getRepository(Question::class);


                if(empty($searchedquestion)){
                    $searchedquestion = "*";
                }




                return $this->render('question/listSearchedQuestions.html.twig', array(
                    'form' => $form->createView(),
                ));


            // data is an array with "name", "email", and "message" keys




             // return $this->redirectToRoute('searched_questions');

        }

        return $this->render('question/searchbar.html.twig', array(
            'form' => $form->createView(),
        ));
    }




    /**
     * @Route("/askquestion", name="new_question")
     */
    public function newQuestion(Request $request){


        if (!$this->container->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_homepage');
        }


        $question = new Question();

        $form = $this->createForm(QuestionType::class, $question);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // $question = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();

            $user = $this->getUser();

            $question->setUser($user);
            $question->setResolved(false);
            $today = new \DateTime('now', (new \DateTimeZone('Europe/Madrid')));
            $question->setCreationDate($today);



            $entityManager->persist($question);
            $entityManager->flush();

            return $this->redirectToRoute('app_homepage');
        }


        return $this->render('question/askQuestion.html.twig', array(
            'form' => $form->createView(),
        ));

    }
}