<?php
namespace src\controllers;

use \core\Controller;
use \src\helpers\UserHelper;

class ConfigController extends Controller {

    private $loggedUser;

    public function __construct()
    {
        $this->loggedUser = UserHelper::checkLogin();
        if ($this->loggedUser === false){
            $this->redirect('/login');
        }
        
    }

    public function index() {
        $flash = '';
        if(!empty($_SESSION['flash'])){
            $flash = $_SESSION['flash'];
            $_SESSION['flash'] = '';
        }

       $this->render('config', [
            'loggedUser' => $this->loggedUser,
            'flash' => $flash,
       ]);
    }

    public function indexAction(){
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS	);
        $birthdate = filter_input(INPUT_POST, 'birthdate', FILTER_SANITIZE_SPECIAL_CHARS	 );
        $password = filter_input(INPUT_POST, 'password');
        $confirmPassword = filter_input(INPUT_POST, 'confirmPassword');
        $work = filter_input(INPUT_POST, 'work', FILTER_SANITIZE_SPECIAL_CHARS	);
        $city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_SPECIAL_CHARS	);

        if(!empty(trim($birthdate))){
            $birthdate = explode('/', $birthdate);

            if(count($birthdate) != 3){
                $_SESSION['flash'] = 'Data de nascimento inválida!';
                $this->redirect('/config');
            }
            
            $birthdate = $birthdate[2].'-'.$birthdate[1].'-'.$birthdate[0];
            
            if(strtotime($birthdate) == false){
                $_SESSION['flash'] = 'Data de nascimento inválida!';
                $this->redirect('/config');
            }

            UserHelper::updateUserBirthdate($this->loggedUser->id, $birthdate,);

        }

        if(!empty(trim($name))){
            UserHelper::updateUserName($this->loggedUser->id, $name,);
        }else{
            $_SESSION['flash'] = 'Preencha campo nome';
        }

        if(!empty(trim($email))){
            if($email !== $this->loggedUser->email){
                if(UserHelper::emailExists($email) === false){
                    UserHelper::updateUserEmail($this->loggedUser->id, $email,);
                }else{
                    $_SESSION['flash'] = 'Email já cadastrado!';
                    $this->redirect('/config');
                }
            }
           
        }
        
        //avatar
        if(isset($_FILES['avatar']) && !empty($_FILES['avatar']['tmp_name'])){
            $newAvatar = $_FILES['avatar'];

            if(in_array($newAvatar['type'], ['image/jpg', 'image/jpeg', 'image/png'])){
                $avartarName = $this->cutImage($newAvatar, 200, 200, 'media/avatars');
                UserHelper::updateUserAvatar($this->loggedUser->id, $avartarName,);
            }
        }

        //cover
        if(isset($_FILES['cover']) && !empty($_FILES['cover']['tmp_name'])){
            $newCover = $_FILES['cover'];

            if(in_array($newCover['type'], ['image/jpg', 'image/jpeg', 'image/png'])){
                $coverName = $this->cutImage($newCover, 850, 310, 'media/covers');
                UserHelper::updateUserCover($this->loggedUser->id, $coverName,);
            }
        }
        
        UserHelper::updateUserWork($this->loggedUser->id, $work,);
        UserHelper::updateUserCity($this->loggedUser->id, $city,);        

        if(!empty(trim($password)) && !empty(trim($password))){
            if($password === $confirmPassword){
                UserHelper::updateUserPassword($this->loggedUser->id, $password,);
                }else{
                    $_SESSION['flash'] = 'Senhas não batem!';
                }
        }else{
            $this->redirect('/config');
        }

        $this->redirect('/config');

    }

    private function cutImage($file, $w, $h, $folder){
        list($widthOrigin, $heightOrigin) = getimagesize($file['tmp_name']);
        $ratio = $widthOrigin / $heightOrigin;

        $newWidth = $w;
        $newHeight = $newWidth / $ratio;

        if($newHeight < $h){
            $newHeight = $h;
            $newWidth = $newHeight * $ratio;
        }

        $x = $w - $newWidth;
        $y = $h - $newHeight;
        $x = $x < 0 ? $x / 2 : $x;
        $y = $y < 0 ? $y / 2 : $y;

        $finalImage = imagecreatetruecolor($w, $h);
        switch($file['type']){
            case 'image/jpg':
            case 'image/jpeg':
                $image = imagecreatefromjpeg($file['tmp_name']);
            break;
            case 'image/png':
                $image = imagecreatefrompng($file['tmp_name']);
            break;
        }

        imagecopyresized($finalImage, $image, $x, $y, 0, 0, $newWidth, $newHeight, $widthOrigin, $heightOrigin);

        $fileName = md5(time().rand(0, 9999)).'.jpg';

        imagejpeg($finalImage, $folder.'/'.$fileName);

        return $fileName;
    }

}