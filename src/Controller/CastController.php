<?php

namespace App\Controller;

use App\Entity\Cast;
use App\Entity\User;
use App\Form\CastType;
use App\Repository\AssociationMemberRepository;
use App\Repository\WebHookRepository;
use App\Utils\WebhooksSender;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CastController extends AbstractController
{
    /**
     * @Route("/cast", name="cast")
     * @param Request $request
     * @param AssociationMemberRepository $associationMemberRepository
     * @param WebHookRepository $webHookRepository
     * @return Response
     * @throws \Exception
     * @IsGranted("ROLE_USER")
     */
    public function index(Request $request, AssociationMemberRepository $associationMemberRepository,
                          WebHookRepository $webHookRepository)
    {
        /** @var User $user */
        $user = $this->getUser();
        $cast = (new Cast())
            ->setAuthor($user)
            ->setDate(new \DateTimeImmutable());

        // Get the associations of the users
        $allowed_associations = $this->getAssociations($user, $associationMemberRepository);

        // Check if there is some associations
        if (count($allowed_associations) == 0)
            $this->addFlash('danger', "Il semblerait que vous ne fassiez pas partie d'association");

        $form = $this->createForm(CastType::class, $cast, array(
            'associations' => $allowed_associations,
            'webhooks' => $webHookRepository->findAll(),
            'disabled' => count($allowed_associations) == 0
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $webhookSender = (new WebhooksSender())
                ->setDescription($cast->getDescription())
                ->setImage($cast->getImage())
                ->setAuthor($cast->getAssociation()->getName(), $cast->getAssociation()->getLogo());

            foreach ($cast->getWebhooks() as $webhook) {
                $webhookSender->send($webhook->getUrl());
            }
        }

        // Render the view
        return $this->render('cast/index.html.twig', [
            'form' => $form->createView(),
            'disabled' => count($allowed_associations) == 0
        ]);
    }

    private function getAssociations(User $user, AssociationMemberRepository $associationMemberRepository)
    {
        $associationMembers = $associationMemberRepository->findBy([
            'user' => $user
        ]);

        $associations = [];

        foreach ($associationMembers as $associationMember) {
            $associations[] = $associationMember->getAssociation();
        }
        return $associations;
    }
}
