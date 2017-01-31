CREATE TABLE IF NOT EXISTS post (
  id integer PRIMARY KEY AUTOINCREMENT,
  title TEXT(60) NOT NULL,
  content TEXT NOT NULL,
  published_date TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS comment (
  id integer PRIMARY KEY AUTOINCREMENT,
  post_id integer,
  author TEXT(60) NOT NULL,
  content TEXT NOT NULL,
  published_date TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS admin (
  id integer PRIMARY KEY AUTOINCREMENT,
  email TEXT(32) NOT NULL,
  pass TEXT(32) NOT NULL,
  cookie TEXT(32)
);
-- Добавление пользователя email = blog@blog.comб, pass = blog
--INSERT INTO admin (email, pass) VALUES ('blog@blog.com', '126ac9f6149081eb0e97c2e939eaad52');