<form class="well form-inline" method="post">
    <input type="hidden" name="op" value="login">
    <input type="text" name="email" class="input-small" placeholder="Email">
    <input type="password" name="pass" class="input-small" placeholder="Password">
    <button type="submit" class="btn">Войти</button>
    <span class="help-inline"><?=@$blog->error?></span>
</form>