-- Kullanıcılar Tablosu (Hesap Yönetimi) 
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('customer', 'artist', 'admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
); 
-- SANAT ESERLERİ 
CREATE TABLE IF NOT EXISTS artworks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    artist_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255),
    stock_status BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (artist_id) REFERENCES users(id) ON DELETE CASCADE
    ALTER TABLE artworks 
    ADD COLUMN category VARCHAR(50), -- Karşılaştırma için kategori 
    ADD COLUMN view_count INT DEFAULT 0; -- İstatistik için gerekli
);

-- ETKİNLİKLER 
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    event_date DATE NOT NULL,
    event_time TIME NOT NULL,
    capacity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL
    ALTER TABLE events 
    ADD COLUMN current_participants INT DEFAULT 0;
);
-- REZERVASYONLAR 
CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,                -- Rezervasyonu yapan kullanıcı
    event_id INT NOT NULL,               -- Hangi etkinliğe kayıt oldu?
    participant_count INT DEFAULT 1,     -- Katılımcı sayısı
    status ENUM('active', 'cancelled') DEFAULT 'active', -- Durum 
    reservation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (event_id) REFERENCES events(id)
);

-- SİPARİŞLER 
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,                -- Satın alan kullanıcı
    artwork_id INT NOT NULL,             -- Satın alınan eser
    total_price DECIMAL(10, 2) NOT NULL, -- Ödenen miktar
    payment_method VARCHAR(50),          -- Ödeme yöntemi 
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (artwork_id) REFERENCES artworks(id)
);