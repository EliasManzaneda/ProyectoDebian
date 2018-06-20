<?php
/**
 * Created by PhpStorm.
 * User: eliasmanzaneda
 * Date: 30/04/18
 * Time: 17:25
 */

namespace App\Controller;


use App\Entity\QuestionScore;
use App\Entity\User;
use App\Entity\Question;
use App\Entity\Answer;
use Exception;
use Psr\Log\LoggerInterface;
use function Sodium\add;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Form\QuestionType;
use App\Form\AnswerType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\HttpFoundation\RedirectResponse;

class QuestionController extends AbstractController
{



    /**
     * @Route("/question/{questionid}", name="article_show")
     */
    public function show(Request $request, $questionid)
    {
        /*
        $comments = [
            'I ate a normal rock once. It did NOT taste like bacon!',
            'Woohoo! I\'m going on an all-asteroid diet!',
            'I like bacon too! Buy some from my site! bakinsomebacon.com',
        ];
        return $this->render('question/show.html.twig', [
            'title' => ucwords(str_replace('-', ' ', $slug)),
            'comments' => $comments,
        ]);
        */



        /*
        $repository = $this->getDoctrine()->getRepository(Question::class);
        $question = $repository->findOneBy(array('id' => $questionid));

        $questions = $repository->findForHomepage();
        $newQuestions = array_slice($questions, -3);

        return $this->render('question/questioning.html.twig', [
            'question' => $question,
            'newQuestions' => $newQuestions,
        ]);
        */
        if (!$this->container->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            // return $this->redirectToRoute('app_homepage');
        }



        $repository = $this->getDoctrine()->getRepository(Question::class);
        $questions = $repository->findForHomepage();

        $questionsResolved = $repository->findForResolved();
        $newQuestions = array_slice($questionsResolved, -5);

        $originalQuestion = $repository->findOneBy(array('id' => $questionid));


        $repository = $this->getDoctrine()->getRepository(Answer::class);
        $otherAnswers = $repository->findBy(array('question' => $questionid));


        if($originalQuestion->getResolved() == true){



        }


        $answer = new Answer();

        $form = $this->createForm(AnswerType::class, $answer);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // $question = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();

            $user = $this->getUser();

            if (strlen($answer->getText()) >= 1024) {
                $modifyAnswer = $answer->getQuestion();
                $maxLength = 1024;
                $modifiedAnswer = substr($modifyAnswer, 0, $maxLength);
                $answer->setText($modifiedAnswer);
            }

            $answer->setUser($user);
            $answer->setQuestion($originalQuestion);

            $today = new \DateTime('now', (new \DateTimeZone('Europe/Madrid')));
            $answer->setCreationDate($today);
            $answer->setPoints(0);



            $entityManager->persist($answer);
            $entityManager->flush();

            /*
            return $this->redirectToRoute('app_homepage');
            */


            $repository = $this->getDoctrine()->getRepository(Answer::class);
            $otherAnswers = $repository->findBy(array('question' => $questionid));

            return $this->render('question/questioning.html.twig', [
                'form' => $form->createView(),
                'question' => $originalQuestion,
                'newQuestions' => $newQuestions,
                'otherAnswers' => $otherAnswers,
                "answering" => "YES."
            ]);
        }

        return $this->render('question/questioning.html.twig', array(
            'form' => $form->createView(),
            'question' => $originalQuestion,
            'newQuestions' => $newQuestions,
            'otherAnswers' => $otherAnswers,
            "answering" => "NO."
        ));
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
    public function homepage(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Question::class);
        $questions = $repository->findForHomepage();

        // $newQuestions = $questions;
        // $newQuestions = array_slice($questions, -3);
        $questionsResolved = $repository->findForResolved();
        $newQuestions = array_slice($questionsResolved, -5);

        // SEARCHBAR
        $data = [
            "searchbar" => "default"
        ];

        $user = $this->getUser();
        $username = null;
        $userrole = null;
        if ($user != null) {
            $username = $user->getUsername();
            $userrole = $user->getRoles();
            /*
            if($userrole->contains("admin")){
                $userrole = "admin";
            }elseif ($userrole->contains("user")){
                $userrole = "user";
            }else{
                $userrole = "none";
            }
            */
        }


        $defaultData = array('message' => 'Type your message here');

        $form = $this->createFormBuilder($defaultData)
            ->add('searchtext', TextType::class)
            ->getForm();

        $form->handleRequest($request);


        // SEARCHBAR
        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();


            $searchedquestion = $data["searchtext"];

            $repository = $this->getDoctrine()->getRepository(Question::class);


