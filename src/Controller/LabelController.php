<?php

namespace App\Controller;

use App\Entity\Label;
use App\Form\LabelType;
use App\Service\LabelService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LabelController extends AbstractController
{
    private $labelService;

    public function __construct(LabelService $labelService)
    {
        $this->labelService = $labelService;
    }

    
    /**
     * @Route("/labels", name="label_all")
     * @IsGranted("ROLE_ADMIN")
     */
    public function all(): Response
    {
        $labels = $this->labelService->findAll();

        return $this->render('administration/label/all.html.twig', [
            'labels' => $labels
        ]);
    }


    /**
     * @Route("/label/create", name="label_create")
     * @IsGranted("ROLE_ADMIN")
     */
    public function create(Request $request): Response
    {
        $label = new Label();

        $form = $this->createForm(LabelType::class, $label);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->labelService->create($label);
            $this->addFlash('success', 'Le label a correctement été importé');
        }

        return $this->render('administration/label/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/label/edit/{id<\d+>}", name="label_edit")
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, $id): Response
    {
        $label = $this->labelService->find($id);
        if (!$label) {
            $this->addFlash('danger', 'Ce label n\'existe pas');
            return $this->redirectToRoute('label_all');
        }

        $form = $this->createForm(LabelType::class, $label);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->labelService->create($label, true);
            $this->addFlash('success', 'Le label a correctement été importé');
        }

        return $this->render('administration/label/edit.html.twig', [
            'form' => $form->createView(),
            'label' => $label
        ]);
    }


    /**
     * @Route("/label/delete/{id<\d+>}", name="label_delete")
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, $id): Response
    {
        $label = $this->labelService->find($id);
        if (!$label) {
            $this->addFlash('danger', 'Ce label n\'existe pas');
            return $this->redirectToRoute('label_all');
        }

        $this->labelService->delete($label);
        $this->addFlash('success', 'Le label a correctement été supprimé');

        return $this->redirectToRoute('label_all');
    }
}
