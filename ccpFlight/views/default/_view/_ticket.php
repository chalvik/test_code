<?php
/**
 * Created by PhpStorm.
 * User: Chernogor Alexey
 * Date: 08.12.17
 * Time: 13:01
 */

$count_message = count($ticket->messages);

use common\modules\ccpUser\models\User;

?>

<div class="col-md-4">
    <!-- DIRECT CHAT -->
    <div class="box box-warning direct-chat direct-chat-warning">
        <div class="box-header with-border">
            <h3 class="box-title">
                <?=(isset($ticket->theme->title))?$ticket->theme->title:"Error link" ?>
            </h3>

            <div class="box-tools pull-right">
                <span data-toggle="tooltip" title="" class="badge bg-yellow" data-original-title="3 New Messages">
                    <?=$count_message; ?>
                </span>
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
            </div>
        </div>

        <!-- /.box-header -->
        <div class="box-body">
            <!-- Conversations are loaded here -->
            <div class="direct-chat-messages">

                <?php if ($count_message) : ?>
                    <?php foreach ($ticket->messages as $message) : ?>
                        <div class="direct-chat-msg <?php if ($message->user->type == User::TYPE_SSO) :?>right<?php endif;?>"> <?php // right or left?>
                            <div class="direct-chat-info clearfix">
                                <span class="direct-chat-name pull-left">
                                    <?php if ($message->user->type == User::TYPE_SSO) :?>
                                        <?= $message->user->email  ?>
                                    <?php else :?>
                                        <?= $message->user->roster_id  ?>
                                    <?php endif; ?>
                                </span>
                                <span class="direct-chat-timestamp pull-right"><?= $message->created_at ?></span>
                            </div>
                            <!-- /.direct-chat-info -->
<!--                            <img class="direct-chat-img" src="dist/img/user1-128x128.jpg" alt="message user image">-->
                            <div class="direct-chat-text">
                                <?= $message->message ?>
                            </div>
                       </div>
                    <?php endforeach; ?>
                <?php else :?>
                    <p> Not found messages </p>
                <?php endif;?>

            </div>
            <!--/.direct-chat-messages-->


            <!-- /.direct-chat-pane -->
        </div>
        <!-- /.box-body -->
        <div class="box-footer">
<!--            <form action="#" method="post">-->
<!--                <div class="input-group">-->
<!--                    <input type="text" name="message" placeholder="Type Message ..."-->
<!--                           class="form-control">-->
<!--                    <span class="input-group-btn">-->
<!--                            <button type="button" class="btn btn-warning btn-flat">Send</button>-->
<!--                          </span>-->
<!--                </div>-->
<!--            </form>-->
        </div>
        <!-- /.box-footer-->
    </div>
    <!--/.direct-chat -->
</div>
