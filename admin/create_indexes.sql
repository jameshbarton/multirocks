use multi;

# drop index idx_student_teacherEmail on student;
create index idx_student_teacherEmail on student(teacherEmail);

# drop index idx_drill_studentID on drill;
create index idx_drill_studentID on drill(studentId);

# drop index idx_problem_drillId on problem;
create index idx_problem_drillId on problem(drillId);

# drop index idx_student_userName on student;
create index idx_student_userName on student(userName);

