drop database if exists Chatdb; 

create database Chatdb;

use Chatdb;


create table Servers(
	SID int not null unique auto_increment,
    langcode varchar(5) unique not null,
    langname varchar(30) not null,
    primary key(SID)
);

create table Users(
    UID int not null unique auto_increment,
    username varchar(20) not null unique,
    sourcelang varchar(5) not null,
    passhash varchar(60) not null,
    SID int,
    nickname varchar(45),
    constraint S_ID_User FOREIGN KEY (SID)
    References Servers(SID),
    primary key(UID, username)
);

create table Messages(
	CID int not null auto_increment,
    content varchar(255) not null,
    sent TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    SID int NOT NULL,
    UID int NOT NULL,
	constraint S_ID FOREIGN KEY (SID)
    References Servers(SID),
	constraint U_ID FOREIGN KEY (UID)
    References Users(UID),
    primary key(CID)
);
