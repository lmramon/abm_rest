<?php


class UserMapper extends Mapper
{
    public function getUsers()
    {
        $sql = "SELECT u.id, u.name, u.email, u.image
            from users u";
        $stmt = $this->db->query($sql);

        $results = [];
        while ($row = $stmt->fetch()) {
            $results[] = new UserEntity($row);
        }
        return $results;
    }

    /**
     * Get one user by its ID
     *
     * @param int $user_id The ID of the user
     * @return UsertEntity  The User
     */
    public function getUserById($user_id)
    {
        $sql = "SELECT u.id, u.name, u.email, u.image
            from users u 
            where u.id = :user_id";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute(["user_id" => $user_id]);

        if ($result) {
            return new UserEntity($stmt->fetch());
        }
    }

    public function deleteUserById($user_id)
    {
        $sql = "DELETE FROM users WHERE id = :user_id";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute(["user_id" => $user_id]);
        if ($result && $stmt->rowCount() == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function save(UserEntity $user) {
        $sql = "insert into users
            (name, email, image) values
            (:name, :email, :image)";

        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            "name" => $user->getName(),
            "description" => $user->getEmail(),
            "component" => $user->getImage(),
        ]);

        if(!$result) {
            throw new Exception("could not save record");
        }
    }}
