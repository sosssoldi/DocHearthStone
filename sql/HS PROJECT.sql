--  PROJECT NAME:   HS PLATFORM
--  SUBJECT:        TECWEB
--  AUTHORS:
--              TINTORRI    NICOLA      1102860
--              CARLIN      MAURO       1102351
--              BERTOLINI	LUCA        1099549
--              BONOLO      MARCO       1102360

-- FILE DEFINITION: SQL DUMP OF THE DATABASE 'HEARTHSTONE'.
--                  (THIS FILE CAN BE USED TO CREATE THE BASIC STRUCTURE OF THE DATABASE).

-- **********************************************
-- INITIAL OPERATIONS SECTION
-- **********************************************

-- STATEMENT THAT DELETE THE 'EXISTING' DATABASE
drop database if exists hearthstone;

-- CREATING THE DATABASE
create database hearthstone;

-- USE CLAUSE
use hearthstone;

-- **********************************************
-- TABLE'S CREATION SECTION
-- **********************************************

create table if not exists user (
    email varchar(100) unique not null,
	name varchar(50) not null,
	surname varchar(50) not null,
    username varchar(50) primary key,
    password varchar(64) not null,
    entry_date datetime not null,
	count_post integer(5) default 0,
	photo_id varchar(50) /*default (da mettere path immagine base)*/
)engine=innodb;

create table if not exists section (
    section_id int auto_increment primary key,
    name varchar(100) not null,
	num_thread integer(5) default 0
)engine=innodb;

create table if not exists topic (
    topic_id int auto_increment primary key,
    title varchar(100) not null,
    content varchar(1000),
    creation_date datetime not null,
    user_name varchar(50) not null,
	num_comments integer(5) default 0,
    section_id int not null,
    foreign key (user_name) references user(username),
    foreign key (section_id) references section(section_id)
)engine=innodb;

create table if not exists comment (
    comment_id int auto_increment primary key,
    content varchar(2000) not null,
    creation_date datetime not null,
    user_name varchar(50) not null,
    topic_id int not null,
    foreign key (user_name) references user(username),
    foreign key (topic_id) references topic(topic_id)
)engine=innodb;

create table if not exists hero_power(
	hero_power_id varchar(15) primary key,
	name varchar(50) not null,
	description varchar(100) not null,
	image varchar(100) not null
)engine=innodb;

create table if not exists hero (
	hero_id varchar(15) primary key,
	name varchar(50) not null,
	image varchar(100) not null,
	type varchar(25) not null,
	h_power varchar(15) not null,
	foreign key (h_power) references hero_power(hero_power_id)
)engine=innodb;

create table if not exists deck (
	deck_id int auto_increment primary key,
	name varchar(100) not null,
	description varchar(250) default '',
	likes int default 0,
	hero_id varchar(15) not null,
	user_name varchar(50) not null,
	foreign key (hero_id) references hero(hero_id),
	foreign key (user_name) references user(username)
)engine=innodb;

create table if not exists deck_like(
	user_name varchar(50) not null,
	vote boolean not null,
	deck_id int not null,
	primary key(user_name,deck_id),
	foreign key (user_name) references user(username),
	foreign key (deck_id) references deck(deck_id)
)engine=innodb;

create table if not exists suggest(
	suggest_id int auto_increment primary key,
	content varchar(2000) not null,
	user_name varchar(50) not null,
	deck_id int not null,
	foreign key (user_name) references user(username),
	foreign key (deck_id) references deck(deck_id)
)engine=innodb;

create table if not exists expansion (
	name varchar(50) primary key,
	description varchar(500) default '',
	data_entry datetime not null,
 	pack_image varchar(100) not null
)engine=innodb;

create table if not exists guide (
	guide_id int auto_increment primary key,
	title varchar(50) not null,
	content varchar(5000) not null,
	valutation int(3) default 0,
	hero_id varchar(15) not null,
	user_name varchar(50) not null,
	foreign key (hero_id) references hero(hero_id),
	foreign key (user_name) references user(username)
)engine=innodb;

create table if not exists guide_vote(
	user_name varchar(50) not null,
	vote int(3) not null,
	guide_id int not null,
	primary key(user_name,guide_id),
	foreign key (user_name) references user(username),
	foreign key (guide_id) references guide(guide_id)
)engine=innodb;

create table if not exists adventure (
	name varchar(100) primary key,
	desciption varchar(500) default '',
	image varchar(100) not null
)engine=innodb;

create table if not exists wing (
	number int(2) unsigned,
	adventure_name varchar(100) not null,
	primary key(number,adventure_name),
	foreign key (adventure_name) references adventure(name)
)engine=innodb;

create table if not exists rarity(
	name varchar(50) primary key,
	r_craft int(5) not null,
	r_destroy int(5) not null,
	golden_craft int(5) not null,
	golden_destroy int(5) not null
)engine=innodb;

create table if not exists card(
	card_id varchar(20) primary key,
	name varchar(50) not null,
	image varchar(100) not null,
	description varchar(50) not null,
	rarity varchar(50) not null,
	c_type varchar(25) not null,
	c_race varchar(25) default '',
	wild boolean default false,
	attack int(3) unsigned not null,
	health int(3) unsigned not null,
	mana int(3) unsigned not null,
	adventure_wing int unsigned,
	adventure_name varchar(100),
	expansion_name varchar(50),
	foreign key (rarity) references rarity(name),
	foreign key (expansion_name) references expansion(name),
	foreign key (adventure_wing, adventure_name) references wing(number,adventure_name)
)engine=innodb;

create table if not exists card_deck(
	deck_id int not null,
	card_id varchar(20) not null,
	primary key(deck_id,card_id),
	foreign key (deck_id) references deck(deck_id),
	foreign key (card_id) references card(card_id)
)engine=innodb;

create table if not exists hero_card(
	hero_id varchar(15) not null,
	card_id varchar(20) not null,
	primary key(hero_id,card_id),
	foreign key (hero_id) references hero(hero_id),
	foreign key (card_id) references card(card_id)
)engine=innodb;
