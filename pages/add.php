<!-- Add Post Form -->
<div class="well">
    <form role="form" method="post">
        <input type="hidden" name="op" value="addPost">
        <div class="form-group">
            <h5>Заголовок</h5>
            <input type="text" class="form-control" name="title" value="">
            <h5>Текст</h5>
            <textarea class="form-control" name="post" style="height: 300px;"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Добавить пост</button>
        <span class="help-inline"><?=@$blog->error?></span>
    </form>
</div>