            if (empty($searchedquestion)) {
                $searchedquestion = "%";
            }

            $questions = $repository->findForSearchbar($searchedquestion);


            if ($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                return $this->render('adminhomepage.html.twig', [
                    'questions' => $questions,
                    'newQuestions' => $newQuestions,
                    'form' => $form->createView(),
                    'searched' => $data['searchbar'],
                    'searching' => false
                ]);
            }else{
                return $this->render('homepage.html.twig', [
                    'questions' => $questions,
                    'newQuestions' => $newQuestions,
                    'form' => $form->createView(),
                    'searched' => $data['searchtext'],
                    'searching' => false
                ]);
            }





        }


        if ($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return $this->render('adminhomepage.html.twig', [
                'questions' => $questions,
                'newQuestions' => $newQuestions,
                'form' => $form->createView(),
                'searched' => $data['searchbar'],
                'searching' => false
            ]);
        }else{
            return $this->render('homepage.html.twig', [
                'questions' => $questions,
                'newQuestions' => $newQuestions,
                'form' => $form->createView(),
                'searched' => $data['searchbar'],
                'searching' => false
            ]);
        }





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

        $data = [
            "searchbar" => "default"
        ];

        // $request = $this->get('request_stack')->getMasterRequest();

        $defaultData = array('message' => 'Type your message here');

        $form = $this->createFormBuilder($defaultData)
            ->add('searchtext', TextType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

                $data = $form->getData();


                $searchedquestion = $data["searchtext"];

                $repository = $this->getDoctrine()->getRepository(Question::class);


                if(empty($searchedquestion)){
                    $searchedquestion = "*";
                }

                $questions = $repository->findForSearchbar($searchedquestion);


                // return $this->render('question/listSearchedQuestions.html.twig', array(

            return $this->render('homepage.html.twig', array(
                    'form' => $form->createView(),
                    'searched' => $data['searchtext'],
                    'questions' => $questions,
                    'searching' => true
                ));

            /*
            return $this->redirectToRoute('app_homepage', array(
                'form' => $form->createView(),
                'searched' => $data['searchbar'],
                'searching' => false
            ));
            */

        }





        // TO-DO: IF THE PAGE IS REFRESHED, SOMETHINGS NEEDS TO BE DONE TO SHOW AN ACTUAL PAGE INSTEAD OF THE SEARCHBAR HTML TWIG
        return $this->render('question/searchbar.html.twig', array(
            'form' => $form->createView(),
            'searched' => $data['searchbar'],
            'searching' => false
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
            $question->setPoints(0);



            $entityManager->persist($question);
            $entityManager->flush();

            return $this->redirectToRoute('app_homepage');
        }


        return $this->render('question/askQuestion.html.twig', array(
            'form' => $form->createView(),
        ));

    }

    /**
     * @Route("/admin/delete/question", name="admin_delete_question")
     */
    public function adminDeleteQuestion(Request $request){




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


    /**
     * @Route("/question/{slug}/score", name="question_score", methods={"POST"})
     */
    public function toggleArticleHeart($slug, LoggerInterface $logger)
    {
        // TODO - actually heart/unheart the article!
        $logger->info('Article is being hearted!');
        return new JsonResponse(['hearts' => rand(5, 100)]);
    }

    /**
     * Increases a question score
     *
     * @Route("/question/{questionid}/score/increase", name="question_score_increase", methods={"POST"})
     */
    public function increaseQuestionScore($questionid)
    {

        $repository = $this->getDoctrine()->getRepository(Question::class);


        $originalQuestion = $repository->findOneBy(array('id' => $questionid));

        $status = "";

        $user = $this->getUser();

        $repository = $this->getDoctrine()->getRepository(QuestionScore::class);
        $alreadyScored = null;
        try{
            $alreadyScored =  $repository->findOneBy([
                'question' => $originalQuestion,
                'scoredBy' => $user,
            ]);

        }catch (\Exception $e){

        }

        $entityManager = $this->getDoctrine()->getManager();

        if($alreadyScored == null){
            // Answer was not yet scored by this user
            $questionScoring = new QuestionScore();
            $questionScoring->setQuestion($originalQuestion);
            $questionScoring->setScoredBy($user);
            $questionScoring->setScore(1);

            $originalQuestion->setPoints(($originalQuestion->getPoints() + 1));




            try{
                $entityManager->persist($questionScoring);
                $entityManager->persist($originalQuestion);

                $entityManager->flush();

                $status = "Se ha puntuado la pregunta.";
            }catch(Exception $e){
                $status = $e->getMessage();
            }
            $newScore = $originalQuestion->getPoints();
            return new JsonResponse(array('status' => $status, 'newScore' => $newScore));



        }else{
            // Answer was already scored by this user
            if($alreadyScored->getScore() != 1){
                $alreadyScored->setScore(1);
                $originalQuestion->setPoints(($originalQuestion->getPoints() + 2));
            }
            try{
                $entityManager->persist($alreadyScored);
                $entityManager->persist($originalQuestion);

                $entityManager->flush();

                $status = "Puntuación actualizada.";
            }catch(Exception $e){
                $status = $e->getMessage();
            }
            $newScore = $originalQuestion->getPoints();
            return new JsonResponse(array('status' => $status, 'newScore' => $newScore));

        }

    }

    /**
     * Decreases a question score
     *
     * @Route("/question/{questionid}/score/decrease", name="question_score_decrease", methods={"POST"})
     */
    public function decreaseQuestionScore($questionid)
    {

        $repository = $this->getDoctrine()->getRepository(Question::class);


        $originalQuestion = $repository->findOneBy(array('id' => $questionid));

        $status = "";

        $user = $this->getUser();

        $repository = $this->getDoctrine()->getRepository(QuestionScore::class);
        $alreadyScored = null;
        try{
            $alreadyScored =  $repository->findOneBy([
                'question' => $originalQuestion,
                'scoredBy' => $user,
            ]);

        }catch (\Exception $e){

        }

        $entityManager = $this->getDoctrine()->getManager();

        if($alreadyScored == null){
            // Answer was not yet scored by this user
            $questionScoring = new QuestionScore();
            $questionScoring->setQuestion($originalQuestion);
            $questionScoring->setScoredBy($user);
            $questionScoring->setScore(-1);

            $originalQuestion->setPoints(($originalQuestion->getPoints() - 1));




            try{
                $entityManager->persist($questionScoring);
                $entityManager->persist($originalQuestion);

                $entityManager->flush();

                $status = "Se ha puntuado la pregunta.";
            }catch(Exception $e){
                $status = $e->getMessage();
            }
            $newScore = $originalQuestion->getPoints();
            return new JsonResponse(array('status' => $status, 'newScore' => $newScore));



        }else{
            // Answer was already scored by this user
            if($alreadyScored->getScore() != -1){
                $alreadyScored->setScore(-1);
                $originalQuestion->setPoints(($originalQuestion->getPoints() - 2));
            }
            try{
                $entityManager->persist($alreadyScored);
                $entityManager->persist($originalQuestion);

                $entityManager->flush();

                $status = "Puntuación actualizada.";
            }catch(Exception $e){
                $status = $e->getMessage();
            }
            $newScore = $originalQuestion->getPoints();
            return new JsonResponse(array('status' => $status, 'newScore' => $newScore));

        }

    }

    /**
     * Ends a question
     *
     * @Route("/question/{questionid}/end", name="question_end", methods={"POST"})
     */
    public function endQuestion($questionid)
    {
        $repository = $this->getDoctrine()->getRepository(Question::class);


        $originalQuestion = $repository->findOneBy(array('id' => $questionid));

        $status = "";

        $user = $this->getUser();




        $entityManager = $this->getDoctrine()->getManager();
        try{
            $originalQuestion->setResolved(true);
            $entityManager->persist($originalQuestion);

            $entityManager->flush();

            $status = "Pregunta finalizada.";
        }catch(Exception $e){
            $status = $e->getMessage();
        }
        $newScore = $originalQuestion->getPoints();
        return new JsonResponse(array('status' => $status));
    }

    /**
     * Resolves a question
     *
     * @Route("/question/{questionid}/resolve/{answerid}", name="question_resolve", methods={"POST"})
     */
    public function resolveQuestion($questionid, $answerid)
    {
        $repository = $this->getDoctrine()->getRepository(Question::class);


        $originalQuestion = $repository->findOneBy(array('id' => $questionid));

        $status = "";

        $user = $this->getUser();

        $repository = $this->getDoctrine()->getRepository(Answer::class);
        $originalAnswer = $repository->findOneBy(array('id' => $answerid));



        $entityManager = $this->getDoctrine()->getManager();
        try{
            $originalQuestion->setResolved(true);
            $originalQuestion->setSelectedAnswer($originalAnswer);
            $entityManager->persist($originalQuestion);

            $entityManager->flush();

            $status = "Pregunta finalizada con respuesta.";
        }catch(Exception $e){
            $status = $e->getMessage();
        }
        $newScore = $originalQuestion->getPoints();
        return new JsonResponse(array('status' => $status));
    }


}