<?php 

namespace App\Models;

use App\Models\Database;
use PDO;

class User extends Database
{
    public static function save(array $data)
    {
        $pdo = self::getConnection();

        $stmt = $pdo->prepare("
            INSERT 
            INTO 
                users (name, email, password)
            VALUES
                (?, ?, ?)
        ");

        $stmt->execute([
            $data['name'],
            $data['email'],
            $data['password'],
        ]);

        return $pdo->lastInsertId() > 0 ? true : false;
    }

    public static function authentication(array $data)
    {
        $pdo = self::getConnection();

        $stmt = $pdo->prepare("
            SELECT 
                *
            FROM 
                users
            WHERE 
                email = ?
        ");

        $stmt->execute([$data['email']]);

        if ($stmt->rowCount() < 1) return false;

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!password_verify($data['password'], $user['password'])) return false;

        return [
            'id'   => $user['id'],
            'name' => $user['name'],
            'email'=> $user['email'],
        ];
    }

    public static function find(int|string $id)
    {
        $pdo = self::getConnection();

        $stmt = $pdo->prepare('
            SELECT 
                id, name, email
            FROM 
                users
            WHERE 
                id = ?
        ');

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function update(int|string $id, array $data)
    {
        $pdo = self::getConnection();
        
        $stmt = $pdo->prepare('
            UPDATE 
                users
            SET 
                name = ?
            WHERE 
                id = ?
        ');

        $stmt->execute([$data['name'], $id]);

        return $stmt->rowCount() > 0 ? true : false;
    }

    public static function delete(int|string $id)
    {
        $pdo = self::getConnection();

        $stmt = $pdo->prepare('
            DELETE 
            FROM 
                users
            WHERE 
                id = ?
        ');

        $stmt->execute([$id]);

        return $stmt->rowCount() > 0 ? true : false;
    }
}