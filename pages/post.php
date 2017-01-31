<!-- Blog Post Content Column -->
<div class="col-lg-8">
    <!-- Blog Post -->
    <!-- Title -->
    <h1><?=$blog->post['title']?></h1>
    <!-- Date/Time -->
    <p><span class="glyphicon glyphicon-time"></span> <?=$blog->post['published_date']?></p>
    <hr>
    <!-- Post Content -->

    <div>
    <p><?=$blog->post['content']?></p>
    </div>
    <? if($blog->user): ?>
    <div>
        <form method="post">
            <input type="hidden" name="id" value="<?=$blog->post['id']?>">
            <input type="hidden" name="op" value="removePost">
            <button type="submit" class="btn btn-mini btn-danger" onclick="return confirm('Точно удалить?');"><i class="icon-trash">удалить</button>
        </form>
    </div>
    <? endif; ?>
    <hr>
    <!-- Blog Comments -->

    <!-- Comments Form -->
    <div class="well">
        <h4>Оставить коментарий:</h4>
        <form role="form" method="post">
            <input type="hidden" name="id" value="<?=$blog->post['id']?>">
            <input type="hidden" name="op" value="addComment">
            <div class="form-group">
                <h5>Имя</h5>
                <input type="text" class="form-control" name="name" value="">
                <h5>Текст</h5>
                <textarea class="form-control" name="comment" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Отправить</button>
            <span class="help-inline"><?=@$blog->error?></span>
        </form>
    </div>
    <!-- Posted Comments -->

    <? foreach ($blog->comments as $comment): ?>
    <!-- Comment -->

        <hr>
    <div class="media">
        <div class="media-body">
            <h4 class="media-heading"><?= $comment['author']?>
                <small><?= $comment['published_date']?></small>
            </h4>
            <?= $comment['content']?>
        </div>
    </div>
    <? endforeach;?>
</div>