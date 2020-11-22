CREATE TABLE categories (
categorie_id  INT UNSIGNED AUTO_INCREMENT NOT NULL,
categorie_nom VARCHAR(255)                NOT NULL,
PRIMARY KEY (categorie_id)
) ENGINE=InnoDB;

INSERT INTO categories VALUES
(1, 'Randonnée'),
(2, 'Course'),
(3, 'Vélo'),
(4, 'Nautique'),
(5, 'Camping');

CREATE TABLE marques (
marque_id  INT UNSIGNED AUTO_INCREMENT NOT NULL,
marque_nom VARCHAR(255)                NOT NULL,
PRIMARY KEY (marque_id)
) ENGINE=InnoDB;

INSERT INTO marques VALUES
(1, 'Louis Garneau'),
(2, 'Salomon'),
(3, 'The North Face'),
(4, 'Mustang'),
(5, 'Vaude');

CREATE TABLE produits (
produit_id              INT UNSIGNED AUTO_INCREMENT NOT NULL,
produit_nom             VARCHAR(255)                NOT NULL,
produit_desc            TEXT                        NOT NULL,
produit_prix            DECIMAL(5,2) UNSIGNED       NOT NULL,
produit_stock           SMALLINT UNSIGNED           NOT NULL,
produit_fk_marque_id    INT UNSIGNED                NOT NULL,
produit_fk_categorie_id INT UNSIGNED                NOT NULL,
PRIMARY KEY (produit_id),
CONSTRAINT produit_fk_marque_id    FOREIGN KEY (produit_fk_marque_id)    REFERENCES marques (marque_id),
CONSTRAINT produit_fk_categorie_id FOREIGN KEY (produit_fk_categorie_id) REFERENCES categories (categorie_id)
) ENGINE=InnoDB;

INSERT INTO produits VALUES
(1, 'STROOL LOGO H', 'Homme, chandail à col rond (bleu, vert)', '39.99', 25, 2, 2),
(2, 'STROOL LOGO F', 'Femme, chandail à col rond (bleu, vert et mauve)', '39.99', 18, 2, 2),
(3, 'TRAIL H', 'Homme, chandail à col en V (bleu ou vert)', '44.99', 45, 2, 2),
(4, 'TRAIL F', 'Femme, chandail à col en V (bleu, vert ou mauve)', '44.99', 55, 2, 2),
(5, 'ASTORIA', 'Femme, Jersey à demi-glissière (blanc)', '59.99', 32, 1, 3),
(6, 'LITE SKIN', 'Femme, haut sans manche vert', '49.99', 48, 1, 3),
(7, 'NEO POWER FIT', 'Femme, cuissard 7 pro, 13 panneaux, coutures à plat', '119.99', 72, 1, 3),
(8, 'EQUIPE SEMI-PRO', 'Homme, Jersey à glissière pleine longueur (bleue, rouge)', '89.99', 105, 1, 3),
(9, 'MIT 35 AUTOMATIQUE', 'Gilet de sauvetage rouge ou bleu marine,\r\ntaille unique', '149.99', 68, 4, 4),
(10, 'MIT 35 MANUELLE', 'Gilet de sauvetage rouge ou bleu marine,\r\ntaille unique', '129.99', 58, 4, 4),
(11, 'DELUXE MANUELLE', 'Gilet de sauvetage rouge ou bleu marine,\r\ntaille unique', '174.99', 44, 4, 4),
(12, 'DELUXE AUTOMATIQUE', 'Gilet de sauvetage rouge ou bleu marine, taille unique', '199.99', 29, 4, 4),
(13, 'WINGS SKY 2', 'Homme, chaussure imperméable tous terrains, orange ou noir, pointure 8-12, 13', '229.99', 1, 2, 1),
(14, 'X-ULTRA MID GTX', 'Homme, chaussure mi-montante, légère, stable et protectrice. Pointure 8-12, 13', '169.99', 25, 2, 1),
(15, 'VERTO 26', 'Sac à dos, poids 1 lb 2 oz, volume 26 L, bleu ou noir', '69.99', 70, 3, 5),
(16, 'VERTO 32', 'Sac à dos, poids 1 lb 7 oz, volume 32 L, bleu ou noir', '79.99', 44, 3, 5),
(17, 'TERRA 45', 'Sac à dos, poids 3 lb 14 oz, volume 45 L, noir ou orange', '169.99', 11, 3, 5),
(18, 'CONNESS 70', 'Sac à dos, poids 5 lb 12 oz, volume 70 L, beige ou gris', '319.99', 29, 3, 5),
(19, 'CONNESS 55', 'Sac à dos, poids 5 lb 6 oz, volume 55 L, beige ou gris', '299.99', 70, 3, 5),
(20, 'CASIMIR 32', 'Sac à dos, poids 2 lb 2 oz, volume 32 L, noir ou bleu', '159.99', 106, 3, 5),
(21, 'CASIMIR 36', 'Sac à dos, poids 2 lb 5 oz, volume 36 L, noir ou bleu', '179.99', 29, 3, 5),
(22, 'ALTEO 35', 'Sac à dos, poids 3 lb 2 oz, volume 35 L, gris graphite ou gris citronnelle', '179.99', 88, 3, 5),
(23, 'NEVIS 25', 'Sac à dos, poids 2 lb 6 oz, volume 25 L, noir', '149.99', 42, 5, 5),
(24, 'GRANIT 25', 'Sac à dos, poids 3 lb 10 oz, volume 25 L, bleu ou noir', '159.99', 53, 5, 5),
(25, 'CIMONE 55', 'Sac à dos, poids 5 lb 3 oz, volume 55 L, sangria', '239.99', 22, 5, 5),
(26, 'VERBERA LIGHTPACKER GTX', 'Homme,  bottes de trekking légères et imperméables, munies d''une membrane GORE-TEX respirante. Pointure 8-12, 13', '239.99', 65, 3, 1),
(27, 'WRECK MID GTX', 'Homme, bottes de trekking légères et imperméables, munies d''une membrane GORE-TEX respirante. Pointure 8-12, 13', '169.99', 32, 3, 1);

