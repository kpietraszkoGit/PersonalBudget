<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Flash;

/**
 * Signup controller
 *
 * PHP version 7.0
 */
class Signup extends \Core\Controller
{

    /**
     * Show the signup page
     *
     * @return void
     */
    public function newAction()
    {
        View::renderTemplate('Signup/new.html');
    }

    /**
     * Sign up a new user
     *
     * @return void
     */
    public function createAction()
    {
        $user = new User($_POST);
		$user->save();
		$default1 = new User($_POST);
		$default1->addDefaultTableIncomes();
		$default2 = new User($_POST);
		$default2->addDefaultTableExpenses();
		$default3 = new User($_POST);

        if ($default3->addDefaultTablePayment()) {
			
			Flash::addMessage('Rejestracja powiodła się');
            $this->redirect('/signup/success');

        } else {

			Flash::addMessage('Rejestracja nie powiodła się, spróbuj jeszcze raz', Flash::WARNING);
            View::renderTemplate('Signup/new.html', [
                'user' => $user
            ]);

        }
    }
		
	/**
	 * Show the signup success page
	 *
	 * @return void
	 */
	public function successAction()
	{
		View::renderTemplate('Signup/success.html');
	}
}
