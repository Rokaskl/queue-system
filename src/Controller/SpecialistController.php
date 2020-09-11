<?php

namespace App\Controller;

use App\Entity\Visit;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class SpecialistController extends AbstractController
{
    /**
     * @Route("/specialist", name="specialist")
     */
    public function index()
    {

        $user = $this->getUser();
        $visits = $this->getDoctrine()->getRepository(Visit::Class)->findBy(['specialist' => $user]);
        return $this->render('specialist/index.html.twig', [
            'visits' => $visits,
            'isWorking'=>$user->getIsWorking()
        ]);
    }

    /**
     * @Route("/specialist/start", name="specialistStart")
     * @param Request $request
     * @return RedirectResponse
     */
    public function start(Request $request): Response
    {

        $em = $this->getDoctrine()->getManager();

        $currentVisitId = $request->query->get('visitId');

        if ($currentVisitId) {


            $user = $this->getUser();
            $user->setIsWorking(true);
            $currentVisit = $this->getDoctrine()->getRepository(Visit::Class)->find($currentVisitId);
            $currentVisit->setStartDate(new DateTime());
            $em->flush();
        }
        return $this->redirect('/specialist');

    }

    /**
     * @Route("/specialist/end", name="specialistStop")
     * @param Request $request
     * @return RedirectResponse
     */
    public function stop(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $currentVisitId = $request->query->get('visitId');

        if ($currentVisitId) {
            $user = $this->getUser();
            $currentVisit = $this->getDoctrine()->getRepository(Visit::Class)->find($currentVisitId);

            //Set not working and End Time for visit
            $user->setIsWorking(false);
            $currentVisit->setEndDate(new DateTime());

            //Update EST Gap
            $visitDuration = $currentVisit->getStartDate()->diff($currentVisit->getEndDate());
            $visitDurationSeconds =  $visitDuration->h*3600
                + $visitDuration->i*60 + $visitDuration->s;

            $user = $currentVisit->getSpecialist();
            $totalVisits = $user->getTotalVisits();
            $estGap=$user->getEstGap();
            if( !$estGap ){$newGapTime =$visitDurationSeconds;}
            else {
                $newGapTime = ($totalVisits * $estGap + $visitDurationSeconds) / ($totalVisits + 1);
            }
            $user->setEstGap($newGapTime);

            //Update user visits count
            $user->setTotalVisits($totalVisits + 1);

            //Update EST Times for all visits
            $visits = $this->getDoctrine()->getRepository(Visit::Class)->findBy(['specialist' => $user], array('registrationDate' => 'ASC'));
            for ($i = 0; $i < count($visits); $i++) {
                $visits[$i]->setEstTime(time()+($newGapTime * ($i-1))); // 0 is current
            }


        }
        //Delete current visit (easy to make soft delete from here)
        $em->remove($currentVisit);
        $em->flush();

        return $this->redirect('/specialist');

    }


}
