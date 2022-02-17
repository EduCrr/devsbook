<?php $render('header', ['loggedUser' => $loggedUser]); ?>
    <section class="container main">
       <?php $render('aside', [
            'menuActive' => 'perfil'
       ]); ?>
        <section class="feed">
            <?=$render('perfilHeader', ['user'=> $user, 'loggedUser'=> $loggedUser, 'isFollowing' => $isFollowing ])?>
            <div class="row">
                <div class="column side pr-5">
                    <div class="box">
                        <div class="box-body">
                            
                            <div class="user-info-mini">
                                <img src="<?=$base?>/assets/images/calendar.png" />
                                <?=date('d/m/Y', strtotime($user->birthdate)) ?> (<?= $user->ageYears;?> anos)
                            </div>
                            <?php if(!empty($user->city)) :?>
                                <div class="user-info-mini">
                                    <img src="<?=$base?>/assets/images/pin.png" />
                                    <?=$user->city?>
                                </div>
                            <?php endif;?>
                            <?php if(!empty($user->work)) :?>
                                <div class="user-info-mini">
                                    <img src="<?=$base?>/assets/images/work.png" />
                                    <?=$user->work?>
                                </div>
                            <?php endif;?>
                        </div>
                    </div>

                    <div class="box">
                        <div class="box-header m-10">
                            <div class="box-header-text">
                                Seguindo
                                <span>(<?=count($user->following)?>)</span>
                            </div>
                            <div class="box-header-buttons">
                                <a href="<?=$base?>/perfil/<?=$user->id?>/amigos">ver todos</a>
                            </div>
                        </div>
                        <div class="box-body friend-list">

                                <?php for($i = 0; $i < 9; $i++): ?>
                                    <?php if(isset($user->following[$i])): ?>
                                        <div class="friend-icon">
                                            <a href="<?=$base?>/perfil/<?=$user->following[$i]->id?>">
                                                <div class="friend-icon-avatar">
                                                    <img src="<?=$base?>/media/avatars/<?=$user->following[$i]->avatar?>" />
                                                </div>
                                                <div class="friend-icon-name">
                                                    <?=$user->following[$i]->name?>
                                                </div>
                                            </a>
                                        </div>
                                    <?php endif?>
                                <?php endfor ?>
                          
                        </div>
                    </div>

                </div>
                <div class="column pl-5">

                    <div class="box">
                        <div class="box-header m-10">
                            <div class="box-header-text">
                                Fotos
                                <span>(<?=count($user->photos)?>)</span>
                            </div>
                            <div class="box-header-buttons">
                                <a href="<?=$base?>/perfil/<?=$user->id?>/fotos">ver todos</a>
                            </div>
                        </div>
                        <div class="box-body row m-20">
                            <?php for($i=0; $i < 4; $i++):?>
                                <?php if(isset($user->photos[$i])):?>
                                    <div class="user-photo-item">
                                        <a href="#modal-<?=$user->photos[$i]->id?>" rel="modal:open">
                                            <img src="<?=$base?>/media/uploads/<?=$user->photos[$i]->body?>" />
                                        </a>
                                        <div id="modal-<?=$user->photos[$i]->id?>" style="display:none">
                                            <img src="<?=$base?>/media/uploads/<?=$user->photos[$i]->body?>" />
                                        </div>
                                    </div>
                                <?php endif?>
                            <?php endfor?>

                        </div>
                    </div>
                    <?php if($user->id === $loggedUser->id): ?>
                        <?php $render('feedEditor',  ['user' => $loggedUser]);?>
                    <?php endif ?>
                    <?php foreach($feed['posts'] as $item):?>
                               
                        <?php $render('feedItem', [
                            'data' => $item,
                            'loggedUser' => $loggedUser,
                            ]);
                        ?>
                
                    <?php endforeach?>
                    <div class="feedPagination">
                        <?php for($i = 0; $i < $feed['pageCount']; $i++):?>
                            <a class="<?= ($feed['currentPage'] === $i ? 'active' : '')?>" href="<?=$base;?>/perfil/<?=$user->id?>?page=<?=$i?>"><?=$i+1?></a>
                        <?php endfor?>
                    </div>

                </div>
                
            </div>

        </section>
    </section>
<?php $render('footer');?>   