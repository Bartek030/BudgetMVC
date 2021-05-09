<?php

namespace App\Models;

use PDO;
use App\Token;
use App\Mail;
use \Core\View;

/**
 * Example user model
 *
 * PHP version 7.0
 */
class User extends \Core\Model
{
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
     * Save the user model with the current property values
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
        // Name
        if($this -> name == '') {
            $this -> errors[] = 'Imię jest wymagane!';
        }

        // email address
        if(filter_var($this -> email, FILTER_VALIDATE_EMAIL) === false) {
            $this -> errors[] = 'Niepoprawny adres e-mail!';
        }

        if(static::emailExists($this -> email, $this -> id ?? null)) {
            $this -> errors[] = 'E-mail jest już przypisany do innego konta!';
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

    /**
     * See if a user record already exists with the specified email
     * 
     * @param string $email email address to search for
     * 
     * @return boolean True if a record already exists with the specified email, false otherwise
     */
    public static function emailExists($email, $ignore_id = null) {
        $user = static::findByEmail($email);

        if($user) {
            if($user -> id != $ignore_id) {
                return true;
            }
        }
        return false;
    }

    /**
     * Find a user model by email address
     * 
     * @param string $email email address to search for
     * 
     * @return mixed User object if found, false otherwise
     */
    public static function findByEmail($email) {
        $sql = 'SELECT * FROM users WHERE email = :email';

        $db = static::getDB();
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':email', $email, PDO::PARAM_STR);

        $stmt -> setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt -> execute();

        return $stmt -> fetch();
    }

    /**
     * Get user ID
     * 
     * @param string user's email
     * 
     * @return int User ID
     */
    public static function getUserID($email) {
        $sql = 'SELECT id 
                FROM users 
                WHERE email = :email';

        $db = static::getDB();
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':email', $email, PDO::PARAM_STR);

        $stmt -> execute();
        $id = $stmt -> fetch();
        
