<?php $render('header', ['loggedUser' => $loggedUser]); ?>
    <section class="container main">
       <?php $render('aside', [
            'menuActive' => 'pesquisa'
       ]); ?>
        <section class="feed">
            <div class="full-friend-list">
                <?php foreach($usersList as $item) :?>
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
        </section>
    </section>
<?php $render('footer');?>   