--
-- Table structure for table `remembered_logins`
--

CREATE TABLE remembered_logins (
  `token_hash` varchar(64) NOT NULL,
  `user_id` int(11) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- RELATIONS FOR TABLE `remembered_logins`:
--   `user_id`
--       `users` -> `id`
--

--
-- Indexes for dumped tables
--

--
-- Indexes for table `remembered_logins`
--
ALTER TABLE `remembered_logins`
  ADD PRIMARY KEY (`token_hash`),
  ADD KEY `user_id` (`user_id`);

