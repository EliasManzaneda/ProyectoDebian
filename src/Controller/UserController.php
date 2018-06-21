<?php
/**
 * Created by PhpStorm.
 * User: eliasmanzaneda
 * Date: 01/05/18
 * Time: 11:39
 */

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Role;
use App\Entity\User;
use App\Form\AvatarType;
use App\Form\ProfileType;
use App\Form\UserType;
use App\Service\FileUploader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Exception;



class UserController extends AbstractController
{


    /**
     * @Route("/super/{userid}", name="superzone")
     */
    public function adminZone(Request $request, $userid)
    {

        try{
            $now = new \DateTime('now', (new \DateTimeZone('Europe/Madrid')));
            $me = $this->getUser();

            if($me != null){
                if($me->getBanDate() != null ){
                    if($me->getBanDate() >= $now){
                        return $this->render('suspendido.html.twig', array(

                        ));
                    }

                }
            }
        }catch(\Exception $e){

        }

        if (!$this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_homepage');
        }


       // $repository = $this->getDoctrine()->getRepository(Question::class);
        // $questions = $repository->findForHomepage();











        $answer = new Answer();

        $form = $this->createForm(AnswerType::class, $answer);
    }

    /**
     * @Route("/super/{adminid}/users", name="superzone_user")
     */
    public function adminUserProfile(Request $request, $adminid)
    {


        try{
            $now = new \DateTime('now', (new \DateTimeZone('Europe/Madrid')));
            $me = $this->getUser();

            if($me != null){
                if($me->getBanDate() != null ){
                    if($me->getBanDate() >= $now){
                        return $this->render('suspendido.html.twig', array(

                        ));
                    }

                }
            }
        }catch(\Exception $e){

        }

        if (!$this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
           // return $this->redirectToRoute('app_homepage');
        }
        $me = $this->getUser();
        $editedUsers = [];
        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findAll();

        foreach ($users as &$user) {
            if($user->getId() != $me->getId()){
                array_push($editedUsers,$user);
            }

        }





        return $this->render('admin/adminuserprofile.html.twig', array(
            "users" => $editedUsers
        ));
    }



    /**
     * @Route("/user/{userid}/profile", name="user_profile")
     */
    public function profileAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, $userid, FileUploader $fileUploader)
    {


        try{
            $now = new \DateTime('now', (new \DateTimeZone('Europe/Madrid')));
            $me = $this->getUser();

            if($me != null){
                if($me->getBanDate() != null ){
                    if($me->getBanDate() >= $now){
                        return $this->render('suspendido.html.twig', array(

                        ));
                    }

                }
            }
        }catch(\Exception $e){

        }


        $submitttedAndValid = 'maybe';
        $repository = $this->getDoctrine()->getRepository(User::class);

        $entityManager = $this->getDoctrine()->getManager();


        // $originalUser= $repository->findOneBy(array('id' => $userid));
        $user = $repository->findOneBy(array('id' => $userid));
        // $user = $this->getUser();



        $form = $this->createForm(ProfileType::class, $user);


       // $form->handleRequest($request);
        if ($request->isMethod('POST')) {
            $form->submit($request->request->get($form->getName()));


            $data = $form->getData();


            $submitttedAndValid = 'yes';
            // $originalUser= $repository->findOneBy(array('id' => $userid));

            // $question = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();

            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            // $user->setPassword($password);
            $user->setPassword($password);
            $user->setUsername($user->getUsername());


            $entityManager->persist($user);
            $entityManager->flush();

            // return $this->redirectToRoute('app_homepage');
            return $this->render('user/profile.html.twig', array(
                'form' => $form->createView(),
                'userid' => $userid,
                'error' => 'no',
                'submitttedAndValid' => $submitttedAndValid,

            ));

            try{
                if ($form->isSubmitted() && $form->isValid()) {
                    $data = $form->getData();


                    $submitttedAndValid = 'yes';
                    // $originalUser= $repository->findOneBy(array('id' => $userid));

                    // $question = $form->getData();

                    $entityManager = $this->getDoctrine()->getManager();

                    $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
                    // $user->setPassword($password);
                    $user->setPassword($password);
                    $user->setUsername($user->getUsername());


                    $entityManager->persist($user);
                    $entityManager->flush();

                    // return $this->redirectToRoute('app_homepage');
                    return $this->render('user/profile.html.twig', array(
                        'form' => $form->createView(),
                        'userid' => $userid,
                        'error' => 'no',
                        'submitttedAndValid' => $submitttedAndValid,

                    ));
                    // $entityManager->refresh($user);
                }else{
                    $submitttedAndValid = 'no';
                }


            }catch(\Exception $e){
                $submitttedAndValid = 'yes but error';
                return $this->render('user/profile.html.twig', array(
                    'form' => $form->createView(),
                    'userid' => $userid,
                    'error' => $e->getMessage(),
                    'submitttedAndValid' => $submitttedAndValid,

                ));
            }

        }





        return $this->render('user/profile.html.twig', array(
            'form' => $form->createView(),
            'userid' => $userid,
            'error' => 'none',
            'submitttedAndValid' => $submitttedAndValid,

        ));


    }


    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }



    /**
     * @Route("/super/{userid}/banforever", name="superzona_user_ban_forever")
     */
    public function banForever(Request $request, $userid){


        try{
            $now = new \DateTime('now', (new \DateTimeZone('Europe/Madrid')));
            $me = $this->getUser();

            if($me != null){
                if($me->getBanDate() != null ){
                    if($me->getBanDate() >= $now){
                        return $this->render('suspendido.html.twig', array(

                        ));
                    }

                }
            }
        }catch(\Exception $e){

        }

        $repository = $this->getDoctrine()->getRepository(User::class);

        $entityManager = $this->getDoctrine()->getManager();

        $userToBan = $repository->findOneBy(array('id' => $userid));


        $status = "";


        try{

            $registerDate = new \DateTime('now + 200 years', (new \DateTimeZone('Europe/Madrid')));
            $userToBan->setBanDate($registerDate);
            $entityManager->persist($userToBan);


            $entityManager->flush();

            $status = "Usuario suspendido .";
        }catch(Exception $e){
            $status = $e->getMessage();
        }



        return new JsonResponse(array('status' => $status));

    }




    /**
     * @Route("/super/{userid}/bandays", name="superzona_user_ban_days")
     */
    public function banDays(Request $request, $userid){


        try{
            $now = new \DateTime('now', (new \DateTimeZone('Europe/Madrid')));
            $me = $this->getUser();

            if($me != null){
                if($me->getBanDate() != null ){
                    if($me->getBanDate() >= $now){
                        return $this->render('suspendido.html.twig', array(

                        ));
                    }

                }
            }
        }catch(\Exception $e){

        }

        $repository = $this->getDoctrine()->getRepository(User::class);

        $entityManager = $this->getDoctrine()->getManager();

        $userToBan = $repository->findOneBy(array('id' => $userid));


        $status = "";


        try{

            $registerDate = new \DateTime('now + 3 days', (new \DateTimeZone('Europe/Madrid')));
            $userToBan->setBanDate($registerDate);
            $entityManager->persist($userToBan);


            $entityManager->flush();

            $status = "Usuario suspendido .";
        }catch(Exception $e){
            $status = $e->getMessage();
        }



        return new JsonResponse(array('status' => $status));

    }

}