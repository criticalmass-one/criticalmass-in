-- =============================================================================
-- REGION HIERARCHY COMPLETION
-- =============================================================================
--
-- Bestehende Struktur:
--   World (1)
--     ├── Europe (2)        — 30+ Länder, viele mit Unterteilungen
--     ├── America (87)      — 12 Länder, US-Staaten vorhanden
--     ├── Asia (271)        — Pakistan, Japan, Indonesia
--     ├── Africa (337)      — Uganda, South Africa
--     └── Australia (339)   — direkt unter World, ohne Unterteilungen
--
-- HINWEIS: Statements in Reihenfolge ausführen (Länder vor Unterteilungen)
-- =============================================================================


-- =============================================================================
-- 1. KONTINENTE
-- =============================================================================

-- Oceania fehlt als Kontinent. Aktuell hängt Australia (339) direkt unter World.
-- Option: Oceania anlegen und Australia darunter verschieben:
--
-- INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Oceania', 'oceania', 1, 'Q538');
-- UPDATE region SET parent_id = (SELECT id FROM region WHERE slug = 'oceania') WHERE id = 339;
-- INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('New Zealand', 'new-zealand', (SELECT id FROM region WHERE slug = 'oceania'), 'Q664');
--
-- Alternativ: New Zealand als Geschwister neben Australia direkt unter World:
-- INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('New Zealand', 'new-zealand', 1, 'Q664');


-- =============================================================================
-- 2. FEHLENDE LÄNDER
-- =============================================================================

-- -----------------------------------------------------------------------------
-- 2.1 Europa (parent_id = 2)
-- -----------------------------------------------------------------------------

INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Denmark', 'denmark', 2, 'Q35');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Estonia', 'estonia', 2, 'Q191');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Albania', 'albania', 2, 'Q222');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Bulgaria', 'bulgaria', 2, 'Q219');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Slovenia', 'slovenia', 2, 'Q215');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Moldova', 'moldova', 2, 'Q217');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Cyprus', 'cyprus', 2, 'Q229');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Malta', 'malta', 2, 'Q233');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Andorra', 'andorra', 2, 'Q228');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Monaco', 'monaco', 2, 'Q235');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('San Marino', 'san-marino', 2, 'Q238');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Liechtenstein', 'liechtenstein', 2, 'Q347');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Kosovo', 'kosovo', 2, 'Q1246');

-- HINWEIS: Poland existiert doppelt (id 77 "poland" + id 304 "polska").
-- Die Woiwodschaften hängen unter 304. Ggf. id 77 bereinigen.

-- -----------------------------------------------------------------------------
-- 2.2 Amerika (parent_id = 87)
-- -----------------------------------------------------------------------------

INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Ecuador', 'ecuador', 87, 'Q736');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Paraguay', 'paraguay', 87, 'Q733');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Guatemala', 'guatemala', 87, 'Q774');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Honduras', 'honduras', 87, 'Q783');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Nicaragua', 'nicaragua', 87, 'Q811');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Panama', 'panama', 87, 'Q804');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Cuba', 'cuba', 87, 'Q241');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Dominican Republic', 'dominican-republic', 87, 'Q786');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Jamaica', 'jamaica', 87, 'Q766');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Trinidad and Tobago', 'trinidad-and-tobago', 87, 'Q754');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('El Salvador', 'el-salvador', 87, 'Q792');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Haiti', 'haiti', 87, 'Q790');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Belize', 'belize', 87, 'Q242');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Guyana', 'guyana', 87, 'Q734');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Suriname', 'suriname', 87, 'Q730');

-- -----------------------------------------------------------------------------
-- 2.3 Asien (parent_id = 271)
-- -----------------------------------------------------------------------------

INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('India', 'india', 271, 'Q668');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('China', 'china', 271, 'Q148');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('South Korea', 'south-korea', 271, 'Q884');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Philippines', 'philippines', 271, 'Q928');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Thailand', 'thailand', 271, 'Q869');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Vietnam', 'vietnam', 271, 'Q881');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Malaysia', 'malaysia', 271, 'Q833');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Myanmar', 'myanmar', 271, 'Q836');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Nepal', 'nepal', 271, 'Q837');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Bangladesh', 'bangladesh', 271, 'Q902');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Sri Lanka', 'sri-lanka', 271, 'Q854');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Taiwan', 'taiwan', 271, 'Q865');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Iran', 'iran', 271, 'Q794');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Iraq', 'iraq', 271, 'Q796');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Israel', 'israel', 271, 'Q801');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Lebanon', 'lebanon', 271, 'Q822');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Jordan', 'jordan', 271, 'Q810');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Saudi Arabia', 'saudi-arabia', 271, 'Q851');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('United Arab Emirates', 'united-arab-emirates', 271, 'Q878');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Armenia', 'armenia', 271, 'Q399');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Azerbaijan', 'azerbaijan', 271, 'Q227');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Georgia', 'georgia', 271, 'Q230');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Kazakhstan', 'kazakhstan', 271, 'Q232');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Singapore', 'singapore', 271, 'Q334');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Cambodia', 'cambodia', 271, 'Q424');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Mongolia', 'mongolia', 271, 'Q711');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Afghanistan', 'afghanistan', 271, 'Q889');

-- -----------------------------------------------------------------------------
-- 2.4 Afrika (parent_id = 337)
-- -----------------------------------------------------------------------------

INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Kenya', 'kenya', 337, 'Q114');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Nigeria', 'nigeria', 337, 'Q1033');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Ghana', 'ghana', 337, 'Q117');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Tanzania', 'tanzania', 337, 'Q924');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Morocco', 'morocco', 337, 'Q1028');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Egypt', 'egypt', 337, 'Q79');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Ethiopia', 'ethiopia', 337, 'Q115');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Senegal', 'senegal', 337, 'Q1041');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Tunisia', 'tunisia', 337, 'Q948');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Algeria', 'algeria', 337, 'Q262');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Cameroon', 'cameroon', 337, 'Q1009');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('DR Congo', 'dr-congo', 337, 'Q974');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Mozambique', 'mozambique', 337, 'Q1029');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Zimbabwe', 'zimbabwe', 337, 'Q954');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Namibia', 'namibia', 337, 'Q1030');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Rwanda', 'rwanda', 337, 'Q1037');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Zambia', 'zambia', 337, 'Q953');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Madagascar', 'madagascar', 337, 'Q1019');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Ivory Coast', 'ivory-coast', 337, 'Q1008');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Angola', 'angola', 337, 'Q916');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Botswana', 'botswana', 337, 'Q963');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Libya', 'libya', 337, 'Q1016');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Sudan', 'sudan', 337, 'Q1049');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Somalia', 'somalia', 337, 'Q1045');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Eritrea', 'eritrea', 337, 'Q986');


-- =============================================================================
-- 3. VERWALTUNGSEINHEITEN
-- =============================================================================

-- -----------------------------------------------------------------------------
-- 3.1 Kanada — Provinzen und Territorien (parent_id = 88)
-- -----------------------------------------------------------------------------

INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Alberta', 'alberta', 88, 'Q1951');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('British Columbia', 'british-columbia', 88, 'Q1974');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Manitoba', 'manitoba', 88, 'Q1948');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('New Brunswick', 'new-brunswick', 88, 'Q1965');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Newfoundland and Labrador', 'newfoundland-and-labrador', 88, 'Q2003');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Northwest Territories', 'northwest-territories', 88, 'Q2007');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Nova Scotia', 'nova-scotia', 88, 'Q1952');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Nunavut', 'nunavut', 88, 'Q2023');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Ontario', 'ontario', 88, 'Q1904');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Prince Edward Island', 'prince-edward-island', 88, 'Q1979');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Quebec', 'quebec', 88, 'Q176');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Saskatchewan', 'saskatchewan', 88, 'Q1989');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Yukon', 'yukon', 88, 'Q2009');

-- -----------------------------------------------------------------------------
-- 3.2 Dänemark — Regionen (parent per Subselect)
-- -----------------------------------------------------------------------------

INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Hovedstaden', 'hovedstaden', (SELECT id FROM (SELECT id FROM region WHERE slug = 'denmark') AS tmp), 'Q26073');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Midtjylland', 'midtjylland', (SELECT id FROM (SELECT id FROM region WHERE slug = 'denmark') AS tmp), 'Q26076');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Nordjylland', 'nordjylland', (SELECT id FROM (SELECT id FROM region WHERE slug = 'denmark') AS tmp), 'Q26077');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Sjælland', 'sjaelland', (SELECT id FROM (SELECT id FROM region WHERE slug = 'denmark') AS tmp), 'Q26078');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Syddanmark', 'syddanmark', (SELECT id FROM (SELECT id FROM region WHERE slug = 'denmark') AS tmp), 'Q26079');

-- -----------------------------------------------------------------------------
-- 3.3 Indien — Bundesstaaten und Unionsterritorien (parent per Subselect)
-- -----------------------------------------------------------------------------

INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Andhra Pradesh', 'andhra-pradesh', (SELECT id FROM (SELECT id FROM region WHERE slug = 'india') AS tmp), 'Q1159');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Arunachal Pradesh', 'arunachal-pradesh', (SELECT id FROM (SELECT id FROM region WHERE slug = 'india') AS tmp), 'Q1162');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Assam', 'assam', (SELECT id FROM (SELECT id FROM region WHERE slug = 'india') AS tmp), 'Q1164');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Bihar', 'bihar', (SELECT id FROM (SELECT id FROM region WHERE slug = 'india') AS tmp), 'Q1165');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Chhattisgarh', 'chhattisgarh', (SELECT id FROM (SELECT id FROM region WHERE slug = 'india') AS tmp), 'Q1168');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Goa', 'goa', (SELECT id FROM (SELECT id FROM region WHERE slug = 'india') AS tmp), 'Q1171');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Gujarat', 'gujarat', (SELECT id FROM (SELECT id FROM region WHERE slug = 'india') AS tmp), 'Q1061');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Haryana', 'haryana', (SELECT id FROM (SELECT id FROM region WHERE slug = 'india') AS tmp), 'Q1174');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Himachal Pradesh', 'himachal-pradesh', (SELECT id FROM (SELECT id FROM region WHERE slug = 'india') AS tmp), 'Q1177');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Jharkhand', 'jharkhand', (SELECT id FROM (SELECT id FROM region WHERE slug = 'india') AS tmp), 'Q1184');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Karnataka', 'karnataka', (SELECT id FROM (SELECT id FROM region WHERE slug = 'india') AS tmp), 'Q1185');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Kerala', 'kerala', (SELECT id FROM (SELECT id FROM region WHERE slug = 'india') AS tmp), 'Q1186');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Madhya Pradesh', 'madhya-pradesh', (SELECT id FROM (SELECT id FROM region WHERE slug = 'india') AS tmp), 'Q1188');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Maharashtra', 'maharashtra', (SELECT id FROM (SELECT id FROM region WHERE slug = 'india') AS tmp), 'Q1191');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Manipur', 'manipur', (SELECT id FROM (SELECT id FROM region WHERE slug = 'india') AS tmp), 'Q1193');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Meghalaya', 'meghalaya', (SELECT id FROM (SELECT id FROM region WHERE slug = 'india') AS tmp), 'Q1195');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Mizoram', 'mizoram', (SELECT id FROM (SELECT id FROM region WHERE slug = 'india') AS tmp), 'Q1502');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Nagaland', 'nagaland', (SELECT id FROM (SELECT id FROM region WHERE slug = 'india') AS tmp), 'Q1599');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Odisha', 'odisha', (SELECT id FROM (SELECT id FROM region WHERE slug = 'india') AS tmp), 'Q22048');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Punjab', 'punjab-india', (SELECT id FROM (SELECT id FROM region WHERE slug = 'india') AS tmp), 'Q22424');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Rajasthan', 'rajasthan', (SELECT id FROM (SELECT id FROM region WHERE slug = 'india') AS tmp), 'Q1437');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Sikkim', 'sikkim', (SELECT id FROM (SELECT id FROM region WHERE slug = 'india') AS tmp), 'Q1505');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Tamil Nadu', 'tamil-nadu', (SELECT id FROM (SELECT id FROM region WHERE slug = 'india') AS tmp), 'Q1445');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Telangana', 'telangana', (SELECT id FROM (SELECT id FROM region WHERE slug = 'india') AS tmp), 'Q677037');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Tripura', 'tripura', (SELECT id FROM (SELECT id FROM region WHERE slug = 'india') AS tmp), 'Q1363');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Uttar Pradesh', 'uttar-pradesh', (SELECT id FROM (SELECT id FROM region WHERE slug = 'india') AS tmp), 'Q1498');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Uttarakhand', 'uttarakhand', (SELECT id FROM (SELECT id FROM region WHERE slug = 'india') AS tmp), 'Q1499');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('West Bengal', 'west-bengal', (SELECT id FROM (SELECT id FROM region WHERE slug = 'india') AS tmp), 'Q1356');
-- Unionsterritorien
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Delhi', 'delhi', (SELECT id FROM (SELECT id FROM region WHERE slug = 'india') AS tmp), 'Q1353');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Chandigarh', 'chandigarh', (SELECT id FROM (SELECT id FROM region WHERE slug = 'india') AS tmp), 'Q43433');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Jammu and Kashmir', 'jammu-and-kashmir', (SELECT id FROM (SELECT id FROM region WHERE slug = 'india') AS tmp), 'Q66743035');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Ladakh', 'ladakh', (SELECT id FROM (SELECT id FROM region WHERE slug = 'india') AS tmp), 'Q200667');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Puducherry', 'puducherry', (SELECT id FROM (SELECT id FROM region WHERE slug = 'india') AS tmp), 'Q66743');

