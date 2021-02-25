<?php

namespace App\Controller\Admin;

use App\Entity\Association;
use App\Form\AssociationType;
use App\Repository\AssociationRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/associations", name="admin_associations_")
 */
class AdminAssociationsController extends AbstractController
{
    private string $menu = 'associations';

    /**
     * @Route("/", name="home")
     * @param AssociationRepository $associationRepository
     * @param $request
     * @return Response
     */
    public function index(AssociationRepository $associationRepository, Request $request, PaginatorInterface $paginator)
    {
        // Get associations
        $query = $associationRepository->findAllQuery();

        if ($request->get('q')) {
            $query->where('LOWER(row.name) LIKE :search')
                ->setParameter('search', '%' . $request->get('q') . '%');
        }

        $associations = $paginator->paginate(
            $query->getQuery(),
            $request->query->getInt('page', 1),
            20
        );

        // Render the view
        return $this->render('admin/associations/index.html.twig', [
            'associations' => $associations,
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
        $association = new Association();
        $form = $this->createForm(AssociationType::class, $association);
        $form->handleRequest($request);

        // If the form is good
        if ($form->isSubmitted() && $form->isValid()) {
            // Save the data
            $em = $this->getDoctrine()->getManager();
            $em->persist($association);
            $em->flush();

            // Send a confirmation
            $this->addFlash('success', 'Association <strong>' . $association->getName() . '</strong> has been created.');

            // Redirect to the webhooks list
            return $this->redirect($this->generateUrl('admin_associations_home'));
        }

        return $this->render('admin/associations/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/", name="edit")
     * @param Association $association
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function edit(Association $association, Request $request)
    {
        // Create the form form the association and the request
        $form = $this->createForm(AssociationType::class, $association);
        $form->handleRequest($request);

        // If the form is submitted
        if ($form->isSubmitted() && $form->isValid()) {
            // Save the data
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            // Send a confirmation
            $this->addFlash('success', 'Association <strong>' . $association->getName() . '</strong> has been edited.');

            // Redirect to the association list
            return $this->redirect($this->generateUrl('admin_associations_home'));
        }

        // Render the view
        return $this->render('admin/associations/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="delete")
     * @param Association $association
     * @return RedirectResponse
     */
    public function delete(Association $association)
    {
        // Remove the $association from the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($association);
        $entityManager->flush();

        $this->addFlash('danger', 'Association <strong>' . $association->getName() . '</strong> has been deleted.');

        // Redirect to the association list
        return $this->redirect($this->generateUrl('admin_associations_home'));
    }
}