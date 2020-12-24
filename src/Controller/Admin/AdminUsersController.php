<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * @Route("/admin/users", name="admin_users_")
 */
class AdminUsersController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @param UserRepository $userRepository
     * @return Response
     */
    public function index(UserRepository $userRepository): Response
    {
        // Get users
        $users = $userRepository->findAll();

        // Render the view
        return $this->render('admin/users/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/{id}/", name="edit")
     * @param User $user
     * @param Request $request
     * @return Response
     */
    public function edit(User $user, Request $request)
    {
        // Create the form form the user and the request
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        // If the form is submitted
        if ($form->isSubmitted() && $form->isValid()) {
            // Save the data
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            $this->addFlash('success', 'User <strong>' . $user->getEmail() . '</strong> has been edited.');

            // Redirect to the user list
            return $this->redirect($this->generateUrl('admin_users_home'));
        }

        // Render the view
        return $this->render('admin/users/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route("/{id}/delete", name="delete")
     * @param User $user
     * @return RedirectResponse
     */
    public function delete(User $user)
    {
        // Remove the user from the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('danger', 'User <strong>' . $user->getEmail() . '</strong> has been deleted.');

        // Redirect to the user list
        return $this->redirect($this->generateUrl('admin_users_home'));
    }

    /**
     * @Route("/{id}/log", name="login_as")
     * @param SessionInterface $session
     * @param User $user
     * @return RedirectResponse
     */
    public function loginAs(SessionInterface $session, User $user)
    {
        $firewallName = 'main';
        $firewallContext = $firewallName;
        $token = new UsernamePasswordToken($user, null, $firewallName, $user->getRoles());
        $session->set('_security_' . $firewallContext, serialize($token));
        $session->save();

        return $this->redirect('/');
    }
}
