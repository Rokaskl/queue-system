<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Visit;
use App\Form\FindVisitType;
use App\Form\NewVisitType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VisitController extends AbstractController
{
    /**
     * @Route("/visit", name="visit")
     */
    public function index()
    {


        return $this->render('visit/index.html.twig', [

        ]);
    }

    /**
     * @Route("/visit/new", name="newVisit" )
     * @param Request $request
     * @return Response
     */
    public function newVisit(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $specialists = $this->getDoctrine()->getRepository(User::class)->findByRole("ROLE_SPECIALIST");

        $form = $this->createForm(NewVisitType::class, [], array(
            'specialists' => $specialists
        ));
        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $visit = new Visit();
                $visit->setRegistrationDate(new DateTime());
                $visit->setCustomer($data['name']);
                $visit->setSpecialist($data['specialist']);
                $visit->setCode(uniqid());

                //set EST time
                $user = $visit->getSpecialist();
                $estGap = $user->getEstGap();
                if (!$estGap) {
                    $visit->setEstTime(null);
                } else {
                    $visits = $this->getDoctrine()->getRepository(Visit::class)->findBy(['specialist' => $user], array('registrationDate' => 'ASC'));
                    $visit->setEstTime(time() + ($estGap * (count($visits))));
                }
                //

                
                $em->persist($visit);
                $em->flush();

                return $this->redirectToRoute('visitInfo', ['code' => $visit->getCode()]);
            }
        }

        return $this->render('visit/newVisit.html.twig', [
            'newVisitForm' => $form->createView(),
            'specialists' => $specialists
        ]);
    }

    /**
     * @Route("/visit/info", name="visitInfo",  methods="GET")
     */
    public function getVisitByCode(Request $request): Response
    {
        $code = $request->query->get('code');
        $visit = $this->getDoctrine()->getRepository(Visit::class)->findOneBy(['code' => $code]);

        return $this->render('visit/visitInfo.html.twig', ["visit" => $visit,
        ]);
    }

    /**
     * @Route("/visit/find", name="findVisit")
     * @param Request $request
     * @return Response
     */
    public function findVisit(Request $request): Response
    {

        $form = $this->createForm(FindVisitType::class);
        $form->handleRequest($request);
        if ($request->isMethod('POST')) {
            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $visit = $this->getDoctrine()->getRepository(Visit::class)->findOneBy(['code' => $data['code']]);
                if ($visit) {
                    return $this->redirectToRoute('visitInfo', ['code' => $visit->getCode()]);
                }

            }
        }
        return $this->render('visit/findVisit.html.twig', ['findVisitForm' => $form->createView(),]);
    }

    /**
     * @Route("/visit/delete", name="deleteVisit",  methods="GET")
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteVisit(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $code = $request->query->get('code');
        $isSpecialist = $request->query->get('isSpecialist');

        $visit = $this->getDoctrine()->getRepository(Visit::class)->findOneBy(['code' => $code]);

        if (!$visit) {
            throw $this->createNotFoundException(
                'There are no visit with the following code: ' . $code
            );
        }

        $em->remove($visit);
        $em->flush();
        if ($isSpecialist) {
            return $this->redirect('/specialist');
        }

        return $this->redirect('/visit');

    }


}
