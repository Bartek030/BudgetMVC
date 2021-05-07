<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Categories;
use \App\Auth;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Settings extends Authenticated {
    /**
     * Show the settings page
     *
     * @return void
     */
    public function showAction() {

        $incomeCategories = Categories::getIncomeCategories($_SESSION['user_id']);
        $expenseCategories = Categories::getExpenseCategories($_SESSION['user_id']);
        $paymentMethods = Categories::getPaymentMethods($_SESSION['user_id']);
        $user = Auth::getUser();

        View::renderTemplate('Settings/show.html',[
            'incomeCategories' => $incomeCategories,
            'expenseCategories' => $expenseCategories,
            'paymentMethods' => $paymentMethods,
            'user' => $user
        ]);
    }

    /**
     * Add category to database
     *
     * @return void
     */
    public function addCategoryAction() {
        $newCategory = new Categories($_GET);
        $operation = $this -> route_params['operation'];

        $newCategory -> addCategoryToDatabase($operation, $_SESSION['user_id']);
    }
}
