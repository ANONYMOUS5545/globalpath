-- Global Path Africa Database Schema
-- Compatible with MySQL 5.7+ / MariaDB

CREATE DATABASE IF NOT EXISTS globalpath_africa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE globalpath_africa;

-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(30),
    country VARCHAR(100),
    nationality VARCHAR(100),
    membership_type ENUM('free', 'premium', 'premium_plus') DEFAULT 'free',
    membership_expires DATETIME NULL,
    scholarship_access TINYINT(1) DEFAULT 0,
    profile_photo VARCHAR(255) NULL,
    email_verified TINYINT(1) DEFAULT 0,
    verification_token VARCHAR(255) NULL,
    reset_token VARCHAR(255) NULL,
    reset_expires DATETIME NULL,
    ip_address VARCHAR(45),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login DATETIME NULL,
    status ENUM('active','suspended','pending') DEFAULT 'active'
);

-- Admin Users
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('super_admin','admin','moderator') DEFAULT 'admin',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_login DATETIME NULL
);

-- Scholarships Table
CREATE TABLE scholarships (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(300) NOT NULL,
    provider VARCHAR(200) NOT NULL,
    country VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    eligibility TEXT,
    benefits TEXT,
    deadline DATE,
    link VARCHAR(500),
    source_org VARCHAR(200),
    field_of_study VARCHAR(300),
    level ENUM('undergraduate','postgraduate','phd','all') DEFAULT 'all',
    type ENUM('full','partial','fellowship','exchange') DEFAULT 'full',
    image VARCHAR(255) NULL,
    is_featured TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    african_countries TEXT COMMENT 'JSON array of eligible countries, empty means all',
    created_by INT,
    listing_origin VARCHAR(20) NOT NULL DEFAULT 'manual',
    external_source_key VARCHAR(120) NULL,
    external_id VARCHAR(191) NULL,
    published_at DATETIME NULL,
    last_seen_at DATETIME NULL,
    last_synced_at DATETIME NULL,
    live_metadata MEDIUMTEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admins(id) ON DELETE SET NULL
);

-- Jobs Table
CREATE TABLE jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(300) NOT NULL,
    organization VARCHAR(200) NOT NULL,
    location VARCHAR(200) NOT NULL,
    country VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    requirements TEXT,
    salary_range VARCHAR(100),
    deadline DATE,
    link VARCHAR(500),
    source_org VARCHAR(200),
    job_type ENUM('full_time','part_time','contract','internship','volunteer') DEFAULT 'full_time',
    sector VARCHAR(150),
    is_premium_only TINYINT(1) DEFAULT 0 COMMENT '1 = Premium Plus first access',
    is_featured TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    image VARCHAR(255) NULL,
    created_by INT,
    listing_origin VARCHAR(20) NOT NULL DEFAULT 'manual',
    external_source_key VARCHAR(120) NULL,
    external_id VARCHAR(191) NULL,
    published_at DATETIME NULL,
    last_seen_at DATETIME NULL,
    last_synced_at DATETIME NULL,
    live_metadata MEDIUMTEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admins(id) ON DELETE SET NULL
);

-- Blog Posts Table
CREATE TABLE blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    excerpt TEXT NOT NULL,
    content LONGTEXT NOT NULL,
    category VARCHAR(80) NOT NULL DEFAULT 'guides',
    author_name VARCHAR(120) NOT NULL DEFAULT 'Global Path Africa',
    cover_icon VARCHAR(80) NOT NULL DEFAULT 'fas fa-newspaper',
    reading_time_minutes INT NOT NULL DEFAULT 5,
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    published_at DATETIME NULL,
    created_by INT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_blog_posts_status (is_active, published_at),
    FOREIGN KEY (created_by) REFERENCES admins(id) ON DELETE SET NULL
);

-- Job Application Resources Table
CREATE TABLE job_resources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    resource_key VARCHAR(120) NOT NULL UNIQUE,
    title VARCHAR(255) NOT NULL,
    organization VARCHAR(180) NOT NULL,
    category VARCHAR(80) NOT NULL DEFAULT 'general_jobs',
    region VARCHAR(120) NOT NULL DEFAULT 'Global',
    country VARCHAR(120) NULL,
    resource_type VARCHAR(80) NOT NULL DEFAULT 'official_employer',
    summary TEXT NOT NULL,
    apply_url VARCHAR(500) NOT NULL,
    application_cost_type VARCHAR(30) NOT NULL DEFAULT 'free',
    cost_notes TEXT NULL,
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    sort_order INT NOT NULL DEFAULT 0,
    created_by INT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_job_resources_status (is_active, category, application_cost_type, sort_order),
    FOREIGN KEY (created_by) REFERENCES admins(id) ON DELETE SET NULL
);

