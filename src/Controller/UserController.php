<?php
/**
 * Created by PhpStorm.
 * User: eliasmanzaneda
 * Date: 01/05/18
 * Time: 11:39
 */

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    /**
     * @Route("/profile", name="user_profile")
     */
    public function profile()
    {
        if (!$this->container->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_homepage');
        }

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        //             'user' => $user,
        return $this->render('user/profile.html.twig', [


        ]);
    }
}