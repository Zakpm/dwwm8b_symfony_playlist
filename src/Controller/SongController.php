<?php

namespace App\Controller;

use App\Entity\Song;
use App\Form\SongFormType;
use App\Repository\SongRepository;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SongController extends AbstractController
{
    #[Route('/', name: 'song.index')]
    public function index(SongRepository $songRepository): Response
    {
        $songs = $songRepository->findAll();
        return $this->render('song/index.html.twig', compact('songs'));
    }



    /**
     * Cette méthode effectue 4 actions :
     *      - Via la méthode GET accéder la page d'ajout d'un nouveau son sur laquelle on retouve le formaulaire 
     *      - Via la méthode POST récupérer les données du formulaire, les traiter en fonction des différentes contraintes de validation mise en place
     *      - Demander à "SongRepository" de réaliser la requête d'inserstion du son en base de données
     *      - Effectuer la redirection vers la page d'accueil accompagné d'un message de succés
     * 
     *
     * @param Request $request
     * @param SongRepository $songRepository
     * 
     * @return Response
     */
    #[Route('/create', name: 'song.create', methods: ['GET', 'POST'])]
    public function create(Request $request, SongRepository $songRepository) : Response 
    {
        // Pour insérer un nouveau son en base de données

        // 1- Créer une nouvelle instance de type Song
        $song = new Song();

        /**
         * 2- a) Créer le type de formulaire du son (symfony console make:form SongFormType)
         * 2- b) Demander à SongController de créer le formulaire en se basant sur :
         *       -- Le type de formulaire
         *       -- La nouvelle instance de type Song créee 
        */   
        
        // 3- Création d'un son
        $form = $this->createForm(SongFormType::class, $song);
        
        // Le code de la ligne 52 à la ligne 65 prend effet si et seulement si les données du formaulaire ont été envoyées
        // 4- Associer les données du formulaire à l'objet $form 
        $form->handleRequest($request);


        
         // 5- Si le formulaire est soumis et qu'il est valide 
        if ( $form->isSubmitted() && $form->isValid() ){

            $data = $request->request->all();
            $score = round($data['song_form']['score'], 1);

            // 6- Initialiser manuellement les propriétés de l'objet dont les données ne proviennent pas du formulaire
            $song->setScore($score);
            $song->setCreatedAt(new \DateTimeImmutable('now'));
            $song->setUpdatedAt(new \DateTimeImmutable('now'));

            /**
             * 7- Demander au gestionnaire de requ$etes de la table "song (songRepository)
             * d'effectuer la reuête d'insertion du nouveau son 
             * Cette tâche est effectuée grâce au "EntityMnager"
             */
            $songRepository->save($song, true);

            /**
             * 8- Afin d'indiquer à l'utilisateur que sa requête a été effetuée avec succès,
             * préparer un message flash
             */
            $this->addFlash("success", "Le son a été ajouté avec succès.");

            /**
             * 9- Rediriger l'utlisateur vers la page d'accueil de l'application
             * afin qu'il puisse directement consulter la liste 
             */
            return $this->redirectToRoute("song.index");
        }

        /**
         * Retourner la page d'ajout d'un nouveau son accompagné du formulaire
         */
        return $this->render('song/create.html.twig', [
            "form" => $form->createView()
        ]);
    }


    /**
     *  Cette méthode effectue 4 actions : 
     *      - Via la méthode GET, accéder à la page de modification d'un son, sur laquelle on retrouve le formulaire
     *      - Via la méthode POST, récupérer les données du formulaire, les traiter en fonction des différentes contraintes de validation mise en place
     *      - Demander au "SongRepository" de réaliser la requête de modification du son en base de données
     *      - Effectuer la redirection vers la page d'accueil accompagnée d'un message de succès 
     *  
     * @param Request $request
     * @param SongRepository $songRepository
     * @return Response
     */
    #[Route('/edit/{id<[0-9]+>}', name: 'song.edit', methods: ['GET', 'POST'])]
    public function edit(Song $song, SongRepository $songRepository, Request $request) : Response
    {
        
        // Création du formulaire de modification du son
        $form = $this->createForm(SongFormType::class, $song);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $data = $request->request->all();
            $score = round($data['song_form']['score'], 1);

            $song->setScore($score);
            $song->setUpdatedAt(new \DateTimeImmutable('now'));

            $songRepository->save($song, true);

            $this->addFlash('success', $song->getTitle() . " a été modifié avec succès!");
            return $this->redirectToRoute('song.index');

        }

        
        //Retourner la page de modification du son existant, accompagnée de la partie visible du formulaire
        return $this->render("song/edit.html.twig", [
            "form" => $form->createView(),
            "song" => $song
        ]);
    }


    #[Route('/delate/{id<[0-9]+>}', name: 'song.delate', methods: ['POST'])]
    public function delate(Song $song, Request $request, SongRepository $songRepository) : Response
    {
        if ( $this->isCsrfTokenValid('song_' . $song->getId(), $request->request->get('_csrf_token') )){
            $songRepository->remove($song, true);
            $this->addFlash("success", "Le son a été supprimé avec succès !");
        }

        return $this->redirectToRoute("song.index");
    }

}
