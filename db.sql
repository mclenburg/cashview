CREATE TABLE `Initialwerte` (
                                `initId` int(11) NOT NULL,
                                `Betrag` int(11) NOT NULL,
                                `KtoId` int(11) DEFAULT NULL,
                                PRIMARY KEY (`initId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci;

CREATE TABLE `kategorien` (
                              `ID` int(11) NOT NULL,
                              `bez` varchar(45) NOT NULL,
                              `sortorder` int(11) DEFAULT NULL,
                              `statscolor` varchar(45) NOT NULL DEFAULT '0,0,0',
                              `manId` int(11) NOT NULL DEFAULT 0,
                              PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci;

CREATE TABLE `Konten` (
                          `id` int(11) NOT NULL,
                          `Bez` varchar(50) NOT NULL,
                          `Grenze` varchar(45) NOT NULL DEFAULT '0',
                          `manId` int(11) NOT NULL,
                          PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci;

CREATE TABLE `laufendes` (
                             `Wert` int(11) NOT NULL,
                             `ktoID` int(11) DEFAULT NULL,
                             `katID` int(11) DEFAULT NULL,
                             `modulo` int(11) DEFAULT NULL,
                             `Beschreibung` varchar(45) DEFAULT NULL,
                             `id` int(11) NOT NULL,
                             `manId` int(11) NOT NULL,
                             `first_run` date DEFAULT current_timestamp(),
                             PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci;

CREATE TABLE `mandanten` (
                             `manId` int(11) NOT NULL,
                             `Bezeichnung` varchar(45) NOT NULL,
                             PRIMARY KEY (`manId`),
                             UNIQUE KEY `manId_UNIQUE` (`manId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci;

CREATE TABLE `transaktionen` (
                                 `wert` int(11) NOT NULL,
                                 `KtoID` int(11) NOT NULL,
                                 `katID` int(11) DEFAULT NULL,
                                 `Datum` datetime DEFAULT NULL,
                                 `manId` int(11) NOT NULL,
                                 KEY `manId_idx` (`wert`) USING BTREE,
                                 KEY `konten_fk_idx` (`KtoID`),
                                 KEY `mandanten_fk_idx` (`manId`),
                                 KEY `kategorien_fk_idx` (`katID`),
                                 CONSTRAINT `kategorien_fk` FOREIGN KEY (`katID`) REFERENCES `kategorien` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
                                 CONSTRAINT `konten_fk` FOREIGN KEY (`KtoID`) REFERENCES `Konten` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                                 CONSTRAINT `mandanten_fk` FOREIGN KEY (`manId`) REFERENCES `mandanten` (`manId`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci;
