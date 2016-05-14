<?php

namespace TarasTestBundle\Controller;

use TarasTestBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        $user = new User();
        $response =  new Response();

        $form = $this->createFormBuilder($user, array(
            'attr'=>array('onsubmit'=>'login(document.getElementById(\'form_login\').value)')
        ))
            ->add("login", TextType::class, array('label' => 'Enter your login please '))
            ->add("passwordHash", TextType::class)
            ->add("enter", SubmitType::class, array('label' => 'Enter'))
            ->getForm();

        $form->handleRequest($request);
        $login = $request->request->get('login');
        $pass = $request->request->get('pass');
        if(!$login && !$pass){
            return $this->render('TarasTestBundle:Default:login.html.twig', array('form' => $form->createView(),));
        }else if($login && !$pass){
            $user_em =$this->getDoctrine()->getManager();
            $user_repo = $user_em->getRepository('TarasTestBundle:User');
            $user=$user_repo->findOneBy(array('login'=>$login));
            if($user){
                $user->generateSalt($user_em);
                return $response->setContent(sha1($user->getSalt()));

            }
        }else if($login && $pass){
            if ($form->isSubmitted() && $form->isValid()) {
                return $this->render('TarasTestBundle:Default:index.html.twig', array('login' => $user->getLogin(),
                    'passwordHash' => $user->getPasswordHash(),));
            }
        }
    }
}
