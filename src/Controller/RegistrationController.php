<?php
/**
 * Created by PhpStorm.
 * User: eliasmanzaneda
 * Date: 30/04/18
 * Time: 17:02
 */

namespace App\Controller;

use App\Entity\Role;
use App\Form\UserType;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends Controller
{
    /**
     * @Route("/register", name="user_registration")
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {


        if ($this->container->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_homepage');
        }

        // 1) build the form
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            $repository = $this->getDoctrine()->getRepository(Role::class);
            // $user->addRole($repository->findRoleByName("ROLE_USER"));
            $defaultRole = $repository->findOneBy(['name' => 'ROLE_USER']);
            $user->addRole($defaultRole);
            $user->setPoints(0);
            $user->setBanStrikes(0);
            // $today = new \DateTime('now', (new \DateTimeZone('Europe/Madrid')));
            $registerDate = new \DateTime('now', (new \DateTimeZone('Europe/Madrid')));
            $user->setRegisterDate($registerDate);
            $user->setAvatarPath('build/images/UserDefaultAvatar.png');
            $user->setOldAvatar('build/images/UserDefaultAvatar.png');
            $user->setNewAvatar('build/images/UserDefaultAvatar.png');

            // 4) save the User!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user

            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $this->container->get('security.token_storage')->setToken($token);
            $this->container->get('session')->set('_security_main', serialize($token));

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render(
            'registration/register.html.twig',
            array('form' => $form->createView())
        );
    }



    /**
     * @Route("/registrationsuccess", name="registrationsuccess")
     */
    public function registrationsucess()
    {

        return $this->render('registration/registersuccess.html.twig', [

        ]);
    }

}