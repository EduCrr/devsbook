<?php
namespace src\controllers;

use \core\Controller;
use \src\helpers\UserHelper;
use src\helpers\PostHelper;

class PostController extends Controller {

    private $loggedUser;

    public function __construct()
    {
        $this->loggedUser = UserHelper::checkLogin();
        if ($this->loggedUser === false){
            $this->redirect('/login');
        }
        
    }

    //criando post

    public function new() {
        $body = filter_input(INPUT_POST, 'body');
        
        if($body){
            PostHelper::addPost($this->loggedUser->id, 'text', $body);
        }
    
        $this->redirect('/');
    }


    public function delete($atts = []){
        if(!empty($atts['id'])){
            $idPost = $atts['id'];

            PostHelper::deletePost($idPost, $this->loggedUser->id);
        }

        $this->redirect('/');
        
    }

}