<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Operation;
use \App\Models\Categories;

/**
 * Add an expense to the database into expense table
 * 
 * PHP version 7.0
 */
class Expense extends Authenticated {
    /**
     * Show new expense page
     * 
     * @return void
     */
    public function newAction() {
        $expenseCategories = Categories::getExpenseCategories($_SESSION['user_id']);
        $paymentMethods = Categories::getPaymentMethods($_SESSION['user_id']);
        View::renderTemplate('Expense/new.html', [
            'expenseCategories' => $expenseCategories,
            'paymentMethods' => $paymentMethods
        ]);
    }

    /**
     * Save expense in databse
     * 
     * @return void
     */
    public function createAction() {
        $expense = new Operation($_POST);

        if($expense -> saveExpense($_SESSION['user_id'])) {
            $this -> redirect('/expense/success');
        } else {
            View::renderTemplate('Expense/new.html',[
                'expense' => $expense
            ]);
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