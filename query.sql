CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE password_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(50) NOT NULL,
    color VARCHAR(7)
);

CREATE TABLE password_type_fields (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type_id INT NOT NULL,
    field_name VARCHAR(100) NOT NULL,
    field_label VARCHAR(100) NOT NULL,
    field_type VARCHAR(50) DEFAULT 'text',
    sort_order INT DEFAULT 0,

    FOREIGN KEY (type_id) REFERENCES password_types(id) ON DELETE CASCADE
);

CREATE TABLE passwords (
    id INT AUTO_INCREMENT PRIMARY KEY,

    project_id INT NOT NULL,
    type_id INT NOT NULL,
    label VARCHAR(100) NOT NULL,

    extra JSON DEFAULT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (type_id) REFERENCES password_types(id)
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO `users` (`id`, `email`, `password`, `name`) VALUES
(1, 'admin@test.com', '$2y$10$MrgKzApkZYJNbgWzTTHgXOYLFCefeyP7xhpAnXDyaLoxLfmwSfUTa', 'Admin');

