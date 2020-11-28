<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Flash;
use \App\Auth;


/**
 * Items controller (example)
 *
 * PHP version 7.0
 */
class Settings extends Authenticated
{
    /**
     * Items index
     *
     * @return void
     */
	protected function before()
    {
        parent::before();

        $this->user = Auth::getUser();
    }
    /**
     * Show the profile
     *
     * @return void
     */

    public function indexAction()
    {
		$settingsExpenses = new User($_POST);
		$settingsIncomesRemove = new User($_POST);
		$settingsExpensesRemove = new User($_POST);
		$settingsPay = new User($_POST);
		$args=[];

		$args['rowCategory'] = $settingsExpenses->selectCategoryExpense();
		$args['rowCategoryIncomes'] = $settingsIncomesRemove->selectCategoryIncomeRemove();
		$args['rowCategoryExpenses'] = $settingsExpensesRemove->selectCategoryExpenseRemove();
		$args['rowPay'] = $settingsPay->selectPayRemove();
		$args['user'] = $this->user;
        View::renderTemplate('Settings/index.html', $args);
    }

    /**
     * Sign up a new user
     *
     * @return void
     */
    public function createAction()
    {
        $settings= new User($_POST);


        if ($settings->addCategoryIncome()) {
			
			Flash::addMessage('Nowa kategoria przychodów została dodana');
            $this->redirect('/settings/index');

        } else {

			Flash::addMessage('Nowa kategoria przychodów nie została dodana bo już istnieje', Flash::WARNING);
			$this->redirect('/settings/index');
        }

    }
	
	public function createExpensesAction()
    {
       
		$settingsExpenses = new User($_POST);
		
        if ($settingsExpenses->addCategoryExpense()) {
			
			Flash::addMessage('Nowa kategoria wydatków została dodana');
            $this->redirect('/settings/index');

        } else {

			Flash::addMessage('Nowa kategoria wydatków nie została dodana bo już istnieje', Flash::WARNING);
			$this->redirect('/settings/index');
        }
    }
	
	public function createPayAction()
    {
       
		$settingsPay = new User($_POST);
		
        if ($settingsPay->addPay()) {
			
			Flash::addMessage('Nowy sposób płatności został dodany');
            $this->redirect('/settings/index');

        } else {

			Flash::addMessage('Nowy sposób płatności nie został dodany bo już istnieje', Flash::WARNING);
            $this->redirect('/settings/index');
        }
    }
	
	public function deleteAction()
	{
		$settingsIdRemove = new User($_POST);
		$settingsRemove = new User($_POST);
		$settingsIdRemove->removeIdCategoryIncome();
		if ($settingsRemove->removeCategoryIncome()){
			
			Flash::addMessage('Wybrana kategoria przychodów została usunięta');
            $this->redirect('/settings/index');

        } else {

			Flash::addMessage('Wybrana kategoria przychodów nie została usunięta', Flash::WARNING);
            $this->redirect('/settings/index');
        }
	}
	
	
	public function deleteExpensesAction()
	{
		$settingsIdRemoveExpenses = new User($_POST);
		$settingsRemoveExpenses = new User($_POST);
		$settingsIdRemoveExpenses->removeIdCategoryExpense();
		if ($settingsRemoveExpenses->removeCategoryExpense()){
			
			Flash::addMessage('Wybrana kategoria wydatków została usunięta');
            $this->redirect('/settings/index');

        } else {

			Flash::addMessage('Wybrana kategoria wydatków nie została usunięta', Flash::WARNING);
            $this->redirect('/settings/index');
        }
	}

	
	public function deletePayAction()
	{
		$settingsIdRemovePay = new User($_POST);
		$settingsRemovePay = new User($_POST);
		$settingsIdRemovePay->removeIdPay();
		if ($settingsRemovePay->removePay()){
			
			Flash::addMessage('Wybrany sposób płatności został usunięty');
            $this->redirect('/settings/index');

        } else {

			Flash::addMessage('Wybrany sposób płatności nie został usunięty', Flash::WARNING);
			$this->redirect('/settings/index');
        }
	}
	
	public function removeAll()
	{
		$deleteAllIncomes = new User($_POST);
		$deleteAllExpenses = new User($_POST);
		$deleteAllIncomes->removeAllIncomes();
		if ($deleteAllExpenses->removeAllExpenses()){
			
			Flash::addMessage('Wszystkie przychody i wydatki zostały usunięte');
            $this->redirect('/settings/index');

        } else {

			Flash::addMessage('Przychody i wydatki nie zostały usunięte', Flash::WARNING);
			$this->redirect('/settings/index');
        }
	}
	
	
	public function removeUser()
	{
		$deleteAllIncomes = new User($_POST);
		$deleteAllExpenses = new User($_POST);
		$deleteAllIncomesCategory = new User($_POST);
		$deleteAllExpensesCategory = new User($_POST);
		$deleteAllPaymentCategory = new User($_POST);		
		$deleteUser = new User($_POST);
		$deleteAllIncomes->removeAllIncomes();
		$deleteAllExpenses->removeAllExpenses();
		$deleteAllIncomesCategory->removeAllIncomesCategory();
		$deleteAllExpensesCategory->removeAllExpensesCategory();
		$deleteAllPaymentCategory->removeAllPaymentCategory();
		if ($deleteUser->removeUserFromApp()){
			
			Flash::addMessage('Twoje konto zostało usunięte');
            $this->redirect('/logout');

        } else {

			Flash::addMessage('Twoje konto nie zostało usunięte', Flash::WARNING);
			$this->redirect('/settings/index');
        }
	}
	

	public function editAction()
    {
		$settingsUpdate = new User($_POST);
		
		if ($settingsUpdate->updateCategoryIncome()){
						
			Flash::addMessage('Wybrana kategoria przychodów została zaktualizowana');
            $this->redirect('/settings/index');

        } else {

			Flash::addMessage('Wybrana kategoria przychodów nie została zaktualizowana, bo już istnieje', Flash::WARNING);
			$this->redirect('/settings/index');
        }
    }
	
	
	public function editExpensesAction()
    {
		$settingsUpdateExpenses = new User($_POST);
		
		if ($settingsUpdateExpenses->updateCategoryExpense()){
						
			Flash::addMessage('Wybrana kategoria wydatków została zaktualizowana');
            $this->redirect('/settings/index');

        } else {

			Flash::addMessage('Wybrana kategoria wydatków nie została zaktualizowana, bo już istnieje', Flash::WARNING);
			$this->redirect('/settings/index');
        }
    }
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function limitExpensesAction()
    {
		$settingsLimitExpenses = new User($_POST);
		
		if ($settingsLimitExpenses->limitCategoryExpense()){
						
			Flash::addMessage('Limit dla wybranego wydatku został wprowadzony');
            $this->redirect('/settings/index');

        } else {

			Flash::addMessage('Limit dla wybranego wydatku nie został wprowadzony', Flash::WARNING);
			$this->redirect('/settings/index');
        }
    }
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	public function editPayAction()
    {
		$settingsEditPay = new User($_POST);
		
		if ($settingsEditPay->updatePay()){
						
			Flash::addMessage('Wybrany sposób płatności został zaktualizowany');
            $this->redirect('/settings/index');

        } else {

			Flash::addMessage('Wybrany sposób płatności nie został zaktualizowany, bo już istnieje', Flash::WARNING);
			$this->redirect('/settings/index');
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
