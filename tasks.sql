INSERT INTO todolist.tasks (id, task_dt_add, task_status, task_name, task_file, deadline, user_id, project_id) VALUES (1, '2023-03-20 00:00:00', 1, 'Собеседование в IT компании', '', '2023-03-24', 1, 3);
INSERT INTO todolist.tasks (id, task_dt_add, task_status, task_name, task_file, deadline, user_id, project_id) VALUES (2, '2023-03-22 17:13:16', 0, 'Выполнить тестовое задание', '', '2023-03-25', 1, 3);
INSERT INTO todolist.tasks (id, task_dt_add, task_status, task_name, task_file, deadline, user_id, project_id) VALUES (3, '2023-03-22 17:13:16', 1, 'Сделать задание первого раздела', '', '2023-03-26', 1, 2);
INSERT INTO todolist.tasks (id, task_dt_add, task_status, task_name, task_file, deadline, user_id, project_id) VALUES (4, '2023-03-22 17:13:16', 0, 'Новая встреча с другом', '', '2023-03-27', 1, 1);
INSERT INTO todolist.tasks (id, task_dt_add, task_status, task_name, task_file, deadline, user_id, project_id) VALUES (5, '2023-03-22 17:13:16', 0, 'Купить корм для кота', '', '2023-03-28', 1, 4);
INSERT INTO todolist.tasks (id, task_dt_add, task_status, task_name, task_file, deadline, user_id, project_id) VALUES (6, '2023-03-22 17:13:16', 0, 'Заказать пиццу', '', '2023-03-29', 1, 4);
INSERT INTO todolist.tasks (id, task_dt_add, task_status, task_name, task_file, deadline, user_id, project_id) VALUES (7, '2023-03-24 21:52:05', 0, 'Собеседование в клиринговой компании', '', '2023-03-30', 2, 8);
INSERT INTO todolist.tasks (id, task_dt_add, task_status, task_name, task_file, deadline, user_id, project_id) VALUES (8, '2023-03-24 21:52:05', 0, 'Выполнить тестовое задание', '', '2023-03-31', 2, 8);
INSERT INTO todolist.tasks (id, task_dt_add, task_status, task_name, task_file, deadline, user_id, project_id) VALUES (9, '2023-03-24 21:52:05', 1, 'Сделать задание пятого раздела', '', '2023-04-01', 2, 7);
INSERT INTO todolist.tasks (id, task_dt_add, task_status, task_name, task_file, deadline, user_id, project_id) VALUES (10, '2023-03-24 21:52:05', 0, 'Встреча с однокласниками', '', '2023-04-02', 2, 6);
INSERT INTO todolist.tasks (id, task_dt_add, task_status, task_name, task_file, deadline, user_id, project_id) VALUES (11, '2023-03-24 21:52:05', 0, 'Купить корм для рыбок', '', '2023-04-03', 2, 9);
INSERT INTO todolist.tasks (id, task_dt_add, task_status, task_name, task_file, deadline, user_id, project_id) VALUES (12, '2023-03-24 21:52:05', 0, 'Заказать новое весло', '', '2023-04-04', 2, 9);


delete from tasks where id = 13;