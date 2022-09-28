create database cehlab;

use cehlab;

create table accounts
(
	_id varchar(255) primary key unique,
	_email varchar(255),
	_password varchar(255)
)

insert into accounts
values
	('54f52dfe095bab3dcb845e70c4b1f87a630268a3', 'matatizo@unique.co.tz', 'matatizo');

select *
from accounts;