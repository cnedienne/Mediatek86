<?php
namespace App\Controller;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controleur des formations
 */
class FormationsController extends AbstractController {

    // Définition des constantes pour les templates
    private const FORMATION_TEMPLATE = "pages/formations.html.twig"; // Chemin pour afficher les formations
    private const FORMATION_DETAIL_TEMPLATE = "pages/formation.html.twig"; // Chemin pour le détail d'une formation
    private const FORMATION_EDIT_TEMPLATE = "admin/formation_edit.html.twig"; // Chemin pour l'édition d'une formation

    private $formationRepository;
    private $categorieRepository;

    /**
     * Constructeur avec injection des repositories
     */
    public function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository) {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository= $categorieRepository;
    }
    
    // Route pour afficher toutes les formations
    #[Route('/formations', name: 'formations')]
    public function index(): Response {
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::FORMATION_TEMPLATE, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }

    // Route pour trier les formations
    #[Route('/formations/tri/{champ}/{ordre}/{table}', name: 'formations.sort')]
    public function sort($champ, $ordre, $table=""): Response {
        $formations = $this->formationRepository->findAllOrderBy($champ, $ordre, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::FORMATION_TEMPLATE, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }     

    // Route pour rechercher dans les formations
    #[Route('/formations/recherche/{champ}/{table}', name: 'formations.findAllContain')]
    public function findAllContain($champ, Request $request, $table=""): Response {
        $valeur = $request->get("recherche");
        $formations = $this->formationRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::FORMATION_TEMPLATE, [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }  

    // Route pour afficher le détail d'une formation
    #[Route('/formations/formation/{id}', name: 'formations.showone')]
    public function showOne($id): Response {
        $formation = $this->formationRepository->find($id);

        if (!$formation) {
            throw $this->createNotFoundException("Formation avec l'ID {$id} non trouvée.");
        }

        return $this->render(self::FORMATION_DETAIL_TEMPLATE, [
            'formation' => $formation
        ]);        
    }
    
    // Route pour éditer une formation
    #[Route('/admin/formations/edit/{id}', name: 'formations.edit')]
    public function edit($id, Request $request): Response {
        $formation = $this->formationRepository->find($id);

        if (!$formation) {
            throw $this->createNotFoundException("Formation avec l'ID {$id} non trouvée.");
        }

        // Création du formulaire
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        // Validation et sauvegarde
        if ($form->isSubmitted() && $form->isValid()) {
            $this->formationRepository->add($formation, true);

            $this->addFlash('success', "La formation '{$formation->getTitle()}' a été mise à jour avec succès !");
            return $this->redirectToRoute('formations');
        }

        // Rendu de la vue pour modifier
        dd(SELF::FORMATION_EDIT_TEMPLATE);
        return $this->render(self::FORMATION_EDIT_TEMPLATE, [
            'formFormation' => $form->createView(),
            'formation' => $formation
        ]);
    }
}
