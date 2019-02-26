DROP DATABASE patienten;
CREATE DATABASE patienten;
USE patienten;
CREATE TABLE patient (
p_id INT(128) NOT NULL AUTO_INCREMENT PRIMARY KEY,
vorname VARCHAR(32),
nachname VARCHAR(32),
geburtsdatum DATE
z_id INT(128)
);

CREATE TABLE zimmer (
z_id	INT(128) PRIMARY KEY,
raum    VARCHAR(128)
);
ALTER TABLE patient ADD FOREIGN KEY (z_id) REFERENCES zimmer (z_id);

INSERT INTO zimmer (z_id,raum)
VALUES
('1','Hammerbrook'),
('2','Jungfernstieg'),
('3','Billwerder'),
('4','Moorfleet'),
('5','Schanze'),
('6','Stellingen'),
('99','Besenkammer');

INSERT INTO patient (vorname,nachname,geburtsdatum,z_id)
VALUES
('Susanne','Martin','1953-03-16','1'),
('Gabriele','Dahm','1966-05-18','1'),
('Martina','Berg','1969-08-31','1'),
('Nicole','Kerling','1978-02-14','1'),
('Michael','Gruppe','1967-09-12','3'),
('Bernhard','Maske','1961-12-24','3'),
('Janusz','Karmzie','1988-11-26','6'),
('Yusuf','Hrabely','1968-06-22','6'),
('Martin','Kramer','2001-03-23','3');
