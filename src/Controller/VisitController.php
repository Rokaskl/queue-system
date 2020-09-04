<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;



class VisitController extends AbstractController
{
    /**
     * @Route("/visit", name="visit")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $specialists = $repository->findBy(
            ['roles' => 'specialist']
        );

        return $this->render('visit/index.html.twig', [
            'specialists' => $specialists
        ]);
    }
    /**
     * @Route("/visit/new", name="new visit",  methods="GET")
     */
    public function newVisit(Request $request): Response
    {
        $name = $request->query->get("name");
        $message = $request->query->get("message");

        return $this->render('visit/newVisit.html.twig', ["name" => $name,
            "message" => $message]);
    }
}
