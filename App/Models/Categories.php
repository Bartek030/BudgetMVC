<?php

namespace App\Models;

use PDO;

/**
 * Categories model
 * 
 * PHP version 7.0
 */
class Categories extends \Core\Model {
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
     * Get income categories assigned to user
     * 
     * @param int $userID user ID
     * 
     * @return array Array of categories assigned to user
     */
    public static function getIncomeCategories($userID) {
        $sql = 'SELECT id, name
                FROM incomes_category_assigned_to_users as inc
                WHERE inc.user_id = :userID';

        $db = static::getDB();
        $stmt = $db -> prepare($sql);
        
        $stmt -> bindValue(':userID', $userID, PDO::PARAM_INT);

        $stmt -> execute();

        return ($stmt -> fetchAll());
    }

    /**
     * Get expense categories assigned to user
     * 
     * @param int $userID user ID
     * 
     * @return array Array of categories assigned to user
     */
    public static function getExpenseCategories($userID) {
        $sql = 'SELECT id, name, expense_limit
                FROM expenses_category_assigned_to_users as exp
                WHERE exp.user_id = :userID';

        $db = static::getDB();
        $stmt = $db -> prepare($sql);
        
        $stmt -> bindValue(':userID', $userID, PDO::PARAM_INT);

        $stmt -> execute();

        return ($stmt -> fetchAll());
    }

