<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Categories;
use \App\Models\User;
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

    /**
     * Delete category from database
     *
     * @return void
     */
    public function deleteCategoryAction() {
        $deletedCategory = new Categories($_GET);
        $operation = $this -> route_params['operation'];

        $deletedCategory -> deleteCategoryFromDatabase($operation, $_SESSION['user_id']);
    }

    /**
     * Delete all operations from database
     *
     * @return void
     */
    public function clearOperationDataAction() {
        Categories::clearUserDatabase($_SESSION['user_id']);
    }

    /**
     * Delete user account
     *
     * @return void
     */
    public function deleteAccountAction() {
        Categories::deleteUserOperationData($_SESSION['user_id']);
        User::deleteUserAccount($_SESSION['user_id']);
        Auth::logout();
    }
}