<?php $render('header', ['loggedUser' => $loggedUser]); ?>
    <section class="container main">
       <?php $render('aside', [
            'menuActive' => 'amigos'
       ]); ?>
        <section class="feed">
            <?=$render('perfilHeader', ['user'=> $user, 'loggedUser'=> $loggedUser, 'isFollowing' => $isFollowing ])?>
            <div class="row">
            <div class="column">
                    
                <div class="box">
                    <div class="box-body">
                        <div class="tabs">
                            <div class="tab-item" data-for="followers">
                                Seguidores
                            </div>
                            <div class="tab-item active" data-for="following">
                                Seguindo
                            </div>
                        </div>
                        <div class="tab-content">
                            <div class="tab-body" data-item="followers">
                                
                                <div class="full-friend-list">
                                    <?php foreach($user->followers as $item) :?>
                                        <div class="friend-icon">
                                            <a href="<?=$base?>/perfil/<?=$item->id?>">
                                                <div class="friend-icon-avatar">
                                                    <img src="<?=$base?>/media/avatars/<?=$item->avatar?>" />
                                                </div>
                                                <div class="friend-icon-name">
                                                    <?=$item->name?>
                                                </div>
                                            </a>
                                        </div>
                                    <?php endforeach?>
                                    
                                </div>

                            </div>
                            <div class="tab-body" data-item="following">
                                
                                <div class="full-friend-list">
                                    <?php foreach($user->following as $item) :?>
                                        <div class="friend-icon">
                                            <a href="<?=$base?>/perfil/<?=$item->id?>">
                                                <div class="friend-icon-avatar">
                                                    <img src="<?=$base?>/media/avatars/<?=$item->avatar?>" />
                                                </div>
                                                <div class="friend-icon-name">
                                                    <?=$item->name?>
                                                </div>
                                            </a>
                                        </div>
                                    <?php endforeach?>
                                    
                                </div>

                            </div>
                        </div>

                    </div>
                </div>

                
                                    
            </div>

        </section>
    </section>
<?php $render('footer');?>   