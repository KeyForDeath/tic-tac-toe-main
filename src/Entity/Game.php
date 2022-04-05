<?php
// src/Entity/Game.php
namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Game
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=9)
     */
    private $field = "---------";

    /**
     * @ORM\Column(type="boolean")
     */
    private $isGameOver = false;

    /**
     * @ORM\Column(type="string", length=4)
     */
    private $winner = "-";

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getField(): ?string
    {
        return $this->field;
    }

    public function setField(string $field)
    {
        $this->field = $field;
    }

    public function getWinner(): ?string
    {
        return $this->winner;
    }

    public function setWinner(string $winner)
    {
        $this->winner = $winner;
    }

    public function getIsGameOver(): ?string
    {
        return $this->isGameOver;
    }

    public function setIsGameOver(bool $isGameOver)
    {
        $this->isGameOver = $isGameOver;
    }

    // ... getter and setter methods
}