-- -----------------------------------------------------------------------------
-- 3.4 Japan — Präfekturen (parent_id = 458)
-- -----------------------------------------------------------------------------

INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Hokkaido', 'hokkaido', 458, 'Q19868');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Aomori', 'aomori', 458, 'Q82053');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Iwate', 'iwate', 458, 'Q82061');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Miyagi', 'miyagi', 458, 'Q82068');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Akita', 'akita', 458, 'Q82075');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Yamagata', 'yamagata', 458, 'Q82082');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Fukushima', 'fukushima', 458, 'Q82087');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Ibaraki', 'ibaraki', 458, 'Q82096');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Tochigi', 'tochigi', 458, 'Q82103');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Gunma', 'gunma', 458, 'Q82107');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Saitama', 'saitama', 458, 'Q82111');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Chiba', 'chiba', 458, 'Q82115');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Tokyo', 'tokyo', 458, 'Q1490');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Kanagawa', 'kanagawa', 458, 'Q127513');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Niigata', 'niigata', 458, 'Q132946');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Toyama', 'toyama', 458, 'Q134779');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Ishikawa', 'ishikawa', 458, 'Q132684');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Fukui', 'fukui', 458, 'Q130290');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Yamanashi', 'yamanashi', 458, 'Q135669');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Nagano', 'nagano', 458, 'Q131025');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Gifu', 'gifu', 458, 'Q131277');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Shizuoka', 'shizuoka', 458, 'Q134568');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Aichi', 'aichi', 458, 'Q80447');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Mie', 'mie', 458, 'Q131551');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Shiga', 'shiga', 458, 'Q134375');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Kyoto', 'kyoto-prefecture', 458, 'Q120730');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Osaka', 'osaka-prefecture', 458, 'Q122723');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Hyogo', 'hyogo', 458, 'Q131481');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Nara', 'nara-prefecture', 458, 'Q131287');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Wakayama', 'wakayama', 458, 'Q135672');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Tottori', 'tottori', 458, 'Q134017');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Shimane', 'shimane', 458, 'Q134252');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Okayama', 'okayama', 458, 'Q132052');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Hiroshima', 'hiroshima', 458, 'Q130280');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Yamaguchi', 'yamaguchi', 458, 'Q135646');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Tokushima', 'tokushima', 458, 'Q134099');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Kagawa', 'kagawa', 458, 'Q130300');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Ehime', 'ehime', 458, 'Q123376');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Kochi', 'kochi', 458, 'Q132682');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Fukuoka', 'fukuoka', 458, 'Q123258');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Saga', 'saga', 458, 'Q133865');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Nagasaki', 'nagasaki', 458, 'Q131558');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Kumamoto', 'kumamoto', 458, 'Q130308');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Oita', 'oita', 458, 'Q133928');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Miyazaki', 'miyazaki', 458, 'Q131614');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Kagoshima', 'kagoshima', 458, 'Q130302');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Okinawa', 'okinawa', 458, 'Q766218');

