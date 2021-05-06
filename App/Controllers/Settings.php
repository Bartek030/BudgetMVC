<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Categories;

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

        View::renderTemplate('Settings/show.html',[
            'incomeCategories' => $incomeCategories,
            'expenseCategories' => $expenseCategories,
            'paymentMethods' => $paymentMethods
        ]);
    }


}
