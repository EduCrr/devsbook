<?php
namespace src\helpers;
use \src\models\Post;
use \src\models\User;
use \src\models\UserRelation;
use \src\models\PostLike;
use \src\models\PostComment;

class PostHelper{

    //add post

    public static function addPost($idUser, $type, $body){
        $body = trim($body);
        if(!empty($idUser) && !empty($body)){
            Post::insert([
                'id_user' => $idUser,
                'type' => $type,
                'created_at' => date('Y-m-d H:i:s'),
                'body' => $body
            ])->execute();
        }
    }

    public function _postListToObject($postList, $loggedUserId){
        $posts = [];

        foreach($postList as $item){
            $newPost = new Post();
            $newPost->id = $item['id'];
            $newPost->type = $item['type'];
            $newPost->body = $item['body'];
            $newPost->created_at = $item['created_at'];
            $newPost->mine = false;

            if($item['id_user'] === $loggedUserId){
                $newPost->mine = true;
            }
            
            // PREENCHER AS INFORMAÇOES ADICIONAIS NO POST
            $newUser = User::select()->where('id', $item['id_user'])->one();
            $newPost->user = new User();
            $newPost->user->id = $newUser['id'];
            $newPost->user->name = $newUser['name'];
            $newPost->user->avatar = $newUser['avatar'];
             
            //PREENCHER LIKES
            $likes = PostLike::select()->where('id_post', $item['id'])->get();
            
            $newPost->likeCount = count($likes);
            $newPost->liked = self::isLiked($item['id'], $loggedUserId);
            //PREENCHER COMMENTS

            $newPost->comments = PostComment::select()->where('id_post', $item['id'])->get();
            foreach($newPost->comments as $key => $item){
                $newPost->comments[$key]['user'] = User::select()->where('id', $item['id_user'])->one();
            }
            $posts[] = $newPost;
        };

        return $posts;
    }

    public static function isLiked($id, $loggedUserId){
        $myLike = PostLike::select()->where('id_post', $id)->where('id_user', $loggedUserId)->get();

        if(count($myLike) > 0){
            return true;
        }else{
            return false;
        }
    }
    public static function delLike($id, $loggedUserId){
        PostLike::delete()->where('id_post', $id)->where('id_user', $loggedUserId)->execute();
    }
    public static function addLike($id, $loggedUserId){
        
        PostLike::insert([
            'id_post'=> $id,
            'id_user'=> $loggedUserId,
            'created_at' => date('Y-m-d H:i:s'),
        ])->execute();
    }
    //listar post
    public static function getHomeFeed($idUser, $page){
        // PEGAR LISTA DE USER QUE EU SIGO
        $perPage = 2;
        $userList = UserRelation::select()->where('user_from', $idUser)->get();
        $users = [];

        foreach($userList as $item){
            $users[] = $item['user_to'];
        }

        $users[] = $idUser;

        // PEGAR POST DA GALERA QUE EU SIGO E EU ORDENADO PELA DATA
        $postList = Post::select()->where('id_user', 'in', $users)->orderBy('created_at', 'desc')->page($page, $perPage)->get();
        $total = Post::select()->where('id_user', 'in', $users)->count();

        //total pages
        $pageCount = ceil($total / $perPage);

        // TRANSFORMA EM OBJ DOS MODELS
        $posts = self::_postListToObject($postList, $idUser);

        // RETORNAR RESULTADO
        return [
            'posts' => $posts,
            'pageCount' => $pageCount,
            'currentPage' => $page
        ];

    }

    public static function getUserFeed($idUser, $page, $loggedUserId){
        $perPage = 2;
        // PEGAR POST meu post
        $postList = Post::select()->where('id_user',  $idUser)->orderBy('created_at', 'desc')->page($page, $perPage)->get();
        $total = Post::select()->where('id_user', $idUser)->count();

        //total pages
        $pageCount = ceil($total / $perPage);

        // TRANSFORMA EM OBJ DOS MODELS
        $posts = self::_postListToObject($postList, $loggedUserId);

        // RETORNAR RESULTADO
        return [
            'posts' => $posts,
            'pageCount' => $pageCount,
            'currentPage' => $page
        ];
    }

    public static function getPhotosFrom($idUser){
        $photoList = Post::select()->where('id_user', $idUser)->where('type', 'photo')->get();
        $photos = [];

        foreach($photoList as $item){
            $newPost = new Post();
            $newPost->id = $item['id'];
            $newPost->type = $item['type'];
            $newPost->created_at = $item['created_at'];
            $newPost->body = $item['body'];

            $photos[] = $newPost;
            
        }

        return $photos;
    }

    public static function addComment($id, $txt, $loggedUserId){
        PostComment::insert([
            'id_post' => $id,
            'id_user' => $loggedUserId,
            'created_at' => date('Y-m-d H:i:s'),
            'body' => $txt
        ])->execute();
    }


    public static function deletePost($idPost, $loggedUserId){
        // o post existe e seu?
        $post = Post::select()->where('id', $idPost)->where('id_user', $loggedUserId)->get();
        
        if(count($post) > 0){
            $post = $post[0];

            //deletar likes commets
            PostLike::delete()->where('id_post', $idPost)->execute();
            PostComment::delete()->where('id_post', $idPost)->execute();

            //type for photo, deletar arquivo

            if($post['type'] === 'photo'){
                $img = __DIR__.'/../../public/media/uploads/'.$post['body'];
                if(file_exists($img)){
                    unlink($img);
                }
            }
            //deletar foto
            Post::delete()->where('id', $idPost)->execute();

        }
        


    }
}