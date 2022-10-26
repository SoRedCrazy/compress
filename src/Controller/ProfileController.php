<?php

namespace App\Controller;

use App\Repository\ResourceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use App\Entity\User;
use App\Form\UserFormType;
use App\Form\ResourceFormType;
use App\Entity\Resource;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(Request $request, UserRepository $userrepository,ResourceRepository $resourcerepository ): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userrepository->save($user, true);
        }
        return $this->render('profile/index.html.twig', [
            'liens' => $resourcerepository->findByUserAndFilter($user->getid(),$request->request->all()),
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    #[Route('/profile/edit/{url}', name: 'app_edit_resource')]
    public function modifresource(Resource $resource ,Request $request, ResourceRepository $resourcerepository ): Response
    {
        $form = $this->createForm(ResourceFormType::class, $resource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $resource->setDateModified();
            $resourcerepository->save($resource, true);
            return $this->redirectToRoute('app_profile');
        }
        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
