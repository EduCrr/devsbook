<?php
namespace src\controllers;

use \core\Controller;
use \src\helpers\UserHelper;
use src\helpers\PostHelper;

class AjaxController extends Controller {

    private $loggedUser;

    public function __construct()
    {
        $this->loggedUser = UserHelper::checkLogin();
        if ($this->loggedUser === false){
           header("Content-Type: application/json");
           echo json_encode(['error'=> "Usuário não logado"]);
           exit;
        }
        
    }

    public function like($atts) {
       $id = $atts['id'];

       if(PostHelper::isLiked($id, $this->loggedUser->id)){
           //delete like
            PostHelper::delLike($id, $this->loggedUser->id);
        }else{
           //insere
           PostHelper::addLike($id, $this->loggedUser->id);
       }

    }

    public function comment() {
        $array = ['error'=> ''];
        $id = filter_input(INPUT_POST, 'id');
        $txt = filter_input(INPUT_POST, 'txt');

        if($id && $txt){
            PostHelper::addComment($id, $txt, $this->loggedUser->id);
            $array['name'] = $this->loggedUser->name;
            $array['avatar'] = '/media/avatars/'.$this->loggedUser->avatar;
            $array['link'] = '/perfil/'.$this->loggedUser->id;
            $array['body'] = $txt;
        }

        header("Content-Type: application/json");
           echo json_encode($array);
           exit;
     }

     public function photo() {
        $array = ['error'=> ''];
        
        if(isset($_FILES['photo']) && !empty($_FILES['photo']['tmp_name'])){
            $photo = $_FILES['photo'];
            //tamanho maximo
            $maxWidht = 800;
            $maxHeight = 800;

            if(in_array($photo['type'], ['image/png', 'image/jpg', 'image/jpeg'])){
                list($widthOrigin, $heightOrigin) = getimagesize($photo['tmp_name']);
                $ratio = $widthOrigin / $heightOrigin;
                $newWidth = $maxWidht;
                $newHeight = $maxHeight;
                $ratioMax = $maxWidht / $maxHeight;

                if($ratioMax > $ratio){
                    $newWidth = $newHeight * $ratio;
                }else{
                    $newHeight = $newWidth / $ratio;
                }

                $finalImage = imagecreatetruecolor($newWidth, $newHeight);
                switch($photo['type']){
                    case 'image/jpg':
                    case 'image/jpeg':
                        $image = imagecreatefromjpeg($photo['tmp_name']);
                    break;
                    case 'image/png':
                        $image = imagecreatefrompng($photo['tmp_name']);
                    break;
                }

                imagecopyresized($finalImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $widthOrigin, $heightOrigin);
                
                $photoName = md5(time().rand(0, 9999)).'.jpg';

                imagejpeg($finalImage, 'media/uploads/'.$photoName);

                //enviar imagem

                PostHelper::addPost($this->loggedUser->id, 'photo', $photoName);

        
            }
        }else{
            $array['error'] = "Nenhuma imagem enviada";
        }

        
        header("Content-Type: application/json");
           echo json_encode($array);
           exit;
     }

}