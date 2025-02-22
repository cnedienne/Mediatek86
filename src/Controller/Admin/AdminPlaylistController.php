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
 *
 * @author emds
 */
class AdminPlaylistController extends AbstractController {
    
    // Définition des constantes pour les templates
    private const PLAYLISTS_TEMPLATE = "admin/playlist/playlists.html.twig";
    private const PLAYLIST_EDIT = "admin/playlist/playlist_edit.html.twig";
    private const PLAYLIST_ADD = "admin/playlist/playlist_add.html.twig";
    /**
     * 
     * @var PlaylistRepository
     */
    private $playlistRepository;
    
    /**
     * 
     * @var FormationRepository
     */
    private $formationRepository;
    
    /**
     * 
     * @var CategorieRepository
     */
    private $categorieRepository;    
    
    function __construct(PlaylistRepository $playlistRepository, 
            CategorieRepository $categorieRepository,
            FormationRepository $formationRespository) {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRespository;
    }
    
    /**
     * @Route("/playlists", name="playlists")
     * @return Response
     */
    #[Route('/admin/playlists', name: 'admin.playlists')]
    public function index(): Response{
        $playlists = $this->playlistRepository->findAllOrderByName('ASC');
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PLAYLISTS_TEMPLATE, [
            'playlists' => $playlists,
            'categories' => $categories            
        ]);
    }

#[Route('/admin/playlists/tri/{champ}/{ordre}', name: 'admin.playlists.sort')]
public function sort($champ, $ordre): Response {
    switch ($champ) {
        case "name":
            $playlists = $this->playlistRepository->findAllOrderByName($ordre);
            break;
        case "formationCount":
            $playlists = $this->playlistRepository->findAllOrderByAmount($ordre);
            break;
    }
    $categories = $this->categorieRepository->findAll();
    return $this->render("pages/playlists.html.twig", [
        'playlists' => $playlists,
        'categories' => $categories
    ]);
}
    

    #[Route('/admin/playlists/recherche/{champ}/{table}', name: 'admin.playlists.findallcontain')]
    public function findAllContain($champ, Request $request, $table=""): Response{
        $valeur = $request->get("recherche");
        $playlists = $this->playlistRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PLAYLISTS_TEMPLATE, [
            'playlists' => $playlists,
            'categories' => $categories,            
            'valeur' => $valeur,
            'table' => $table
        ]);
    }  

    #[Route('/admin/playlists/playlist/{id}', name: 'admin.playlists.showone')]
    public function showOne($id): Response{
        $playlist = $this->playlistRepository->find($id);
        $playlistCategories = $this->categorieRepository->findAllForOnePlaylist($id);
        $playlistFormations = $this->formationRepository->findAllForOnePlaylist($id);
        return $this->render(self::PLAYLIST_DETAIL_TEMPLATE, [
            'playlist' => $playlist,
            'playlistcategories' => $playlistCategories,
            'playlistformations' => $playlistFormations
        ]);        
    }   
    
    #[Route('/admin/playlists/delete/{id}', name: 'admin.playlist.delete')]
    public function supprimerPlayilst($id): Response{
        $playlist = $this->playlistRepository->find($id);
        if ($playlist != null && count($playlist->getFormations())===0){
            $this->playlistRepository->remove($playlist);
            $this->addFlash("success","la playlist a été supprimée avec succès");
        }
        else {
            $this->addFlash("danger","la playlist ne peut pas être supprimée car elle contient des formations");
        }
        return $this->redirectToRoute("admin.playlists"); 
    }
    
    
     #[Route('/admin/playlists/edit/{id}', name: 'admin.playlist.edit')]
    public function modifierPlayilst($id,Request $request): Response{
        $playlist = $this->playlistRepository->find($id);
        $formPlaylist = $this->createForm(PlaylistType::class,$playlist);
        $formPlaylist->handleRequest($request);
        $formationsArray = $playlist->getFormations()->toArray();
        if ($formPlaylist->isSubmitted() && $formPlaylist->isValid())
        {
            $formations = $playlist->getFormations()->toArray();
            foreach($formations as $formation){
                if (!in_array($formation,$formationsArray)){
                    $formation->setPlaylist($playlist);
                    $this->formationRepository->add($formation,true);
                }
            }
            foreach($formationsArray as $formation){
                if (!in_array($formation,$formations)){
                    $formation->setPlaylist(null);
                    $this->formationRepository->add($formation,true);
                }
            }
            $this->playlistRepository->add($playlist,true);
            $this->addFlash('success', 'La playlist '.$playlist->getName().' a bien été modifiée');
            return $this->redirectToRoute('admin.playlists');
        }
        return $this->render(SELF::PLAYLIST_EDIT,[
            'playlistForm'=>$formPlaylist->createView(),
            'playlist'=>$playlist
        ]); 
    }
    
     #[Route('/admin/playlists/add', name: 'admin.playlists.add')]
    public function addPlayilst(Request $request): Response{
        $playlist = new Playlist();
        $formPlaylist = $this->createForm(PlaylistType::class ,$playlist);
        $formPlaylist->handleRequest($request);
               if ($formPlaylist->isSubmitted() && $formPlaylist->isValid())
        {
           $this->playlistRepository->add($playlist, true);
           $this->addFlash('success','La playlist'.$playlist->getName(). 'a bien été crée');
            return $this->redirectToRoute('admin.playlists');  
        }
        return $this->render(SELF::PLAYLIST_ADD,[
            'playlistForm'=>$formPlaylist->createView(),
            
        ]); 
    }
}
