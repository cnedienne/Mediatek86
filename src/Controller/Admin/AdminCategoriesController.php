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

class AdminCategoriesController extends AbstractController
{
    private const CATEGORIE_TEMPLATE = 'admin/categorie/categories.html.twig';
    private $categorieRepository;
    public function __construct(CategorieRepository $categorieRepository)
    {
        $this->categorieRepository = $categorieRepository;
    }
    #[Route('/admin/categories', name: 'admin.categories')]
    public function index(): Response
    {
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::CATEGORIE_TEMPLATE, [
            'categories' => $categories
        ]);
    }
    #[Route('/admin/categories/add', name: 'admin.categories.add')]
    public function add(Request $request): Response
    {
        $name = trim($request->request->get('name'));
        if (empty($name)) {
            $this->addFlash('error', 'Le nom de la catégorie est obligatoire');
            return $this->redirectToRoute('admin.categories');
        }
        if ($this->categorieRepository->findOneBy(['name' => $name])) {
            $this->addFlash('error', 'La catégorie existe déjà');
            return $this->redirectToRoute('admin.categories');
        }
        $categorie = new Categorie();
        $categorie->setName($name);
        $this->categorieRepository->add($categorie);
        $this->addFlash('success', 'La catégorie a été ajoutée');
        return $this->redirectToRoute('admin.categories');
    }
    #[Route('/admin/categories/delete/{id}', name: 'admin.categories.delete')]
    public function delete(Categorie $categorie): Response
    {
        if (count($categorie->getFormations()) > 0) {
            $this->addFlash('error', 'La catégorie ne peut pas être supprimée');
            return $this->redirectToRoute('admin.categories');
        } else {
            $this->categorieRepository->remove($categorie);
            $this->addFlash('success', 'La catégorie a été supprimée');
        }
        return $this->redirectToRoute('admin.categories');
    }
}
