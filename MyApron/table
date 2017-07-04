CREATE TABLE items (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(100) NOT NULL COLLATE utf8_general_ci,
  price int(11) NOT NULL DEFAULT '0',
  img varchar(100) NOT NULL,
  status int(11) NOT NULL,
  stock int(11) NOT NULL,
  create_datetime datetime,
  update_datetime datetime,
  primary key(id)
);
CREATE TABLE carts (
  id int(11) NOT NULL,
  user_id int(11) NOT NULL COLLATE utf8_general_ci,
  item_id int(11) NOT NULL,
  amount int(11) NOT NULL,
  created_at datetime,
  updated_at datetime,
  primary key(id)
);

CREATE TABLE users (
  id int(11) NOT NULL AUTO_INCREMENT,
  user_name int(11) NOT NULL COLLATE utf8_general_ci,
  password varchar(100) NOT NULL COLLATE utf8_general_ci,
  created_at datetime,
  updated_at datetime,
  primary key(id)
) ;

CREATE TABLE favorite (
  id int(11) NOT NULL AUTO_INCREMENT,
  item_id int(11) NOT NULL,
  created_at datetime,
  updated_at datetime,
  primary key(id)
) ;