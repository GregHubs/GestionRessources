<?php

namespace BirdOffice\UserBundle\Manager;


use BirdOffice\UserBundle\Helper\Helper;
use BirdOffice\UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Session\Session;

class GoogleCalendarManager
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var \Google_Client
     */
    protected $client;

    /*
     * @var \Helper
     */
    protected $helper;

    /**
     * @param EntityManager $em
     * @param Helper $helper
     */
    public function __construct(EntityManager $em, Helper $helper) {
        $this->em = $em;
        $this->helper = $helper;
    }


    /**
     * @param \Google_Client $client
     */
    public function setGoogleClient(\Google_Client $client) {
        $this->client = $client;
    }


    /**
     * Permet de récupérer un event depuis un calendrier google
     * @param $calendarId
     * @param $eventId
     * @return \Google_Service_Calendar_Event|null
     */
    public function getEventFromGoogle($calendarId, $eventId){
        $service = new \Google_Service_Calendar($this->client);

        try {
            $event = $service->events->get($calendarId, $eventId);
        } catch (Exception $e){
            $event = null;
        }
        return $event;
    }


    /**
     * Permet de mettre à jour un évènement google
     * @param $calendarId
     * @param \Google_Service_Calendar_Event $event
     * @return \Google_Service_Calendar_Event|null
     */
    public function updateEventFromGoogle($calendarId, \Google_Service_Calendar_Event $event){
        $service = new \Google_Service_Calendar($this->client);

        try {
            $updatedEvent = $service->events->update($calendarId, $event->getId(), $event);
        } catch (Exception $e){
            $updatedEvent = null;
        }

        return $updatedEvent;
    }


    /**
     * Permet de supprimer un évènement google
     * @param $calendarId
     * @param \Google_Service_Calendar_Event $event
     * @param bool $sendNotification
     * @return bool
     */
    public function deleteEventFromGoogle($calendarId, \Google_Service_Calendar_Event $event, $sendNotification = false){
        $service = new \Google_Service_Calendar($this->client);

        try {
            $service->events->delete($calendarId, $event->getId(), array('sendNotifications' => $sendNotification));
        } catch (Exception $e){
            return false;
        }

        return true;
    }


}