-- -----------------------------------------------------------------------------
-- 3.5 Indonesien — Provinzen (parent_id = 459)
-- -----------------------------------------------------------------------------

INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Aceh', 'aceh', 459, 'Q1823');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('North Sumatra', 'north-sumatra', 459, 'Q2140');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('West Sumatra', 'west-sumatra', 459, 'Q2788');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Riau', 'riau', 459, 'Q2786');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Jambi', 'jambi', 459, 'Q2787');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('South Sumatra', 'south-sumatra', 459, 'Q2789');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Bengkulu', 'bengkulu', 459, 'Q2790');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Lampung', 'lampung', 459, 'Q2791');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Bangka Belitung Islands', 'bangka-belitung', 459, 'Q2792');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Riau Islands', 'riau-islands', 459, 'Q2793');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('DKI Jakarta', 'dki-jakarta', 459, 'Q3630');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('West Java', 'west-java', 459, 'Q3724');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Central Java', 'central-java', 459, 'Q3557');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('DI Yogyakarta', 'di-yogyakarta', 459, 'Q3741');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('East Java', 'east-java', 459, 'Q3586');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Banten', 'banten', 459, 'Q3609');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Bali', 'bali', 459, 'Q3125978');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('West Nusa Tenggara', 'west-nusa-tenggara', 459, 'Q3542');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('East Nusa Tenggara', 'east-nusa-tenggara', 459, 'Q3543');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('West Kalimantan', 'west-kalimantan', 459, 'Q3903');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Central Kalimantan', 'central-kalimantan', 459, 'Q3904');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('South Kalimantan', 'south-kalimantan', 459, 'Q3905');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('East Kalimantan', 'east-kalimantan', 459, 'Q3906');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('North Kalimantan', 'north-kalimantan', 459, 'Q14256');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('North Sulawesi', 'north-sulawesi', 459, 'Q3710');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Central Sulawesi', 'central-sulawesi', 459, 'Q3711');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('South Sulawesi', 'south-sulawesi', 459, 'Q3712');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Southeast Sulawesi', 'southeast-sulawesi', 459, 'Q3713');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Gorontalo', 'gorontalo', 459, 'Q3715');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('West Sulawesi', 'west-sulawesi', 459, 'Q3716');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Maluku', 'maluku', 459, 'Q3492');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('North Maluku', 'north-maluku', 459, 'Q3493');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Papua', 'papua', 459, 'Q3499');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('West Papua', 'west-papua', 459, 'Q3500');

-- -----------------------------------------------------------------------------
-- 3.6 Südafrika — Provinzen (parent_id = 461)
-- -----------------------------------------------------------------------------

INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Eastern Cape', 'eastern-cape', 461, 'Q130741');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Free State', 'free-state', 461, 'Q133083');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Gauteng', 'gauteng', 461, 'Q133772');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('KwaZulu-Natal', 'kwazulu-natal', 461, 'Q81725');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Limpopo', 'limpopo', 461, 'Q134208');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Mpumalanga', 'mpumalanga', 461, 'Q134461');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('North West', 'north-west', 461, 'Q133082');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Northern Cape', 'northern-cape', 461, 'Q132418');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Western Cape', 'western-cape', 461, 'Q127167');

-- -----------------------------------------------------------------------------
-- 3.7 Australien — Bundesstaaten und Territorien (parent_id = 339)
-- -----------------------------------------------------------------------------

INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('New South Wales', 'new-south-wales', 339, 'Q3224');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Victoria', 'victoria', 339, 'Q36687');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Queensland', 'queensland', 339, 'Q36074');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('South Australia', 'south-australia', 339, 'Q35715');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Western Australia', 'western-australia', 339, 'Q3206');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Tasmania', 'tasmania', 339, 'Q34366');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Australian Capital Territory', 'australian-capital-territory', 339, 'Q3258');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Northern Territory', 'northern-territory', 339, 'Q3235');

