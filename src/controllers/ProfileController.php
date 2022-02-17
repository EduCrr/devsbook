<?php
namespace src\controllers;

use \core\Controller;
use \src\helpers\UserHelper;
use \src\helpers\PostHelper;

class ProfileController extends Controller {

    private $loggedUser;

    public function __construct()
    {
        $this->loggedUser = UserHelper::checkLogin();
        if ($this->loggedUser === false){
            $this->redirect('/login');
        }
        
    }

    public function index($atts = []) {

        $id = $this->loggedUser->id;
        //detectando o usuario acessado
        if(!empty($atts['id'])){
            $id = $atts['id'];
        }
        //pegando dados do user
        $user = UserHelper::getUser($id, true);

        if(!$user){
            $this->redirect('/');
        }

        $dateFrom = new \DateTime($user->birthdate);
        $dateTo = new \DateTime('today');
        $user->ageYears = $dateFrom->diff($dateTo)->y;
        
        $page = intval(filter_input(INPUT_GET, 'page'));
        //pegando post do user
        $feed = PostHelper::getUserFeed($id, $page, $this->loggedUser->id);

        //verificar se eu sigo o user
        $isFollowing = false;
        if($user->id !== $this->loggedUser->id){
            //retorna true ou false         //logado //id do user da pg
            $isFollowing = UserHelper::isFollowing($this->loggedUser->id, $user->id);
        }

        $this->render('perfil', [
            'loggedUser' => $this->loggedUser, //dados usuario logado
            'feed' => $feed,
            'user' => $user, //qual usuario
            'isFollowing' => $isFollowing,
        ]);
    }

    public function follow($atts){
        $to = intval($atts['id']);

        $exists = UserHelper::idExists($to);

        if($exists){

            if(UserHelper::isFollowing($this->loggedUser->id, $to)){
                //desseguir
                UserHelper::unfollow($this->loggedUser->id, $to);
            }else{
                //seguir 
                UserHelper::follow($this->loggedUser->id, $to);
            }

        }

        $this->redirect('/perfil/'.$to);
    }

    public function friends($atts = []){
        $id = $this->loggedUser->id;
        //detectando o usuario acessado
        if(!empty($atts['id'])){
            $id = $atts['id'];
        }
        //pegando dados do user
        $user = UserHelper::getUser($id, true);

        if(!$user){
            $this->redirect('/');
        }

        $dateFrom = new \DateTime($user->birthdate);
        $dateTo = new \DateTime('today');
        $user->ageYears = $dateFrom->diff($dateTo)->y;

        //verificar se eu sigo o user
        $isFollowing = false;
        if($user->id !== $this->loggedUser->id){
            //retorna true ou false         //logado //id do user da pg
            $isFollowing = UserHelper::isFollowing($this->loggedUser->id, $user->id);
        }

        $this->render('amigos', [
            'loggedUser' => $this->loggedUser, //dados usuario logado
            'user' => $user, //qual usuario
            'isFollowing' => $isFollowing,
        ]);   
    }

    public function photos($atts = []){
        $id = $this->loggedUser->id;
        //detectando o usuario acessado
        if(!empty($atts['id'])){
            $id = $atts['id'];
        }
        //pegando dados do user
        $user = UserHelper::getUser($id, true);

        if(!$user){
            $this->redirect('/');
        }

        $dateFrom = new \DateTime($user->birthdate);
        $dateTo = new \DateTime('today');
        $user->ageYears = $dateFrom->diff($dateTo)->y;

        //verificar se eu sigo o user
        $isFollowing = false;
        if($user->id !== $this->loggedUser->id){
            //retorna true ou false         //logado //id do user da pg
            $isFollowing = UserHelper::isFollowing($this->loggedUser->id, $user->id);
        }
        
        $this->render('fotos', [
            'loggedUser' => $this->loggedUser, //dados usuario logado
            'user' => $user, //qual usuario
            'isFollowing' => $isFollowing,
        ]);   
    }

}