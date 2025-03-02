<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// Déclaration de la classe CategoriesController qui étend AbstractController
class CategoriesController extends AbstractController
{
    // Constante pour le chemin du template des catégories
    private const CATEGORIE_TEMPLATE = 'pages/categories.html.twig';
    
    // Propriété pour le repository des catégories
    private $categorieRepository;

    // Constructeur pour injecter le repository des catégories
    public function __construct(CategorieRepository $categorieRepository)
    {
        $this->categorieRepository = $categorieRepository;
    }

    // Route pour afficher la liste des catégories
    #[Route('/categories', name: 'categories')]
    public function index(): Response {
        // Récupération de toutes les catégories
        $categories = $this->categorieRepository->findAll();
        
        // Rendu du template avec les catégories
        return $this->render(self::CATEGORIE_TEMPLATE, [
            'categories' => $categories
        ]);
    }
}