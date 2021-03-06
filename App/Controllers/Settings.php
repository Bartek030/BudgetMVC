<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Categories;
use \App\Models\User;
use \App\Auth;

/**
 * Settings controller
 *
 * PHP version 7.0
 */
class Settings extends Authenticated {
    /**
     * Show the settings page
     *
     * @return void
     */
    public function showAction($isSnackbar = false, $snackbarText = '', $snackbarType = '') {
        $incomeCategories = Categories::getIncomeCategories($_SESSION['user_id']);
        $expenseCategories = Categories::getExpenseCategories($_SESSION['user_id']);
        $paymentMethods = Categories::getPaymentMethods($_SESSION['user_id']);
        $user = Auth::getUser();

        View::renderTemplate('Settings/show.html',[
            'incomeCategories' => $incomeCategories,
            'expenseCategories' => $expenseCategories,
            'paymentMethods' => $paymentMethods,
            'user' => $user,
            'isSnackbar' => $isSnackbar,
            'snackbarText' => $snackbarText,
            'snackbarType' => $snackbarType
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
            $this -> showAction(true, 'Kategoria została dodana', 'success');
        } else {
            $this -> showAction(true, 'Nie dodano kategorii. Uzupełnij dane poprawnie', 'fault');
        }
    }

    /**
     * Edit category data
     * 
     * @return void
     */
    public function editCategoryAction() {
        $editedCategory = new Categories($_GET);
        $operation = $this -> route_params['operation'];

        if($editedCategory -> editCategoryInDatabase($operation, $_SESSION['user_id'])) {
            $this -> showAction(true, 'Kategoria została zmieniona', 'success');
        } else {
            $this -> showAction(true, 'Nie zmieniono kategorii. Uzupełnij dane poprawnie', 'fault');
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
            $this -> showAction(true, 'Kategoria została usunięta', 'success');
        } else {
            $this -> showAction(true, 'Nie usunięto kategorii', 'fault');
        }
    }

    /**
     * Delete all operations from database
     *
     * @return void
     */
    public function clearOperationDataAction() {
        Categories::clearUserDatabase($_SESSION['user_id']);
        $this -> showAction(true, 'Baza została wyczyszczona', 'success');
    }

    /**
     * Delete user account
     *
     * @return void
     */
    public function deleteAccountAction() {
        Categories::deleteUserOperationData($_SESSION['user_id']);
        User::deleteUserAccount($_SESSION['user_id']);
        View::renderTemplate('Settings/userDeleted.html');
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
            $this -> showAction(true, 'Imię zostało zmienione', 'success');
        } else {
            $this -> showAction(true, 'Nie podano imienia', 'fault');
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