CREATE TABLE utilisateurs (
utilisateur_id       INT UNSIGNED AUTO_INCREMENT NOT NULL,
utilisateur_type     CHAR(1)      NOT NULL DEFAULT "U",
utilisateur_nom      VARCHAR(255) NOT NULL,
utilisateur_prenom   VARCHAR(255) NOT NULL,
utilisateur_mdp      VARCHAR(255) NOT NULL,
utilisateur_courriel VARCHAR(70)  NOT NULL UNIQUE,
PRIMARY KEY (utilisateur_id)
) ENGINE=InnoDB;

INSERT INTO utilisateurs VALUES
(NULL, "A", "Aubin",  "Alain",  SHA2("Test1234", 256), "alain.aubin@magasin.ca"),
(NULL, "U", "Brel",   "Bruno",  SHA2("Test1234", 256), "bruno.brel@magasin.ca"),
(NULL, "U", "Chabot", "Clara",  SHA2("Test1234", 256), "clara.chabot@magasin.ca"),
(NULL, "U", "Dubois", "Didier", SHA2("Test1234", 256), "didier.dubois@magasin.ca"),
(NULL, "U", "Escot",  "Ernest", SHA2("Test1234", 256), "ernest.escot@magasin.ca"),
(NULL, "U", "Fortin", "France", SHA2("Test1234", 256), "france.fortin@magasin.ca"),
(NULL, "U", "Gravel", "Gérard", SHA2("Test1234", 256), "gerard.gravel@magasin.ca"),
(NULL, "U", "Hébert", "Hugo",   SHA2("Test1234", 256), "hugo.hebert@magasin.ca"),
(NULL, "U", "Imbert", "Iris",   SHA2("Test1234", 256), "iris.imbert@magasin.ca"),
(NULL, "U", "Jouan",  "Jean",   SHA2("Test1234", 256), "jean.jouan@magasin.ca"),
(NULL, "U", "Kahn", "Katia",    SHA2("Test1234", 256), "katia.kahn@magasin.ca"),
(NULL, "U", "Léger",  "Louis",  SHA2("Test1234", 256), "louis.leger@magasin.ca");

CREATE TABLE commandes (
commande_id                INT UNSIGNED AUTO_INCREMENT NOT NULL,
commande_date              DATETIME                    NOT NULL,
commande_fk_utilisateur_id INT UNSIGNED                NOT NULL,
PRIMARY KEY (commande_id),
CONSTRAINT commande_fk_utilisateur_id   FOREIGN KEY (commande_fk_utilisateur_id)   REFERENCES utilisateurs (utilisateur_id)
) ENGINE=InnoDB;

INSERT INTO commandes VALUES
(1, '2018-07-17 12:14:12', 1),
(2, '2018-07-18 01:21:17', 2),
(3, '2018-07-19 17:23:33', 3),
(4, '2018-07-27 13:25:07', 4),
(5, '2018-08-12 09:27:44', 5),
(6, '2018-08-15 07:29:29', 6),
(7, '2018-08-18 14:35:22', 7),
(8, '2018-08-19 19:03:11', 8),
(9, '2018-08-22 22:05:49', 9),
(10, '2018-09-05 16:08:51', 10),
(11, '2018-09-10 20:10:52', 11),
(12, '2018-09-12 22:12:51', 12),
(13, '2018-09-16 15:50:50', 1),
(14, '2018-09-20 09:22:45', 2);

CREATE TABLE lignes (
ligne_fk_commande_id INT UNSIGNED      NOT NULL,
ligne_fk_produit_id  INT UNSIGNED      NOT NULL,
ligne_qte            SMALLINT UNSIGNED NOT NULL,
PRIMARY KEY (ligne_fk_commande_id, ligne_fk_produit_id),
CONSTRAINT ligne_fk_commande_id    FOREIGN KEY (ligne_fk_commande_id)    REFERENCES commandes (commande_id),
CONSTRAINT ligne_fk_produit_id     FOREIGN KEY (ligne_fk_produit_id)     REFERENCES produits (produit_id)   
) ENGINE=InnoDB;

INSERT INTO lignes VALUES
(1, 22, 1),
(2, 20, 2),
(3, 21, 3),
(4, 25, 4),
(5, 19, 5),
(6, 18, 6),
(7, 24, 7),
(8, 23, 8),
(9, 17, 9),
(10, 15, 10),
(11, 16, 11),
(12, 2, 12),
(13, 19, 5),
(13, 20, 2),
(13, 21, 3),
(13, 22, 1),
(13, 25, 4),
(14, 9, 2),
(14, 10, 2),
(14, 11, 1),
(14, 12, 1);