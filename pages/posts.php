<!-- Blog Entries Column -->
<div class="col-md-8">
    <h1 class="page-header">Список постов</h1>
    <? foreach ($blog->posts as $post): ?>
        <!-- First Blog Post -->
        <h2><a href="/?op=get&id=<?=$post['id']?>"><?=$post['title']?></a></h2>
        <p><span class="glyphicon glyphicon-time"></span> <?=$post['published_date']?></p>
        <hr>
    <? endforeach; ?>
</div>
<!-- Blog Sidebar Widgets Column -->
<div class="col-md-4">
    <!-- Blog Search Well -->
    <div class="well">
        <h4>Поиск</h4>
        <form method="get">
            <div class="input-group">
                <input type="hidden" name="op" value="find">
                <input type="text" name="filter" class="form-control">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="submit" onclick="return send()">
                        <span class="glyphicon glyphicon-search"></span>
                    </button>
                </span>
            </div>
        </form>
        <!-- /.input-group -->
    </div>
</div>