<?php
/**
 * Created by PhpStorm.
 * User: Chernogor Alexey
 * Date: 08.12.17
 * Time: 13:09
 */
?>


<div class="row">
    <?php foreach ($model->chatTickets as $ticket) :?>
        <?=$this->render('_ticket', ['ticket' => $ticket]); ?>
    <?php endforeach; ?>
</div>

