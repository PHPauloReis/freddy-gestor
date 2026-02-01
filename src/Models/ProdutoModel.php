<?php

namespace App\Models;

use App\Exceptions\ResourceNotFoundException;
use App\Entities\Produto;
use App\Support\LoggerFactory;
use Doctrine\DBAL\Connection;

class ProdutoModel
{
    private $logger;
    private Connection $connection;

    public function __construct() {
        $this->logger = LoggerFactory::create('produtos');
        $this->connection = require __DIR__ . '/../../config/database.php';
    }

    public function contarProdutos(): int
    {
        return (int) $this->connection->createQueryBuilder()
            ->select('COUNT(*) as total')
            ->from('produtos')
            ->where('data_exclusao IS NULL')
            ->executeQuery()
            ->fetchOne();
    }

    public function listarProdutos(int $paginaAtual = 1, int $itensPorPagina = 5): array
    {
        $rows = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('produtos')
            ->where('data_exclusao IS NULL')
            ->setFirstResult(($paginaAtual - 1) * $itensPorPagina)
            ->setMaxResults($itensPorPagina)
            ->executeQuery()
            ->fetchAllAssociative();

        return array_map(fn($row) => new Produto(...$row), $rows);
    }

    public function buscarProdutoPorId(int $id): ?Produto
    {
        $row = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('produtos')
            ->where('id = :id')
            ->andWhere('data_exclusao IS NULL')
            ->setParameter('id', $id)

            ->executeQuery()
            ->fetchAssociative();

        if (!$row) {
            $this->logger->warning("Produto com ID {id} não encontrado.", ['id' => $id]);
            
            throw new ResourceNotFoundException();
        }

        return new Produto(...$row);
    }

    public function criarProduto(array $data): void
    {
        $this->connection->insert('produtos', [
            'titulo' => $data['titulo'],
            'descricao' => $data['descricao'],
            'preco' => $data['preco'],
            'foto_path' => $data['foto_path'] ?? null,
        ]);

        $this->logger->info("Novo produto criado: {titulo}", ['titulo' => $data['titulo']]);
    }

    public function atualizarProduto(int $id, array $data): void
    {
        $produto = $this->buscarProdutoPorId($id);

        if (!$produto) {
            $this->logger->warning("Tentativa de atualização falhou. Produto com ID {id} não encontrado.", ['id' => $id]);
            throw new ResourceNotFoundException();
        }

        $this->connection->update('produtos', [
            'titulo' => $data['titulo'],
            'descricao' => $data['descricao'],
            'preco' => $data['preco'],
            'foto_path' => $data['foto_path'] ?? $produto->foto_path,
            'data_atualizacao' => date('Y-m-d H:i:s'),
        ], [
            'id' => $id,
        ]);

        $this->logger->info("Produto com ID {id} atualizado.", ['id' => $id]);
    }

    public function excluirProduto(int $id): void
    {
        $produto = $this->buscarProdutoPorId($id);

        if (!$produto) {
            $this->logger->warning("Tentativa de exclusão falhou. Produto com ID {id} não encontrado.", ['id' => $id]);
            throw new ResourceNotFoundException();
        }

        $this->connection->update('produtos', [
            'data_exclusao' => date('Y-m-d H:i:s'),
        ], [
            'id' => $id,
        ]);

        $this->logger->info("Produto com ID {id} excluído.", ['id' => $id]);
    }
}
