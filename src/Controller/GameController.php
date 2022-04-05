<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use App\Entity\Game;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{

    #[Route('/tic_tac_toe/', name: 'home')]
    public function Home(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $entityManager = $managerRegistry->getManager();

        if($request->get('start') != null)
        {
            $game = new Game();
            $entityManager->persist($game);
            $entityManager->flush();

            return $this->redirectToRoute('game', ['game' => $game->getId()] );

        }

        return $this->render('tic_tac_toe/Home.html.twig');
    }

    #[Route('/tic_tac_toe/game', name:'game')]
    public function TicTacToe(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $nextMove = $request->get('field');
        $gameId = $request->get('game');

        $entityManager = $managerRegistry->getManager();

        $game = $entityManager->find(Game::class, $gameId);

        $activePlayer = "X";

        if ($nextMove != null && $game != null)
        {
            if(!$game->getIsGameOver()) $this->MakeMove($game, $activePlayer, $nextMove);
            if(!$game->getIsGameOver()) $this->MakeKIMove($game);
        }

        $entityManager->persist($game);
        $entityManager->flush();

        if($request->get('reset') != null)
        {
            $game->setField("---------");
            $entityManager->persist($game);
            $entityManager->flush();
        }
        if(!$game->getIsGameOver()) {
            return $this->render('tic_tac_toe/Game.html.twig', ['field' => $game->getField()]);
        }else
        {
            dump($game->getWinner());
            return $this->render('tic_tac_toe/Victory.html.twig', ['winner' => $game->getWinner()]);
        }
    }


    private function MakeMove(Game $game, string $activePlayer, string $nextMove)
    {
        $field = $game->getField();

        if($field[$nextMove-1] == "-")
        {
            $field[$nextMove-1] = $activePlayer;
            $game->setField($field);
        }else
        {
            throw new BadRequestHttpException("Field already set!");
        }
        if ($this->DetermineWinner($game, $activePlayer) != "-")
        {
            $game->setIsGameOver(true);
            $game->setWinner($activePlayer);
        }
    }

    private function MakeKIMove(Game $game)
    {
        $field = $game->getField();

        $emptyField = str_split($field, 1);

        $emptyField = array_filter($emptyField, function (string $f){
            return $f === "-";
        });

        $nextMove = array_rand($emptyField, 1);

        $this->MakeMove($game, "O", $nextMove + 1);
    }


    private function DetermineWinner(Game $game, string $activePlayer): string
    {
        $field = $game->getField();

        if (($field[0] != "-" && $field[0] == $field[3] && $field[0] == $field[6]) || // 1. Spalte
            ($field[1] != "-" && $field[1] == $field[4] && $field[1] == $field[7]) || // 2. Spalte
            ($field[2] != "-" && $field[2] == $field[5] && $field[2] == $field[8]) || // 3. Spalte
            ($field[0] != "-" && $field[0] == $field[4] && $field[0] == $field[8]) || // Diag links oben nach rechts unten
            ($field[2] != "-" && $field[2] == $field[4] && $field[2] == $field[6]) || // Diag Recht oben nach links unten
            ($field[0] != "-" && $field[0] == $field[1] && $field[0] == $field[2]) || // 1. Zeile
            ($field[3] != "-" && $field[3] == $field[4] && $field[3] == $field[5]) || // 2. Zeile
            ($field[6] != "-" && $field[6] == $field[7] && $field[6] == $field[8]))   // 3. Zeile
        {
            return $activePlayer;
        } elseif (!str_contains($field, "-")){
            return "Draw";
        }
        return "-";
    }


}
