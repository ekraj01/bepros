-- Drop and create database
DROP DATABASE IF EXISTS bepros_portfolio;
CREATE DATABASE bepros_portfolio;
USE bepros_portfolio;

-- Create admins table
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create services table
CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    icon VARCHAR(255),
    is_visible BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create projects table
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255),
    is_visible BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create blogs table
CREATE TABLE blogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    featured_image VARCHAR(255),
    tags VARCHAR(255),
    status ENUM('draft', 'published') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create testimonials table
CREATE TABLE testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    company VARCHAR(255),
    quote TEXT NOT NULL,
    is_visible BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create contacts table
CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample admins (password: admin123)
INSERT INTO admins (email, password, role) VALUES
('admin@bepros.com', '$2y$10$3z7bJq8k3z7bJq8k3z7bJu8k3z7bJq8k3z7bJq8k3z7bJq8k3z7bJ', 'super_admin'),
('staff1@bepros.com', '$2y$10$3z7bJq8k3z7bJq8k3z7bJu8k3z7bJq8k3z7bJq8k3z7bJq8k3z7bJ', 'admin'),
('staff2@bepros.com', '$2y$10$3z7bJq8k3z7bJq8k3z7bJu8k3z7bJq8k3z7bJq8k3z7bJq8k3z7bJ', 'admin');

-- Insert sample services
INSERT INTO services (title, description, icon, is_visible) VALUES
('Software Development', 'Custom software solutions for businesses.', 'software.png', 1),
('Web Development', 'Responsive and modern websites.', 'web.png', 1),
('Mobile App Development', 'Cross-platform mobile applications.', 'mobile.png', 1),
('IT Consulting', 'Strategic IT solutions and support.', 'consulting.png', 0);

-- Insert sample projects
INSERT INTO projects (title, description, image, is_visible) VALUES
('E-Commerce Platform', 'A scalable online store with payment integration.', 'ecommerce.jpg', 1),
('CRM System', 'Custom CRM for client management.', 'crm.jpg', 1),
('Blog Website', 'A dynamic blog platform with admin panel.', 'blogsite.jpg', 0);

-- Insert sample blogs
INSERT INTO blogs (title, content, featured_image, tags, status) VALUES
('Web Design Trends 2025', '<p>Explore the latest trends in web design, including minimalism and AI-driven interfaces.</p>', 'web-trends.jpg', 'web, design, trends', 'published'),
('The Future of Mobile Apps', '<p>Insights into cross-platform development and user experience.</p>', 'mobile-apps.jpg', 'mobile, apps', 'published'),
('Why IT Consulting Matters', '<p>Benefits of strategic IT consulting for businesses.</p>', 'consulting.jpg', 'consulting, IT', 'draft');

-- Insert sample testimonials
INSERT INTO testimonials (name, company, quote, is_visible) VALUES
('John Doe', 'Tech Inc.', 'BePros delivered an outstanding website that boosted our sales!', 1),
('Jane Smith', 'Global Solutions', 'Their team was professional and exceeded our expectations.', 1),
('Mike Johnson', 'Startup Hub', 'The mobile app they built is user-friendly and robust.', 0);

-- Insert sample contacts
INSERT INTO contacts (name, email, message, is_read) VALUES
('Alice Brown', 'alice@example.com', 'Interested in web development services for my business.', 0),
('Bob Wilson', 'bob@example.com', 'Can you provide a quote for a mobile app?', 1),
('Carol Lee', 'carol@example.com', 'Questions about your IT consulting services.', 0);