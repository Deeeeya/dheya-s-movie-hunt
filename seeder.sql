CREATE DATABASE IF NOT EXISTS movie_watchlist;

USE movie_watchlist;

CREATE TABLE IF NOT EXISTS movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tmdb_id INT NOT NULL UNIQUE,
    title VARCHAR(255) NOT NULL,
    overview TEXT,
    release_date DATE,
    poster_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    movie_id INT NOT NULL,
    username VARCHAR(100) NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT NOT NULL,
    created_at DATETIME NOT NULL
);

INSERT INTO movies (tmdb_id, title, overview, release_date, poster_path) VALUES
(550, 'Fight Club', 'A ticking-time-bomb insomniac and a slippery soap salesman channel primal male aggression into a shocking new form of therapy.', '1999-10-15', '/pB8BM7pdSp6B6Ih7QZ4DrQ3PmJK.jpg'),
(299536, 'Avengers: Infinity War', 'As the Avengers and their allies have continued to protect the world from threats too large for any one hero to handle, a new danger has emerged from the cosmic shadows: Thanos.', '2018-04-25', '/7WsyChQLEftFiDOVTGkv3hFpyyt.jpg'),
(278, 'The Shawshank Redemption', 'Framed in the 1940s for the double murder of his wife and her lover, upstanding banker Andy Dufresne begins a new life at the Shawshank prison.', '1994-09-23', '/q6y0Go1tsGEsmtFryDOJo3dEmqu.jpg');

INSERT INTO reviews (movie_id, username, rating, comment, created_at) VALUES
(550, 'MovieFan1', 5, 'Fight Club is a masterpiece! The twist ending blew my mind.', '2025-04-15 10:30:00'),
(550, 'CinemaLover', 4, 'Great performances and direction. A bit too violent for my taste but still a classic.', '2025-04-16 15:45:00'),
(299536, 'MarvelGeek', 5, 'Infinity War is the best Marvel movie ever made! The ending left me speechless.', '2025-04-10 20:15:00'),
(299536, 'SuperheroFan', 4, 'Amazing action sequences and emotional moments. Thanos is the best MCU villain.', '2025-04-12 11:20:00'),
(278, 'ClassicMovieBuff', 5, 'The Shawshank Redemption is perfect in every way. A powerful story of hope and friendship.', '2025-04-05 09:10:00');