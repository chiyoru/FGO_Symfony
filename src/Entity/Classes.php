<?php
namespace App\Entity;

class Classes
{
    protected string $classe;

    public function getClasse(): string
    {
        return $this->classe;
    }

    public function setClasse(string $classe): void
    {
        $this->classe = $classe;
    }
}
?>