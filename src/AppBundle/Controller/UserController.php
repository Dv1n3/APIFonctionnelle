<?php
/**
 * Created by PhpStorm.
 * User: dvine
 * Date: 11/04/2018
 * Time: 11:02
 */

namespace AppBundle\Controller;


use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class UserController extends Controller
{
    /**
     * @Rest\View()
     * @Rest\Get("/users")
     *
     * @param Request $request
     * @return View|JsonResponse
     */
    public function getUsersAction()
    {
        $users = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:User')
            ->findAll();

        if (empty($users)) {
            return \FOS\RestBundle\View\View::create(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
        return $users;

    }

    /**
     * @Rest\View()
     * @Rest\Get("/users/{id}")
     *
     * @param $id
     * @param Request $request
     * @return View|JsonResponse
     */
    public function getUserAction(Request $request)
    {
        $user = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:User')
            ->find($request->get('id'));

        if (empty($user)) {
            return \FOS\RestBundle\View\View::create(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
        return $user;
    }


    /**
     * @Rest\View(StatusCode = 201)
     * @Rest\Post("/users")
     *
     * @param Request $request
     */
    public function postUserAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $user;
        } else {
            return $form;
        }
    }

    /**
     * #Modification Totale de User
     * @Rest\View(StatusCode = 201)
     * @Rest\Put("/users/{id}")
     */
    public function putUserAction(Request $request)
    {
        return $this->updateUserAction($request, true);
    }

    /**
     * #Modification partielle de User
     * @Rest\View(StatusCode = 201)
     * @Rest\Patch("/users/{id}")
     */
    public function patchPlaceAction(Request $request)
    {
        return $this->updateUserAction($request, false);
    }


    public function updateUserAction(Request $request, $clearmissing)
    {
        $user = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:User')
            ->find($request->get('id'));

        if (empty($user)) {
            return \FOS\RestBundle\View\View::create(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(UserType::class, $user);

        $form->submit($request->request->all(), $clearmissing);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->merge($user);
            $em->flush();
            return $user;
        } else {
            return $form;
        }
    }
}