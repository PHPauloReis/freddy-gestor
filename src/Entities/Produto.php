<?php

namespace App\Entities;

use DateTime;

class Produto
{
    public DateTime $data_criacao;
    public DateTime $data_atualizacao;
    public ?DateTime $data_exclusao;

    public function __construct(
        public int $id,
        public string $titulo,
        public string $descricao,
        public float $preco,
        public ?string $foto_path,
        string $data_criacao,
        string $data_atualizacao,
        ?string $data_exclusao
    ) {
        $this->data_criacao = new DateTime($data_criacao);
        $this->data_atualizacao = new DateTime($data_atualizacao);
        $this->data_exclusao = $data_exclusao ? new DateTime($data_exclusao) : null;
    }
}