-- -----------------------------------------------------------------------------
-- 3.8 Bosnien und Herzegowina — Entitäten (parent_id = 460)
-- -----------------------------------------------------------------------------

INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Federation of Bosnia and Herzegovina', 'federation-of-bih', 460, 'Q11198');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Republika Srpska', 'republika-srpska', 460, 'Q11196');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Brčko District', 'brcko-district', 460, 'Q193272');

-- -----------------------------------------------------------------------------
-- 3.9 Kolumbien — Departamentos (parent_id = 95)
-- -----------------------------------------------------------------------------

INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Amazonas', 'amazonas-co', 95, 'Q229411');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Antioquia', 'antioquia', 95, 'Q123304');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Atlántico', 'atlantico', 95, 'Q123381');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Bogotá D.C.', 'bogota', 95, 'Q2841');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Bolívar', 'bolivar', 95, 'Q123444');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Boyacá', 'boyaca', 95, 'Q121233');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Caldas', 'caldas', 95, 'Q238884');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Cundinamarca', 'cundinamarca', 95, 'Q244158');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Norte de Santander', 'norte-de-santander', 95, 'Q245085');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Santander', 'santander', 95, 'Q244402');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Valle del Cauca', 'valle-del-cauca', 95, 'Q245066');

-- -----------------------------------------------------------------------------
-- 3.10 Chile — Regionen (parent_id = 96)
-- -----------------------------------------------------------------------------

INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Arica y Parinacota', 'arica-y-parinacota', 96, 'Q2121');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Tarapacá', 'tarapaca', 96, 'Q2124');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Antofagasta', 'antofagasta', 96, 'Q2126');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Atacama', 'atacama', 96, 'Q2128');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Coquimbo', 'coquimbo', 96, 'Q2130');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Valparaíso', 'valparaiso', 96, 'Q2132');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Región Metropolitana', 'region-metropolitana', 96, 'Q2134');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('O''Higgins', 'ohiggins', 96, 'Q2136');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Maule', 'maule', 96, 'Q2138');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Biobío', 'biobio', 96, 'Q2142');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Araucanía', 'araucania', 96, 'Q2144');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Los Ríos', 'los-rios', 96, 'Q2146');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Los Lagos', 'los-lagos', 96, 'Q2148');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Aysén', 'aysen', 96, 'Q2150');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Magallanes', 'magallanes', 96, 'Q2152');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Ñuble', 'nuble', 96, 'Q56039');

-- -----------------------------------------------------------------------------
-- 3.11 Argentinien — Provinzen (parent_id = 92)
-- -----------------------------------------------------------------------------

INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Buenos Aires', 'buenos-aires', 92, 'Q44754');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Ciudad Autónoma de Buenos Aires', 'caba', 92, 'Q1486');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Catamarca', 'catamarca', 92, 'Q44817');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Chaco', 'chaco', 92, 'Q44821');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Chubut', 'chubut', 92, 'Q44825');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Córdoba', 'cordoba', 92, 'Q44829');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Corrientes', 'corrientes', 92, 'Q44833');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Entre Ríos', 'entre-rios', 92, 'Q44837');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Formosa', 'formosa', 92, 'Q44841');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Jujuy', 'jujuy', 92, 'Q44845');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('La Pampa', 'la-pampa', 92, 'Q44849');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('La Rioja', 'la-rioja-ar', 92, 'Q44853');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Mendoza', 'mendoza', 92, 'Q44857');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Misiones', 'misiones', 92, 'Q44861');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Neuquén', 'neuquen', 92, 'Q44865');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Río Negro', 'rio-negro-ar', 92, 'Q44869');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Salta', 'salta', 92, 'Q44873');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('San Juan', 'san-juan', 92, 'Q44877');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('San Luis', 'san-luis', 92, 'Q44881');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Santa Cruz', 'santa-cruz-ar', 92, 'Q44885');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Santa Fe', 'santa-fe', 92, 'Q44889');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Santiago del Estero', 'santiago-del-estero', 92, 'Q44893');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Tierra del Fuego', 'tierra-del-fuego', 92, 'Q44897');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Tucumán', 'tucuman', 92, 'Q44901');

