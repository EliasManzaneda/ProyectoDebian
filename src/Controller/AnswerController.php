<?php
/**
 * Created by PhpStorm.
 * User: eliasmanzaneda
 * Date: 19/06/18
 * Time: 13:29
 */

namespace App\Controller;

use App\Entity\AnswerScore;
use App\Entity\User;
use App\Entity\Answer;
use App\Entity\Question;
use Exception;
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


class AnswerController extends AbstractController
{

    /**
     * @Route("/answerquestion/{questionid}", name="new_answer")
     */
    public function newAnswer(Request $request, $question){


        if (!$this->container->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_homepage');
        }



        $repository = $this->getDoctrine()->getRepository(Question::class);
        $questions = $repository->findForHomepage();
        $newQuestions = array_slice($questions, -3);



        $answer = new Answer();

        $form = $this->createForm(AnswerType::class, $answer);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // $question = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();

            $user = $this->getUser();

            $answer->setUser($user);
            $answer->setQuestion($question);

            $today = new \DateTime('now', (new \DateTimeZone('Europe/Madrid')));
            $answer->setCreationDate($today);
            $answer->setPoints(0);



            $entityManager->persist($answer);
            $entityManager->flush();

            /*
            return $this->redirectToRoute('app_homepage');
            */


            return $this->render('question/questioning.html.twig', [
                'question' => $question,
                'newQuestions' => $newQuestions,
            ]);
        }

        return $this->render('question/questioning.html.twig', array(
            'form' => $form->createView(),
            'question' => $question,
            'newQuestions' => $newQuestions,
        ));
        /*
        return $this->render('question/askQuestion.html.twig', array(
            'form' => $form->createView(),
        ));
        */

    }



    /**
     * Increases a answer score
     *
     * @Route("/answer/{answerid}/score/increase", name="answer_score_increase", methods={"POST"})
     */
    public function increaseAnswerScore($answerid)
    {




        $repository = $this->getDoctrine()->getRepository(Answer::class);


        $originalAnswer = null;
        $originalAnswer = $repository->findOneBy(array('id' => $answerid));

        $status = "";

        $user = $this->getUser();

        $repository = $this->getDoctrine()->getRepository(AnswerScore::class);
        $alreadyScored = null;
        try{
            $alreadyScored =  $repository->findOneBy([
                'answer' => $originalAnswer,
                'scoredBy' => $user,
            ]);

            /*
            if($alreadyScored != $originalAnswer){
                $alreadyScored = null;
            }
            */

        }catch (\Exception $e){
            $status = $e->getMessage();
            return new JsonResponse(array('status' => $status, 'newScore' => "fail"));
        }

        $entityManager = $this->getDoctrine()->getManager();

        if($alreadyScored == null){
            // Answer was not yet scored by this user
            $questionScoring = new AnswerScore();
            $questionScoring->setAnswer($originalAnswer);
            $questionScoring->setScoredBy($user);
            $questionScoring->setScore(1);

            $originalAnswer->setPoints(($originalAnswer->getPoints() + 1));




            try{
                $entityManager->persist($questionScoring);
                $entityManager->persist($originalAnswer);

                $entityManager->flush();

                $status = "Respuesta: Puntuación creada";
            }catch(Exception $e){
                $status = $e->getMessage();
            }
            $newScore = $originalAnswer->getPoints();
            return new JsonResponse(array('status' => $status, 'newScore' => $newScore));



        }else{
            // Answer was already scored by this user
            if($alreadyScored->getScore() != 1){
                $alreadyScored->setScore(1);
                $originalAnswer->setPoints(($originalAnswer->getPoints() + 2));
            }
            try{
                $entityManager->persist($alreadyScored);
                $entityManager->persist($originalAnswer);

                $entityManager->flush();

                $status = "Respuesta: Puntuación actualizada.";
            }catch(Exception $e){
                $status = $e->getMessage();

            }
            $newScore = $originalAnswer->getPoints();
            return new JsonResponse(array('status' => $status, 'newScore' => $newScore));

        }

    }

    /**
     * Decreases a answer score
     *
     * @Route("/answer/{answerid}/score/decrease", name="answer_score_decrease", methods={"POST"})
     */
    public function decreaseAnswerScore($answerid)
    {


        $repository = $this->getDoctrine()->getRepository(Answer::class);


        $originalAnswer = null;
        $originalAnswer = $repository->findOneBy(array('id' => $answerid));

        $status = "";

        $user = $this->getUser();

        $repository = $this->getDoctrine()->getRepository(AnswerScore::class);
        $alreadyScored = null;
        try{
            $alreadyScored =  $repository->findOneBy([
                'answer' => $originalAnswer,
                'scoredBy' => $user,
            ]);

        }catch (\Exception $e){

        }

        $entityManager = $this->getDoctrine()->getManager();

        if($alreadyScored == null){
            // Answer was not yet scored by this user
            $questionScoring = new AnswerScore();
            $questionScoring->setAnswer($originalAnswer);
            $questionScoring->setScoredBy($user);
            $questionScoring->setScore(-1);

            $originalAnswer->setPoints(($originalAnswer->getPoints() - 1));




            try{
                $entityManager->persist($questionScoring);
                $entityManager->persist($originalAnswer);

                $entityManager->flush();

                $status = "Se ha puntuado la respuesta.";
            }catch(Exception $e){
                $status = $e->getMessage();
            }
            $newScore = $originalAnswer->getPoints();
            return new JsonResponse(array('status' => $status, 'newScore' => $newScore));



        }else{
            // Answer was already scored by this user
            if($alreadyScored->getScore() != -1){
                $alreadyScored->setScore(-1);
                $originalAnswer->setPoints(($originalAnswer->getPoints() - 2));
            }
            try{
                $entityManager->persist($alreadyScored);
                $entityManager->persist($originalAnswer);

                $entityManager->flush();

                $status = "Puntuación actualizada.";
            }catch(Exception $e){
                $status = $e->getMessage();
            }
            $newScore = $originalAnswer->getPoints();
            return new JsonResponse(array('status' => $status, 'newScore' => $newScore));

        }

    }

}