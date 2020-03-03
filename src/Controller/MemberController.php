<?php

namespace App\Controller;

use App\Entity\Member;
use App\Form\MemberType;
use App\Repository\MemberRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MemberController extends AbstractController
{

    private $memberRepository;

    public function __construct(MemberRepository $memberRepository)
    {
        $this->memberRepository = $memberRepository;
    }

    /**
     * @Route("/members", name="list_member")
     *
     * List of member
     */
    public function index()
    {
        $members = $this->memberRepository->findAll();

        return $this->render('member/index.html.twig', [
            'members' => $members
        ]);
    }


    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * Create new Member
     */
    public function addMember(Request $request, UserPasswordEncoderInterface $passwordEncoder){

        $member = new Member();
        $form = $this->createForm(MemberType::class, $member);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            //Encodage password
            $password = $passwordEncoder->encodePassword($member, $member->getPassword());
            $member->setPassword($password);

            //Add member on database
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($member);
            $entityManager->flush();

            //Add notification message and redirect
            $this->addFlash("notification", "Inscription effectuée ! Connectez-vous !");
            return $this->redirectToRoute("app_login");
        }

        //Form member
        return $this->render('member/form.html.twig', [
            "form" => $form->createView(),
        ]);
    }
}