-- -----------------------------------------------------------------------------
-- 3.12 Mexiko — Bundesstaaten (parent_id = 91)
-- -----------------------------------------------------------------------------

INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Aguascalientes', 'aguascalientes', 91, 'Q79913');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Baja California', 'baja-california', 91, 'Q58731');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Baja California Sur', 'baja-california-sur', 91, 'Q46508');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Campeche', 'campeche', 91, 'Q80908');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Chiapas', 'chiapas', 91, 'Q60130');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Chihuahua', 'chihuahua', 91, 'Q80907');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Ciudad de México', 'ciudad-de-mexico', 91, 'Q1489');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Coahuila', 'coahuila', 91, 'Q80906');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Colima', 'colima', 91, 'Q80905');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Durango', 'durango', 91, 'Q80904');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Guanajuato', 'guanajuato', 91, 'Q80903');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Guerrero', 'guerrero', 91, 'Q80902');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Hidalgo', 'hidalgo', 91, 'Q80901');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Jalisco', 'jalisco', 91, 'Q13160');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('México', 'estado-de-mexico', 91, 'Q80899');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Michoacán', 'michoacan', 91, 'Q80898');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Morelos', 'morelos', 91, 'Q80897');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Nayarit', 'nayarit', 91, 'Q80896');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Nuevo León', 'nuevo-leon', 91, 'Q80895');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Oaxaca', 'oaxaca', 91, 'Q80894');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Puebla', 'puebla', 91, 'Q80893');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Querétaro', 'queretaro', 91, 'Q80892');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Quintana Roo', 'quintana-roo', 91, 'Q80891');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('San Luis Potosí', 'san-luis-potosi', 91, 'Q80890');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Sinaloa', 'sinaloa', 91, 'Q80889');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Sonora', 'sonora', 91, 'Q80888');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Tabasco', 'tabasco', 91, 'Q80887');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Tamaulipas', 'tamaulipas', 91, 'Q80886');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Tlaxcala', 'tlaxcala', 91, 'Q80885');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Veracruz', 'veracruz', 91, 'Q80884');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Yucatán', 'yucatan', 91, 'Q80883');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Zacatecas', 'zacatecas', 91, 'Q80882');

-- -----------------------------------------------------------------------------
-- 3.13 Südkorea — Provinzen und Sonderstädte (parent per Subselect)
-- -----------------------------------------------------------------------------

INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Seoul', 'seoul', (SELECT id FROM (SELECT id FROM region WHERE slug = 'south-korea') AS tmp), 'Q8684');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Busan', 'busan', (SELECT id FROM (SELECT id FROM region WHERE slug = 'south-korea') AS tmp), 'Q16520');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Daegu', 'daegu', (SELECT id FROM (SELECT id FROM region WHERE slug = 'south-korea') AS tmp), 'Q41234');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Incheon', 'incheon', (SELECT id FROM (SELECT id FROM region WHERE slug = 'south-korea') AS tmp), 'Q41263');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Gwangju', 'gwangju', (SELECT id FROM (SELECT id FROM region WHERE slug = 'south-korea') AS tmp), 'Q41295');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Daejeon', 'daejeon', (SELECT id FROM (SELECT id FROM region WHERE slug = 'south-korea') AS tmp), 'Q41257');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Ulsan', 'ulsan', (SELECT id FROM (SELECT id FROM region WHERE slug = 'south-korea') AS tmp), 'Q41278');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Sejong', 'sejong', (SELECT id FROM (SELECT id FROM region WHERE slug = 'south-korea') AS tmp), 'Q484637');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Gyeonggi', 'gyeonggi', (SELECT id FROM (SELECT id FROM region WHERE slug = 'south-korea') AS tmp), 'Q20937');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Gangwon', 'gangwon', (SELECT id FROM (SELECT id FROM region WHERE slug = 'south-korea') AS tmp), 'Q41188');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('North Chungcheong', 'north-chungcheong', (SELECT id FROM (SELECT id FROM region WHERE slug = 'south-korea') AS tmp), 'Q41208');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('South Chungcheong', 'south-chungcheong', (SELECT id FROM (SELECT id FROM region WHERE slug = 'south-korea') AS tmp), 'Q41211');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('North Jeolla', 'north-jeolla', (SELECT id FROM (SELECT id FROM region WHERE slug = 'south-korea') AS tmp), 'Q41213');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('South Jeolla', 'south-jeolla', (SELECT id FROM (SELECT id FROM region WHERE slug = 'south-korea') AS tmp), 'Q41216');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('North Gyeongsang', 'north-gyeongsang', (SELECT id FROM (SELECT id FROM region WHERE slug = 'south-korea') AS tmp), 'Q41171');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('South Gyeongsang', 'south-gyeongsang', (SELECT id FROM (SELECT id FROM region WHERE slug = 'south-korea') AS tmp), 'Q41093');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Jeju', 'jeju', (SELECT id FROM (SELECT id FROM region WHERE slug = 'south-korea') AS tmp), 'Q41217');

