<?php

namespace App\Controller\Restricted;

use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;


class StructuresController extends FOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"Default", "getStructures"})
     */
    public function getStructuresAction(Request $request)
    {

        if ($request->query->get('sort')) {
            $sort = explode(":", $request->query->get('sort'));
            $sortParams = array('id', 'title', 'email', 'createdAt', 'updatedAt');
            if ((strtoupper($sort[1]) != 'ASC' && strtoupper($sort[1]) != 'DESC') || (!in_array($sort[0], $sortParams))) {
                unset($sort);
            }
        }
        if ($request->query->get('filter')) {
            $filter = explode(":", $request->query->get('filter'));
            $filterParams = array('id', 'title', 'email', 'createdAt', 'updatedAt');
            if (!in_array($filter[0], $filterParams)) {
                unset($filter);
            }
        }
        if (!isset($sort) && !isset($filter)) {
            $structures = $this->getDoctrine()->getRepository('App:Structure')->findAll();
        } elseif (isset($sort) && isset($filter)) {
            $structures = $this->getDoctrine()->getManager()->getRepository('App:Structure')->findBy(array($filter[0] => $filter[1]), array($sort[0] => $sort[1]));
        } elseif (isset($sort)) {
            $structures = $this->getDoctrine()->getManager()->getRepository('App:Structure')->findBy(array(), array($sort[0] => $sort[1]));
        } else {
            $structures = $this->getDoctrine()->getManager()->getRepository('App:Structure')->findBy(array($filter[0] => $filter[1]));
        }

        if (!is_array($structures)) {
            throw $this->createNotFoundException();
        } else {
            $page = $request->query->get('page');
            if (isset($page)) {
                $perPage = $request->query->get('perPage');
                $paginator = $this->get('knp_paginator');
                if (!isset($perPage)) {
                    $perPage = 20;
                }
                $results = $paginator->paginate($structures, $page, $perPage);
                return $results;
            } else {
                return $structures;
            }
        }
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getStructure"})
     */
    public function getStructureAction($id)
    {

        $structure = $this->getDoctrine()->getRepository('App:Structure')->find($id);
        if (!is_object($structure)) {
            throw $this->createNotFoundException();
        } else {
            return $structure;
        }
    }
}
