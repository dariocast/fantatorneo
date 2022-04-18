<?php

class Player
{
    private $conn;
    private $table_name = "ft_players";

    public $id;
    public $name;
    public $group_name;
    public $gender;
    public $position;
    public $cost;

    public function __construct($db, array $arguments = array())
    {
        $this->conn = $db;

        if (!empty($arguments)) {
            foreach ($arguments as $property => $argument) {
                $this->{$property} = $argument;
            }
        }
    }

    function read()
    {
        if ($this->id != null) {
            $query = "SELECT
                        *
                    FROM
                   " . $this->table_name . " WHERE id=:id ";
        } else {
            $query = "SELECT
                        *
                    FROM
                   " . $this->table_name;
        }

        $stmt = $this->conn->prepare($query);

        if ($this->id != null) {
            $stmt->bindParam(":id", $this->id);
        }

        // execute query
        $stmt->execute();
        return $stmt;
    }

    function create()
    {


        $query = "INSERT INTO
                " . $this->table_name . "
            SET
                name=:name,
                group_name=:group_name,
                gender=:gender,
                position=:position,
                cost=:cost";


        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->group_name = htmlspecialchars(strip_tags($this->group_name));
        $this->gender = htmlspecialchars(strip_tags($this->gender));
        $this->position = htmlspecialchars(strip_tags($this->position));
        $this->cost = htmlspecialchars(strip_tags($this->cost));

        // binding
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":group_name", $this->group_name);
        $stmt->bindParam(":gender", $this->gender);
        $stmt->bindParam(":position", $this->position);
        $stmt->bindParam(":cost", $this->cost);

        // execute query
        if ($stmt->execute()) {
            return true;
        }

//        print_r($stmt->errorInfo());
//        $stmt->debugDumpParams();
        return false;

    }

    function update() {
        $query = "UPDATE
                " . $this->table_name . "
            SET
                name=:name,
                group_name=:group_name,
                gender=:gender,
                position=:position,
                cost=:cost
            WHERE
                id = :id";

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->group_name = htmlspecialchars(strip_tags($this->group_name));
        $this->gender = htmlspecialchars(strip_tags($this->gender));
        $this->position = htmlspecialchars(strip_tags($this->position));
        $this->cost = htmlspecialchars(strip_tags($this->cost));

        // binding
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":group_name", $this->group_name);
        $stmt->bindParam(":gender", $this->gender);
        $stmt->bindParam(":position", $this->position);
        $stmt->bindParam(":cost", $this->cost);
        $stmt->bindParam(":id", $this->id);

        // execute query
        if($stmt->execute()){
            return true;
        }

        return false;

    }

    function delete(){

        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";


        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));


        $stmt->bindParam(":id", $this->id);

        // execute query
        if($stmt->execute()){
            return true;
        }

        return false;

    }

    function id_by_name_and_group() {
        if ($this->name == null || $this->group_name == null) {
            return null;
        }

        $query = "SELECT
                        id
                    FROM
                   " . $this->table_name . " WHERE name=:name AND group_name=:group_name";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":group_name", $this->group_name);

        // execute query
        $stmt->execute();
        $num = $stmt->rowCount();

        if ($num == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            extract($row);
            return $id;
        } else {
            return null;
        }
    }

    function player_already_in_group() {
        if ($this->name == null || $this->group_name == null) {
            return false;
        }

        $query = "SELECT EXISTS (SELECT
                        *
                    FROM
                   " . $this->table_name . " WHERE name=:name AND group_name=:group_name)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":group_name", $this->group_name);

        // execute query
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_NUM);
        return $row[0];
    }
}