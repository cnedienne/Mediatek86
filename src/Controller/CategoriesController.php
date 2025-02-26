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

class CategoriesController extends AbstractController
{
private const CATEGORIE_TEMPLATE = 'pages/categories.html.twig';
    private $categorieRepository;
    public function __construct(CategorieRepository $categorieRepository)
    {
        $this->categorieRepository = $categorieRepository;
    }
    #[Route('/categories', name: 'categories')]
    public function index(): Response {
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::CATEGORIE_TEMPLATE, [
            'categories' => $categories
        ]);
    }
}