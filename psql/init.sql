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
    ,creation_date TIMESTAMP NOT NULL
    ,last_edit_date TIMESTAMP
    ,CONSTRAINT ooo_review_article_from_author UNIQUE(author_id, article_id)
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

CREATE VIEW author AS
    SELECT
        u.id
        ,u.nick
        ,u.name
        ,u.surname
        ,u.academic_titles
        ,u.loggin_tries
        ,u.is_active
        ,u.is_admin
        ,CONCAT_WS(
            ' '
            ,CASE
                WHEN u.academic_titles=''
                    THEN NULL
                ELSE
                    u.academic_titles
            END
            ,u.name
            ,u.surname
        ) author
    FROM usr u;

CREATE VIEW article_with_author_and_degree AS
    SELECT
        a.*
        ,u.author
        ,((a.publish_date IS NOT NULL) AND (a.is_archived=false)) is_published
        ,COALESCE(avg_ad.degree, 0) degree
    FROM article a
        JOIN author u ON u.id=a.author_id
        LEFT JOIN (
            SELECT
                r.article_id id
                ,AVG(r.degree) degree
            FROM review r
            GROUP BY r.article_id
        ) avg_ad ON avg_ad.id=a.id;


CREATE VIEW review_with_author_and_article_title AS
    SELECT
        r.*
        ,a.title article_title
        ,u.author
        ,a.is_archived is_article_archived
        ,a.author_id article_author_id
    FROM review r
        JOIN article a ON a.id=r.article_id
        JOIN author u ON u.id=r.author_id;

INSERT INTO usr(nick, password, "name", surname, academic_titles, is_active, is_admin) VALUES
    ('admin', E'\\x4b504453593146712b6e4473353974744439672f64415a656a6a646332666e37702f4965376e726c76624659314c4a7759336b61776f51466e6939544d37744d754b4a545634592f6c68646a624f7366556747594644684c63504d596f33716a6d4a3869556561714f426552724d317570304978646d7068625535744a51317146372b4145513d3d', 'Administrator', 'Administrowalek', 'inż.', true, true);
