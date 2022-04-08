<?php

namespace App\Services;

use App\Repository\JuronRepository;

class Censurator
{
    private array $jurons = [];

    public function __construct(JuronRepository $juronRepository)
    {
        $this->jurons = $juronRepository->findAll();
    }

    public function purify(string $sentence): string
    {
        foreach ($this->jurons as $juron)
        {
            $mot = $juron->getMot();
            $str = substr($mot, 0, 1)
                . str_repeat('*', mb_strlen($mot)-2)
                . substr($mot, -1);
            $sentence = str_ireplace($mot, $str, $sentence);
        }
        return $sentence;
    }
}