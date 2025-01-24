drop database if exists Fantasy;
create database Fantasy;
create table Fantasy.Equipo(
    nombreEquipo VARCHAR(32),
    nivelCoche Decimal(1,0),
    CONSTRAINT PK_Equipo PRIMARY KEY (nombreEquipo)
);
create table Fantasy.Piloto(
    nombrePiloto VARCHAR(32),
    velocidad Decimal(2,0),
    habilidad Decimal(2,0),
    lluvia Decimal(2,0),
    CONSTRAINT PK_Piloto PRIMARY KEY (nombrePiloto)
);
create table Fantasy.Carrera(
    código Decimal(2,0),
    lugar VARCHAR(32),
    meteorología Decimal(1,0),
    CONSTRAINT PK_Carrera PRIMARY KEY (código)
);

create table Fantasy.Corre_para(
    equipo VARCHAR(32),
    piloto VARCHAR(32),
    CONSTRAINT PK_CORRE PRIMARY KEY (equipo,piloto),
    CONSTRAINT FK_CORRE_EQUIPO FOREIGN KEY (equipo) REFERENCES Fantasy.Equipo(nombreEquipo),
    CONSTRAINT FK_CORRE_PILOTO FOREIGN KEY (piloto) REFERENCES Fantasy.Piloto(nombrePiloto)
);

create table Fantasy.Puntúa_en(
    carrera Decimal(2,0),
    piloto VARCHAR(32),
    posición Decimal(2,0),
    puntos Decimal(2,0),
    CONSTRAINT PK_PUNTÚA PRIMARY KEY (carrera,piloto),
    CONSTRAINT FK_PUNTÚA_CARRERA FOREIGN KEY (carrera) REFERENCES Fantasy.Carrera(código),
    CONSTRAINT FK_PUNTÚA_PILOTO FOREIGN KEY (piloto) REFERENCES Fantasy.Piloto(nombrePiloto)
);

insert into Fantasy.Piloto (nombrePiloto,lluvia,habilidad,velocidad)
values ("Max Verstappen",85,98,96),
("Sergio Perez",91,93,85),
("Lewis Hamilton",97,90,87),
("George Russell",78,87,88),
("Charles Leclerc",78,91,89),
("Carlos Sainz",85,93,88),
("Lando Norris",78,89,90),
("Nikita Mazespin",1,50,99),
("Fernando Alonso",99,94,91),
("Lance Stroll",80,83,79),
("Esteban Ocon",79,83,83),
("Pierre Gasly",79,85,85),
("Alexander Albon",77,83,87),
("Logan Sargeant",62,68,72),
("Yuki Tsunoda",72,74,86),
("Daniel Ricciardo",89,81,82),
("Valtteri Bottas",88,72,83),
("Zhou Guanyu",68,78,83),
("Kevin Magnussen",82,75,82),
("Nico Hulkenberg",85,82,81);
insert into Fantasy.Equipo (nombreEquipo,nivelCoche)
values ("RedBull Fantasy",9),
("Mclaren Fantasy",7),
("Ferrary Fantasy",8),
("Aston Martin Fantasy",7);
