DROP DATABASE IF EXISTs todolist;
create database IF NOT EXISTS todolist
    character set = utf8
    collate = utf8_general_ci;


USE todolist;

DROP TABLE IF EXISTS users;
CREATE TABLE users (
                       id INT AUTO_INCREMENT PRIMARY KEY,
                       user_dt_add TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                       email VARCHAR(128) NOT NULL UNIQUE,
                       user_name VARCHAR(128) NOT NULL,
                       user_pass CHAR(12) NOT NULL
) engine=innodb default charset=utf8 collate=utf8_unicode_ci;

DROP TABLE IF EXISTS projects;
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_name VARCHAR(128) NOT NULL,
    user_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id)
) engine=innodb default charset=utf8 collate=utf8_unicode_ci;

DROP TABLE IF EXISTS tasks;
CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task_dt_add TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    task_status TINYINT DEFAULT 0,
    task_name VARCHAR(255) NOT NULL,
    task_file VARCHAR(255),
    deadline DATE,
    user_id INT,
    project_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (project_id) REFERENCES projects(id)
) engine=innodb default charset=utf8 collate=utf8_unicode_ci;

CREATE FULLTEXT INDEX task_ft_search ON tasks(task_name)

