<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\SportEvent;
use App\Entity\User;
use App\Entity\EventWallMessage;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Form\CreateEventStep1Type;
use App\Form\CreateEventStep2Type;
use App\Form\CreateEventStep3Type;
use App\Form\CreateEventStep4Type;
use App\Form\CreateEventStep5Type;
use App\Form\ContactPlayerType;
use App\Form\DeletePlayerType;
use App\Form\WallMessageType; 


class EventManagerController extends AbstractController
{
    /**
     * @Route("/event/join/{eventId}", name="app_join_event", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function joinEvent(EntityManagerInterface $em, $eventId)
    {
        $repo = $em->getRepository(SportEvent::class);

        $event = $repo->findOneBy(['id'=>$eventId]);

        $event->addPlayer($this->getUser());

        $em->persist($event);
        $em->flush(); 
        
        return $this->redirectToRoute("home"); 
    }


    /**
     * @Route("/mes-sorties", name="app_list_event")
     * @IsGranted("ROLE_USER")
     */
    public function displayEventList(EntityManagerInterface $em)
    {
        return $this->render('/event_manager/eventList.html.twig'); 
    }

    /**
     * @Route("/mes-sorties/{id}", name="event_wall")
     * @IsGranted("ROLE_USER")
     */
    public function displayEventWall(SportEvent $event, EntityManagerInterface $em, Request $request, $id)
    {
        $this->denyAccessUnlessGranted('WALL_VIEW', $event);


        $form = $this->createForm(WallMessageType::class);

        $form->handleRequest($request); 

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData(); 

            $message = new EventWallMessage;
            $message->setAuthor($this->getUser())
                    ->setMessage($data->getMessage())
                    ->setPin(false)
                    ->setEvent($event);
            $em->persist($message);
            $em->flush();

            return $this->redirectToRoute('event_wall', ['id' => $event->getId() ]); 
        }

