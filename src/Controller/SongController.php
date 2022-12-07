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
    public function index(): Response
    {
        return $this->render('song/index.html.twig');
    }

    #[Route('/create', name: 'song.create')]
    public function create(Request $request, SongRepository $songRepository) : Response 
    {
        $song = new Song();
        $form = $this->createForm(SongFormType::class, $song);
        
        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ){

            $song->setCreatedAt(new \DateTimeImmutable('now'));
            $song->setUpdatedAt(new \DateTimeImmutable('now'));
            $songRepository->save($song, true);
            $this->addFlash("success", "Le son a été ajouté avec succès.");
            return $this->redirectToRoute("song.index");
        }
        
        return $this->render('song/create.html.twig', [
            "form" => $form->createView()
        ]);
    }
}
