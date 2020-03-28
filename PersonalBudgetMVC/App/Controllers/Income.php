<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;

/**
 * Items controller (example)
 *
 * PHP version 7.0
 */
class Income extends Authenticated
{

    /**
     * Items index
     *
     * @return void
     */
    public function indexAction()
    {
		unset($_SESSION['ok2']);
        View::renderTemplate('Income/index.html');
    }
	
	public function createAction()
    {
        $income = new User($_POST);

        if ($income->saveIncome()) {
			
			$_SESSION['ok'] = 'Przychód został dodany!';
            $this->redirect('/income/index');
			
        } else {

            View::renderTemplate('Income/index.html', [
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
