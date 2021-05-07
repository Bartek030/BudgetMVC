<?php

namespace App\Models;

use PDO;

/**
 * Remembered login model
 * 
 * PHP version 7.0
 */
class Categories extends \Core\Model {
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
     * Get income categories assigned to user
     * 
     * @param int user ID
     * 
     * @return array Array of categories assigned to user
     */
    public static function getIncomeCategories($userID) {
        $sql = 'SELECT name
                FROM incomes_category_assigned_to_users as inc
                WHERE inc.user_id = :userID';

        $db = static::getDB();
        $stmt = $db -> prepare($sql);
        
        $stmt -> bindValue(':userID', $userID, PDO::PARAM_INT);

        $stmt -> execute();

       return ($stmt -> fetchAll());
    }

    /**
     * Get income categories assigned to user
     * 
     * @param int user ID
     * 
     * @return array Array of categories assigned to user
     */
    public static function getExpenseCategories($userID) {
        $sql = 'SELECT name
                FROM expenses_category_assigned_to_users as exp
                WHERE exp.user_id = :userID';

        $db = static::getDB();
        $stmt = $db -> prepare($sql);
        
        $stmt -> bindValue(':userID', $userID, PDO::PARAM_INT);

        $stmt -> execute();

       return ($stmt -> fetchAll());
    }

    /**
     * Get income categories assigned to user
     * 
     * @param int user ID
     * 
     * @return array Array of categories assigned to user
     */
    public static function getPaymentMethods($userID) {
        $sql = 'SELECT name
                FROM payment_methods_assigned_to_users as pay
                WHERE pay.user_id = :userID';

        $db = static::getDB();
        $stmt = $db -> prepare($sql);
        
        $stmt -> bindValue(':userID', $userID, PDO::PARAM_INT);

        $stmt -> execute();

       return ($stmt -> fetchAll());
    }

    /**
     * Add category to the database
     * 
     * @param int user ID
     * 
     * @return boolean true if success, false otherwise
     */
    public function addCategoryToDatabase($operation, $userID) {
        $name = ucfirst($this -> newCategory);
            
        if($operation == 'income') {
            $sql = 'INSERT INTO incomes_category_assigned_to_users
                    VALUES(NULL, :userID, :name)';
        } else if($operation == 'expense') {
            $sql = 'INSERT INTO expenses_category_assigned_to_users
                    VALUES(NULL, :userID, :name, NULL)';
        } else {
            $sql = 'INSERT INTO payment_methods_assigned_to_users
                    VALUES(NULL, :userID, :name)';
        }

        if(!empty($name)) {
            $db = static::getDB();
            $stmt = $db -> prepare($sql);

            $stmt -> bindValue(':userID', $userID, PDO::PARAM_INT);
            $stmt -> bindValue(':name', $name, PDO::PARAM_STR);

            return $stmt -> execute();
        }
        return false;
    }
}