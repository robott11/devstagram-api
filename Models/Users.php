<?php
namespace Models;

use \Core\Model;
use \Models\Jwt;

class Users extends Model
{
    private $id_user;

    /**
     * registra um novo usuário no banco de dados
     *
     * @param string $name
     * @param string $email
     * @param string $pass
     * @return bool
     */
    public function create(string $name, string $email, string $pass): bool
    {
        if (!$this->emailExists($email)) {
            $hash = password_hash($pass, PASSWORD_BCRYPT);
            
            $sql = "INSERT INTO users (name, email, pass) VALUES (:name, :email, :pass)";
            $sql = $this->db->prepare($sql);
            $sql->bindValue(":name", $name);
            $sql->bindValue(":email", $email);
            $sql->bindValue(":pass", $hash);
            $sql->execute();

            $this->id_user = $this->db->lastInsertId();

            return true;
        } else {
            return false;
        }
    }

    public function checkCredentials(string $email, string $password): bool
    {
        $sql = "SELECT id, pass FROM users WHERE email = :email";
        $sql = $this->db->prepare($sql);
        $sql->bindValue(":email", $email);
        $sql->execute();

        if ($sql->rowCount() > 0) {
            $info = $sql->fetch();

            if (password_verify($password, $info["pass"])) {
                $this->id_user = $info["id"];

                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * gera um JWT
     *
     * @return void
     */
    public function createJwt()
    {
        $jwt = new Jwt();
        return $jwt->create([
            "id_user" => $this->id_user
        ]);
    }

    /**
     * verifica se um email existe
     *
     * @param string $email
     * @return bool
     */
    private function emailExists(string $email): bool
    {
        $sql = "SELECT id FROM users WHERE email = :email";
        $sql = $this->db->prepare($sql);
        $sql->bindValue(":email", $email);
        $sql->execute();

        if ($sql->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }
}
