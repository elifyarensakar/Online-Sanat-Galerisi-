-- Favoriler Tablosu (Gereksinim 3)
CREATE TABLE favorites (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL, -- Arkadaşın 'users' tablosunu yapınca buraya bağlanacak
    artwork_id INT NOT NULL -- Arkadaşın 'artworks' tablosunu yapınca buraya bağlanacak
);

-- Yorumlar ve Puanlama Tablosu (Gereksinim 12, 13)
CREATE TABLE comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    item_id INT NOT NULL, -- Eser veya etkinlik ID'si
    item_type ENUM('artwork', 'event') NOT NULL, -- Yorumun nereye yapıldığını ayırmak için
    comment_text TEXT,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    is_verified_purchase BOOLEAN DEFAULT FALSE, -- Gereksinim 15 (Doğrulama)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Destek Talepleri (Gereksinim 10)
CREATE TABLE support_tickets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    subject VARCHAR(255),
    message TEXT,
    status ENUM('open', 'in_progress', 'closed') DEFAULT 'open'
);