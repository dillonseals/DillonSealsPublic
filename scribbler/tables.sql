+---------------------+
| Tables_in_Scribbler |
+---------------------+
| scribbler_comments  |
| scribbler_stories   |
| scribbler_users     |
+---------------------+
3 rows in set (0.00 sec)


+----------+---------------------+------+-----+---------+----------------+
| Field    | Type                | Null | Key | Default | Extra          |
+----------+---------------------+------+-----+---------+----------------+
| user_id  | tinyint(3) unsigned | NO   | PRI | NULL    | auto_increment |
| username | varchar(20)         | NO   |     | NULL    |                |
| password | varchar(255)        | NO   |     | NULL    |                |
+----------+---------------------+------+-----+---------+----------------+
3 rows in set (0.00 sec)


+-----------+----------------------+------+-----+---------+----------------+
| Field     | Type                 | Null | Key | Default | Extra          |
+-----------+----------------------+------+-----+---------+----------------+
| story_id  | smallint(5) unsigned | NO   | PRI | NULL    | auto_increment |
| title     | varchar(40)          | NO   |     | NULL    |                |
| text      | text                 | NO   |     | NULL    |                |
| user_id   | tinyint(3) unsigned  | NO   | MUL | NULL    |                |
| link      | varchar(200)         | YES  |     | NULL    |                |
| viewcount | smallint(5) unsigned | NO   |     | 0       |                |
+-----------+----------------------+------+-----+---------+----------------+
6 rows in set (0.00 sec)


+------------+----------------------+------+-----+---------+----------------+
| Field      | Type                 | Null | Key | Default | Extra          |
+------------+----------------------+------+-----+---------+----------------+
| comment_id | smallint(5) unsigned | NO   | PRI | NULL    | auto_increment |
| text       | text                 | NO   |     | NULL    |                |
| user_id    | tinyint(3) unsigned  | NO   | MUL | NULL    |                |
| story_id   | smallint(5) unsigned | NO   | MUL | NULL    |                |
+------------+----------------------+------+-----+---------+----------------+
4 rows in set (0.00 sec)