        return $this->render('/event_manager/eventWall.html.twig', [
                "event" => $event, 
                "form" => $form->createView(),
        ]); 
    }


    /**
     * @Route("/event/manage/{id}", name="app_manage_event")
     */
    public function manageEvent(SportEvent $event)
    {

        $this->denyAccessUnlessGranted('MANAGE', $event); 

        return $this->render('/event_manager/manageEvent.html.twig',[
                "event" => $event, 
        ]); 
    }


    /**
     * @Route("/event/manage/{id}/sport", name="app_manage_event_sport")
     */
    public function manageEventSport(SportEvent $event, EntityManagerInterface $em, Request $request)
    {
        $this->denyAccessUnlessGranted('MANAGE', $event); 

        $form = $this->createForm(CreateEventStep1Type::class, $event);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            return $this->redirectToRoute('app_manage_event_infos', [ 'id' => $event->getId()] );
        }

        return $this->render('/event_manager/manageEventSport.html.twig',[
                "event" => $event, 
                "form" => $form->createView(), 
        ]); 
    }

    /**
     * @Route("/event/manage/{id}/infos", name="app_manage_event_infos")
     */
    public function manageEventInfos(SportEvent $event, EntityManagerInterface $em, Request $request)
    {

       
        $this->denyAccessUnlessGranted('MANAGE', $event); 

        $form = $this->createForm(CreateEventStep2Type::class, $event);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            $this->addFlash('success', "Les informations ont bien été mises à jour !"); 
            return $this->redirectToRoute('app_manage_event', ['id' => $event->getId()]);
        }

        return $this->render('/event_manager/manageEventInfos.html.twig',[
                "event" => $event, 
                "form" => $form->createView(),  
        ]); 
    }

    /**
     * @Route("/event/manage/{id}/date-lieu-horaires", name="app_manage_event_place")
     */
    public function manageEventPlace(SportEvent $event, EntityManagerInterface $em, Request $request)
    {
       
        $this->denyAccessUnlessGranted('MANAGE', $event); 

        $form = $this->createForm(CreateEventStep3Type::class, $event);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            $this->addFlash('success', "Les informations ont bien été mises à jour !"); 
            return $this->redirectToRoute('app_manage_event', ['id' => $event->getId()]);
        }

        return $this->render('/createEventForm/step3.html.twig',[
                "event" => $event, 
                "createEventForm" => $form->createView(), 
        ]); 
    }

    /**
     * @Route("/event/manage/{id}/materiel-niveau-prix", name="app_manage_event_material")
     */
    public function manageEventMaterial(SportEvent $event, EntityManagerInterface $em, Request $request)
    {
       
        $this->denyAccessUnlessGranted('MANAGE', $event); 

        $form = $this->createForm(CreateEventStep4Type::class, $event);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            $this->addFlash('success', "Les informations ont bien été mises à jour !"); 
            return $this->redirectToRoute('app_manage_event', ['id' => $event->getId()]);
        }

        return $this->render('/event_manager/manageEventMaterial.html.twig',[
                "event" => $event, 
                "createEventForm" => $form->createView(), 
        ]); 
    }


    /**
     * @Route("/event/manage/{id}/autres-caracteristiques", name="app_manage_event_other_attributes")
     */
    public function manageEventOtherAttributes(SportEvent $event, EntityManagerInterface $em, Request $request)
    {
       
        $this->denyAccessUnlessGranted('MANAGE', $event); 

        $form = $this->createForm(CreateEventStep5Type::class, $event);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            $this->addFlash('success', "Les informations ont bien été mises à jour !"); 
            return $this->redirectToRoute('app_manage_event', ['id' => $event->getId()]);
        }

        return $this->render('/createEventForm/step5.html.twig',[
                "event" => $event, 
                "createEventForm" => $form->createView(), 
        ]); 
    }

    /**
     * @Route("/event/manage/{id}/contact-player-{playerId}", name="app_manage_contact_player")
     */
    public function manageEventContactPlayer(SportEvent $event, EntityManagerInterface $em, Request $request, $playerId)
    {

        $playerRepo = $em->getRepository(User::class);

        $playerToReach = $playerRepo->findOneBy(['id' => $playerId ]);

        
       
        $this->denyAccessUnlessGranted('MANAGE', $event); 

        $this->denyAccessUnlessGranted('MANAGE_PLAYER', $playerToReach); 

        $form = $this->createForm(ContactPlayerType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            
            // Add send email to player logic
            $this->addFlash('success', "Votre message a été envoyé avec succès"); 
            return $this->redirectToRoute('app_manage_event', ['id' => $event->getId()]);
        }

        return $this->render('/event_manager/contactPlayer.html.twig',[
                "event" => $event, 
                "player" => $playerToReach,
                "form" => $form->createView(), 
        ]); 
    }

    /**
     * @Route("/event/manage/{id}/cancel-player-{playerId}", name="app_manage_delete_player")
     */
    public function manageEventDeletePlayer(SportEvent $event, EntityManagerInterface $em, Request $request, $playerId)
    {

        $playerRepo = $em->getRepository(User::class);

        $playerToDelete = $playerRepo->findOneBy(['id' => $playerId ]);

        $this->denyAccessUnlessGranted('MANAGE', $event); 

        $this->denyAccessUnlessGranted('MANAGE_PLAYER', $playerToDelete); 

        $form = $this->createForm(DeletePlayerType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            
            // Add send email to player logic

            if($form['confirmDelete']->getData() === true ){
                
                if($this->getUser() !== $playerToDelete){
                    $data = $form->getData();

                    $event->removePlayer($playerToDelete); 

                    $em->persist($event);
                    $em->flush();   
                    $this->addFlash('success', $playerToDelete->getPseudo()." a été retiré de votre évènement"); 
                    return $this->redirectToRoute('app_manage_event', ['id' => $event->getId()]);
                }

                $this->addFlash('error', "Vous ne pouvez pas supprimer votre participation à cet évènement. Si vous ne pouvez pas venir, il vous faut annuler l'évènement."); 
                
                return $this->redirectToRoute('app_manage_event', ['id' => $event->getId()]);




            }

           

        }

        return $this->render('/event_manager/deletePlayer.html.twig',[
                "event" => $event, 
                "player" => $playerToDelete,
                "form" => $form->createView(), 
        ]); 
    }

    /**
     * @Route("/event/manage/{id}/cancel-event", name="app_manage_delete_event")
     */
    public function manageEventDeleteEvent(SportEvent $event, EntityManagerInterface $em, Request $request)
    {
        $this->denyAccessUnlessGranted('MANAGE', $event); 

        $form = $this->createForm(DeletePlayerType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            
            if($form['confirmDelete']->getData() === true){
                $this->getUser()->removeSportEventsOrganiser($event);

                $em->flush(); 

                return $this->redirectToRoute('app_list_event');
            }
        }

        return $this->render('/event_manager/deleteEvent.html.twig',[
                "event" => $event, 
                "form" => $form->createView(), 
        ]); 
    }



}
