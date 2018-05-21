<?php
/**
 * Created by PhpStorm.
 * User: eliasmanzaneda
 * Date: 21/05/18
 * Time: 08:29
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

use Symfony\Component\HttpFoundation\RedirectResponse;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/homepage", name="admin_homepage")
     */
    public function homepage(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Question::class);
        $questions = $repository->findForHomepage();

        // $newQuestions = $questions;
        $newQuestions= array_slice($questions, -3);

        // SEARCHBAR
        $data = [
            "searchbar" => "default"
        ];



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

            $questions = $repository-> findForSearchbar($searchedquestion);


            // return $this->render('question/listSearchedQuestions.html.twig', array(

            $user = $this->getUser();
            $usernamelower = strtolower($user->getUsername());

            return $this->render('adminhomepage.html.twig', array(
                'form' => $form->createView(),
                'searched' => $data['searchtext'],
                'questions' => $questions,
                'searching' => true,
                'usernamelower' => $usernamelower
            ));

            /*
            return $this->redirectToRoute('app_homepage', array(
                'form' => $form->createView(),
                'searched' => $data['searchbar'],
                'searching' => false
            ));
            */

        }
    }
}