    /**
     * Get payment methods assigned to user
     * 
     * @param int $userID user ID
     * 
     * @return array Array of categories assigned to user
     */
    public static function getPaymentMethods($userID) {
        $sql = 'SELECT id, name
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
     * @param string $operation operation type
     * @param int $userID user ID
     * 
     * @return boolean true if success, false otherwise
     */
    public function addCategoryToDatabase($operation, $userID) {
        if($this -> newCategory == '') {
            $this -> errors[] = 'Nie podano nazwy kategorii!';
        }

        if(empty($this -> errors)) {
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

            $db = static::getDB();
            $stmt = $db -> prepare($sql);

            $stmt -> bindValue(':userID', $userID, PDO::PARAM_INT);
            $stmt -> bindValue(':name', $name, PDO::PARAM_STR);

            return $stmt -> execute();
        }
        return false;
    }

     /**
     * Edit category in database
     * 
     * @param string $operation operation type
     * @param int $userID user ID
     * 
     * @return boolean true if success, false otherwise
     */
    public function editCategoryInDatabase($operation, $userID) {
        if($this -> editedName == '') {
            $this -> errors[] = 'Nie podano nazwy kategorii!';
        }
        
        if($operation == 'income') {
            $sql = 'UPDATE incomes_category_assigned_to_users
                    SET name = :editedName
                    WHERE id = :categoryID';
        } else if($operation == 'expense') {
            
            if (isset($this -> limitValue)) {
                $sql = 'UPDATE  expenses_category_assigned_to_users
                        SET name = :editedName,
                            expense_limit = :expense_limit
                        WHERE id = :categoryID';
            } else {
                $sql = 'UPDATE expenses_category_assigned_to_users
                        SET name = :editedName,
                        expense_limit = NULL
                        WHERE id = :categoryID';
            }
        } else {
            $sql = 'UPDATE payment_methods_assigned_to_users
                    SET name = :editedName
                    WHERE id = :categoryID';
        }

        if(empty($this -> errors)) {
            $db = static::getDB();
            $stmt = $db -> prepare($sql);

            $stmt -> bindValue(':editedName', $this -> editedName, PDO::PARAM_STR);
            $stmt -> bindValue(':categoryID', $this -> editedCategory, PDO::PARAM_INT);
            if (isset($this -> limitValue)) {
                $stmt -> bindValue(':expense_limit', $this -> limitValue, PDO::PARAM_STR);
            }

            return $stmt -> execute();
        }
        return false;
    }

    /**
     * Delete category from the database
     * 
     * @param string $operation operation type
     * @param int $userID user ID
     * 
     * @return boolean true if success, false otherwise
     */
    public function deleteCategoryFromDatabase($operation, $userID) {
        $otherCategoryId = $this -> getOtherCategoryId($operation, $userID);
        if($operation == 'income') {
            $sql = 'DELETE 
                    FROM incomes_category_assigned_to_users
                    WHERE id = :categoryID';
        } else if($operation == 'expense') {
            $sql = 'DELETE 
                    FROM expenses_category_assigned_to_users
                    WHERE expenses_category_assigned_to_users.id = :categoryID';
        } else {
            $sql = 'DELETE 
                    FROM payment_methods_assigned_to_users
                    WHERE payment_methods_assigned_to_users.id = :categoryID';
        }

        if($this -> changeCategoryId($operation, $otherCategoryId)) {
            $db = static::getDB();
            $stmt = $db -> prepare($sql);

            $stmt -> bindValue(':categoryID', $this -> deletedCategory, PDO::PARAM_INT);

            return $stmt -> execute();
        }
        return false;
    }

    /**
     * Get ID of other category assigned to the given type of operation
     * 
     * @param string $operation operation type
     * @param int $userID user ID
     * 
     * @return int other category ID
     */
    protected function getOtherCategoryId($operation, $userID) {
        if($operation == 'income') {
            $sql = 'SELECT id
                    FROM incomes_category_assigned_to_users as inc
                    WHERE inc.user_id = :userID
                    AND inc.name = :other';
            
        } else if($operation == 'expense') {
            $sql = 'SELECT id
                    FROM expenses_category_assigned_to_users as exp
                    WHERE exp.user_id = :userID
                    AND exp.name = :other';
        } else {
            $sql = 'SELECT id
                    FROM payment_methods_assigned_to_users as pay
                    WHERE pay.user_id = :userID
                    AND pay.name = :other';
        }

        $db = static::getDB();
        $stmt = $db -> prepare($sql);
        
        $stmt -> bindValue(':userID', $userID, PDO::PARAM_INT);
        $stmt -> bindValue(':other', 'Inne', PDO::PARAM_STR);

        $stmt -> execute();
        return ($stmt -> fetch());
    }

    /**
     * Change ID of deleted category to ID of other category
     * 
     * @param string $operation operation type
     * @param int $otherCategoryId ID of deleted category
     * 
     * @return mixed true if success, NULL otherwise
     */
    protected function changeCategoryId($operation, $otherCategoryId) {
        if($operation == 'income') {
            $sql = 'UPDATE incomes
                    SET income_category_assigned_to_user_id = :otherCategoryId 
                    WHERE income_category_assigned_to_user_id = :deletedCategoryID';
        } else if($operation == 'expense') {
            $sql = 'UPDATE expenses
                    SET expense_category_assigned_to_user_id = :otherCategoryId 
                    WHERE expense_category_assigned_to_user_id = :deletedCategoryID';
        } else {
            $sql = 'UPDATE expenses
                    SET payment_method_assigned_to_user_id = :otherCategoryId 
                    WHERE payment_method_assigned_to_user_id = :deletedCategoryID';
        }

        $db = static::getDB();
        $stmt = $db -> prepare($sql);
        
        $stmt -> bindValue(':otherCategoryId', $otherCategoryId['id'], PDO::PARAM_INT);
        $stmt -> bindValue(':deletedCategoryID', $this -> deletedCategory, PDO::PARAM_INT);

        return $stmt -> execute();
    }

    /**
     * Clear operation datas, assigned to user
     * 
     * @param int $userID user ID
     * 
     * @return void
     */
    public static function clearUserDatabase($userID) {
        static::clearUserIncomes($userID);
        static::clearUserExpenses($userID);
    }

    /**
     * Clear income operation datas, assigned to user
     * 
     * @param int $userID user ID
     * 
     * @return void
     */
    protected static function clearUserIncomes($userID) {
        $sql = 'DELETE 
                FROM incomes
                WHERE user_id = :userID';

        $db = static::getDB();
        $stmt = $db -> prepare($sql);

        $stmt -> bindValue(':userID', $userID, PDO::PARAM_INT);

        $stmt -> execute();
    }

    /**
     * Clear expense operation datas, assigned to user
     * 
     * @param int $userID user ID
     * 
     * @return void
     */
    protected static function clearUserExpenses($userID) {
        $sql = 'DELETE 
                FROM expenses
                WHERE user_id = :userID';

        $db = static::getDB();
        $stmt = $db -> prepare($sql);

        $stmt -> bindValue(':userID', $userID, PDO::PARAM_INT);

        $stmt -> execute();
    }

    /**
     * Delete all users operation datas from database
     * 
     * @param int $userID user ID
     * 
     * @return void
     */
    public static function deleteUserOperationData($userID) {
        static::clearUserIncomes($userID);
        static::clearUserExpenses($userID);
        static::deleteUserIncomeCategories($userID);
        static::deleteUserExpenseCategories($userID);
        static::deleteUserPaymentMethods($userID);
    }

    /**
     * Delete user income categories from database
     * 
     * @param int $userID user ID
     * 
     * @return void
     */
    public static function deleteUserIncomeCategories($userID) {
        $sql = 'DELETE 
                FROM incomes_category_assigned_to_users
                WHERE user_id = :userID';

        $db = static::getDB();
        $stmt = $db -> prepare($sql);

        $stmt -> bindValue(':userID', $userID, PDO::PARAM_INT);

        $stmt -> execute();
    }

    /**
     * Delete user expense categories from database
     * 
     * @param int $userID user ID
     * 
     * @return void
     */
    public static function deleteUserExpenseCategories($userID) {
        $sql = 'DELETE 
                FROM expenses_category_assigned_to_users
                WHERE user_id = :userID';

        $db = static::getDB();
        $stmt = $db -> prepare($sql);

        $stmt -> bindValue(':userID', $userID, PDO::PARAM_INT);

        $stmt -> execute();
    }

    /**
     * Delete user payment methods from database
     * 
     * @param int $userID user ID
     * 
     * @return void
     */
    public static function deleteUserPaymentMethods($userID) {
        $sql = 'DELETE 
                FROM payment_methods_assigned_to_users
                WHERE user_id = :userID';

        $db = static::getDB();
        $stmt = $db -> prepare($sql);

        $stmt -> bindValue(':userID', $userID, PDO::PARAM_INT);

        $stmt -> execute();
    }

    /**
     * Return expense summary for every category
     * 
     * @param int $userID user ID
     * 
     * @return array Array of expense summary for every category
     */
    public static function getExpenseSummary($userID, $date) {
        
        $sql = 'SELECT exp.id, exp.name, exp.expense_limit, COALESCE(SUM(expenses.amount),0) as summary
                FROM expenses_category_assigned_to_users as exp
                LEFT JOIN expenses
                ON exp.id = expenses.expense_category_assigned_to_user_id
                AND expenses.date_of_expense LIKE :date 
                WHERE exp.user_id = :userID
                GROUP BY exp.id';

        $db = static::getDB();
        $stmt = $db -> prepare($sql);
        
        $stmt -> bindValue(':userID', $userID, PDO::PARAM_INT);
        $stmt -> bindValue(':date', "$date%", PDO::PARAM_STR);

        $stmt -> execute();

        return ($stmt -> fetchAll());
    }
}