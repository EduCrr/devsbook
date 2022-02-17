<?php $render('header', ['loggedUser' => $loggedUser]); ?>
    <section class="container main">
    <?php $render('aside', [
            'menuActive' => 'home'
       ]); ?>
        <section class="feed mt-10">        
            <div class="row">
                <div class="column pr-5">
                 <?php $render('feedEditor',  ['user' => $loggedUser]);?>
                    <?php foreach($feed['posts'] as $item):?>
                               
                        <?php $render('feedItem', [
                            'data' => $item,
                            'loggedUser' => $loggedUser,
                        ]);?>
                
                    <?php endforeach?>
                    <div class="feedPagination">
                        <?php for($i = 0; $i < $feed['pageCount']; $i++):?>
                            <a class="<?= ($feed['currentPage'] === $i ? 'active' : '')?>" href="<?=$base?>/?page=<?=$i?>"><?=$i+1?></a>
                        <?php endfor?>
                    </div>
                </div>
                <div class="column side pl-5">
                    <div class="box banners">
                        <div class="box-header">
                            <div class="box-header-text">Patrocinios</div>
                            <div class="box-header-buttons">
                                
                            </div>
                        </div>
                        <div class="box-body">
                            <a href=""><img src="https://alunos.b7web.com.br/media/courses/php.jpg" /></a>
                            <a href=""><img src="https://alunos.b7web.com.br/media/courses/laravel.jpg" /></a>
                        </div>
                    </div>
                    <div class="box">
                        <div class="box-body m-10">
                            Criado com ❤️ por Eduardo
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </section>
<?php $render('footer');?>