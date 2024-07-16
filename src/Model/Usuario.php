<?php

namespace Alura\Leilao\Model;

class Usuario
{
    public function __construct(private string $nome)
    {
        //
    }

    public function getNome(): string
    {
        return $this->nome;
    }
}
