DROP TABLE IF EXISTS  country;

CREATE TABLE IF NOT EXISTS  country (
    id INT NOT NULL AUTO_INCREMENT,
    shortname VARCHAR(100) NULL,      -- Короткое наименование страны 
    fullname VARCHAR(200) NOT NULL,   -- Полное наименование страны 
    isoalpha2 CHAR(2) NOT NULL,      -- Двухбуквенный ISO-код 
    isoalpha3 CHAR(3) NOT NULL,      -- Трехбуквенный ISO-код (
    isonumeric CHAR(3) NOT NULL,      -- Числовой ISO-код 
    population INT NULL,               -- Население
    square FLOAT NULL,                 -- Площадь 
    PRIMARY KEY (id),
    UNIQUE KEY uk_isoalpha2 (isoalpha2),
    UNIQUE KEY uk_isoalpha3 (isoalpha3),
    UNIQUE KEY uk_isonumeric (isonumeric)
);

-- Вставка 
INSERT INTO  country (shortname, fullname, isoalpha2, isoalpha3, isonumeric, population, square) VALUES
(N'Россия', N'Российская Федерация', 'RU', 'RUS', '643', 146150789, 17125191),
(N'США', N'Соединённые Штаты Америки', 'US', 'USA', '840', 331002651, 9833517),
(N'Канада', N'Канада', 'CA', 'CAN', '124', 37742154, 9984670),
(N'Франция', N'Французская Республика', 'FR', 'FRA', '250', 65273511, 551695),
(N'Германия', N'Федеративная Республика Германия', 'DE', 'DEU', '276', 83783942, 357022),
(N'Китай', N'Китайская Народная Республика', 'CN', 'CHN', '156', 1439323776, 9596961),
(N'Индия', N'Республика Индия', 'IN', 'IND', '356', 1380004385, 3287263),
(N'Бразилия', N'Федеративная Республика Бразилия', 'BR', 'BRA', '076', 212559417, 8514877),
(N'Австралия', N'Австралийский Союз', 'AU', 'AUS', '036', 25499884, 7692024),
(N'Япония', N'Япония', 'JP', 'JPN', '392', 126476461, 377975);
