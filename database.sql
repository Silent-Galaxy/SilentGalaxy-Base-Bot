sql
CREATE TABLE `users` (
  `chat_id` bigint(20) NOT NULL PRIMARY KEY,
  `step` varchar(50) DEFAULT '0',
  `joined_at` timestamp DEFAULT CURRENT_TIMESTAMP
);
