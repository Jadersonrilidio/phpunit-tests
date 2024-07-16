<?php

namespace Alura\Leilao\Service;

use Alura\Leilao\Model\Leilao;

class EnviadorEmail
{
    /**
     * 
     */
    public function notificarTerminoLeilao(Leilao $leilao)
    {
        $resultado = mail(
            to: 'usuario@mail.com',
            subject: "Leilao finalizado",
            message: "O leilao {$leilao->getDescricao()} foi finalizado.",
            additional_headers: [],
            additional_params: '',
        );

        if (!$resultado) {
            throw new \DomainException('erro ao enviar email');
        }
    }
}
