<?php

namespace App\Controller\Admin;

use App\Entity\WebHook;
use App\Form\WebHookType;
use App\Repository\WebHookRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/webhooks", name="admin_webhooks_")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminWebHooksController extends AbstractController
{
    private string $menu = 'webhooks';

    /**
     * @Route("/", name="home")
     * @param WebHookRepository $webHookRepository
     * @return Response
     */
    public function index(WebHookRepository $webHookRepository, Request $request, PaginatorInterface $paginator): Response
    {
        // Get webhooks
        $query = $webHookRepository->findAllQuery();

        if ($request->get('q')) {
            $query->where('LOWER(row.name) LIKE :search')
                ->orWhere('LOWER(row.url) LIKE :search')
                ->setParameter('search', '%' . $request->get('q') . '%');
        }

        $webhooks = $paginator->paginate(
            $query->getQuery(),
            $request->query->getInt('page', 1),
            20
        );

        // Render the view
        return $this->render('admin/webhooks/index.html.twig', [
            'webhooks' => $webhooks,
            'searchable' => true,
            'menu' => $this->menu
        ]);
    }

    /**
     * @Route("/new", name="new")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request)
    {
        // Create the form and handle request
        $webhook = new WebHook();
        $form = $this->createForm(WebHookType::class, $webhook);
        $form->handleRequest($request);

        // If the form is good
        if ($form->isSubmitted() && $form->isValid()) {
            // Save the data
            $em = $this->getDoctrine()->getManager();
            $em->persist($webhook);
            $em->flush();

            // Send a confirmation
            $this->addFlash('success', 'Webhook ' . $webhook->getName() . ' has been created.');

            // Redirect to the webhooks list
            return $this->redirect($this->generateUrl('admin_webhooks_home'));
        }

        // Render the view
        return $this->render('admin/webhooks/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/", name="edit")
     * @param WebHook $webhook
     * @param Request $request
     * @return Response
     */
    public function edit(WebHook $webhook, Request $request)
    {
        // Create the form form the user and the request
        $form = $this->createForm(WebHookType::class, $webhook);
        $form->handleRequest($request);

        // If the form is submitted
        if ($form->isSubmitted() && $form->isValid()) {
            // Save the data
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            // Send a confirmation
            $this->addFlash('success', 'Webhook ' . $webhook->getName() . ' has been edited.');

            // Redirect to the webhook list
            return $this->redirect($this->generateUrl('admin_webhooks_home'));
        }

        // Render the view
        return $this->render('admin/webhooks/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/delete", name="delete")
     * @param WebHook $webHook
     * @return RedirectResponse
     */
    public function delete(WebHook $webHook)
    {
        // Remove the webhook from the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($webHook);
        $entityManager->flush();

        $this->addFlash('success', 'Webhook ' . $webHook->getName() . ' has been deleted.');

        // Redirect to the webhooks list
        return $this->redirect($this->generateUrl('admin_webhooks_home'));
    }
}
