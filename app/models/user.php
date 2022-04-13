<?php

class User
{
    private $conn;
    private $table_name = "ft_users";

    public $id;
    public $firstname;
    public $lastname;
    public $email;
    public $password;
    public $username;
    public $admin;

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
                firstname=:firstname,
                lastname=:lastname,
                username=:username,
                email=:email,
                password=:password,
                admin=:admin";


        $stmt = $this->conn->prepare($query);

        $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);

        $this->firstname = htmlspecialchars(strip_tags($this->firstname));
        $this->lastname = htmlspecialchars(strip_tags($this->lastname));
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = $hashed_password;

        // binding
        $stmt->bindParam(":firstname", $this->firstname);
        $stmt->bindParam(":lastname", $this->lastname);
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":admin", $this->admin, PDO::PARAM_BOOL);

        // execute query
        if ($stmt->execute()) {
            return true;
        }
//        $stmt->debugDumpParams();
        return false;

    }

    // AGGIORNARE LIBRO

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

    function get_hashed_password() {
        if ($this->username == null) {
            return false;
        }

        $query = "SELECT
                        password
                    FROM
                   " . $this->table_name . " WHERE username=:username ";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":username", $this->username);

        // execute query
        $stmt->execute();
        return $stmt;
    }

    function username_already_exixts() {
        if ($this->username == null) {
            return false;
        }

        $query = "SELECT EXISTS (SELECT
                        *
                    FROM
                   " . $this->table_name . " WHERE username=:username)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":username", $this->username);

        // execute query
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_NUM);
        return $row[0];
    }

    function id_by_username() {
        if ($this->username == null) {
            return null;
        }

        $query = "SELECT
                        id
                    FROM
                   " . $this->table_name . " WHERE username=:username";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":username", $this->username);

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

    function email_already_exixts() {
        if ($this->email == null) {
            return false;
        }

        $query = "SELECT EXISTS (SELECT
                        *
                    FROM
                   " . $this->table_name . " WHERE email=:email)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":email", $this->email);

        // execute query
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_NUM);
        return $row[0];
    }
}