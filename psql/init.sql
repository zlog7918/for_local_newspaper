CREATE TABLE usr (
    id SERIAL NOT NULL PRIMARY KEY
    ,nick TEXT UNIQUE NOT NULL
    ,password BYTEA NOT NULL
    ,"name" TEXT NOT NULL
    ,surname TEXT NOT NULL
    ,academic_titles TEXT NOT NULL
    ,loggin_tries INT NOT NULL DEFAULT 0
    ,is_active BOOLEAN NOT NULL DEFAULT true
    ,is_admin BOOLEAN NOT NULL DEFAULT false
);

CREATE TABLE article (
    id SERIAL NOT NULL PRIMARY KEY
    ,author_id INT NOT NULL REFERENCES usr(id)
    ,title TEXT NOT NULL
    ,"text" TEXT UNIQUE NOT NULL
    ,creation_date TIMESTAMP NOT NULL
    ,last_edit_date TIMESTAMP
    ,publish_date TIMESTAMP
    ,is_archived BOOLEAN NOT NULL DEFAULT false
);

CREATE TABLE review (
    id SERIAL NOT NULL PRIMARY KEY
    ,author_id INT NOT NULL REFERENCES usr(id)
    ,article_id INT NOT NULL REFERENCES article(id)
    ,"text" TEXT NOT NULL
    ,degree INT NOT NULL
);

CREATE TABLE log (
    id SERIAL NOT NULL PRIMARY KEY
    ,user_id INT REFERENCES usr(id)
    ,ip TEXT NOT NULL
    ,action TEXT NOT NULL
    ,is_success BOOLEAN NOT NULL
    ,timest TIMESTAMP NOT NULL
    ,log JSONB
);

CREATE VIEW article_with_author AS
    SELECT
        a.*
        ,CONCAT_WS(
            ' '
            ,CASE
                WHEN u.academic_titles='' THEN NULL
                ELSE u.academic_titles
            END
            ,u.name
            ,u.surname
        ) author
    FROM article a
        JOIN usr u ON u.id=a.author_id;

INSERT INTO usr(nick, password, "name", surname, academic_titles, is_active, is_admin) VALUES
    ('admin', E'\\x4b504453593146712b6e4473353974744439672f64415a656a6a646332666e37702f4965376e726c76624659314c4a7759336b61776f51466e6939544d37744d754b4a545634592f6c68646a624f7366556747594644684c63504d596f33716a6d4a3869556561714f426552724d317570304978646d7068625535744a51317146372b4145513d3d', 'Administrator', 'Administrowalek', 'inż.', true, true);
