<?php
namespace src\controllers;

use \core\Controller;
use \src\helpers\UserHelper;

class SearchController extends Controller {

    private $loggedUser;

    public function __construct()
    {
        $this->loggedUser = UserHelper::checkLogin();
        if ($this->loggedUser === false){
            $this->redirect('/login');
        }
        
    }

    public function index() {
       $search = filter_input(INPUT_GET, 's');

       if(empty($search)){
           $this->redirect('/');
       }

       $usersList = UserHelper::searchUser($search);

       $this->render('pesquisa', [
            'loggedUser' => $this->loggedUser,
            'search' => $search,
            'usersList' => $usersList
       ]);
    }

}