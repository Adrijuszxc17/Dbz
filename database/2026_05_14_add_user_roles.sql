ALTER TABLE users
  ADD COLUMN role ENUM('admin', 'vip', 'user', 'remejas') NOT NULL DEFAULT 'user'
  AFTER password_hash;
