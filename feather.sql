
create database if not exists `feather`;

use feather;

create table if not exists `feather_token`(
    `token` varchar(255) not null,
    `remote` varchar(20) not null,
    `updatetime` int(11) not null default 0,
    `valid` tinyint(1) not null default 0,
    primary key token(`token`),
    index remote(`remote`),
    index updatetime(`updatetime`),
    index valid(`valid`)
)engine=MyISAM charset=utf8;

-- --------------------------------
-- about user, for example
-- --------------------------------
create user 'feather_user'@'172.31.169.91' identified by 'Nns<:%6-';
grant select on feather.* to 'feather_user'@'172.31.169.91';
grant insert on feather.* to 'feather_user'@'172.31.169.91';
grant update on feather.* to 'feather_user'@'172.31.169.91';
grant delete on feather.* to 'feather_user'@'172.31.169.91';


