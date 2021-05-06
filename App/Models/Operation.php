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

    public function saveIncome($userID) {
        $this -> validate();
        $categoryID = static::getIncomeCategoryID($userID, $this -> category);

        if(empty($this -> errors) && $categoryID) {
            $sql = 'INSERT INTO incomes
                    VALUES(NULL, :userID, :categoryID, :amount, :date, :comment)';

            $db = static::getDB();
            $stmt = $db -> prepare($sql);

            $stmt -> bindValue(':userID', $userID, PDO::PARAM_INT);
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

    public function saveExpense($userID) {
        $this -> validate();
        $categoryID = static::getExpenseCategoryID($userID, $this -> category);
        $paymentMethodID = static::getPaymentMethodID($userID, $this -> paymentMethod);

        if(empty($this -> errors) && $categoryID && $paymentMethodID) {
            $sql = 'INSERT INTO expenses
                    VALUES(NULL, :userID, :categoryID, :paymentMethodID, :amount, :date, :comment)';

            $db = static::getDB();
            $stmt = $db -> prepare($sql);

            $stmt -> bindValue(':userID', $userID, PDO::PARAM_INT);
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
    protected function validate() {
        // Amount
        if($this -> amount < 0) {
            $this -> errors[] = 'Podana kwota musi być większa od 0';
        }

        // Date
        static::validateData($this -> operationDate);
    }

    /**
     * Validate date if belong to the specific period of time
     * 
     * @param string date for validate
     * 
     * @return void 
     */
    protected static function validateData($date) {
        if(strtotime($date) < strtotime('2000-01-01')) {
            $this -> errors[] = 'Dopuszczalny zakres daty obejmuje od 01-01-2000 do końca obecnego roku!';
        }

        if(strtotime($date) > strtotime(date("Y") . '-12-31')) {
            $this -> errors[] = 'Dopuszczalny zakres daty obejmuje od 01-01-2000 do końca obecnego roku!';
        }
    }

    /**
     * Get category ID assigned to the current user
     * 
     * @param user current user
     * @param string category for searching ID
     * 
     * @return mixed ID - category ID assigned to the current user, NULL otherwise
     */
    protected static function getIncomeCategoryID($userID, $category) {
        $sql = 'SELECT id
                FROM incomes_category_assigned_to_users
                WHERE user_id = :id
                AND name = :category';
        
        $db = static::getDB();
        $stmt = $db -> prepare($sql);

        $stmt -> bindValue(':id', $userID, PDO::PARAM_INT);
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
     * @return mixed ID - category ID assigned to the current user, NULL otherwise
     */
    protected static function getExpenseCategoryID($userID, $category) {
        $sql = 'SELECT id
                FROM expenses_category_assigned_to_users
                WHERE user_id = :id
                AND name = :category';
        
        $db = static::getDB();
        $stmt = $db -> prepare($sql);

        $stmt -> bindValue(':id', $userID, PDO::PARAM_INT);
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
     * @return mixed ID - category ID assigned to the current user, NULL otherwise
     */
    protected static function getPaymentMethodID($userID, $paymentMethod) {
        $sql = 'SELECT id
                FROM payment_methods_assigned_to_users
                WHERE user_id = :id
                AND name = :paymentMethod';
        
        $db = static::getDB();
        $stmt = $db -> prepare($sql);

        $stmt -> bindValue(':id', $userID, PDO::PARAM_INT);
        $stmt -> bindValue(':paymentMethod', $paymentMethod, PDO::PARAM_STR);
        
        $stmt -> execute();
        $paymentMethodID = $stmt -> fetch();

        return $paymentMethodID[0];
    }

    /**
     * Get incomes data
     * 
     * @param Operation input operation data from user
     * @param user current user
     * 
     * @return mixed array of incomes data, null otherwise
     */
    public static function getIncomeData($balance, $userID) {

        if($balance -> balanceTime == 'other_period') {
            return static::getIncomeDataFromOtherTimePeriod($balance, $userID);
        } else {
            return static::getIncomeDataFromDefinedTimePeriod($balance, $userID);
        }   
    }
    
    /**
     * Get expenses data
     * 
     * @param Operation input operation data from user
     * @param user current user
     * 
     * @return mixed array of expenses data, null otherwise
     */
    public static function getExpenseData($balance, $userID) {

        if($balance -> balanceTime == 'other_period') {
            return static::getExpenseDataFromOtherTimePeriod($balance, $userID);
        } else {
            return static::getExpenseDataFromDefinedTimePeriod($balance, $userID);
        }   
    }

    /**
     * Get incomes data from other period of time
     * 
     * @param Operation input operation data from user
     * @param user current user
     * 
     * @return mixed array of incomes data, null otherwise
     */
    protected static function getIncomeDataFromOtherTimePeriod($balance, $userID) {

        $sql = 'SELECT inc.name, incomes.amount, incomes.date_of_income, incomes.income_comment
                FROM incomes_category_assigned_to_users AS inc, incomes
                WHERE incomes.income_category_assigned_to_user_id = inc.id
                AND incomes.user_id = :userID
                AND incomes.date_of_income >= :startDate
                AND incomes.date_of_income <= :endDate';

        $db = static::getDB();
        $stmt = $db -> prepare($sql);
        
        $stmt -> bindValue(':userID', $userID, PDO::PARAM_INT);
        $stmt -> bindValue(':startDate', $balance -> startDate, PDO::PARAM_STR);
        $stmt -> bindValue(':endDate', $balance -> endDate, PDO::PARAM_STR);
        
        //$stmt -> setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt -> execute();

        return $stmt -> fetchAll();
    }

    /**
     * Get incomes data from defined period of time
     * 
     * @param Operation input operation data from user
     * @param user current user
     * 
     * @return mixed array of incomes data, null otherwise
     */
    protected static function getIncomeDataFromDefinedTimePeriod($balance, $userID) {

        if($balance -> balanceTime == 'current_month') {
            $time = date("Y-m");
        } else if ($balance -> balanceTime == 'previous_month') {
            $year = date("Y");
            $month = date("m") - 1;

            if($month < 1) {
                $month = 12;
                $year = $year - 1;
            }

            if($month < 10) {
                $time = $year . "-0" . $month;
            } else {
                $time = $year . "-" . $month;
            }

        } else if ($balance -> balanceTime == 'current_year') {
            $time = date("Y");
        }

        $sql = 'SELECT inc.name, incomes.amount, incomes.date_of_income, incomes.income_comment
                FROM incomes_category_assigned_to_users AS inc, incomes
                WHERE incomes.income_category_assigned_to_user_id = inc.id
                AND incomes.user_id = :userID
                AND incomes.date_of_income LIKE :time';

        $db = static::getDB();
        $stmt = $db -> prepare($sql);
        
        $stmt -> bindValue(':userID', $userID, PDO::PARAM_INT);
        $stmt -> bindValue(':time', "$time%", PDO::PARAM_STR);
        
        //$stmt -> setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt -> execute();

        return $stmt -> fetchAll();
    }

    /**
     * Get expenses data from other period of time
     * 
     * @param Operation input operation data from user
     * @param user current user
     * 
     * @return mixed array of incomes data, null otherwise
     */
    protected static function getExpenseDataFromOtherTimePeriod($balance, $userID) {

        $sql = 'SELECT exp.name, expenses.amount, expenses.date_of_expense, expenses.expense_comment, pay.name
                FROM expenses_category_assigned_to_users AS exp, expenses, payment_methods_assigned_to_users AS pay
                WHERE expenses.expense_category_assigned_to_user_id = exp.id
                AND expenses.payment_method_assigned_to_user_id = pay.id
                AND expenses.user_id = :userID
                AND expenses.date_of_expense >= :startDate
                AND expenses.date_of_expense <= :endDate';

        $db = static::getDB();
        $stmt = $db -> prepare($sql);
        
        $stmt -> bindValue(':userID', $userID, PDO::PARAM_INT);
        $stmt -> bindValue(':startDate', $balance -> startDate, PDO::PARAM_STR);
        $stmt -> bindValue(':endDate', $balance -> endDate, PDO::PARAM_STR);

        //$stmt -> setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt -> execute();

       return ($stmt -> fetchAll());
    }
    
    /**
     * Get expenses data from defined period of time
     * 
     * @param Operation input operation data from user
     * @param user current user
     * 
     * @return mixed array of expenses data, null otherwise
     */
    protected static function getExpenseDataFromDefinedTimePeriod($balance, $userID) {

        if($balance -> balanceTime == 'current_month') {
            $time = date("Y-m");
        } else if ($balance -> balanceTime == 'previous_month') {
            $year = date("Y");
            $month = date("m") - 1;

            if($month < 1) {
                $month = 12;
                $year = $year - 1;
            }

            if($month < 10) {
                $time = $year . "-0" . $month;
            } else {
                $time = $year . "-" . $month;
            }

        } else if ($balance -> balanceTime == 'current_year') {
            $time = date("Y");
        }

        $sql = 'SELECT exp.name, expenses.amount, expenses.date_of_expense, expenses.expense_comment, pay.name as payname
                FROM expenses_category_assigned_to_users AS exp, expenses, payment_methods_assigned_to_users AS pay
                WHERE expenses.expense_category_assigned_to_user_id = exp.id
                AND expenses.payment_method_assigned_to_user_id = pay.id
                AND expenses.user_id = :userID
                AND expenses.date_of_expense LIKE :time';


        $db = static::getDB();
        $stmt = $db -> prepare($sql);
        
        $stmt -> bindValue(':userID', $userID, PDO::PARAM_INT);
        $stmt -> bindValue(':time', "$time%", PDO::PARAM_STR);
        
        //$stmt -> setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt -> execute();
 
        return $stmt -> fetchAll();
    }
}