-- External Sync State
CREATE TABLE external_sync_state (
    sync_key VARCHAR(120) PRIMARY KEY,
    content_type VARCHAR(30) NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'idle',
    last_started_at DATETIME NULL,
    last_completed_at DATETIME NULL,
    last_success_at DATETIME NULL,
    last_item_count INT NOT NULL DEFAULT 0,
    last_error TEXT NULL,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Visa Resources Table
CREATE TABLE visas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(300) NOT NULL,
    country VARCHAR(150) NOT NULL,
    visa_type VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    requirements TEXT,
    processing_time VARCHAR(100),
    link VARCHAR(500),
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Applications Table
CREATE TABLE applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('scholarship','job','visa') NOT NULL,
    reference_id INT NOT NULL COMMENT 'ID from scholarships or jobs table',
    status ENUM('submitted','under_review','accepted','rejected','withdrawn') DEFAULT 'submitted',
    notes TEXT,
    admin_notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Payments / Transactions Table (no card data stored)
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    transaction_id VARCHAR(255) UNIQUE NOT NULL,
    gateway ENUM('stripe','paypal','flutterwave','mpesa','bank_transfer') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(10) DEFAULT 'USD',
    plan ENUM('premium_monthly','premium_annual','premium_plus_monthly','premium_plus_annual','scholarship_support','visa_support') NOT NULL,
    status ENUM('pending','completed','failed','refunded') DEFAULT 'pending',
    payment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    metadata TEXT COMMENT 'JSON: gateway-specific safe metadata only',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Chat / Support Messages
CREATE TABLE support_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    session_id VARCHAR(100),
    name VARCHAR(150),
    email VARCHAR(255),
    message TEXT NOT NULL,
    reply TEXT NULL,
    is_escalated TINYINT(1) DEFAULT 0,
    status ENUM('open','replied','closed') DEFAULT 'open',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    replied_at DATETIME NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Newsletter Subscribers
CREATE TABLE subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(150),
    country VARCHAR(100),
    subscribed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1
);

-- African Countries Reference
CREATE TABLE african_countries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    code VARCHAR(5) NOT NULL,
    region VARCHAR(100)
);

INSERT INTO african_countries (name, code, region) VALUES
('Algeria','DZ','North Africa'),('Angola','AO','Central Africa'),('Benin','BJ','West Africa'),
('Botswana','BW','Southern Africa'),('Burkina Faso','BF','West Africa'),('Burundi','BI','East Africa'),
('Cabo Verde','CV','West Africa'),('Cameroon','CM','Central Africa'),('Central African Republic','CF','Central Africa'),
('Chad','TD','Central Africa'),('Comoros','KM','East Africa'),('Congo (Brazzaville)','CG','Central Africa'),
('Congo (DRC)','CD','Central Africa'),('Djibouti','DJ','East Africa'),('Egypt','EG','North Africa'),
('Equatorial Guinea','GQ','Central Africa'),('Eritrea','ER','East Africa'),('Eswatini','SZ','Southern Africa'),
('Ethiopia','ET','East Africa'),('Gabon','GA','Central Africa'),('Gambia','GM','West Africa'),
('Ghana','GH','West Africa'),('Guinea','GN','West Africa'),('Guinea-Bissau','GW','West Africa'),
('Ivory Coast','CI','West Africa'),('Kenya','KE','East Africa'),('Lesotho','LS','Southern Africa'),
('Liberia','LR','West Africa'),('Libya','LY','North Africa'),('Madagascar','MG','East Africa'),
('Malawi','MW','East Africa'),('Mali','ML','West Africa'),('Mauritania','MR','North Africa'),
('Mauritius','MU','East Africa'),('Morocco','MA','North Africa'),('Mozambique','MZ','Southern Africa'),
('Namibia','NA','Southern Africa'),('Niger','NE','West Africa'),('Nigeria','NG','West Africa'),
('Rwanda','RW','East Africa'),('São Tomé and Príncipe','ST','Central Africa'),('Senegal','SN','West Africa'),
('Seychelles','SC','East Africa'),('Sierra Leone','SL','West Africa'),('Somalia','SO','East Africa'),
('South Africa','ZA','Southern Africa'),('South Sudan','SS','East Africa'),('Sudan','SD','North Africa'),
('Tanzania','TZ','East Africa'),('Togo','TG','West Africa'),('Tunisia','TN','North Africa'),
('Uganda','UG','East Africa'),('Zambia','ZM','Southern Africa'),('Zimbabwe','ZW','Southern Africa');

-- Default Admin (password: Admin@2024 - CHANGE ON FIRST LOGIN)
INSERT INTO admins (name, email, password_hash, role) VALUES
('Super Admin', 'admin@globalpathAfrica.org', '$2a$12$m3uFjLnja3WOhfS6WkuhouvoxFTLB0qZ7ltqGFA.SOubfU9iAq1TC', 'super_admin');