        return $id[0];
    }

    /**
     * Copy default income categories in database from table with default income categories to table with income categories assigned to user
     * 
     * @param int $userID Id of new user
     * 
     * @return void
     */
    public function copyIncomesCategories($userID) {
        $sql = 'INSERT INTO incomes_category_assigned_to_users (user_id, name) 
                SELECT u.id, inc.name 
                FROM users AS u, incomes_category_default AS inc 
                WHERE u.id = :userID';
        
        $db = static::getDB();
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':userID', $userID, PDO::PARAM_STR);

        $stmt -> execute();
    }

    /**
     * Copy default expense categories in database from table with default expense categories to table with expense categories assigned to user
     * 
     * @param int $userID Id of new user
     * 
     * @return void
     */
    public function copyExpensesCategories($userID) {
        $sql = 'INSERT INTO expenses_category_assigned_to_users (user_id, name) 
                SELECT u.id, exp.name 
                FROM users AS u, expenses_category_default AS exp
                WHERE u.id = :userID';
        
        $db = static::getDB();
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':userID', $userID, PDO::PARAM_INT);

        $stmt -> execute();
    }

    /**
     * Copy default payment methods in database from table with default payment methods to table with payment methods assigned to user
     * 
     * @param int $userID Id of new user
     * 
     * @return void
     */
    public function copyPaymentMethods($userID) {
        $sql = 'INSERT INTO payment_methods_assigned_to_users (user_id, name) 
                SELECT u.id, pay.name 
                FROM users AS u, payment_methods_default AS pay 
                WHERE u.id = :userID';
        
        $db = static::getDB();
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':userID', $userID, PDO::PARAM_INT);

        $stmt -> execute();
    }

    /**
     * Authenticate a user by email and password
     * 
     * @param string $email email address
     * @param string $password password
     * 
     * @return mixed The user object or false if authentication fails
     */
    public static function authenticate($email, $password) {
        $user = static::findByEmail($email);

        if($user && $user -> is_active) {
            if(password_verify($password, $user -> password_hash)) {
                return $user;
            }
        }
        return false;
    }

    /**
     * Find a user model by ID
     * 
     * @param string $id The user ID
     * 
     * @return mixed User object if found, false otherwise
     */
    public static function findByID($id) {
        $sql = 'SELECT * FROM users WHERE id = :id';

        $db = static::getDB();
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':id', $id, PDO::PARAM_INT);

        $stmt -> setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt -> execute();

        return $stmt -> fetch();
    }

    /**
     * Remember the login by inserting a new unique token into the remembered_logins table
     * for this user record
     * 
     * @return boolen True if the login was remembered successfully, false otherwise
     */
    public function rememberLogin() {
        $token = new Token();
        $hashed_token = $token -> getHash();
        $this -> remember_token = $token -> getValue();
        
        $this -> expiry_timestamp = time() + 60 * 60 * 24 * 7; // 7 days from now

        $sql = 'INSERT INTO remembered_logins (token_hash, user_id, expires_at)
                VALUES (:token_hash, :user_id, :expires_at)';

        $db = static::getDB();
        $stmt = $db -> prepare($sql);

        $stmt -> bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt -> bindValue(':user_id', $this -> id, PDO::PARAM_INT);
        $stmt -> bindValue(':expires_at', date('Y-m-d H:i:s', $this -> expiry_timestamp), PDO::PARAM_STR);

        return $stmt -> execute();
    }

    /**
     * Send password reset instructions to the user specified
     * 
     * @param string $email The email address
     * 
     * @return void
     */
    public static function sendPasswordReset($email) {
        $user = static::findByEmail($email);

        if($user) {
            if($user -> startPasswordReset()) {
                $user -> sendPasswordResetEmail();
            }
        }
    }

    /**
     * Start the password reset process by generating a new token and expiry
     * 
     * @return void
     */
    public function startPasswordReset() {
        $token = new Token();
        $hashed_token = $token -> getHash();
        $this -> password_reset_token = $token -> getValue();

        $expiry_timestamp = time() + 60 * 60 * 1; // 1 hour from now

        $sql = 'UPDATE users
                SET password_reset_hash = :token_hash,
                    password_reset_expires_at = :expires_at
                WHERE id = :id';

        $db = static::getDB();
        $stmt = $db -> prepare($sql);

        $stmt -> bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt -> bindValue(':expires_at', date('Y-m-d H:i:s', $expiry_timestamp), PDO::PARAM_STR);
        $stmt -> bindValue(':id', $this -> id, PDO::PARAM_INT);

        return $stmt -> execute();
    }

    /**
     * Send password reset instructions in an email to the user
     * 
     * @return void
     */
    protected function sendPasswordResetEmail() {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/password/reset/' . $this -> password_reset_token;

        $text = View::getTemplate('Password/reset_email.txt', ['url' => $url]);
        $html = View::getTemplate('Password/reset_email.html', ['url' => $url]);

        Mail::send($this -> email, 'Resetowanie hasła', $text, $html, $this -> name);
    }

    /**
     * Find a user model by password reset token and expiry
     * 
     * @param string $token Password reset token sent to user
     * 
     * @return mixed User object if found and the token hasn't expired, null otherwise
     */
    public static function findByPasswordReset($token) {
        $token = new Token($token);
        $hashed_token = $token -> getHash();

        $sql = 'SELECT * FROM users 
                WHERE password_reset_hash = :token_hash';

        $db = static::getDB();
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);

        $stmt -> setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt -> execute();

        $user = $stmt -> fetch();

        if($user) {
            //Check password reset token hasn't expired
            if(strtotime($user -> password_reset_expires_at) > time()) {
                return $user;
            }
        }
    }

    /**
     * Reset the password
     * 
     * @param string $password The new password
     * 
     * @return boolean True if the password was updated successfully, false otherwise
     */
    public function resetPassword($password) {
        $this -> password = $password;

        $this -> validate();

        if(empty($this -> errors)) {
            $password_hash = password_hash($this -> password, PASSWORD_DEFAULT);

            $sql = 'UPDATE users 
                    SET password_hash = :password_hash,
                        password_reset_hash = NULL,
                        password_reset_expires_at = NULL
                    WHERE id = :id';

            $db = static::getDB();
            $stmt = $db -> prepare($sql);
            $stmt -> bindValue(':id', $this -> id, PDO::PARAM_INT);
            $stmt -> bindValue(':password_hash', $password_hash, PDO::PARAM_STR);

            return $stmt -> execute();
        }
        return false;
    }

    /**
     * Send an email to the user containing the activation link
     * 
     * @return void
     */
    public function sendActivationEmail() {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/signup/activate/' . $this -> activation_token;

        $text = View::getTemplate('Signup/activation_email.txt', ['url' => $url]);
        $html = View::getTemplate('Signup/activation_email.html', ['url' => $url]);

        Mail::send($this -> email, 'Aktywacja konta', $text, $html, $this -> name);
    }

    /**
     * Activate the user account with the specified activation token
     * 
     * @param string $value Activation token from the URL
     * 
     * @return void
     */
    public static function activate($value) {
        $token = new Token($value);
        $hashed_token = $token -> getHash();

        $sql = 'UPDATE  users 
                SET     is_active = 1,
                        activation_hash = null
                WHERE   activation_hash = :hashed_token';
        
        $db = static::getDB();
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':hashed_token', $hashed_token, PDO::PARAM_STR);

        $stmt -> execute();
    }

    /**
     * Delete user from database
     * 
     * @param int user ID
     * 
     * @return void
     */
    public static function deleteUserOperationData($userID) {   
        $sql = 'DELETE 
                FROM users
                WHERE id = :userID';

        $db = static::getDB();
        $stmt = $db -> prepare($sql);

        $stmt -> bindValue(':userID', $userID, PDO::PARAM_INT);

        $stmt -> execute();
    }
}
