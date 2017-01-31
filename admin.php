<?php
class blog
{
    function __construct()
    {
        global $config;
        // Создание экземпляра PDO для соединения с базой
        $this->PDO = new PDO($config['db']['dsn'], $config['db']['username'], $config['db']['password']);
        $errorCode = $this->PDO->errorCode();
        // Проверка на ошибки
        if($errorCode){
            throw new Exception("Connect failed: code = $errorCode");
        }
        $this->PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Создание таблиц в базе
        if(isset($config['db']['initiation']) && !empty($config['db']['initiation'])){
            if(file_exists($config['db']['initiation'])){
                $sql = file_get_contents($config['db']['initiation']);
                $this->PDO->exec($sql);
            }
            else{
                throw new Exception('File does not exists: '.$config['db']['initiation']);
            }
        }
        // Аутентификация
        if (!empty($_COOKIE['uid']) && !empty($_COOKIE['key'])) {
            $sql = "SELECT * FROM admin WHERE id = ? AND cookie = ?";
            $this->user = $this->query($sql, $_COOKIE['uid'], $_COOKIE['key'])->fetch(PDO::FETCH_ASSOC);
        }
        else {
            $this->user = false;
        }
    }
    // Авторизация
    function login($email, $pass)
    {
        $sql = "SELECT * FROM admin WHERE email = ? AND pass = ?";
        $this->user = $this->query($sql, $email, md5($pass))->fetch(PDO::FETCH_ASSOC);
        if ($this->user) {
            $id = $this->user['id'];
            $key = md5(microtime().rand(0,10000));
            setcookie('uid', $id, time()+86400*30, '/');
            setcookie('key', $key, time()+86400*30, '/');
            $this->PDO->exec("UPDATE admin SET cookie = '$key' WHERE id = '$id'");
        }
        else
            $this->error = 'Неправильный емейл или пароль';
    }
    // Выход
    function logoff()
    {
        setcookie ("uid", "", time() - 3600, '/');
        setcookie ("key", "", time() - 3600, '/');
        $this->user = null;
    }
    // Выборка данных поста
    function getPost($id)
    {
        $sql = "SELECT * FROM post WHERE id = ?";
        $this->post = $this->query($sql, $id)->fetch();
        $sql = "SELECT * FROM comment WHERE post_id = ? ORDER BY published_date DESC";
        $this->comments = $this->query($sql, $id)->fetchAll(PDO::FETCH_ASSOC);
    }
    // Выборка всех постов
    function getPosts()
    {
        $sql = "SELECT * FROM post ORDER BY published_date DESC";
        $this->posts = $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    // Поиск поста
    function findPost($filter)
    {
        $filter = "%{$filter}%";
        $sql = "SELECT * FROM post WHERE title LIKE ? ORDER BY published_date DESC";
        $this->posts = $this->query($sql, $filter)->fetchAll(PDO::FETCH_ASSOC);
    }
    // Удаление поста
    function removePost($id)
    {
        $sql = "DELETE FROM comment WHERE post_id = ?";
        $this->query($sql, $id);
        $sql = "DELETE FROM post WHERE id = ?";
        $this->query($sql, $id);
    }
    // Добавление поста
    function addPost($title, $content)
    {
        $this->PDO->beginTransaction();
        try {
            $date = date('Y-m-d H:i:s');
            $title = htmlentities($title);
            $content = htmlentities($content);
            $sql = "INSERT INTO post(title, content, published_date) values (?, ?, '$date')";
            $this->query($sql, $title, $content);
            $this->PDO->commit();
        }catch (PDOException $ex){
            $this->PDO->rollBack();
            throw new Exception($ex);
        }
    }
    // Добавление коментария
    function addComment($id, $author, $content)
    {
        $this->PDO->beginTransaction();
        try {
            $date = date('Y-m-d H:i:s');
            $author = htmlentities($author);
            $content = htmlentities($content);
            $sql = "INSERT INTO comment(post_id, author, content, published_date) values ('$id', ?, ?, '$date')";
            $this->query($sql, $author, $content);
            $this->PDO->commit();
        }catch (PDOException $ex){
            $this->PDO->rollBack();
            throw new Exception($ex);
        }
    }
    // Запрос к базе
    function query($sql)
    {
        $query = $this->PDO->prepare($sql);
        $args = func_get_args();
        foreach ($args as $key => &$val) {
            if(!$key) continue;
            $query->bindParam($key, $val);
        }
        $query->execute();
        //$query->setFetchMode(PDO::FETCH_ASSOC);
        return $query;
    }
}
try{
    // Загрузка конфигураций
    if(!$config = parse_ini_file('config/config.ini', true)){
        throw new Exception('Unable to download the configuration settings');
    }
    // Обработка POST запросов
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $blog = new blog();
        switch ($_POST['op']){
            // Вход
            case 'login':
                $blog->login($_POST['email'], $_POST['pass']);
                if($blog->user){
                    $blog->getPosts();
                    $page = 'pages/posts.php';
                    //header("Location: /admin.php");
                }
                else{
                    $page ='pages/login.php';
                }
                break;
            // Добавление поста
            case 'addPost':
                if(empty($_POST['title']) || empty($_POST['post'])){
                    $blog->error = 'Не все поля заполнены';
                    break;
                }
                if($blog->user){
                    $blog->addPost($_POST['title'], $_POST['post']);
                }
                $blog->getPosts();
                $page = 'pages/posts.php';
                break;
            // Добавление коментария
            case 'addComment':
                if(empty($_POST['name']) || empty($_POST['comment'])){
                    $blog->error = 'Не все поля заполнены';
                    break;
                }
                if(ctype_digit($_POST['id'])){
                    $blog->addComment($_POST['id'], $_POST['name'], $_POST['comment']);
                    $blog->getPost($_POST['id']);
                    $page = 'pages/post.php';
                }
                break;
            // Удаление поста
            case 'removePost':
                if($blog->user && !empty($_POST['id'])){
                    $blog->removePost($_POST['id']);
                }
                $blog->getPosts();
                $page = 'pages/posts.php';
                break;
        }
        include('pages/main.php');
        exit();
    }
    // Обработка GET запросов
    if($_SERVER['REQUEST_METHOD'] == 'GET'){
        $blog = new blog();
        switch ($_GET['op']){
            // Вывод формы авторизации
            case 'login':
                $page = 'pages/login.php';
                break;
            // Выход
            case 'logoff':
                $blog->logoff();
                $blog->getPosts();
                $page = 'pages/posts.php';
                break;
            // Вывод формы добавления поста
            case 'add':
                if($blog->user){
                    $page = 'pages/add.php';
                }
                break;
            // Вывод поста
            case 'get':
                if(!empty($_GET['id'])){
                    $blog->getPost($_GET['id']);
                    $page = 'pages/post.php';
                }
                break;
            // Поиск поста
            case 'find':
                if(!empty($_GET['filter'])){
                    $blog->findPost($_GET['filter']);
                    $page = 'pages/posts.php';
                }
                break;
            // Вывод всех постов
            default:
                $blog->getPosts();
                $page = 'pages/posts.php';
                break;
        }
        include('pages/main.php');
        exit();
    }
}catch (Exception $e){
    header($_SERVER['SERVER_PROTOCOL'].' 500 Server Error');
    header('Status:  500 Server Error');
}