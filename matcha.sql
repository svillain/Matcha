/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MariaDB
 Source Server Version : 100135
 Source Host           : 127.0.0.1:3306
 Source Schema         : matcha

 Target Server Type    : MariaDB
 Target Server Version : 100135
 File Encoding         : 65001

 Date: 03/07/2019 17:26:40
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for blacklists
-- ----------------------------
DROP TABLE IF EXISTS `blacklists`;
CREATE TABLE `blacklists`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `user_id_target` int(11) NOT NULL,
  `fake` int(1) NULL DEFAULT NULL,
  `created_at` datetime(0) NULL DEFAULT NULL,
  `updated_at` datetime(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `user_id_target`(`user_id_target`) USING BTREE,
  CONSTRAINT `blacklists_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `blacklists_ibfk_2` FOREIGN KEY (`user_id_target`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for interests
-- ----------------------------
DROP TABLE IF EXISTS `interests`;
CREATE TABLE `interests`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 617 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of interests
-- ----------------------------
INSERT INTO `interests` VALUES (309, 'Aircraft Spotting');
INSERT INTO `interests` VALUES (310, 'Airbrushing');
INSERT INTO `interests` VALUES (311, 'Airsofting');
INSERT INTO `interests` VALUES (312, 'Acting');
INSERT INTO `interests` VALUES (313, 'Aeromodeling');
INSERT INTO `interests` VALUES (314, 'Amateur Astronomy');
INSERT INTO `interests` VALUES (315, 'Amateur Radio');
INSERT INTO `interests` VALUES (316, 'Animals/pets/dogs');
INSERT INTO `interests` VALUES (317, 'Archery');
INSERT INTO `interests` VALUES (318, 'Arts');
INSERT INTO `interests` VALUES (319, 'Aquarium (Freshwater & Saltwater)');
INSERT INTO `interests` VALUES (320, 'Astrology');
INSERT INTO `interests` VALUES (321, 'Astronomy');
INSERT INTO `interests` VALUES (322, 'Backgammon');
INSERT INTO `interests` VALUES (323, 'Badminton');
INSERT INTO `interests` VALUES (324, 'Baseball');
INSERT INTO `interests` VALUES (325, 'Base Jumping');
INSERT INTO `interests` VALUES (326, 'Basketball');
INSERT INTO `interests` VALUES (327, 'Beach/Sun tanning');
INSERT INTO `interests` VALUES (328, 'Beachcombing');
INSERT INTO `interests` VALUES (329, 'Beadwork');
INSERT INTO `interests` VALUES (330, 'Beatboxing');
INSERT INTO `interests` VALUES (331, 'Becoming A Child Advocate');
INSERT INTO `interests` VALUES (332, 'Bell Ringing');
INSERT INTO `interests` VALUES (333, 'Belly Dancing');
INSERT INTO `interests` VALUES (334, 'Bicycling');
INSERT INTO `interests` VALUES (335, 'Bicycle Polo');
INSERT INTO `interests` VALUES (336, 'Bird watching');
INSERT INTO `interests` VALUES (337, 'Birding');
INSERT INTO `interests` VALUES (338, 'BMX');
INSERT INTO `interests` VALUES (339, 'Blacksmithing');
INSERT INTO `interests` VALUES (340, 'Blogging');
INSERT INTO `interests` VALUES (341, 'BoardGames');
INSERT INTO `interests` VALUES (342, 'Boating');
INSERT INTO `interests` VALUES (343, 'Body Building');
INSERT INTO `interests` VALUES (344, 'Bonsai Tree');
INSERT INTO `interests` VALUES (345, 'Bookbinding');
INSERT INTO `interests` VALUES (346, 'Boomerangs');
INSERT INTO `interests` VALUES (347, 'Bowling');
INSERT INTO `interests` VALUES (348, 'Brewing Beer');
INSERT INTO `interests` VALUES (349, 'Bridge Building');
INSERT INTO `interests` VALUES (350, 'Bringing Food To The Disabled');
INSERT INTO `interests` VALUES (351, 'Building A House For Habitat For Humanity');
INSERT INTO `interests` VALUES (352, 'Building Dollhouses');
INSERT INTO `interests` VALUES (353, 'Butterfly Watching');
INSERT INTO `interests` VALUES (354, 'Button Collecting');
INSERT INTO `interests` VALUES (355, 'Cake Decorating');
INSERT INTO `interests` VALUES (356, 'Calligraphy');
INSERT INTO `interests` VALUES (357, 'Camping');
INSERT INTO `interests` VALUES (358, 'Candle Making');
INSERT INTO `interests` VALUES (359, 'Canoeing');
INSERT INTO `interests` VALUES (360, 'Cartooning');
INSERT INTO `interests` VALUES (361, 'Car Racing');
INSERT INTO `interests` VALUES (362, 'Casino Gambling');
INSERT INTO `interests` VALUES (363, 'Cave Diving');
INSERT INTO `interests` VALUES (364, 'Ceramics');
INSERT INTO `interests` VALUES (365, 'Cheerleading');
INSERT INTO `interests` VALUES (366, 'Chess');
INSERT INTO `interests` VALUES (367, 'Church/church activities');
INSERT INTO `interests` VALUES (368, 'Cigar Smoking');
INSERT INTO `interests` VALUES (369, 'Cloud Watching');
INSERT INTO `interests` VALUES (370, 'Coin Collecting');
INSERT INTO `interests` VALUES (371, 'Collecting');
INSERT INTO `interests` VALUES (372, 'Collecting Antiques');
INSERT INTO `interests` VALUES (373, 'Collecting Artwork');
INSERT INTO `interests` VALUES (374, 'Collecting Hats');
INSERT INTO `interests` VALUES (375, 'Collecting Music Albums');
INSERT INTO `interests` VALUES (376, 'Collecting RPM Records');
INSERT INTO `interests` VALUES (377, 'Collecting Sports Cards (Baseball, Football, Basketball, Hockey)');
INSERT INTO `interests` VALUES (378, 'Collecting Swords');
INSERT INTO `interests` VALUES (379, 'Coloring');
INSERT INTO `interests` VALUES (380, 'Compose Music');
INSERT INTO `interests` VALUES (381, 'Computer activities');
INSERT INTO `interests` VALUES (382, 'Conworlding');
INSERT INTO `interests` VALUES (383, 'Cooking');
INSERT INTO `interests` VALUES (384, 'Cosplay');
INSERT INTO `interests` VALUES (385, 'Crafts');
INSERT INTO `interests` VALUES (386, 'Crafts (unspecified)');
INSERT INTO `interests` VALUES (387, 'Croche');
INSERT INTO `interests` VALUES (388, 'Crocheting');
INSERT INTO `interests` VALUES (389, 'Cross-Stitch');
INSERT INTO `interests` VALUES (390, 'Crossword Puzzles');
INSERT INTO `interests` VALUES (391, 'Dancing');
INSERT INTO `interests` VALUES (392, 'Darts');
INSERT INTO `interests` VALUES (393, 'Diecast Collectibles');
INSERT INTO `interests` VALUES (394, 'Digital Photography');
INSERT INTO `interests` VALUES (395, 'Dodgeball');
INSERT INTO `interests` VALUES (396, 'Dolls');
INSERT INTO `interests` VALUES (397, 'Dominoes');
INSERT INTO `interests` VALUES (398, 'Drawing');
INSERT INTO `interests` VALUES (399, 'Dumpster Diving');
INSERT INTO `interests` VALUES (400, 'Eating ou');
INSERT INTO `interests` VALUES (401, 'Educational Courses');
INSERT INTO `interests` VALUES (402, 'Electronics');
INSERT INTO `interests` VALUES (403, 'Embroidery');
INSERT INTO `interests` VALUES (404, 'Entertaining');
INSERT INTO `interests` VALUES (405, 'Exercise (aerobics, weights)');
INSERT INTO `interests` VALUES (406, 'Falconry');
INSERT INTO `interests` VALUES (407, 'Fast cars');
INSERT INTO `interests` VALUES (408, 'Felting');
INSERT INTO `interests` VALUES (409, 'Fencing');
INSERT INTO `interests` VALUES (410, 'Fire Poi');
INSERT INTO `interests` VALUES (411, 'Fishing');
INSERT INTO `interests` VALUES (412, 'Floorball');
INSERT INTO `interests` VALUES (413, 'Floral Arrangements');
INSERT INTO `interests` VALUES (414, 'Fly Tying');
INSERT INTO `interests` VALUES (415, 'Football');
INSERT INTO `interests` VALUES (416, 'Four Wheeling');
INSERT INTO `interests` VALUES (417, 'Freshwater Aquariums');
INSERT INTO `interests` VALUES (418, 'Frisbee Golf – Frolf');
INSERT INTO `interests` VALUES (419, 'Games');
INSERT INTO `interests` VALUES (420, 'Gardening');
INSERT INTO `interests` VALUES (421, 'Garage Saleing');
INSERT INTO `interests` VALUES (422, 'Genealogy');
INSERT INTO `interests` VALUES (423, 'Geocaching');
INSERT INTO `interests` VALUES (424, 'Ghost Hunting');
INSERT INTO `interests` VALUES (425, 'Glowsticking');
INSERT INTO `interests` VALUES (426, 'Gnoming');
INSERT INTO `interests` VALUES (427, 'Going to movies');
INSERT INTO `interests` VALUES (428, 'Golf');
INSERT INTO `interests` VALUES (429, 'Go Kart Racing');
INSERT INTO `interests` VALUES (430, 'Grip Strength');
INSERT INTO `interests` VALUES (431, 'Guitar');
INSERT INTO `interests` VALUES (432, 'Gunsmithing');
INSERT INTO `interests` VALUES (433, 'Gun Collecting');
INSERT INTO `interests` VALUES (434, 'Gymnastics');
INSERT INTO `interests` VALUES (435, 'Gyotaku');
INSERT INTO `interests` VALUES (436, 'Handwriting Analysis');
INSERT INTO `interests` VALUES (437, 'Hang gliding');
INSERT INTO `interests` VALUES (438, 'Herping');
INSERT INTO `interests` VALUES (439, 'Hiking');
INSERT INTO `interests` VALUES (440, 'Home Brewing');
INSERT INTO `interests` VALUES (441, 'Home Repair');
INSERT INTO `interests` VALUES (442, 'Home Theater');
INSERT INTO `interests` VALUES (443, 'Horse riding');
INSERT INTO `interests` VALUES (444, 'Hot air ballooning');
INSERT INTO `interests` VALUES (445, 'Hula Hooping');
INSERT INTO `interests` VALUES (446, 'Hunting');
INSERT INTO `interests` VALUES (447, 'Iceskating');
INSERT INTO `interests` VALUES (448, 'Illusion');
INSERT INTO `interests` VALUES (449, 'Impersonations');
INSERT INTO `interests` VALUES (450, 'Interne');
INSERT INTO `interests` VALUES (451, 'Inventing');
INSERT INTO `interests` VALUES (452, 'Jet Engines');
INSERT INTO `interests` VALUES (453, 'Jewelry Making');
INSERT INTO `interests` VALUES (454, 'Jigsaw Puzzles');
INSERT INTO `interests` VALUES (455, 'Juggling');
INSERT INTO `interests` VALUES (456, 'Keep A Journal');
INSERT INTO `interests` VALUES (457, 'Jump Roping');
INSERT INTO `interests` VALUES (458, 'Kayaking');
INSERT INTO `interests` VALUES (459, 'Kitchen Chemistry');
INSERT INTO `interests` VALUES (460, 'Kites');
INSERT INTO `interests` VALUES (461, 'Kite Boarding');
INSERT INTO `interests` VALUES (462, 'Knitting');
INSERT INTO `interests` VALUES (463, 'Knotting');
INSERT INTO `interests` VALUES (464, 'Lasers');
INSERT INTO `interests` VALUES (465, 'Lawn Darts');
INSERT INTO `interests` VALUES (466, 'Learn to Play Poker');
INSERT INTO `interests` VALUES (467, 'Learning A Foreign Language');
INSERT INTO `interests` VALUES (468, 'Learning An Instrumen');
INSERT INTO `interests` VALUES (469, 'Learning To Pilot A Plane');
INSERT INTO `interests` VALUES (470, 'Leathercrafting');
INSERT INTO `interests` VALUES (471, 'Legos');
INSERT INTO `interests` VALUES (472, 'Letterboxing');
INSERT INTO `interests` VALUES (473, 'Listening to music');
INSERT INTO `interests` VALUES (474, 'Lockspor');
INSERT INTO `interests` VALUES (475, 'Lacrosse');
INSERT INTO `interests` VALUES (476, 'Macramé');
INSERT INTO `interests` VALUES (477, 'Magic');
INSERT INTO `interests` VALUES (478, 'Making Model Cars');
INSERT INTO `interests` VALUES (479, 'Marksmanship');
INSERT INTO `interests` VALUES (480, 'Martial Arts');
INSERT INTO `interests` VALUES (481, 'Matchstick Modeling');
INSERT INTO `interests` VALUES (482, 'Meditation');
INSERT INTO `interests` VALUES (483, 'Microscopy');
INSERT INTO `interests` VALUES (484, 'Metal Detecting');
INSERT INTO `interests` VALUES (485, 'Model Railroading');
INSERT INTO `interests` VALUES (486, 'Model Rockets');
INSERT INTO `interests` VALUES (487, 'Modeling Ships');
INSERT INTO `interests` VALUES (488, 'Models');
INSERT INTO `interests` VALUES (489, 'Motorcycles');
INSERT INTO `interests` VALUES (490, 'Mountain Biking');
INSERT INTO `interests` VALUES (491, 'Mountain Climbing');
INSERT INTO `interests` VALUES (492, 'Musical Instruments');
INSERT INTO `interests` VALUES (493, 'Nail Ar');
INSERT INTO `interests` VALUES (494, 'Needlepoin');
INSERT INTO `interests` VALUES (495, 'Owning An Antique Car');
INSERT INTO `interests` VALUES (496, 'Origami');
INSERT INTO `interests` VALUES (497, 'Painting');
INSERT INTO `interests` VALUES (498, 'Paintball');
INSERT INTO `interests` VALUES (499, 'Papermaking');
INSERT INTO `interests` VALUES (500, 'Papermache');
INSERT INTO `interests` VALUES (501, 'Parachuting');
INSERT INTO `interests` VALUES (502, 'Paragliding or Power Paragliding');
INSERT INTO `interests` VALUES (503, 'Parkour');
INSERT INTO `interests` VALUES (504, 'People Watching');
INSERT INTO `interests` VALUES (505, 'Photography');
INSERT INTO `interests` VALUES (506, 'Piano');
INSERT INTO `interests` VALUES (507, 'Pinochle');
INSERT INTO `interests` VALUES (508, 'Pipe Smoking');
INSERT INTO `interests` VALUES (509, 'Planking');
INSERT INTO `interests` VALUES (510, 'Playing music');
INSERT INTO `interests` VALUES (511, 'Playing team sports');
INSERT INTO `interests` VALUES (512, 'Pole Dancing');
INSERT INTO `interests` VALUES (513, 'Pottery');
INSERT INTO `interests` VALUES (514, 'Powerboking');
INSERT INTO `interests` VALUES (515, 'Protesting');
INSERT INTO `interests` VALUES (516, 'Puppetry');
INSERT INTO `interests` VALUES (517, 'Pyrotechnics');
INSERT INTO `interests` VALUES (518, 'Quilting');
INSERT INTO `interests` VALUES (519, 'Racing Pigeons');
INSERT INTO `interests` VALUES (520, 'Rafting');
INSERT INTO `interests` VALUES (521, 'Railfans');
INSERT INTO `interests` VALUES (522, 'Rapping');
INSERT INTO `interests` VALUES (523, 'R/C Boats');
INSERT INTO `interests` VALUES (524, 'R/C Cars');
INSERT INTO `interests` VALUES (525, 'R/C Helicopters');
INSERT INTO `interests` VALUES (526, 'R/C Planes');
INSERT INTO `interests` VALUES (527, 'Reading');
INSERT INTO `interests` VALUES (528, 'Reading To The Elderly');
INSERT INTO `interests` VALUES (529, 'Relaxing');
INSERT INTO `interests` VALUES (530, 'Renaissance Faire');
INSERT INTO `interests` VALUES (531, 'Renting movies');
INSERT INTO `interests` VALUES (532, 'Rescuing Abused Or Abandoned Animals');
INSERT INTO `interests` VALUES (533, 'Robotics');
INSERT INTO `interests` VALUES (534, 'Rock Balancing');
INSERT INTO `interests` VALUES (535, 'Rock Collecting');
INSERT INTO `interests` VALUES (536, 'Rockets');
INSERT INTO `interests` VALUES (537, 'Rocking AIDS Babies');
INSERT INTO `interests` VALUES (538, 'Roleplaying');
INSERT INTO `interests` VALUES (539, 'Running');
INSERT INTO `interests` VALUES (540, 'Saltwater Aquariums');
INSERT INTO `interests` VALUES (541, 'Sand Castles');
INSERT INTO `interests` VALUES (542, 'Scrapbooking');
INSERT INTO `interests` VALUES (543, 'Scuba Diving');
INSERT INTO `interests` VALUES (544, 'Self Defense');
INSERT INTO `interests` VALUES (545, 'Sewing');
INSERT INTO `interests` VALUES (546, 'Shark Fishing');
INSERT INTO `interests` VALUES (547, 'Skeet Shooting');
INSERT INTO `interests` VALUES (548, 'Skiing');
INSERT INTO `interests` VALUES (549, 'Shopping');
INSERT INTO `interests` VALUES (550, 'Singing In Choir');
INSERT INTO `interests` VALUES (551, 'Skateboarding');
INSERT INTO `interests` VALUES (552, 'Sketching');
INSERT INTO `interests` VALUES (553, 'Sky Diving');
INSERT INTO `interests` VALUES (554, 'Slack Lining');
INSERT INTO `interests` VALUES (555, 'Sleeping');
INSERT INTO `interests` VALUES (556, 'Slingshots');
INSERT INTO `interests` VALUES (557, 'Slot Car Racing');
INSERT INTO `interests` VALUES (558, 'Snorkeling');
INSERT INTO `interests` VALUES (559, 'Snowboarding');
INSERT INTO `interests` VALUES (560, 'Soap Making');
INSERT INTO `interests` VALUES (561, 'Soccer');
INSERT INTO `interests` VALUES (562, 'Socializing with friends/neighbors');
INSERT INTO `interests` VALUES (563, 'Speed Cubing (rubix cube)');
INSERT INTO `interests` VALUES (564, 'Spelunkering');
INSERT INTO `interests` VALUES (565, 'Spending time with family/kids');
INSERT INTO `interests` VALUES (566, 'Stamp Collecting');
INSERT INTO `interests` VALUES (567, 'Storm Chasing');
INSERT INTO `interests` VALUES (568, 'Storytelling');
INSERT INTO `interests` VALUES (569, 'String Figures');
INSERT INTO `interests` VALUES (570, 'Surfing');
INSERT INTO `interests` VALUES (571, 'Surf Fishing');
INSERT INTO `interests` VALUES (572, 'Survival');
INSERT INTO `interests` VALUES (573, 'Swimming');
INSERT INTO `interests` VALUES (574, 'Tatting');
INSERT INTO `interests` VALUES (575, 'Taxidermy');
INSERT INTO `interests` VALUES (576, 'Tea Tasting');
INSERT INTO `interests` VALUES (577, 'Tennis');
INSERT INTO `interests` VALUES (578, 'Tesla Coils');
INSERT INTO `interests` VALUES (579, 'Tetris');
INSERT INTO `interests` VALUES (580, 'Texting');
INSERT INTO `interests` VALUES (581, 'Textiles');
INSERT INTO `interests` VALUES (582, 'Tombstone Rubbing');
INSERT INTO `interests` VALUES (583, 'Tool Collecting');
INSERT INTO `interests` VALUES (584, 'Toy Collecting');
INSERT INTO `interests` VALUES (585, 'Train Collecting');
INSERT INTO `interests` VALUES (586, 'Train Spotting');
INSERT INTO `interests` VALUES (587, 'Traveling');
INSERT INTO `interests` VALUES (588, 'Treasure Hunting');
INSERT INTO `interests` VALUES (589, 'Trekkie');
INSERT INTO `interests` VALUES (590, 'Tutoring Children');
INSERT INTO `interests` VALUES (591, 'TV watching');
INSERT INTO `interests` VALUES (592, 'Ultimate Frisbee');
INSERT INTO `interests` VALUES (593, 'Urban Exploration');
INSERT INTO `interests` VALUES (594, 'Video Games');
INSERT INTO `interests` VALUES (595, 'Violin');
INSERT INTO `interests` VALUES (596, 'Volunteer');
INSERT INTO `interests` VALUES (597, 'Walking');
INSERT INTO `interests` VALUES (598, 'Warhammer');
INSERT INTO `interests` VALUES (599, 'Watching sporting events');
INSERT INTO `interests` VALUES (600, 'Weather Watcher');
INSERT INTO `interests` VALUES (601, 'Weightlifting');
INSERT INTO `interests` VALUES (602, 'Windsurfing');
INSERT INTO `interests` VALUES (603, 'Wine Making');
INSERT INTO `interests` VALUES (604, 'Wingsuit Flying');
INSERT INTO `interests` VALUES (605, 'Woodworking');
INSERT INTO `interests` VALUES (606, 'Working In A Food Pantry');
INSERT INTO `interests` VALUES (607, 'Working on cars');
INSERT INTO `interests` VALUES (608, 'World Record Breaking');
INSERT INTO `interests` VALUES (609, 'Wrestling');
INSERT INTO `interests` VALUES (610, 'Writing');
INSERT INTO `interests` VALUES (611, 'Writing Music');
INSERT INTO `interests` VALUES (612, 'Writing Songs');
INSERT INTO `interests` VALUES (613, 'Yoga');
INSERT INTO `interests` VALUES (614, 'YoYo');
INSERT INTO `interests` VALUES (615, 'Ziplining');
INSERT INTO `interests` VALUES (616, 'Zumba');

-- ----------------------------
-- Table structure for likes
-- ----------------------------
DROP TABLE IF EXISTS `likes`;
CREATE TABLE `likes`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `user_id_target` int(11) NOT NULL,
  `created_at` datetime(0) NULL DEFAULT NULL,
  `updated_at` datetime(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `user_id_target`(`user_id_target`) USING BTREE,
  CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`user_id_target`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for messages
-- ----------------------------
DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `user_id_target` int(11) NOT NULL,
  `content` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `seen` int(1) NULL DEFAULT 0,
  `created_at` datetime(0) NULL DEFAULT NULL,
  `updated_at` datetime(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `user_id_target`(`user_id_target`) USING BTREE,
  CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`user_id_target`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for notifications
-- ----------------------------
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NULL DEFAULT NULL,
  `user_id_action` int(11) NULL DEFAULT NULL,
  `type` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `message` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `updated_at` datetime(0) NULL DEFAULT NULL,
  `created_at` datetime(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `user_id_action`(`user_id_action`) USING BTREE,
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`user_id_action`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for orientations
-- ----------------------------
DROP TABLE IF EXISTS `orientations`;
CREATE TABLE `orientations`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of orientations
-- ----------------------------
INSERT INTO `orientations` VALUES (1, 'Bisexual');
INSERT INTO `orientations` VALUES (2, 'Heterosexual');
INSERT INTO `orientations` VALUES (3, 'Homosexual');

-- ----------------------------
-- Table structure for photos
-- ----------------------------
DROP TABLE IF EXISTS `photos`;
CREATE TABLE `photos`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NULL DEFAULT NULL,
  `fileName` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `profil` int(1) NULL DEFAULT NULL,
  `created_at` datetime(0) NULL DEFAULT NULL,
  `updated_at` datetime(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  CONSTRAINT `photos_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for sexes
-- ----------------------------
DROP TABLE IF EXISTS `sexes`;
CREATE TABLE `sexes`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of sexes
-- ----------------------------
INSERT INTO `sexes` VALUES (1, 'Man');
INSERT INTO `sexes` VALUES (2, 'Woman');

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `email` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `password` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `first_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `last_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `biography` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `sexe_id` int(11) NULL DEFAULT NULL,
  `orientation_id` int(11) NULL DEFAULT NULL,
  `birthdate` datetime(0) NULL DEFAULT NULL,
  `reset` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `confirmation` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `longitude` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `latitude` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `ip` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `online` int(1) NULL DEFAULT NULL,
  `disconnected_at` datetime(0) NULL DEFAULT NULL,
  `created_at` datetime(0) NULL DEFAULT NULL,
  `updated_at` datetime(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `sexe_id`(`sexe_id`) USING BTREE,
  INDEX `orientation_id`(`orientation_id`) USING BTREE,
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`sexe_id`) REFERENCES `sexes` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `users_ibfk_2` FOREIGN KEY (`orientation_id`) REFERENCES `orientations` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for users_interests
-- ----------------------------
DROP TABLE IF EXISTS `users_interests`;
CREATE TABLE `users_interests`  (
  `user_id` int(11) NOT NULL,
  `interest_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`, `interest_id`) USING BTREE,
  INDEX `interest_id`(`interest_id`) USING BTREE,
  CONSTRAINT `users_interests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `users_interests_ibfk_2` FOREIGN KEY (`interest_id`) REFERENCES `interests` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for visits
-- ----------------------------
DROP TABLE IF EXISTS `visits`;
CREATE TABLE `visits`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT ' ',
  `user_id` int(11) NULL DEFAULT NULL,
  `user_id_target` int(11) NULL DEFAULT NULL,
  `updated_at` datetime(0) NULL DEFAULT NULL,
  `created_at` datetime(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `user_id_target`(`user_id_target`) USING BTREE,
  CONSTRAINT `visits_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `visits_ibfk_2` FOREIGN KEY (`user_id_target`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

SET FOREIGN_KEY_CHECKS = 1;
