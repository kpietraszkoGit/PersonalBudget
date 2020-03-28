<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;

/**
 * Items controller (example)
 *
 * PHP version 7.0
 */
class Expense extends Authenticated
{

    /**
     * Items index
     *
     * @return void
     */
    public function indexAction()
    {
		unset($_SESSION['ok']);
        View::renderTemplate('Expense/index.html');
    }
	
	public function createAction()
    {
        $income = new User($_POST);

        if ($income->saveExpense()) {
			
			$_SESSION['ok2'] = 'Wydatek został dodany!';
            $this->redirect('/expense/index');
			
        } else {

            View::renderTemplate('Expense/index.html', [
                'user' => $user
            ]);
			
        }
    }
    /**
     * Add a new item
     *
     * @return void
     */
    public function newAction()
    {
        echo "new action";
    }

    /**
     * Show an item
     *
     * @return void
     */
    public function showAction()
    {
        echo "show action";
    }
}
