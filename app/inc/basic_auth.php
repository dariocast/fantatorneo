<?php

class BasicAuth
{
    static function is_admin()
    {
        $current_user = BasicAuth::current_user();
        return $current_user != null ? $current_user->admin : false;
    }

    static function current_user()
    {
        if (BasicAuth::authenticated()) {
            $database = new Database();
            $db = $database->getConnection();
            $user = new User($db);
            $user->username = $_SERVER['PHP_AUTH_USER'];
            $user->id = $user->id_by_username();
            $stmt = $user->read();
            $num = $stmt->rowCount();

            if ($num == 1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                extract($row);
                $user_item = new User($db, array(
                    "id" => $id,
                    "firstname" => $firstname,
                    "lastname" => $lastname,
                    "email" => $email,
                    "username" => $username,
                    "admin" => $admin,
                    "password" => $password
                ));
                return $user_item;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    static function authenticated()
    {
        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            // If no username provided, present the auth challenge.
            header('WWW-Authenticate: Basic realm="Fanta Torneo"');
            header('HTTP/1.0 401 Unauthorized');
            // User will be presented with the username/password prompt
            // If they hit cancel, they will see this access denied message.
            echo '<p>Access denied. You did not enter a password.</p>';
            exit; // Be safe and ensure no other content is returned.
        }

        $user_supplied_password = $_SERVER['PHP_AUTH_PW'];

        $database = new Database();
        $db = $database->getConnection();
        $user = new User($db);
        $user->username = $_SERVER['PHP_AUTH_USER'];
        $stmt = $user->get_hashed_password();
        $num = $stmt->rowCount();

        if ($num == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            extract($row);
            $hashed_password = $password;
            if ($hashed_password != crypt($user_supplied_password, $hashed_password)) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }
}
