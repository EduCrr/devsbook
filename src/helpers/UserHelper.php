<?php
namespace src\helpers;
use \src\models\User;
use \src\models\UserRelation;
use \src\models\Post;

class UserHelper{
    //verificar se está logado e procurar o usuario que bate com o token e acessar o index
    public static function checkLogin(){
        if(!empty($_SESSION['token'])){

            $token = $_SESSION['token'];

            $data = User::select()->where('token', $token)->one();

            if(count($data) > 0){

                $loggedUser = new User();
                $loggedUser->id = $data['id'];
                $loggedUser->name = $data['name'];
                $loggedUser->email = $data['email'];
                $loggedUser->avatar = $data['avatar'];
                $loggedUser->cover = $data['cover'];
                $loggedUser->birthdate = $data['birthdate'];
                $loggedUser->work = $data['work'];
                $loggedUser->city = $data['city'];

                return $loggedUser;
            }
        }
        return false;
    }

    //verificar login e gerar token para usuario
    public static function verifyLogin($email, $password){
        $user = User::select()->where('email', $email)->one();

        if($user){
            if(password_verify($password, $user['password'])){
                //gerar token
                $token = md5(time().rand(0, 9999).time());

                $user = User::update()->set('token', $token)->where('email', $email)->execute();
                return $token;
            }
        }
        return false;
    }

    public static function emailExists($email){
        $user = User::select()->where('email', $email)->one();
        return $user ? true : false;
    }

    public static function idExists($id){
        $user = User::select()->where('id', $id)->one();
        return $user ? true : false;
    }

    public static function getUser($id, $full = false){
        $data = User::select()->where('id', $id)->one();

        if($data){
            $user = New User();
            $user->id = $data['id'];
            $user->name = $data['name'];
            $user->birthdate = $data['birthdate'];
            $user->city = $data['city'];
            $user->work = $data['work'];
            $user->cover = $data['cover'];
            $user->avatar = $data['avatar'];

            if($full){
                $user->followers = [];
                $user->following = [];
                $user->photos = [];
            }

            //SEGUIDORES
            $followers = UserRelation::select()->where('user_to', $id)->get();

            foreach($followers as $item){
                //pegar id  dos seguidores
                $dataUser = User::select()->where('id', $item['user_from'])->one();
                //criar instancia de user pq estamos pegando os seguidores nome foto id
                $userFollowers = new User();
                $userFollowers->id = $dataUser['id'];
                $userFollowers->name = $dataUser['name'];
                $userFollowers->avatar = $dataUser['avatar'];

                $user->followers[] = $userFollowers;
            }

            //SEGUINDO

            $following = UserRelation::select()->where('user_from', $id)->get();

            foreach($following as $item){
                //pegar id que eu sigo
                $dataUser = User::select()->where('id', $item['user_to'])->one();
                //criar instancia de user pq estamos pegando os que eu sigo nome foto id
                $userFollowing = new User();
                $userFollowing->id = $dataUser['id'];
                $userFollowing->name = $dataUser['name'];
                $userFollowing->avatar = $dataUser['avatar'];

                $user->following[] = $userFollowing;
            }

            //PHOTOS    

            $photos = PostHelper::getPhotosFrom($id);
            $user->photos = $photos;

            return $user;
        }   

        return false;
    }

    public static function addUser($name, $email, $password, $birthdate){
        $token = md5(time().rand(0, 9999).time());
        //hash para senha
        $hash = password_hash($password, PASSWORD_DEFAULT);
        User::insert([
            'email' => $email,
            'name' => $name,
            'password' => $hash,
            'birthdate' => $birthdate,
            'token' => $token
        ])->execute();

        return $token;
    }

    public static function isFollowing($from, $to){
        //eu estou seguindo esse usuario?
       $data = UserRelation::select()->where('user_from', $from)->where('user_to', $to)->one();
        if($data){
            return true;
        }

        return false;
    }

    public static function follow($from, $to){
        UserRelation::insert([
            'user_from' => $from,
            'user_to' => $to,
        ])->execute();
    }

    public static function unfollow($from, $to){
        UserRelation::delete()->where('user_from', $from)->where('user_to', $to)->execute();
    }

    public static function searchUser($name){
        $data = User::select()->where('name', 'like', '%'.$name.'%')->get();
        $users = [];
        if($data){
            foreach($data as $item){
                $newUser = new User();
                $newUser->id = $item['id'];
                $newUser->name = $item['name'];
                $newUser->avatar = $item['avatar'];

                $users[] = $newUser;
            }
        }

        return $users;
    }

   //salvar dados

    public static function updateUserBirthdate($id, $birthdate){
        User::update()->set('birthdate', $birthdate)->where('id', $id)->execute();

    }
    public static function updateUserName($id, $name){
        User::update()->set('name', $name)->where('id', $id)->execute();

    }
    public static function updateUserEmail($id, $email){
        User::update()->set('email', $email)->where('id', $id)->execute();

    }
    public static function updateUserWork($id, $work){
        User::update()->set('work', $work)->where('id', $id)->execute();
    }

    public static function updateUserCity($id, $city){
        User::update()->set('city', $city)->where('id', $id)->execute();
    }

    public static function updateUserPassword($id, $password){
        $hash = password_hash($password, PASSWORD_DEFAULT);
        User::update()->set('password', $hash)->where('id', $id)->execute();
    }

    public static function updateUserAvatar($id, $avatar){
        User::update()->set('avatar', $avatar)->where('id', $id)->execute();
    }

    public static function updateUserCover($id, $cover){
        User::update()->set('cover', $cover)->where('id', $id)->execute();
    }
}