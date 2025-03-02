<?php

namespace App\Controller\Admin;

use App\Entity\Categorie;
use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// Déclaration de la classe AdminCategoriesController qui étend AbstractController
class AdminCategoriesController extends AbstractController
{
    // Constante pour le chemin du template des catégories
    private const CATEGORIE_TEMPLATE = 'admin/categorie/categories.html.twig';
    
    // Propriété pour le repository des catégories
    private $categorieRepository;

    // Constructeur pour injecter le repository des catégories
    public function __construct(CategorieRepository $categorieRepository)
    {
        $this->categorieRepository = $categorieRepository;
    }

    // Route pour afficher la liste des catégories
    #[Route('/admin/categories', name: 'admin.categories')]
    public function index(): Response
    {
        // Récupération de toutes les catégories
        $categories = $this->categorieRepository->findAll();
        
        // Rendu du template avec les catégories
        return $this->render(self::CATEGORIE_TEMPLATE, [
            'categories' => $categories
        ]);
    }

    // Route pour ajouter une nouvelle catégorie
    #[Route('/admin/categories/add', name: 'admin.categories.add')]
    public function add(Request $request): Response
    {
        // Récupération et nettoyage du nom de la catégorie depuis la requête
        $name = trim($request->request->get('name'));
        
        // Vérification si le nom est vide
        if (empty($name)) {
            // Ajout d'un message flash d'erreur et redirection
            $this->addFlash('error', 'Le nom de la catégorie est obligatoire');
            return $this->redirectToRoute('admin.categories');
        }
        
        // Vérification si la catégorie existe déjà
        if ($this->categorieRepository->findOneBy(['name' => $name])) {
            // Ajout d'un message flash d'erreur et redirection
            $this->addFlash('error', 'La catégorie existe déjà');
            return $this->redirectToRoute('admin.categories');
        }
        
        // Création d'une nouvelle catégorie et définition de son nom
        $categorie = new Categorie();
        $categorie->setName($name);
        
        // Ajout de la catégorie au repository
        $this->categorieRepository->add($categorie);
        
        // Ajout d'un message flash de succès et redirection
        $this->addFlash('success', 'La catégorie a été ajoutée');
        return $this->redirectToRoute('admin.categories');
    }

    // Route pour supprimer une catégorie
    #[Route('/admin/categories/delete/{id}', name: 'admin.categories.delete')]
    public function delete(Categorie $categorie): Response
    {
        // Vérification si la catégorie contient des formations
        if (count($categorie->getFormations()) > 0) {
            // Ajout d'un message flash d'erreur et redirection
            $this->addFlash('error', 'La catégorie ne peut pas être supprimée');
            return $this->redirectToRoute('admin.categories');
        } else {
            // Suppression de la catégorie du repository
            $this->categorieRepository->remove($categorie);
            // Ajout d'un message flash de succès
            $this->addFlash('success', 'La catégorie a été supprimée');
        }
        
        // Redirection vers la liste des catégories
        return $this->redirectToRoute('admin.categories');
    }
}