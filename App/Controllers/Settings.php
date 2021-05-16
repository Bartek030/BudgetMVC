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

        if($newCategory -> addCategoryToDatabase($operation, $_SESSION['user_id'])) {
            View::renderTemplate('Settings/newCategorySuccess.html');
        } else {
            View::renderTemplate('Settings/newCategoryFail.html', [
                'category' => $newCategory
            ]);
        }
    }

    /**
     * Change category data
     * 
     * @return void
     */
    public function editCategoryAction() {
        $editedCategory = new Categories($_GET);
        $operation = $this -> route_params['operation'];

        if($editedCategory -> editCategoryInDatabase($operation, $_SESSION['user_id'])) {
            View::renderTemplate('Settings/editCategorySuccess.html');
        } else {
            View::renderTemplate('Settings/editCategoryFail.html', [
                'category' => $editedCategory
            ]);
        }
    }

    /**
     * Delete category from database
     *
     * @return void
     */
    public function deleteCategoryAction() {
        $deletedCategory = new Categories($_GET);
        $operation = $this -> route_params['operation'];

        if($deletedCategory -> deleteCategoryFromDatabase($operation, $_SESSION['user_id'])) {
            View::renderTemplate('Settings/deleteCategorySuccess.html');
        } else {
            View::renderTemplate('500.html');
        }
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

    /**
     * Change user name
     *
     * @return void
     */
    public function changeNameAction() {
        $changeName = new User($_POST);
        if($changeName -> changeUserName($_SESSION['user_id'])) {
            View::renderTemplate('Settings/changeNameSuccess.html');
        } else {
            View::renderTemplate('Settings/changeNameFail.html', [
                'user' => $changeName
            ]);
        }
    }

    /**
     * Change user email
     *
     * @return void
     */
    public function changeEmailAction() {
        $changeEmail = new User($_POST);
        if($changeEmail -> changeUserEmail($_SESSION['user_id'])) {
            $changeEmail -> sendActivationEmail();
            Auth::logout();
            View::renderTemplate('Settings/changeEmailSuccess.html');
        } else {
            View::renderTemplate('Settings/changeEmailFail.html', [
                'user' => $changeEmail
            ]);
        }
    }
}