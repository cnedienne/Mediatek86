<?php
namespace App\Controller\Admin;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\PlaylistType;
use App\Entity\Playlist;

/**
 * Description of PlaylistsController
 */
class AdminPlaylistController extends AbstractController {
    
    // Définition des constantes pour les templates
    private const PLAYLISTS_TEMPLATE = "admin/playlist/playlists.html.twig";
    private const PLAYLIST_EDIT = "admin/playlist/playlist_edit.html.twig";
    private const PLAYLIST_ADD = "admin/playlist/playlist_add.html.twig";
    
    // Propriétés pour les repositories des playlists, formations et catégories
    private $playlistRepository;
    private $formationRepository;
    private $categorieRepository;    
    
    // Constructeur pour injecter les repositories des playlists, formations et catégories
    function __construct(PlaylistRepository $playlistRepository, 
            CategorieRepository $categorieRepository,
            FormationRepository $formationRespository) {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRespository;
    }
    
    // Route pour afficher la liste des playlists
    #[Route('/admin/playlists', name: 'admin.playlists')]
    public function index(): Response {
        // Récupération de toutes les playlists triées par nom et de toutes les catégories
        $playlists = $this->playlistRepository->findAllOrderByName('ASC');
        $categories = $this->categorieRepository->findAll();
        // Rendu du template avec les playlists et les catégories
        return $this->render(self::PLAYLISTS_TEMPLATE, [
            'playlists' => $playlists,
            'categories' => $categories            
        ]);
    }

    // Route pour trier les playlists
    #[Route('/admin/playlists/tri/{champ}/{ordre}', name: 'admin.playlists.sort')]
    public function sort($champ, $ordre): Response {
        // Tri des playlists en fonction du champ et de l'ordre
        switch ($champ) {
            case "name":
                $playlists = $this->playlistRepository->findAllOrderByName($ordre);
                break;
            case "formationCount":
                $playlists = $this->playlistRepository->findAllOrderByAmount($ordre);
                break;
        }
        $categories = $this->categorieRepository->findAll();
        // Rendu du template avec les playlists triées et les catégories
        return $this->render("pages/playlists.html.twig", [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }
    
    // Route pour rechercher des playlists
    #[Route('/admin/playlists/recherche/{champ}/{table}', name: 'admin.playlists.findallcontain')]
    public function findAllContain($champ, Request $request, $table=""): Response {
        // Récupération de la valeur de recherche depuis la requête
        $valeur = $request->get("recherche");
        // Recherche des playlists contenant la valeur
        $playlists = $this->playlistRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        // Rendu du template avec les playlists trouvées et les catégories
        return $this->render(self::PLAYLISTS_TEMPLATE, [
            'playlists' => $playlists,
            'categories' => $categories,            
            'valeur' => $valeur,
            'table' => $table
        ]);
    }  

    // Route pour afficher une playlist spécifique
    #[Route('/admin/playlists/playlist/{id}', name: 'admin.playlists.showone')]
    public function showOne($id): Response {
        // Récupération de la playlist, des catégories et des formations associées
        $playlist = $this->playlistRepository->find($id);
        $playlistCategories = $this->categorieRepository->findAllForOnePlaylist($id);
        $playlistFormations = $this->formationRepository->findAllForOnePlaylist($id);
        // Rendu du template avec les détails de la playlist
        return $this->render(self::PLAYLIST_DETAIL_TEMPLATE, [
            'playlist' => $playlist,
            'playlistcategories' => $playlistCategories,
            'playlistformations' => $playlistFormations
        ]);        
    }   
    
    // Route pour supprimer une playlist
    #[Route('/admin/playlists/delete/{id}', name: 'admin.playlist.delete')]
    public function supprimerPlayilst($id): Response {
        // Récupération de la playlist
        $playlist = $this->playlistRepository->find($id);
        // Vérification si la playlist peut être supprimée
        if ($playlist != null && count($playlist->getFormations()) === 0) {
            // Suppression de la playlist
            $this->playlistRepository->remove($playlist);
            $this->addFlash("success", "La playlist a été supprimée avec succès");
        } else {
            $this->addFlash("danger", "La playlist ne peut pas être supprimée car elle contient des formations");
        }
        // Redirection vers la liste des playlists
        return $this->redirectToRoute("admin.playlists"); 
    }
    
    // Route pour éditer une playlist
    #[Route('/admin/playlists/edit/{id}', name: 'admin.playlist.edit')]
    public function modifierPlayilst($id, Request $request): Response {
        // Récupération de la playlist
        $playlist = $this->playlistRepository->find($id);
        // Création du formulaire pour éditer la playlist
        $formPlaylist = $this->createForm(PlaylistType::class, $playlist);
        $formPlaylist->handleRequest($request);
        $formationsArray = $playlist->getFormations()->toArray();
        // Vérification si le formulaire est soumis et valide
        if ($formPlaylist->isSubmitted() && $formPlaylist->isValid()) {
            // Mise à jour des formations associées à la playlist
            $formations = $playlist->getFormations()->toArray();
            foreach ($formations as $formation) {
                if (!in_array($formation, $formationsArray)) {
                    $formation->setPlaylist($playlist);
                    $this->formationRepository->add($formation, true);
                }
            }
            foreach ($formationsArray as $formation) {
                if (!in_array($formation, $formations)) {
                    $formation->setPlaylist(null);
                    $this->formationRepository->add($formation, true);
                }
            }
            // Mise à jour de la playlist
            $this->playlistRepository->add($playlist, true);
            $this->addFlash('success', 'La playlist ' . $playlist->getName() . ' a bien été modifiée');
            // Redirection vers la liste des playlists
            return $this->redirectToRoute('admin.playlists');
        }
        // Rendu du template pour éditer la playlist
        return $this->render(self::PLAYLIST_EDIT, [
            'playlistForm' => $formPlaylist->createView(),
            'playlist' => $playlist
        ]); 
    }
    
    // Route pour ajouter une nouvelle playlist
    #[Route('/admin/playlists/add', name: 'admin.playlists.add')]
    public function addPlayilst(Request $request): Response {
        // Création d'une nouvelle playlist
        $playlist = new Playlist();
        // Création du formulaire pour ajouter la playlist
        $formPlaylist = $this->createForm(PlaylistType::class, $playlist);
        $formPlaylist->handleRequest($request);
        // Vérification si le formulaire est soumis et valide
        if ($formPlaylist->isSubmitted() && $formPlaylist->isValid()) {
            // Ajout de la playlist dans la base de données
            $this->playlistRepository->add($playlist, true);
            $this->addFlash('success', 'La playlist ' . $playlist->getName() . ' a bien été créée');
            // Redirection vers la liste des playlists
            return $this->redirectToRoute('admin.playlists');  
        }
        // Rendu du template pour ajouter une playlist
        return $this->render(self::PLAYLIST_ADD, [
            'playlistForm' => $formPlaylist->createView(),
        ]); 
    }
}