-- Sample Scholarships
INSERT INTO scholarships (title, provider, country, description, eligibility, benefits, deadline, link, source_org, field_of_study, level, type, is_featured) VALUES
('Erasmus+ Scholarships 2025', 'European Commission', 'European Union', 'The Erasmus+ programme provides opportunities for African students to study at top European universities with full financial support covering tuition, living costs and travel.', 'Open to African nationals, bachelor degree or equivalent, language proficiency required', 'Full tuition waiver, monthly stipend €800-1200, travel allowance, insurance', '2025-03-31', 'https://erasmus-plus.ec.europa.eu', 'Erasmus+', 'All Fields', 'postgraduate', 'full', 1),
('DAAD Scholarships Germany', 'DAAD', 'Germany', 'DAAD offers scholarships for development-related postgraduate courses for professionals from developing countries, including all African nations.', 'University degree, 2 years professional experience, under 36 years old', 'Full tuition, monthly allowance €934, health insurance, travel subsidy', '2024-10-31', 'https://www.daad.de/en/', 'DAAD Germany', 'Development Studies, Engineering, Agriculture', 'postgraduate', 'full', 1),
('Chevening Scholarships UK', 'UK Government', 'United Kingdom', 'Chevening is the UK government global scholarship programme. It offers fully funded Masters scholarships to outstanding emerging leaders from Africa and worldwide.', 'African nationals, 2 years work experience, leadership potential', 'Full tuition, monthly stipend, flights, visa costs', '2024-11-05', 'https://www.chevening.org', 'UK Foreign Office', 'All Fields', 'postgraduate', 'full', 1),
('Fulbright Foreign Student Program', 'U.S. Department of State', 'United States', 'The Fulbright Program is one of the most prestigious scholarship programs globally, offering opportunities for African students to pursue graduate study in the United States.', 'Undergraduate degree, strong academic record, leadership skills', 'Full tuition, living stipend, health insurance, round-trip airfare', '2025-02-28', 'https://foreign.fulbrightonline.org', 'U.S. State Department', 'All Fields', 'postgraduate', 'full', 1),
('World Bank Graduate Scholarship', 'World Bank Group', 'Various Countries', 'The Joint Japan/World Bank Graduate Scholarship Program provides scholarships to development professionals from developing member countries.', 'From World Bank member developing country, 2+ years development work experience', 'Full tuition, living expenses, travel, health insurance', '2025-04-30', 'https://www.worldbank.org/scholarships', 'World Bank', 'Development Economics, Public Policy', 'postgraduate', 'full', 0),
('Commonwealth Scholarship', 'Commonwealth Secretariat', 'United Kingdom', 'Commonwealth Scholarships enable talented and motivated people to gain the skills and qualifications needed to drive their development objectives.', 'Commonwealth country citizen, first degree at upper second class', 'Tuition fees, living allowance, travel costs', '2025-01-31', 'https://cscuk.fcdo.gov.uk', 'Commonwealth', 'All Fields', 'postgraduate', 'full', 0);

-- Sample Jobs
INSERT INTO jobs (title, organization, location, country, description, requirements, deadline, link, source_org, job_type, sector, is_premium_only, is_featured) VALUES
('Programme Officer - Africa', 'United Nations', 'Nairobi, Kenya / Remote', 'Kenya', 'Join the UN team managing development programmes across East Africa. The role involves coordinating regional initiatives and partnerships.', 'Masters degree, 5+ years experience, French/English fluency', '2025-02-28', 'https://careers.un.org', 'UN Jobs', 'full_time', 'International Development', 1, 1),
('Research Fellow - Development Economics', 'World Bank Group', 'Washington DC / Remote', 'United States', 'The World Bank seeks research fellows with strong quantitative skills to work on Africa-focused development projects.', 'PhD in Economics or related field, research experience', '2025-03-15', 'https://jobs.worldbank.org', 'World Bank', 'full_time', 'Research & Economics', 1, 1),
('Health Programme Manager', 'WHO - World Health Organization', 'Geneva / Africa Region', 'Switzerland', 'WHO is recruiting health programme managers to oversee public health initiatives across African member states.', 'Medical degree or public health masters, 7+ years experience', '2025-02-15', 'https://www.who.int/careers', 'WHO', 'full_time', 'Healthcare', 0, 1),
('Climate Change Advisor', 'African Development Bank', 'Abidjan, Ivory Coast', 'Ivory Coast', 'The AfDB seeks a Climate Change Advisor to lead policy development and project management for green finance initiatives across Africa.', 'Masters in environmental science/economics, 5 years experience', '2025-01-31', 'https://afdb.org/careers', 'African Development Bank', 'full_time', 'Environment & Climate', 0, 0);

ALTER TABLE scholarships ADD FULLTEXT INDEX ft_scholarship (title, description, field_of_study);
ALTER TABLE jobs ADD FULLTEXT INDEX ft_jobs (title, description, sector);
ALTER TABLE scholarships ADD UNIQUE KEY ux_scholarships_external (external_source_key, external_id);
ALTER TABLE scholarships ADD INDEX idx_scholarships_origin_active (listing_origin, is_active);
ALTER TABLE jobs ADD UNIQUE KEY ux_jobs_external (external_source_key, external_id);
ALTER TABLE jobs ADD INDEX idx_jobs_origin_active (listing_origin, is_active);
