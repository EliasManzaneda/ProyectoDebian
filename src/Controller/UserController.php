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
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
        // SearchBar Start
        $repository = $this->getDoctrine()->getRepository(Question::class);
        $questions = $repository->findForHomepage();
        $questionsResolved = $repository->findForResolved();
        $newQuestions = array_slice($questionsResolved, -5);
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
            if (empty($searchedquestion)) {
                $searchedquestion = "%";
            }

            $questions = $repository->findForSearchbar($searchedquestion);


            if ($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                return $this->render('homepage.html.twig', [
                    'questions' => $questions,
                    'newQuestions' => $newQuestions,
                    'form' => $form->createView(),
                    'searched' => $data['searchtext'],
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
        // SearchBar End

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
            "users" => $editedUsers,
            'questions' => $questions,
            'newQuestions' => $newQuestions,
            'form' => $form->createView(),
            'searched' => $data['searchbar'],
            'searching' => false
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




    /**
     * @Route("/super/{userid}/banpremove", name="superzona_user_ban_remove")
     */
    public function banRemove(Request $request, $userid){


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

            $registerDate = new \DateTime('now - 3 days', (new \DateTimeZone('Europe/Madrid')));
            $userToBan->setBanDate(null);
            $entityManager->persist($userToBan);


            $entityManager->flush();

            $status = "Usuario perdonado .";
        }catch(Exception $e){
            $status = $e->getMessage();
        }



        return new JsonResponse(array('status' => $status));

    }



    /**
     * @Route("/super/{userid}/banstrike", name="superzona_user_ban_strike")
     */
    public function banStrike(Request $request, $userid){


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

            // $registerDate = new \DateTime('now - 3 days', (new \DateTimeZone('Europe/Madrid')));
            // $userToBan->setBanDate(null);
            $banStrikes = $userToBan->getBanStrikes() + 1;
            $userToBan->setBanStrikes($banStrikes);
            $entityManager->persist($userToBan);


            $entityManager->flush();

            $status = "Usuario perdonado .";
        }catch(Exception $e){
            $status = $e->getMessage();
        }



        return new JsonResponse(array('status' => $status));

    }


    /**
     * @Route("/super/{userid}/changeavatar", name="superzona_user_change_avatar")
     */
    public function changeAvatar(Request $request, $userid){


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

            // $registerDate = new \DateTime('now - 3 days', (new \DateTimeZone('Europe/Madrid')));
            // $userToBan->setBanDate(null);
            // $banStrikes = $userToBan->getBanStrikes() + 1;
            // $userToBan->setBanStrikes($banStrikes);
            $userToBan->setAvatarPath('build/images/UserDefaultAvatar.png');
            $userToBan->setOldAvatar('build/images/UserDefaultAvatar.png');
            $userToBan->setNewAvatar('build/images/UserDefaultAvatar.png');
            $entityManager->persist($userToBan);


            $entityManager->flush();

            $status = "Usuario perdonado .";
        }catch(Exception $e){
            $status = $e->getMessage();
        }



        return new JsonResponse(array('status' => $status));

    }

}