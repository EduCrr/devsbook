<?php $render('header', ['loggedUser' => $loggedUser]); ?>
    <section class="container main">
       <?php $render('aside', [
            'menuActive' => 'config'
       ]); ?>
       <?php
        $birthdate = explode('-', $loggedUser->birthdate);
        $birthdate = $birthdate[2].'-'.$birthdate[1].'-'.$birthdate[0];
       ?>
        <section class="feed">
                <?php if(!empty($flash)): ?>
                    <div class="flash"><?=$flash;?></div>
                <?php endif; ?>
            <form method="POST" class="config" action="<?=$base?>/config" enctype="multipart/form-data">
                <img class="media" src="<?=$base?>/media/avatars/<?=$loggedUser->avatar?>"/>
                <input name="avatar" type="file" />
                <img class="media" src="<?=$base?>/media/covers/<?=$loggedUser->cover?>"/>
                <input name="cover" type="file" />
                <input placeholder="Nome Completo" name="name" type="text" value="<?=$loggedUser->name?>"/>
                <input placeholder="Data de nascimento" name="birthdate" type="text" id="birthdate" value="<?=$birthdate?>"/>
                <input placeholder="Email" name="email" type="email" value="<?=$loggedUser->email?>"/>
                <input placeholder="Cidade" name="city" type="text" value="<?=$loggedUser->city?>"/>
                <input placeholder="Trabalho" name="work" type="text" value="<?=$loggedUser->work?>"/>
                <input placeholder="Senha" name="password" type="password"/>
                <input placeholder="Confirmar senha" name="confirmPassword" type="password"/>
                <input value="Salvar" type="submit"/>
            </form>
        </section>
    </section>
    <script src="https://unpkg.com/imask"></script>
    <script>
        IMask(
            document.getElementById('birthdate'),
            {
                mask: '00/00/0000'
            }
        );
    </script>
    <?php $render('footer');?>   