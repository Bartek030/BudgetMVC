<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Operation;
use \App\Models\Categories;

/**
 * Expense controller
 * 
 * PHP version 7.0
 */
class Expense extends Authenticated {
    /**
     * Show new expense page
     * 
     * @return void
     */
    public function newAction($isSnackbar = false, $snackbarText = '', $snackbarType = '') {
        $expenseCategories = Categories::getExpenseCategories($_SESSION['user_id']);
        $paymentMethods = Categories::getPaymentMethods($_SESSION['user_id']);
        View::renderTemplate('Expense/new.html', [
            'expenseCategories' => $expenseCategories,
            'paymentMethods' => $paymentMethods,
            'isSnackbar' => $isSnackbar,
            'snackbarText' => $snackbarText,
            'snackbarType' => $snackbarType
        ]);
    }

    /**
     * Save expense in databse
     * 
     * @return void
     */
    public function createAction() {
        $expense = new Operation($_POST);
        $expenseCategories = Categories::getExpenseCategories($_SESSION['user_id']);
        $paymentMethods = Categories::getPaymentMethods($_SESSION['user_id']);
        
        if($expense -> saveExpense($_SESSION['user_id'])) {
            $this -> newAction(true, 'Wydatek został dodany', 'success');
        } else {
            $this -> newAction(true, 'Uzupełnij poprawnie dane, aby dodać wydatek', 'fault');
        }
    }

    /**
     * Show the expense success page
     * 
     * @return void
     */
    public function successAction() {
        View::renderTemplate('Expense/success.html');
    }

    /**
     * Get the expense summary for every category
     * 
     * 
     * @return void
     */
    public function getExpenseSummaryAction() {
        $expenseSummary = Categories::getExpenseSummary($_SESSION['user_id']);

        header('Content-Type: application/json');
        echo json_encode($expenseSummary);
    }
}