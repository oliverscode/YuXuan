create table TbSale
(
    Id           integer         not null
        constraint TbSale_pk
            primary key autoincrement,
    SaleName     text default '' not null,
    MgrName      text default '' not null,
    HospitalName text            not null,
    Year         integer         not null,
    Month        integer         not null,
    ProductName  text            not null,
    Amount       real default 0  not null,
    PlanAmount   real default 0  not null,
    IsError      int  default 0  not null
);

create index TbSale_HospitalName_Year_Month_ProductName_index
    on TbSale (HospitalName, Year, Month, ProductName);









create table TbCost
(
    Id     integer not null
        constraint TbCost_pk
            primary key autoincrement,
    Name   text    not null,
    Year   integer not null,
    Month  integer not null,
    Amount real    not null
);

create index TbCost_Name_Year_Month_index
    on TbCost (Name, Year, Month);

