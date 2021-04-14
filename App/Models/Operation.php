<?php

namespace App\Models;

use PDO;

/**
 * Example operaion model
 * 
 * PHP version 7.0
 */
class Operation extends \Core\Model {
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

    public function saveIncome($user) {
        $this -> validate();
        $categoryID = static::getIncomeCategoryID($user, $this -> category);

        if(empty($this -> errors) && $categoryID) {
            $sql = 'INSERT INTO incomes
                    VALUES(NULL, :userID, :categoryID, :amount, :date, :comment)';

            $db = static::getDB();
            $stmt = $db -> prepare($sql);

            $stmt -> bindValue(':userID', $user -> id, PDO::PARAM_INT);
            $stmt -> bindValue(':categoryID', $categoryID, PDO::PARAM_INT);
            $stmt -> bindValue(':amount', $this -> amount, PDO::PARAM_STR);
            $stmt -> bindValue(':date', $this -> operationDate, PDO::PARAM_STR);
            $stmt -> bindValue(':comment', $this -> comment, PDO::PARAM_STR);

            return $stmt -> execute();
        }
        return false;
    }

    /**
     * Save expense model with the current property values
     * 
     * @return boolean true if success, false otherwise
     */

    public function saveExpense($user) {
        $this -> validate();
        $categoryID = static::getExpenseCategoryID($user, $this -> category);
        $paymentMethodID = static::getPaymentMethodID($user, $this -> paymentMethod);

        if(empty($this -> errors) && $categoryID && $paymentMethodID) {
            $sql = 'INSERT INTO expenses
                    VALUES(NULL, :userID, :categoryID, :paymentMethodID, :amount, :date, :comment)';

            $db = static::getDB();
            $stmt = $db -> prepare($sql);

            $stmt -> bindValue(':userID', $user -> id, PDO::PARAM_INT);
            $stmt -> bindValue(':categoryID', $categoryID, PDO::PARAM_INT);
            $stmt -> bindValue(':paymentMethodID', $paymentMethodID, PDO::PARAM_INT);
            $stmt -> bindValue(':amount', $this -> amount, PDO::PARAM_STR);
            $stmt -> bindValue(':date', $this -> operationDate, PDO::PARAM_STR);
            $stmt -> bindValue(':comment', $this -> comment, PDO::PARAM_STR);

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
        if($this -> amount < 0) {
            $this -> errors[] = 'Podana kwota musi być większa od 0';
        }

        // Date
        if(strtotime($this -> operationDate) < strtotime('2000-01-01')) {
            $this -> errors[] = 'Dopuszczalny zakres daty obejmuje od 01-01-2000 do końca obecnego roku!';
        }

        if(strtotime($this -> operationDate) > strtotime(date("Y") . '-12-31')) {
            $this -> errors[] = 'Dopuszczalny zakres daty obejmuje od 01-01-2000 do końca obecnego roku!';
        }
    }

    /**
     * Get category ID assigned to the current user
     * 
     * @param user current user
     * @param string category for searching ID
     * 
     * @return int ID - category ID assigned to the current user, NULL otherwise
     */
    protected static function getIncomeCategoryID($user, $category) {
        $sql = 'SELECT id
                FROM incomes_category_assigned_to_users
                WHERE user_id = :id
                AND name = :category';;
        
        $db = static::getDB();
        $stmt = $db -> prepare($sql);

        $stmt -> bindValue(':id', $user -> id, PDO::PARAM_INT);
        $stmt -> bindValue(':category', $category, PDO::PARAM_STR);
        
        $stmt -> execute();
        $categoryID = $stmt -> fetch();

        return $categoryID[0];
    }

    /**
     * Get category ID assigned to the current user
     * 
     * @param user current user
     * @param string category for searching ID
     * 
     * @return int ID - category ID assigned to the current user, NULL otherwise
     */
    protected static function getExpenseCategoryID($user, $category) {
        $sql = 'SELECT id
                FROM expenses_category_assigned_to_users
                WHERE user_id = :id
                AND name = :category';;
        
        $db = static::getDB();
        $stmt = $db -> prepare($sql);

        $stmt -> bindValue(':id', $user -> id, PDO::PARAM_INT);
        $stmt -> bindValue(':category', $category, PDO::PARAM_STR);
        
        $stmt -> execute();
        $categoryID = $stmt -> fetch();

        return $categoryID[0];
    }

    /**
     * Get category ID assigned to the current user
     * 
     * @param user current user
     * @param string category for searching ID
     * 
     * @return int ID - category ID assigned to the current user, NULL otherwise
     */
    protected static function getPaymentMethodID($user, $paymentMethod) {
        $sql = 'SELECT id
                FROM payment_methods_assigned_to_users
                WHERE user_id = :id
                AND name = :paymentMethod';;
        
        $db = static::getDB();
        $stmt = $db -> prepare($sql);

        $stmt -> bindValue(':id', $user -> id, PDO::PARAM_INT);
        $stmt -> bindValue(':paymentMethod', $paymentMethod, PDO::PARAM_STR);
        
        $stmt -> execute();
        $paymentMethodID = $stmt -> fetch();

        return $paymentMethodID[0];
    }
}