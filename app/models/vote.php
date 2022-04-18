<?php

class Vote
{
    private $conn;
    private $table_name = "ft_votes";

    public $id;
    public $matchday_id;
    public $player_id;
    public $vote;

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
                matchday_id=:matchday_id,
                player_id=:player_id,
                vote=:vote";


        $stmt = $this->conn->prepare($query);

        $this->matchday_id = htmlspecialchars(strip_tags($this->matchday_id));
        $this->player_id = htmlspecialchars(strip_tags($this->player_id));
        $this->vote = htmlspecialchars(strip_tags($this->vote));

        // binding
        $stmt->bindParam(":matchday_id", $this->matchday_id);
        $stmt->bindParam(":player_id", $this->player_id);
        $stmt->bindParam(":vote", $this->vote);

        // execute query
        if ($stmt->execute()) {
            return true;
        }
//        $stmt->debugDumpParams();
        return false;

    }

    function update() {
        $query = "UPDATE
                " . $this->table_name . "
            SET
                matchday_id=:matchday_id,
                player_id=:player_id,
                vote=:vote
            WHERE
                id = :id";

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->matchday_id = htmlspecialchars(strip_tags($this->matchday_id));
        $this->player_id = htmlspecialchars(strip_tags($this->player_id));
        $this->vote = htmlspecialchars(strip_tags($this->vote));

        // binding
        $stmt->bindParam(":matchday_id", $this->matchday_id);
        $stmt->bindParam(":player_id", $this->player_id);
        $stmt->bindParam(":vote", $this->vote);
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
}