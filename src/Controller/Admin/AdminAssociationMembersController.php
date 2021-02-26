<?php

namespace App\Controller\Admin;

use App\Entity\AssociationMember;
use App\Form\AssociationMemberType;
use App\Repository\AssociationMemberRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/association-members", name="admin_association_members_")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminAssociationMembersController extends AbstractController
{
    private string $menu = 'members';

    /**
     * @Route("/", name="home")
     * @param AssociationMemberRepository $associationMemberRepository
     * @return Response
     */
    public function index(AssociationMemberRepository $associationMemberRepository, Request $request, PaginatorInterface $paginator)
    {
        // Get association members
        $query = $associationMemberRepository->findAllQuery();

        if ($request->get('q')) {
            $query
                ->innerJoin('row.user', 'u')
                ->innerJoin('row.association', 'a')
                ->where('LOWER(u.email) LIKE :search')
                ->orWhere('LOWER(a.name) LIKE :search')
                ->setParameter('search', '%' . $request->get('q') . '%');
        }

        $associationMembers = $paginator->paginate(
            $query->getQuery(),
            $request->query->getInt('page', 1),
            20
        );

        // Render the view
        return $this->render('admin/association-members/index.html.twig', [
            'associationMembers' => $associationMembers,
            'searchable' => true,
            'menu' => $this->menu
        ]);
    }

    /**
     * @Route("/new", name="new")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function new(Request $request)
    {
        // Create the form and handle request
        $associationMember = new AssociationMember();
        $form = $this->createForm(AssociationMemberType::class, $associationMember);
        $form->handleRequest($request);

        // If the form is good
        if ($form->isSubmitted() && $form->isValid()) {
            // Save the data
            $em = $this->getDoctrine()->getManager();
            $em->persist($associationMember);
            $em->flush();

            // Send a confirmation
            $this->addFlash('success', $associationMember->getUser()->getEmail() . ' has been linked to ' . $associationMember->getAssociation()->getName() . '.');

            // Redirect to the list
            return $this->redirect($this->generateUrl('admin_association_members_home'));
        }

        return $this->render('admin/association-members/new.html.twig', [
            'form' => $form->createView(),
            'menu' => $this->menu
        ]);
    }

    /**
     * @Route("/{id}/", name="edit")
     * @param AssociationMember $associationMember
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function edit(AssociationMember $associationMember, Request $request)
    {
        // Create the form form the association and the request
        $form = $this->createForm(AssociationMemberType::class, $associationMember);
        $form->handleRequest($request);

        // If the form is submitted
        if ($form->isSubmitted() && $form->isValid()) {
            // Save the data
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            // Send a confirmation
            $this->addFlash('success', $associationMember->getUser()->getEmail() . '\'s link has been edited.');

            // Redirect to the association list
            return $this->redirect($this->generateUrl('admin_association_members_home'));
        }

        // Render the view
        return $this->render('admin/association-members/edit.html.twig', [
            'form' => $form->createView(),
            'menu' => $this->menu
        ]);
    }

    /**
     * @Route("/{id}/delete", name="delete")
     * @param AssociationMember $associationMember
     * @return RedirectResponse
     */
    public function delete(AssociationMember $associationMember)
    {
        // Remove the $association from the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($associationMember);
        $entityManager->flush();

        $this->addFlash('success', $associationMember->getUser()->getEmail() . '\'s link has been deleted.');

        // Redirect to the association members list
        return $this->redirect($this->generateUrl('admin_association_members_home'));
    }
}