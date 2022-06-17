use auth;

CREATE TABLE User(
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(256) NOT NULL,
    user_name VARCHAR(255) NOT NULL,
    pass VARCHAR(255) NOT NULL,
    user_pict VARCHAR(255) NOT NULL,
    GAuthID VARCHAR(255),
    delete_at DATETIME,
    IsTwoFactor INT(0) NOT NULL DEFAULT 0,
    TwoFactorSecret VARCHAR(255),
    delete_flag INT(1) NOT NULL
);