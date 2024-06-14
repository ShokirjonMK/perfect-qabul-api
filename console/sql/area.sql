/*
 Navicat Premium Data Transfer

 Source Server         : digital 62205 oxiri shakl
 Source Server Type    : MySQL
 Source Server Version : 80032
 Source Host           : localhost:3306
 Source Schema         : digital_a1

 Target Server Type    : MySQL
 Target Server Version : 80032
 File Encoding         : 65001

 Date: 07/05/2023 16:24:20
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for area
-- ----------------------------
DROP TABLE IF EXISTS `area`;
CREATE TABLE `area`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `region_id` int(0) NULL DEFAULT NULL,
  `type` tinyint(1) NULL DEFAULT 0,
  `postcode` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `lat` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `long` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `sort` int(0) NULL DEFAULT 0,
  `status` tinyint(1) NULL DEFAULT 0,
  `created_on` timestamp(0) NULL DEFAULT NULL,
  `created_by` int(0) NOT NULL DEFAULT 0,
  `updated_on` timestamp(0) NULL DEFAULT NULL,
  `updated_by` int(0) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx-area-region_id`(`region_id`) USING BTREE,
  CONSTRAINT `fk-area-region_id` FOREIGN KEY (`region_id`) REFERENCES `region` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 784 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of area
-- ----------------------------
INSERT INTO `area` VALUES (1, 'Mo\'ynoq tumani', 1, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (2, 'Kegeyli tumani', 1, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (3, 'Ellikqala tumani', 1, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (4, 'Chimboy tumani', 1, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (5, 'Beruniy tumani', 1, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (6, 'Amudaryo tumani', 1, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (7, 'Nukus tumani', 1, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (8, 'Qonliko\'l tumani', 1, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (9, 'Qorauzaq tumani', 1, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (10, 'Qung\'irot tumani', 1, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (11, 'Shumanay tumani', 1, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (12, 'Taxtako\'pir tumani', 1, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (13, 'To\'rtko\'l tumani', 1, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (14, 'Xo\'jayli tumani', 1, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (15, 'Bo\'zatov tumani', 1, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (16, 'Andijon tumani', 2, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (17, 'Asaka tumani', 2, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (18, 'Baliqchi tumani', 2, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (19, 'Bo\'z tumani', 2, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (20, 'Buloqboshi tumani', 2, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (21, 'Izboskan tumani', 2, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (22, 'Jalolquduq tumani', 2, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (23, 'Marhamat tumani', 2, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (24, 'Oltinko\'l tumani', 2, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (25, 'Paxtaobod tumani', 2, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (26, 'Qo\'rg\'ontepa tumani', 2, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (27, 'Shahrixon tumani', 2, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (28, 'Ulug\'nor tumani', 2, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (29, 'Xo\'jaobod tumani', 2, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (30, 'Xonobod shahri', 2, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (31, 'Andijon shahri', 2, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (32, 'Chortoq tumani', 3, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (33, 'Chust tumani', 3, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (34, 'Kosonsoy tumani', 3, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (35, 'Mingbuloq tumani', 3, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (36, 'Namangan tumani', 3, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (37, 'Norin tumani', 3, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (38, 'Pop tumani', 3, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (39, 'To\'raqo\'rg\'on tumani', 3, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (40, 'Uchqo\'rg\'on tumani', 3, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (41, 'Uychi tumani', 3, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (42, 'Yangiqo\'rg\'on tumani', 3, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (43, 'Namangan shahri', 3, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (44, 'Beshariq tumani', 4, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (45, 'Bog\'dod tumani', 4, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (46, 'Buvayda tumani', 4, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (47, 'Dang\'ara tumani', 4, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (48, 'Farg\'ona tumani', 4, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (49, 'Furqat tumani', 4, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (50, 'Farg\'ona shahri', 4, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (51, 'Oltiariq tumani', 4, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (52, 'Qo\'shtepa tumani', 4, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (53, 'O\'zbekiston tumani', 4, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (54, 'Quva tumani', 4, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (55, 'Rishton tumani', 4, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (56, 'So\'x tumani', 4, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (57, 'Toshloq tumani', 4, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (58, 'Uchko\'prik tumani', 4, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (59, 'Yozyovon tumani', 4, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (60, 'Marg\'ilon shahri', 4, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (61, 'Quvasov shahri', 4, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (62, 'Qo\'qon shahri', 4, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (64, 'Buxoro tumani', 5, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (65, 'G\'ijduvon tumani', 5, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (66, 'Jondor tumani', 5, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (67, 'Kogon tumani', 5, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (68, 'Olot tumani', 5, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (69, 'Peshko\' tumani', 5, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (70, 'Qorako\'l tumani', 5, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (71, 'Qorovulbozor tumani', 5, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (72, 'Romitan tumani', 5, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (73, 'Shofirkon tumani', 5, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (74, 'Vobkent tumani', 5, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (75, 'Buxoro shahar', 5, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (76, 'Bog\'ot tumani', 6, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (77, 'Gurlan tumani', 6, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (78, 'Tuproqqal`a tumani', 6, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (79, 'Qo\'shko\'pir tumani', 6, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (80, 'Shovot tumani', 6, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (81, 'Urganch tumani', 6, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (82, 'Xazorasp tumani', 6, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (83, 'Xiva tumani', 6, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (84, 'Xonqa tumani', 6, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (85, 'Yangiariq tumani', 6, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (86, 'Yangibozor tumani', 6, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (87, 'Urgench shahar', 6, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (88, 'Angor tumani', 7, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (89, 'Bandixon tumani', 7, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (90, 'Boysun tumani', 7, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (91, 'Denov tumani', 7, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (92, 'Jarqo\'rg\'on tumani', 7, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (93, 'Muzrobot tumani', 7, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (94, 'Oltinsoy tumani', 7, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (95, 'Qiziriq tumani', 7, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (96, 'Qumqo\'rg\'on tumani', 7, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (97, 'Sariosiyo tumani', 7, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (98, 'Sherobod tumani', 7, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (99, 'Sho\'rchi tumani', 7, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (100, 'Termiz tumani', 7, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (101, 'Uzun tumani', 7, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (102, 'Chiroqchi tumani', 8, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (103, 'Dehqonobod tumani', 8, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (104, 'G\'uzor tumani', 8, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (105, 'Kasbi tumani', 8, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (106, 'Kitob tumani', 8, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (107, 'Koson tumani', 8, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (108, 'Mirishkor tumani', 8, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (109, 'Mubarak tumani', 8, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (110, 'Nishon tumani', 8, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (111, 'Qamashi tumani', 8, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (112, 'Qarshi tumani', 8, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (113, 'Shahrisabz tumani', 8, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (114, 'Yakkabog\' tumani', 8, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (115, 'Qarshi shahri', 8, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (116, 'Arnasoy tumani', 9, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (117, 'Baxmal tumani', 9, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (118, 'Do\'stlik tumani', 9, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (119, 'Forish tumani', 9, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (120, 'G\'allaorol tumani', 9, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (121, 'Sharof Rashidov', 9, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (122, 'Mirzacho\'l tumani', 9, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (123, 'Paxtakor tumani', 9, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (124, 'Yangiobod tumani', 9, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (125, 'Zafarobod tumani', 9, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (126, 'Zarbdor tumani', 9, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (127, 'Zomin tumani', 9, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (128, 'Jizzax shahri', 9, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (129, 'Karmana tumani', 10, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (130, 'Konimex tumani', 10, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (131, 'Navbahor tumani', 10, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (132, 'Nurota tumani', 10, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (133, 'Qiziltepa tumani', 10, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (134, 'Tomdi tumani', 10, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (135, 'Uchquduq tumani', 10, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (136, 'Xatirchi tumani', 10, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (137, 'Navoiy shahri', 10, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (138, 'Zarafshon shaxar', 10, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (139, 'Bulung\'ur tumani', 11, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (140, 'Ishtixon tumani', 11, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (141, 'Jomboy tumani', 11, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (142, 'Kattaqo\'rg\'on tumani', 11, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (143, 'Narpay tumani', 11, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (144, 'Nurobod tumani', 11, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (145, 'Oqdaryo tumani', 11, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (146, 'Pastdarg\'om tumani', 11, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (147, 'Paxtachi tumani', 11, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (148, 'Poyariq tumani', 11, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (149, 'Qo\'shrabot tumani', 11, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (150, 'Samarqand tumani', 11, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (151, 'Toyloq tumani', 11, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (152, 'Urgut tumani', 11, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (153, 'Samarqand shahar', 11, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (154, 'Boyovut tumani', 12, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (155, 'Guliston tumani', 12, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (156, 'Mirzaobod tumani', 12, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (157, 'Oqoltin tumani', 12, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (158, 'Sardoba tumani', 12, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (159, 'Sayxunobod tumani', 12, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (160, 'Sirdaryo tumani', 12, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (161, 'Xovos tumani', 12, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (162, 'Guliston shahri', 12, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (163, 'Yangiyer shahri', 12, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (164, 'Bekobod tumani', 13, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (165, 'Bo\'ka tumani', 13, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (166, 'Bo\'stonliq tumani', 13, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (167, 'Chinoz tumani', 13, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (168, 'Ohangaron tumani', 13, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (169, 'Oqqo\'rg\'on tumani', 13, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (170, 'Parkent tumani', 13, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (171, 'Piskent tumani', 13, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (172, 'Qibray tumani', 13, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (173, 'Quyi chirchiq tumani', 13, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (174, 'Yangiyo\'l tumani', 13, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (175, 'Yuqori chirchiq tumani', 13, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (176, 'Zangiota tumani', 13, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (177, 'Angren shahri', 13, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (178, 'Chirchiq shahri', 13, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (179, 'Olmaliq shahri', 13, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (181, 'Toshkent tumani', 13, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (182, 'O\'rtachirchiq tumani', 13, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (183, 'Bekobod shahri', 13, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (184, 'Bektemir tumani', 14, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (185, 'Bektemir tumani', 14, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (186, 'Chilonzor tumani', 14, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (187, 'Mirobod tumani', 14, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (188, 'Mirzo Ulug\'bek tumani', 14, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (189, 'Olmazor tumani', 14, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (190, 'Shayxontohur tumani', 14, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (191, 'Sirg\'ali tumani', 14, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (192, 'Uchtepa tumani', 14, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (193, 'Yakkasaroy tumani', 14, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (194, 'Yunusobod tumani', 14, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (196, 'Nukus shahri', 1, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (197, 'Taxiatosh tumani', 1, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (198, 'Asaka shahri', 2, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (199, 'Qorasuv shahri', 2, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (200, 'Kogon shahri', 5, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (201, 'Xiva shahri', 6, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (202, 'Termiz shahri', 7, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (203, 'Kattaqo\'rg\'on shahri', 11, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (204, 'Shirin tumani', 12, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (778, 'Yangiyo\'l shahar', 13, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (779, 'Ohangaron shahar', 13, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (780, 'Nurafshon shahri', 13, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (781, 'Yangihayot tumani', 14, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (782, 'Yashnabod tumani', 14, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);
INSERT INTO `area` VALUES (783, 'Boshqa', 15, 0, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, 0);

SET FOREIGN_KEY_CHECKS = 1;
