CREATE DATABASE devstagram;

use devstagram;

CREATE TABLE users (
    id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    name varchar(100),
    email varchar(100) NOT NULL,
    pass varchar(61) NOT NULL,
    avatar varchar(100) 
);

CREATE TABLE users_following (
    id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    id_user_follower int(11) NOT NULL,
    id_user_followed int(11) NOT NULL
);

CREATE TABLE photos (
    id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    id_user int(11) NOT NULL,
    url varchar(100) NOT NULL
);

CREATE TABLE photos_likes (
    id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    id_user int(11) NOT NULL,
    id_photo int(11) NOT NULL
);

CREATE TABLE photos_comment (
    id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    id_user int(11) NOT NULL,
    id_photo int(11) NOT NULL,
    date_comment datetime NOT NULL,
    txt_comment text NOT NULL
);
