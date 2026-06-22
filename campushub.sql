-- Create Database
CREATE DATABASE IF NOT EXISTS campushub;
USE campushub;

-- Students Table
CREATE TABLE students (
    student_id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(15),
    institution VARCHAR(100),
    course VARCHAR(100),
    profile_pic VARCHAR(255),
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Events Table
CREATE TABLE events (
    event_id INT PRIMARY KEY AUTO_INCREMENT,
    event_title VARCHAR(100) NOT NULL,
    event_description TEXT,
    event_date DATE NOT NULL,
    event_time TIME,
    venue VARCHAR(200),
    category VARCHAR(50),
    max_participants INT,
    image_path VARCHAR(255),
    status ENUM('upcoming', 'ongoing', 'completed') DEFAULT 'upcoming',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Registrations Table
CREATE TABLE registrations (
    registration_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    event_id INT NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    attendance_status ENUM('registered', 'attended', 'absent') DEFAULT 'registered',
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE,
    UNIQUE KEY unique_registration (student_id, event_id)
);

-- Media Table
CREATE TABLE media (
    media_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    file_path VARCHAR(255) NOT NULL,
    file_type ENUM('image', 'video', 'document') NOT NULL,
    event_id INT,
    uploaded_by INT,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE SET NULL,
    FOREIGN KEY (uploaded_by) REFERENCES students(student_id)
);

-- Announcements Table
CREATE TABLE announcements (
    announcement_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    content TEXT NOT NULL,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES students(student_id)
);

-- Insert Sample Data
INSERT INTO students (first_name, last_name, email, phone, institution, course, password) VALUES
('Admin', 'User', 'admin@campushub.com', '0712345678', 'CampusHub', 'Administration', 'admin123');

INSERT INTO events (event_title, event_description, event_date, event_time, venue, category, max_participants, status) VALUES
('Annual Tech Symposium', 'Annual technology symposium featuring workshops and guest speakers', '2026-07-15', '09:00:00', 'Main Auditorium', 'Technology', 200, 'upcoming'),
('Sports Day 2026', 'Inter-college sports competition', '2026-08-01', '08:30:00', 'University Ground', 'Sports', 300, 'upcoming'),
('Cultural Festival', 'Annual cultural festival showcasing talent', '2026-09-10', '10:00:00', 'Cultural Hall', 'Cultural', 150, 'upcoming');

INSERT INTO announcements (title, content, created_by) VALUES
('Welcome to CampusHub', 'Welcome to the CampusHub student services portal. Register for events and stay connected!', 1);