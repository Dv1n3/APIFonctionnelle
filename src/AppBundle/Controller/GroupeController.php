<?php
/**
 * Created by PhpStorm.
 * User: dvine
 * Date: 11/04/2018
 * Time: 11:16
 */

namespace AppBundle\Controller;

use AppBundle\Form\GroupeType;
use AppBundle\Entity\Groupe;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as Rest;

class GroupeController extends Controller
{
    /**
     * @Rest\View()
     * @Rest\Get("/groups")
     *
     * @param Request $request
     * @return View|JsonResponse
     */
    public function getGroupsAction()
    {
        $groups = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:Groupe')
            ->findAll();

        if (empty($groups)) {
            return \FOS\RestBundle\View\View::create(['message' => 'Groupe not found'], Response::HTTP_NOT_FOUND);
        }
        return $groups;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/groups/{id}")
     *
     * @param $id
     * @param Request $request
     * @return View|JsonResponse
     */
    public function getGroupAction(Request $request)
    {
        $group = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:Groupe')
            ->find($request->get('id'));

        if (empty($group)) {
            return \FOS\RestBundle\View\View::create(['message' => 'Groupe not found'], Response::HTTP_NOT_FOUND);
        }
        return $group;
    }

    /**
     * @Rest\View(StatusCode = 201)
     * @Rest\Post("/groups")
     *
     * @param Request $request
     * @return Groupe|\Symfony\Component\Form\FormInterface
     */
    public function postGroupAction(Request $request)
    {
        $groupe = new Groupe();
        $form = $this->createForm(GroupeType::class, $groupe);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($groupe);
            $em->flush();

            return $groupe;
        } else {
            return $form;
        }
    }


    /**
     * @Rest\View(StatusCode = 201)
     * @Rest\Put("/groups/{id}")
     */
    public function putGroupAction(Request $request)
    {
        return $this->updateGroupAction($request, true);
    }

    /**
     * @Rest\View(StatusCode = 201)
     * @Rest\Patch("/groups/{id}")
     */
    public function patchGroupAction(Request $request)
    {
        return $this->updateGroupAction($request, false);
    }

    /**
     * @param Request $request
     * @return Groupe|null|object|\Symfony\Component\Form\FormInterface|JsonResponse
     */
    public function updateGroupAction(Request $request, $clearmissing)
    {
        $groupe = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:Groupe')
            ->find($request->get('id'));

        if (empty($groupe)) {
            return \FOS\RestBundle\View\View::create(['message' => 'Groupe not found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(GroupeType::class, $groupe);

        $form->submit($request->request->all(), $clearmissing);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->merge($groupe);
            $em->flush();
            return $groupe;
        } else {
            return $form;
        }
    }


}
