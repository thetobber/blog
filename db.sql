DROP SCHEMA IF EXISTS `spot`;
DROP USER IF EXISTS 'spot'@'localhost';

-- Create the schema
CREATE SCHEMA `spot`
  CHARACTER SET = 'utf8mb4'
  COLLATE 'utf8mb4_unicode_ci';

-- Create database user
CREATE USER 'spot'@'localhost'
  IDENTIFIED BY 'nhQrQQzf7C6mTybsm47Hy4ae';

-- Gran EXECUTE privilige to database user
GRANT EXECUTE ON spot.* TO 'spot'@'localhost';

-- Delete all procedures from this database
DELETE FROM `mysql`.`proc` WHERE `db` = 'spot' AND `type` = 'PROCEDURE';

-- Select database
USE `spot`;

-- User table
CREATE TABLE `user` (
  `id`        BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `role`      TINYINT UNSIGNED NOT NULL,
  `username`  VARCHAR(191) NOT NULL,
  `email`     VARCHAR(191) NOT NULL,
  `password`  BINARY(60) NOT NULL,
  `created`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `verified`  BOOLEAN NOT NULL DEFAULT false,

  PRIMARY KEY (`id`),
  UNIQUE INDEX (`username`) USING HASH
)
ENGINE = InnoDB;

-- Post table
CREATE TABLE `post` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title`       VARCHAR(191) NOT NULL,
    `author`      VARCHAR(191) NOT NULL,
    `owner`       VARCHAR(191) NOT NULL,
    `content`     TEXT DEFAULT NULL,
    `created`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    FOREIGN KEY (`author`) REFERENCES `user` (`username`),
    FOREIGN KEY (`owner`) REFERENCES `user` (`username`),
    INDEX (`created`) USING BTREE
);

-- Set delimiter to // so commands in procedures are not stopped
DELIMITER //

-- Create post procedure
CREATE PROCEDURE CREATE_POST
(
  IN inTitle   VARCHAR(191),
  IN inAuthor  VARCHAR(191),
  IN inOwner   VARCHAR(191),
  IN inContent TEXT
)
BEGIN
  INSERT INTO `post` (`title`, `author`, `owner`, `content`)
  VALUES (inTitle, inAuthor, inOwner, inContent);
END//

-- Get post by author procedure
CREATE PROCEDURE GET_POSTS_BY_AUTHOR
(
  IN inAuthor VARCHAR(191)
)
BEGIN
  SELECT * FROM `post`
  WHERE `author` = inAuthor
  ORDER BY `created` DESC;
END//

-- Get post by owner procedure
CREATE PROCEDURE GET_POSTS_BY_OWNER
(
  IN inOwner VARCHAR(191)
)
BEGIN
  SELECT * FROM `post`
  WHERE `owner` = inOwner
  ORDER BY `created` DESC;
END//

-- Update post procedure
CREATE PROCEDURE UPDATE_POST
(
  in inId      BIGINT UNSIGNED,
  IN inAuthor  VARCHAR(191),
  IN inTitle   TINYINT UNSIGNED,
  IN inContent TEXT
)
BEGIN
  UPDATE `post`
  SET `title` = inTitle, `content` = inContent
  WHERE `id` = inId AND `author` = inAuthor;
END//

-- Delete post procedure
CREATE PROCEDURE DELETE_POST
(
  in inId      BIGINT UNSIGNED,
  IN inAuthor  VARCHAR(191)
)
BEGIN
  DELETE FROM `post`
  WHERE `id` = inId AND `author` = inAuthor;
END//

-- Delete post procedure
CREATE PROCEDURE USER_EXISTS
(
  IN inUsername VARCHAR(191)
)
BEGIN
  DECLARE userExists BOOLEAN;
  SET userExists = false;

  SELECT true INTO userExists FROM `user` WHERE `username` = inUsername;
  SELECT userExists;
END//

-- Create user procedure
-- DEFINER = 'spot'@'localhost'
CREATE PROCEDURE CREATE_USER
(
  IN inRole     TINYINT UNSIGNED,
  IN inUsername VARCHAR(191),
  IN inEmail    VARCHAR(191),
  IN inPassword BINARY(60)
)
BEGIN
  INSERT INTO `user` (`role`, `username`, `email`, `password`)
  VALUES (inRole, inUsername, inEmail, inPassword);
END//

-- Get an user by username
CREATE PROCEDURE GET_USER
(
  IN inUsername VARCHAR(191)
)
BEGIN
  SELECT * FROM `user`
  WHERE `username` = inUsername;
END//

-- Update user procedure
CREATE PROCEDURE UPDATE_USER_EMAIL
(
  IN inUsername VARCHAR(191),
  IN inEmail    VARCHAR(191)
)
BEGIN
  UPDATE `user`
  SET `email` = inEmail
  WHERE `username` = inUsername;
END//

-- Update user procedure
CREATE PROCEDURE UPDATE_USER_PASSWORD
(
  IN inUsername VARCHAR(191),
  IN inPassword BINARY(60)
)
BEGIN
  UPDATE `user`
  SET `password` = inPassword
  WHERE `username` = inUsername;
END//

-- Delete user procedure
CREATE PROCEDURE DELETE_USER
(
  IN inUsername VARCHAR(191)
)
BEGIN
  DELETE FROM `user`
  WHERE `username` = inUsername;
END//

-- Set delimiter to ;
DELIMITER ;
