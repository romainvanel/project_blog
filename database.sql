CREATE DATABASE IF NOT EXISTS blog_db;
USE blog_db;

CREATE TABLE IF NOT EXISTS users (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100),
    email VARCHAR(100),
    password VARCHAR(100)
);

CREATE TABLE IF NOT EXISTS comments (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, 
    content TEXT,
    comment_date DATETIME,
    user_id INT NOT NULL,
    article_id INT NOT NULL
);

CREATE TABLE IF NOT EXISTS articles (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255),
    content TEXT,
    cover VARCHAR(255),
    publication_date DATETIME,
    user_id INT NOT NULL
);

CREATE TABLE IF NOT EXISTS articles_categories (
    article_id INT NOT NULL,
    category_id INT NOT NULL
);

CREATE TABLE IF NOT EXISTS categories (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100)
);

ALTER TABLE comments ADD CONSTRAINT fk_comments_user_id FOREIGN KEY (user_id) REFERENCES users(id);
ALTER TABLE comments ADD CONSTRAINT fk_comments_article_id FOREIGN KEY (article_id) REFERENCES articles(id);
ALTER TABLE articles ADD CONSTRAINT fk_articles_user_id FOREIGN KEY (user_id) REFERENCES users(id);
ALTER TABLE articles_categories ADD CONSTRAINT fk_articles_categories_article_id FOREIGN KEY (article_id) REFERENCES articles(id);
ALTER TABLE articles_categories ADD CONSTRAINT fk_articles_categories_category_id FOREIGN KEY (category_id) REFERENCES categories(id);