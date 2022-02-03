<?php
namespace Models;

use \Core\Model;
use \Models\Jwt;
use \Models\Photos;

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
     * valida um token
     *
     * @param string $token
     * @return void
     */
    public function validateJwt(string $token)
    {
        $jwt = new Jwt();
        $info = $jwt->validate($token);

        if (isset($info->id_user)) {
            $this->id_user = $info->id_user;
            return true;
        } else {
            return false;
        }
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

    /**
     * retorna o id do ususário logado
     *
     * @return integer
     */
    public function getId(): int
    {
        return $this->id_user;
    }

    /**
     * retorna as informações do usuário pelo id
     *
     * @param integer $id
     * @return array
     */
    public function getInfo(int $id): array
    {
        $array = [];
        
        $sql = "SELECT id, name, email, avatar FROM users WHERE id = :id";
        $sql = $this->db->prepare($sql);
        $sql->bindValue(":id", $id);
        $sql->execute();

        if ($sql->rowCount() > 0) {
            $array = $sql->fetch(\PDO::FETCH_ASSOC);
            
            $photos = new Photos;

            if (!empty($array["avatar"])) {
                $array["avatar"] = BASE_URL."media/avatar/".$array["avatar"];
            } else {
                $array["avatar"] = BASE_URL."media/avatar/default.jpg";
            }

            $array["following"]    = $this->getFollowingCount($id);
            $array["followers"]    = $this->getFollowersCount($id);
            $array["photos_count"] = $photos->getPhotosCount($id);
        }

        return $array;
    }

    /**
     * edita informações do usuário
     *
     * @param integer $id
     * @param array $data
     * @return string
     */
    public function editInfo(int $id, array $data): string
    {
        if ($id === $this->getId()) {
            //VERIFICAR QUAIS DADOS DEVEM SER EDITADOS
            $toChange = [];

            if (!empty($data["name"])) {
                $toChange["name"] = $data["name"];
            }

            if (!empty($data["email"])) {
                if (filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
                    if (!$this->emailExists($data["email"])) {
                        $toChange["email"] = $data["email"];
                    } else {
                        return "Novo email já existente!";
                    }
                } else {
                    return "Email inválido!";
                }
            }

            if (!empty($data["pass"])) {
                $toChange["pass"] = password_hash($data["pass"], PASSWORD_BCRYPT);
            }

            //VERIFICA SE FORAM ENVIADOS DADOS
            if (count($toChange) > 0) {
                //TRANSFORMA OS DADOS EM SQL PARA SER FORMATADO PELO PDO
                $fields = [];
                foreach ($toChange as $key => $value) {
                    $fields[] = $key." = :".$key;
                }

                $sql = "UPDATE users SET ".implode(",", $fields)." WHERE id = :id";
                $sql = $this->db->prepare($sql);
                $sql->bindValue(":id", $id);

                foreach ($toChange as $key => $value) {
                    $sql->bindValue(":".$key, $value);
                }

                $sql->execute();
                return "";
            } else {
                return "Preencha os dados corretamente.";
            }

        } else {
            return "Não é permitido editar outro usuário!";
        }
    }

    /**
     * retorna a quantidade de pessoas que um usuário segue
     *
     * @param integer $id_user
     * @return int
     */
    public function getFollowingCount(int $id_user): int
    {
        $sql = "SELECT COUNT(*) AS c FROM users_following WHERE id_user_follower = :id_user";
        $sql = $this->db->prepare($sql);
        $sql->bindValue(":id_user", $id_user);
        $sql->execute();
        $info = $sql->fetch();

        return $info["c"];
    }

    /**
     * retorna a quantidade seguidores de um usuário
     *
     * @param integer $id_user
     * @return int
     */
    public function getFollowersCount(int $id_user): int
    {
        $sql = "SELECT COUNT(*) AS c FROM users_following WHERE id_user_followed = :id_user";
        $sql = $this->db->prepare($sql);
        $sql->bindValue(":id_user", $id_user);
        $sql->execute();
        $info = $sql->fetch();

        return $info["c"];
    }

    /**
     * deleta um usuário
     *
     * @param integer $id
     * @return void
     */
    public function delete(int $id)
    {
        if ($id === $this->getId()) {
            $photos = new Photos();
            $photos->deleteAll($id);

            //DELETAR TODOS OS SEGUIDORES E TODOS QUE SEGUIA
            $sql = "DELETE FROM users_following WHERE id_user_follower = :id OR id_user_followed = :id";
            $sql = $this->db->prepare($sql);
            $sql->bindValue(":id", $id);
            $sql->execute();

            //DELETAR O USUÁRIO
            $sql = "DELETE FROM users WHERE id = :id";
            $sql = $this->db->prepare($sql);
            $sql->bindValue(":id", $id);
            $sql->execute();

            return "";
        } else {
            return "Não é permitido excluir outro usuário!";
        }
    }
}
