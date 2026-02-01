<?php

namespace App\Models;

use App\Support\LoggerFactory;
use Doctrine\DBAL\Connection;

class UsuarioModel
{
    private $logger;
    private Connection $connection;

    public function __construct() {
        $this->logger = LoggerFactory::create('usuarios');
        $this->connection = require __DIR__ . '/../../config/database.php';
    }

    public function buscarPorEmail(string $email): ?array
    {
        $row = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('usuarios')
            ->where('email = :email')
            ->setParameter('email', $email)
            ->executeQuery()
            ->fetchAssociative();

        if (!$row) {
            $this->logger->info("Falha de login para o email {email}.", ['email' => $email]);
            return null;
        }

        return $row;
    }

    public function buscarPorTokenRedefinicao(string $token): ?array
    {
        $row = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('usuarios')
            ->where('token_lembrar_senha = :token')
            ->setParameter('token', $token)
            ->executeQuery()
            ->fetchAssociative();

        if (!$row) {
            $this->logger->info("Token de redefinição inválido: {token}.", ['token' => $token]);
            return null;
        }

        return $row;
    }

    public function criar(array $data): int
    {
        $this->connection->insert('usuarios', [
            'nome' => $data['nome'],
            'email' => $data['email'],
            'senha' => password_hash($data['password'], PASSWORD_BCRYPT),
        ]);

        $this->logger->info("Novo usuário criado: {email}.", ['email' => $data['email']]);

        return (int) $this->connection->lastInsertId();
    }

    public function atualizar(array $data): void
    {
        $this->connection->update('usuarios', $data, ['id' => $data['id']]);
        $this->logger->info("Usuário atualizado: {email}.", ['email' => $data['email']]);
    }
}
