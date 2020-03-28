<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;

/**
 * Items controller (example)
 *
 * PHP version 7.0
 */
class Yearbalance extends Authenticated
{

    /**
     * Items index
     *
     * @return void
     */
    public function indexAction()
    {
		unset($_SESSION['ok']);
		unset($_SESSION['ok2']);
		$yearbalance = new User($_POST);
		$args=[];
		
		$args['rowIncomes'] = $yearbalance->incomesyearbalance();
		$args['rowExpenses'] = $yearbalance->expensesyearbalance();
		$args['sumrowIncomes'] = $yearbalance->sumincomesyearbalance();
		$args['sumrowExpenses'] = $yearbalance->sumexpensesyearbalance();
		
        View::renderTemplate('Yearbalance/index.html', $args);

    }

	public function createAction()
    {

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
