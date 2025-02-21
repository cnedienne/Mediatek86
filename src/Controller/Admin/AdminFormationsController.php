<?php

namespace App\Controller\Admin;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminFormationsController extends AbstractController
{
    private $formationRepository;
    private $categorieRepository;

    // Constantes pour les templates
    private const PAGE_FORMATIONS = 'admin/formations.html.twig';
    private const PAGE_FORMATION = 'admin/formation.html.twig';
    private const PAGE_FORMATION_EDIT = 'admin/formation_edit.html.twig';
    private const PAGE_FORMATION_AJOUT = 'admin/formation_ajout.html.twig';
    public function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository)
    {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository = $categorieRepository;
    }
    #[Route("/admin", name: "admin.formations")]
    public function index(): Response
{
    return $this->render(self::PAGE_FORMATIONS, [
        'formations' => $this->formationRepository->findAll(),
        'categories' => $this->categorieRepository->findAll(),
    ]); 
       
    }

       
    #[Route('/admin/formations/tri/{champ}/{ordre}/{table}', name: 'admin.formations.sort')]
    public function sort(string $champ, string $ordre, string $table = ''): Response
    {
        return $this->render(self::PAGE_FORMATIONS, [
            'formations' => $this->formationRepository->findAllOrderBy($champ, $ordre, $table),
            'categories' => $this->categorieRepository->findAll(),
        ]);
    }


    #[Route("/admin/formations/recherche/{champ}/{table}", name: 'admin.formations.findAllContain')]
    public function findAllContain(string $champ, Request $request, string $table = ''): Response
    {
        $valeur = $request->get("recherche");
        [
        'champ' => $champ,
        'table' => $table,
        'valeur' => $valeur,
        'formations' => $this->formationRepository->findByContainValue($champ, $valeur, $table)
        ];
        return $this->render(self::PAGE_FORMATIONS, [
            'formations' => $this->formationRepository->findByContainValue($champ, $valeur, $table),
            'categories' => $this->categorieRepository->findAll(),
            'valeur' => $valeur,
            'table' => $table,
        ]);
    }

    #[Route("/admin/formations/formation/{id}", name: 'admin.showone')]
    public function showOne(int $id): Response
    {
        return $this->render(self::PAGE_FORMATION, [
            'formation' => $this->formationRepository->find($id),
        ]);
    }

   
    #[Route("/admin/formations/suppr/{id}", name: 'admin.formation.suppr')]
    public function delete(Formation $formation): Response
    {
        if (!$formation) {
        // Ajoute un message flash si la formation n'existe pas
            $this->addFlash('danger', "La formation n'existe pas.");
        // Redirige vers la liste des formations
            return $this->redirectToRoute('admin.formations');
    }
    // Suppression de la formation de la base de données
        $this->formationRepository->remove($formation, true);
    // Ajoute un message flash pour confirmer la suppression
        $this->addFlash('danger', "La formation '{$formation->getTitle()}' a bien été supprimée.");
    // Redirige vers la page de liste des formations
        return $this->redirectToRoute('admin.formations');
    }


 
    #[Route("/admin/formation/edit/{id}", name:"admin.formation.edit")]
    public function edit(Formation $formation, Request $request): Response
    {
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->formationRepository->add($formation, true);
            $this->addFlash('success', "La formation '{$formation->getTitle()}' a bien été mise à jour.");
            return $this->redirectToRoute('admin.formations');
        }

        return $this->render(self::PAGE_FORMATION_EDIT, [
            'formFormation' => $form->createView(),
            'formation' => $formation
        ]);
    }


    #[Route("/admin/formation/ajout", name:"admin.formation.ajout")]
    
    public function add(Request $request): Response
    {
        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->formationRepository->add($formation, true);
            $this->addFlash('success', "La formation '{$formation->getTitle()}' a bien été ajoutée.");
            return $this->redirectToRoute('admin.formations');
        }

        return $this->render(self::PAGE_FORMATION_AJOUT, [
            'formFormation' => $form->createView(),
        ]);
    }
}
