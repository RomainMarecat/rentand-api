<?php

namespace App\Controller;

use App\Entity\Language;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

class ParamRestController extends FOSRestController
{
    public function getLanguagesAction(Request $request)
    {

        $languages = array(
            'fr' => array(
                'fr' => 'Français',
                'en' => 'French'),
            'en' => array(
                'fr' => 'Anglais',
                'en' => 'English'),
        );

        return $languages;
    }

    public function getLevelsAction(Request $request)
    {

        $languages = array(
            1 => array(
                'fr' => 'Débutant',
                'en' => 'Beginner'),
            2 => array(
                'fr' => 'Intermédiaire',
                'en' => 'Intermediate'),
            3 => array(
                'fr' => 'Avancé',
                'en' => 'Advanced'),
            4 => array(
                'fr' => 'Expert',
                'en' => 'Expert'),
        );

        return $languages;
    }

    public function getLanguagesSystemAction(Request $request)
    {

        $languages = array("fr" => "français", "en" => "anglais");

        return $languages;
    }

    public function getSportsAction(Request $request)
    {

        $sports = $this->getDoctrine()->getRepository('AppBundle:Sport')->findAll();

        return $sports;
    }
}
