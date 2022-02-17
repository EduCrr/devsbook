<?php
namespace src\controllers;

use \core\Controller;
use \src\helpers\UserHelper;

class LoginController extends Controller {


    public function signin() {
        $flash = '';
        if(!empty($_SESSION['flash'])){
            $flash = $_SESSION['flash'];
            $_SESSION['flash'] = '';
        }
        $this->render('login', [
            'flash' => $flash,
        ]);
    }

    public function signinAction() {

        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = filter_input(INPUT_POST, 'password');

        if($email && $password){

            $token = UserHelper::verifyLogin($email, $password);

            if($token){
                $_SESSION['token'] = $token;
                $this->redirect('/');

            }else{
                $_SESSION['flash'] = 'Dados inválidos';
                $this->redirect('/login');

            }


        }else{
            $_SESSION['flash'] = 'Digite os dados';
            $this->redirect('/login');
        }
    }

    
    public function signup() {
        $flash = '';
        if(!empty($_SESSION['flash'])){
            $flash = $_SESSION['flash'];
            $_SESSION['flash'] = '';
        }
        $this->render('cadastro', [
            'flash' => $flash,
        ]);   
    }

    public function signupAction(){
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $name = filter_input(INPUT_POST, 'name');
        $birthdate = filter_input(INPUT_POST, 'birthdate' );
        $password = filter_input(INPUT_POST, 'password');

        if($name && $email && $password && $birthdate){
            $birthdate = explode('/', $birthdate);

            if(count($birthdate) != 3){
                $_SESSION['flash'] = 'Data de nascimento inválida!';
                $this->redirect('/cadastro');
            }
            
            $birthdate = $birthdate[2].'-'.$birthdate[1].'-'.$birthdate[0];
            
            if(strtotime($birthdate) == false){
                $_SESSION['flash'] = 'Data de nascimento inválida!';
                $this->redirect('/cadastro');
            }

            //se n tem email cadastrado, faça cadastro
            if(UserHelper::emailExists($email) === false){
                $token = UserHelper::addUser($name, $email, $password, $birthdate);
                $_SESSION['token'] = $token;
                $this->redirect('/');

            }else{
                $_SESSION['flash'] = 'Email já cadastrado!';
                $this->redirect('/cadastro');

            }
            

        }else{
            $this->redirect('/cadastro');
        }
    }

    public function logout(){
        $_SESSION['token'] = '';
        $this->redirect('/login');   
    }

  
}