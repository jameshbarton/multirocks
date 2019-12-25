use multi;

drop table if exists teacher;
create table teacher (
  email varchar(100) not null,
  password varchar(100) not null,
  firstName varchar(100) not null,
  lastName varchar(100) not null,
  phoneNumber varchar(50),
  school varchar(100),
  ts datetime default current_timestamp,
  primary key (email)
);

drop table if exists student;
create table student (
  sid int not null auto_increment,
  userName varchar(100) not null,
  password varchar(100) not null,
  firstName varchar(100) not null,
  lastName varchar(100),
  points int default 0,
  teacherEmail varchar(100) not null,
  ts datetime default current_timestamp,
  primary key (sid)
);

drop table if exists drill;
create table drill (
  did int not null auto_increment,
  studentID varchar(100) not null,
  duration int,
  ts datetime default current_timestamp,
  primary key (did)
);

drop table if exists problem;
create table problem (
  pid int not null auto_increment,
  drillID int not null,
  op1 int not null,
  op2 int not null,
  operator varchar(10),
  answer int,
  userAnswer int,
  ts datetime default current_timestamp,
  primary key (pid)
);

