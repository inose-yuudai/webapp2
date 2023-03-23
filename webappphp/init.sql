-- これを順番に mysql> に入れ込む 一個ずつ
CREATE DATABASE IF NOT EXISTS webapp;
USE webapp;


CREATE TABLE webapp_hours (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID',
    date DATETIME COMMENT '日時',
    hours INT COMMENT '学習時間'
);


CREATE TABLE webapp_language (
    id INT(11) AUTO_INCREMENT PRIMARY KEY COMMENT 'ID',
    language_id INT COMMENT '言語ID',
    hours_id INT COMMENT '学習時間ID',
    -- 雄真はここrecord_id
    -- record_idはなんかはなんか
    FOREIGN KEY (`hours_id`) REFERENCES webapp_hours(`id`),
    FOREIGN KEY (`language_id`) REFERENCES languages(`id`)
);

CREATE TABLE languages (
    id INT(11) AUTO_INCREMENT PRIMARY KEY COMMENT 'ID',
    language  VARCHAR(255) COMMENT '学習言語',
    color  VARCHAR(255) COMMENT '色'
);

CREATE TABLE webapp_content (
    id INT(11) AUTO_INCREMENT PRIMARY KEY COMMENT 'ID',
    content_id INT COMMENT '学習コンテンツID',
    hours_id INT COMMENT '学習時間ID',
    FOREIGN KEY (`content_id`) REFERENCES contents(`id`),
    FOREIGN KEY (`hours_id`) REFERENCES webapp_hours(`id`)
);

CREATE TABLE contents (
    id INT(11) AUTO_INCREMENT PRIMARY KEY COMMENT 'ID',
    language  VARCHAR(255) COMMENT '学習コンテンツ',
    color  VARCHAR(255) COMMENT '色'
);

INSERT INTO `webapp_hours` VALUES
(1, '2022-06-14', 3),
(2, '2022-02-15', 4),
(3, '2023-02-02', 5),
(4, '2023-02-28', 6),
(5, '2023-02-27', 7),
(6, '2023-02-26', 9),
(7, '2023-02-25', 0),
(8, '2023-02-24', 1),
(9, '2023-02-23', 2),
(10, '2023-02-02', 3),
(11, '2023-02-03', 12),
(12, '2023-02-04', 17),
(13, '2023-02-05', 10),
(14, '2023-02-06', 24),
(15, '2023-02-07', 22),
(16, '2023-02-08', 24),
(17, '2023-02-09', 20),
(18, '2023-02-10', 19),
(19, '2023-02-11', 7),
(20, '2023-02-12', 18),
(21, '2023-02-13', 17),
(22, '2023-02-14', 13),
(23, '2023-02-15', 13),
(24, '2023-02-16', 0),
(25, '2023-02-17', 0),
(26, '2023-02-18', 0),
(27, '2023-02-19', 1),
(28, '2023-02-20', 1),
(29, '2023-02-21', 14),
(30, '2023-02-22', 12);



INSERT INTO `languages` VALUES
(1, 'HTML','#0445EC'),
(2, 'CSS','#0F70BC'),
(3, 'JavaScript','#20BDDE'),
(4, 'PHP','#3CCEFE'),
(5, 'Laravel','#B29EF3'),
(6, 'SQL','#6C46EB'),
(7, 'SHELL','#4A17EF'),
(8, '情報システム基礎知識(その他)','#3005C0');

INSERT INTO `contents` VALUES
(1, 'N予備校','#0445EC'),
(2, 'ドットインストール','#0F70BC'),
(3, 'POSSE課題','#20BDDE');

INSERT INTO `webapp_language` VALUES
(1, 7, 1),
(2, 1, 2),
(3, 3, 3),
(4, 4, 4),
(5, 2, 5),
(6, 1, 6),
(7, 7, 7),
(8, 1, 8),
(9, 3, 9),
(10, 4, 10),
(11, 2, 11),
(12, 1, 12),
(13, 7, 13),
(14, 1, 14),
(15, 3, 15),
(16, 4, 16),
(17, 2, 17),
(18, 1, 18),
(19, 7, 19),
(20, 1, 20),
(21, 3, 21),
(22, 8, 22),
(23, 2, 23),
(24, 1, 24),
(25, 7, 25),
(26, 1, 26),
(27, 3, 27),
(28, 4, 28),
(29, 2, 29),
(30, 8, 30);


INSERT INTO `webapp_content` VALUES
(1, 1, 1),
(2, 1, 2),
(3, 3, 3),
(4, 2, 4),
(5, 2, 5),
(6, 1, 6),
(7, 1, 7),
(8, 1, 8),
(9, 3, 9),
(10, 2, 10),
(11, 2, 11),
(12, 1, 12),
(13, 2, 13),
(14, 1, 14),
(15, 3, 15),
(16, 1, 16),
(17, 2, 17),
(18, 1, 18),
(19, 1, 19),
(20, 1, 20),
(21, 3, 21),
(22, 3, 22),
(23, 3, 23),
(24, 3, 24),
(25, 3, 25),
(26, 1, 26),
(27, 3, 27),
(28, 3, 28),
(29, 2, 29),
(30, 3, 30);
