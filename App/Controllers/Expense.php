<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Flash;

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
		$settingsExpenses = new User($_POST);
		$settingsPay = new User($_POST);
		$args=[];
		
		$args['rowCategoryExpenses'] = $settingsExpenses->selectCategoryExpense();
		$args['rowPay'] = $settingsPay->selectPay();

        View::renderTemplate('Expense/index.html', $args);
		
    }
	
	public function createAction()
    {
        $income = new User($_POST);

        if ($income->saveExpense()) {
			
			Flash::addMessage('Wydatek został dodany');
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
		$args=[];
		$limit = new User($_POST);
		$allExpenses = new User($_POST);
		$allamount = new User($_POST);
		$args['limitExpenses'] = $limit->selectLimitExpense();
		$args['sumrowAllExpenses'] =$allExpenses->sumExpensesAll();
		$args['amountExpense']=$allamount->amountExpenseWrite();

		View::renderTemplate('Expense/limit.html', $args);

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
