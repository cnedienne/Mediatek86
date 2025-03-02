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

// Déclaration de la classe AdminFormationsController qui étend AbstractController
class AdminFormationsController extends AbstractController
{
    // Propriétés pour les repositories des formations et des catégories
    private $formationRepository;
    private $categorieRepository;

    // Constantes pour les templates
    private const PAGE_FORMATIONS = 'admin/formations.html.twig';
    private const PAGE_FORMATION = 'admin/formation.html.twig';
    private const PAGE_FORMATION_EDIT = 'admin/formation_edit.html.twig';
    private const PAGE_FORMATION_AJOUT = 'admin/formation_ajout.html.twig';

    // Constructeur pour injecter les repositories des formations et des catégories
    public function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository)
    {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository = $categorieRepository;
    }

    // Route pour afficher la liste des formations
    #[Route("/admin", name: "admin.formations")]
    public function index(): Response
    {
        // Rendu du template avec les formations et les catégories
        return $this->render(self::PAGE_FORMATIONS, [
            'formations' => $this->formationRepository->findAll(),
            'categories' => $this->categorieRepository->findAll(),
        ]);
    }

    // Route pour trier les formations
    #[Route('/admin/formations/tri/{champ}/{ordre}/{table}', name: 'admin.formations.sort')]
    public function sort(string $champ, string $ordre, string $table = ''): Response
    {
        // Rendu du template avec les formations triées et les catégories
        return $this->render(self::PAGE_FORMATIONS, [
            'formations' => $this->formationRepository->findAllOrderBy($champ, $ordre, $table),
            'categories' => $this->categorieRepository->findAll(),
        ]);
    }

    // Route pour rechercher des formations
    #[Route("/admin/formations/recherche/{champ}/{table}", name: 'admin.formations.findAllContain')]
    public function findAllContain(string $champ, Request $request, string $table = ''): Response
    {
        // Récupération de la valeur de recherche depuis la requête
        $valeur = $request->get("recherche");

        // Rendu du template avec les formations trouvées et les catégories
        return $this->render(self::PAGE_FORMATIONS, [
            'formations' => $this->formationRepository->findByContainValue($champ, $valeur, $table),
            'categories' => $this->categorieRepository->findAll(),
            'valeur' => $valeur,
            'table' => $table,
        ]);
    }

    // Route pour afficher une formation spécifique
    #[Route("/admin/formations/formation/{id}", name: 'admin.showone')]
    public function showOne(int $id): Response
    {
        // Rendu du template avec la formation spécifique
        return $this->render(self::PAGE_FORMATION, [
            'formation' => $this->formationRepository->find($id),
        ]);
    }

    // Route pour supprimer une formation
    #[Route("/admin/formations/suppr/{id}", name: 'admin.formation.suppr')]
    public function delete(Formation $formation): Response
    {
        // Vérification si la formation existe
        if (!$formation) {
            // Ajout d'un message flash si la formation n'existe pas
            $this->addFlash('danger', "La formation n'existe pas.");
            // Redirection vers la liste des formations
            return $this->redirectToRoute('admin.formations');
        }

        // Suppression de la formation de la base de données
        $this->formationRepository->remove($formation, true);
        // Ajout d'un message flash pour confirmer la suppression
        $this->addFlash('danger', "La formation '{$formation->getTitle()}' a bien été supprimée.");
        // Redirection vers la page de liste des formations
        return $this->redirectToRoute('admin.formations');
    }

    // Route pour éditer une formation
    #[Route("/admin/formation/edit/{id}", name:"admin.formation.edit")]
    public function edit(Formation $formation, Request $request): Response
    {
        // Création du formulaire pour éditer la formation
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        // Vérification si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Mise à jour de la formation dans la base de données
            $this->formationRepository->add($formation, true);
            // Ajout d'un message flash pour confirmer la mise à jour
            $this->addFlash('success', "La formation '{$formation->getTitle()}' a bien été mise à jour.");
            // Redirection vers la liste des formations
            return $this->redirectToRoute('admin.formations');
        }

        // Rendu du template pour éditer la formation
        return $this->render(self::PAGE_FORMATION_EDIT, [
            'formFormation' => $form->createView(),
            'formation' => $formation
        ]);
    }

    // Route pour ajouter une nouvelle formation
    #[Route("/admin/formation/ajout", name:"admin.formation.ajout")]
    public function add(Request $request): Response
    {
        // Création d'une nouvelle formation
        $formation = new Formation();
        // Création du formulaire pour ajouter la formation
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        // Vérification si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Ajout de la formation dans la base de données
            $this->formationRepository->add($formation, true);
            // Ajout d'un message flash pour confirmer l'ajout
            $this->addFlash('success', "La formation '{$formation->getTitle()}' a bien été ajoutée.");
            // Redirection vers la liste des formations
            return $this->redirectToRoute('admin.formations');
        }

        // Rendu du template pour ajouter une formation
        return $this->render(self::PAGE_FORMATION_AJOUT, [
            'formFormation' => $form->createView(),
        ]);
    }
}