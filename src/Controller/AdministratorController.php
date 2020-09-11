<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Visit;
use phpDocumentor\Reflection\DocBlock\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;


class AdministratorController extends AbstractController

{

    /**
     * @Route("/administrator", name="administrator")
     */
    public function index()
    {

        $visits = [];
        $specialists = $this->getDoctrine()
            ->getRepository(User::class)
            ->findByRole("ROLE_SPECIALIST");

        for ($i = 0; $i < count($specialists); $i++) {
            $visits[$i] = $this->getDoctrine()->getRepository(Visit::class)->findBy(
                ['specialist' => $specialists[$i]],[ 'registrationDate' => 'ASC'], 5
            );
        }

        return $this->render('administrator/index.html.twig',  [
            'visits' => $visits,
        ]);
    }
    /**
     * @Route("/administrator/tables", name="administratorData")
     */
    public function getData()
    {

        $visits = [];
        $specialists = $this->getDoctrine()
            ->getRepository(User::class)
            ->findByRole("ROLE_SPECIALIST");

        for ($i = 0; $i < count($specialists); $i++) {
            $visits[$i] = $this->getDoctrine()->getRepository(Visit::class)->findBy(
                ['specialist' => $specialists[$i]],[ 'registrationDate' => 'ASC'], 5
            );
        }
        return $this->render('administrator/tables.html.twig', [
            'visits' => $visits,
        ]);
    }
}


