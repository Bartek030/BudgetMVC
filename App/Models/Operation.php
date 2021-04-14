<?php

namespace App\Models;

/**
 * Operaion model
 * 
 * PHP version 7.0
 */
class Operaion extends Model {
    /**
     * Error messages
     * 
     * @var array
     */
    public $errors = [];

    /**
     * Class contructor
     * 
     * @param array $data Initial property values
     * 
     * @return void
     */
    public function __construct($data = []) {
        foreach ($data as $key => $value) {
            $this -> $key = $value;
        }
    }

    /**
     * Save income model with the current property values
     * 
     * @return boolean true if success, false otherwise
     */

    public function save() {
        $this -> validate();

        if(empty($this -> errors)) {
            $password_hash = password_hash($this -> password, PASSWORD_DEFAULT);

            $token = new Token();
            $hashed_token = $token -> getHash();
            $this -> activation_token = $token -> getValue();

            $sql = 'INSERT INTO users (name, email, password_hash, activation_hash)
                    VALUES (:name, :email, :password_hash, :activation_hash)';

            $db = static::getDB();
            $stmt = $db -> prepare($sql);

            $stmt -> bindValue(':name', $this -> name, PDO::PARAM_STR);
            $stmt -> bindValue(':email', $this -> email, PDO::PARAM_STR);
            $stmt -> bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
            $stmt -> bindValue(':activation_hash', $hashed_token, PDO::PARAM_STR);

            return $stmt -> execute();
        }
        return false;
    }

    /**
     * Validate current property values, adding validation error messages to the errors array property
     * 
     * @return void
     */
    public function validate() {
        // Amount
        if($this -> incomeAmount < 0) {
            $this -> errors[] = 'Podana kwota musi być większa od 0';
        }

        // Date
        if(strtotime($this -> operationDate) < strtotime('2000-01-01')) {
            $this -> errors[] = 'Dopuszczalny zakres daty obejmuje od 01-01-2000 do końca obecnego roku!';
        }

        if(strtotime($this -> operationDate) < strtotime('2000-01-01')) {
            $this -> errors[] = 'Dopuszczalny zakres daty obejmuje od 01-01-2000 do końca obecnego roku!';
        }
        // Password
        if(isset($this -> password)) {
            if(strlen($this -> password) < 6) {
                $this -> errors[] = 'Hasło musi posiadać conajmniej 6 znaków!';
            }

            if(preg_match('/.*[a-z]+.*/i', $this -> password) == 0) {
                $this -> errors[] = 'Hasło musi zawierać conajmniej 1 literę!';
            }

            if(preg_match('/.*\d+.*/i', $this -> password) == 0) {
                $this -> errors[] = 'Hasło musi posiadać conajmniej 1 cyfrę!';
            }
            if(isset($this -> repeatedPassword)) {
                if($this -> password != $this -> repeatedPassword) {
                    $this -> errors[] = 'Hasła muszą być jednakowe!';
                }
            }
        }
    }
}