-- -----------------------------------------------------------------------------
-- 3.14 Estland — Landkreise (parent per Subselect)
-- -----------------------------------------------------------------------------

INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Harju', 'harju', (SELECT id FROM (SELECT id FROM region WHERE slug = 'estonia') AS tmp), 'Q180200');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Tartu', 'tartu-county', (SELECT id FROM (SELECT id FROM region WHERE slug = 'estonia') AS tmp), 'Q180231');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Ida-Viru', 'ida-viru', (SELECT id FROM (SELECT id FROM region WHERE slug = 'estonia') AS tmp), 'Q180205');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Pärnu', 'paernu', (SELECT id FROM (SELECT id FROM region WHERE slug = 'estonia') AS tmp), 'Q180223');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Lääne-Viru', 'laane-viru', (SELECT id FROM (SELECT id FROM region WHERE slug = 'estonia') AS tmp), 'Q180212');

-- -----------------------------------------------------------------------------
-- 3.15 Bulgarien — Planungsregionen (parent per Subselect)
-- -----------------------------------------------------------------------------

INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Yugozapaden', 'yugozapaden', (SELECT id FROM (SELECT id FROM region WHERE slug = 'bulgaria') AS tmp), 'Q6939');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Yuzhen tsentralen', 'yuzhen-tsentralen', (SELECT id FROM (SELECT id FROM region WHERE slug = 'bulgaria') AS tmp), 'Q6940');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Severoiztochen', 'severoiztochen', (SELECT id FROM (SELECT id FROM region WHERE slug = 'bulgaria') AS tmp), 'Q6937');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Yugoiztochen', 'yugoiztochen', (SELECT id FROM (SELECT id FROM region WHERE slug = 'bulgaria') AS tmp), 'Q6938');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Severozapaden', 'severozapaden', (SELECT id FROM (SELECT id FROM region WHERE slug = 'bulgaria') AS tmp), 'Q6935');
INSERT INTO region (name, slug, parent_id, wikidataEntityId) VALUES ('Severen tsentralen', 'severen-tsentralen', (SELECT id FROM (SELECT id FROM region WHERE slug = 'bulgaria') AS tmp), 'Q6936');


-- =============================================================================
-- 4. FEHLENDE WIKIDATA-IDs FÜR BESTEHENDE REGIONEN
-- =============================================================================

-- Afrika als Kontinent
UPDATE region SET wikidataEntityId = 'Q15' WHERE id = 337 AND wikidataEntityId IS NULL;

-- America als Kontinent
UPDATE region SET wikidataEntityId = 'Q828' WHERE id = 87 AND wikidataEntityId IS NULL;

-- Australia
UPDATE region SET wikidataEntityId = 'Q3960' WHERE id = 339 AND wikidataEntityId IS NULL;

-- Genf
UPDATE region SET wikidataEntityId = 'Q11917' WHERE id = 55 AND wikidataEntityId IS NULL;

-- St. Gallen
UPDATE region SET wikidataEntityId = 'Q12746' WHERE id = 242 AND wikidataEntityId IS NULL;
