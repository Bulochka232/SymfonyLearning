<?php

namespace App\Controller;

use App\Entity\Cat;
use App\Form\CatType;
use App\Repository\CatRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cat')]
class CatController extends AbstractController
{
    #[Route('/', name: 'app_cat_index', methods: ['GET', 'POST'])]
    public function index(Request $request, CatRepository $catRepository): Response
    {
        if ($request->getMethod() == 'POST') {
            $filters = $request->request->all();

            $cats = $catRepository->findBy([
                'color' => $filters['color'],
                'sex' => $filters['sex'] == '' ? null : $filters['sex'],
                'age' => $filters['age'],
            ]);
        }else {
            $cats = $catRepository->all();
        }


        $params = [
            'cats' => $cats,
            'sex' => ["мужской", "женский"]
        ];

        return $this->render('cat/index.html.twig', $params);
    }

    #[Route('/new', name: 'app_cat_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CatRepository $catRepository): Response
    {
        $cat = new Cat();
        $form = $this->createForm(CatType::class, $cat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $catRepository->save($cat, true);

            return $this->redirectToRoute('app_cat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('cat/new.html.twig', [
            'cat' => $cat,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cat_show', methods: ['GET'])]
    public function show(Cat $cat): Response
    {
        return $this->render('cat/show.html.twig', [
            'cat' => $cat,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cat_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cat $cat, CatRepository $catRepository): Response
    {
        $form = $this->createForm(CatType::class, $cat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $catRepository->save($cat, true);

            return $this->redirectToRoute('app_cat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('cat/edit.html.twig', [
            'cat' => $cat,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cat_delete', methods: ['POST'])]
    public function delete(Request $request, Cat $cat, CatRepository $catRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $cat->getId(), $request->request->get('_token'))) {
            $catRepository->remove($cat, true);
        }

        return $this->redirectToRoute('app_cat_index', [], Response::HTTP_SEE_OTHER);
    }
}
