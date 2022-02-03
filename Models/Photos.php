<?php
namespace Models;

use \Core\Model;

class Photos extends Model
{
    /**
     * retorna a quantidade de fotos de um usuário
     *
     * @param integer $id_user
     * @return int
     */
    public function getPhotosCount(int $id_user): int
    {
        $sql = "SELECT COUNT(*) AS c FROM photos WHERE id_user = :id_user";
        $sql = $this->db->prepare($sql);
        $sql->bindValue(":id_user", $id_user);
        $sql->execute();
        $info = $sql->fetch();

        return $info["c"];
    }

    public function deleteAll(int $id_user)
    {
        //DELETAR AS FOTOS
        $sql = "DELETE FROM photos WHERE id_user = :id_user";
        $sql = $this->db->prepare($sql);
        $sql->bindValue(":id_user", $id_user);
        $sql->execute();

        //DELETAR COMENTÁRIOS
        $sql = "DELETE FROM photos_comments WHERE id_user = :id_user";
        $sql = $this->db->prepare($sql);
        $sql->bindValue(":id_user", $id_user);
        $sql->execute();

        //DELETAR LIKES
        $sql = "DELETE FROM photos_likes WHERE id_user = :id_user";
        $sql = $this->db->prepare($sql);
        $sql->bindValue(":id_user", $id_user);
        $sql->execute();
    }
}
