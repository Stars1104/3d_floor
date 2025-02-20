/*
SQLyog Trial v13.1.8 (64 bit)
MySQL - 10.4.22-MariaDB : Database - floor
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`floor` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `floor`;

/*Table structure for table `objects` */

DROP TABLE IF EXISTS `objects`;

CREATE TABLE `objects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `two_obj` text NOT NULL,
  `three_obj` text NOT NULL,
  `three_mtl` text NOT NULL,
  `thumb_img` text NOT NULL,
  `size` varchar(20) NOT NULL,
  `descr` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=latin1;

/*Data for the table `objects` */

insert  into `objects`(`id`,`name`,`user_id`,`two_obj`,`three_obj`,`three_mtl`,`thumb_img`,`size`,`descr`) values 
(1,'white_simple_chair',1,'Chair with cover white map.png','Chir_whiteMap.obj','Chir_whiteMap.mtl','White_chir.jpeg','0.7,0.7,0.5','White Chair'),
(2,'white_round_table',1,'table_01 top.png','Table_01.obj','Table_3mwhite.mtl','table_01.jpg','2.4,2.4,0.5','Big Table'),
(3,'black_square_table',1,'2_4m_SQ_table_black_noMap.png','TableSq_2_4m_black_noMap.obj','TableSq_2_4m_black_noMap.mtl','TableSq_2_4m_Black_noMap.jpeg','2.4,2.4,1.5','2.4 Square Table'),
(4,'mass_square_table',1,'TableSq_2m_white_12Chairs.png','squire table with White map cover 2M with 12 chairs.obj','squire table with White map cover 2M with 12 chairs.mtl','squire table with White map cover 2M with 12 chairs.jpg','3,3,0.5','2m Sqaure Table'),
(5,'mass_round_table',1,'rounded table with white map cover 2M with 11 chairs.png','rounded table with white map cover 2M with 11 chairs.obj','rounded table with white map cover 2M with 12 chairs.mtl','rounded table with white map cover 2M with 11 chairs.jpeg','3,3,0.5','Rounded Table'),
(24,'Washington_tree',1,'WASHINGTONIA PALM LEAF_alpha.jpg','7m_WASHINGTONIA PALM_tree.obj','7m_WASHINGTONIA PALM_tree.mtl','WASHINGTONIA PALM_tree.jpeg','22,22',''),
(28,'mass_square_table',1,'TableSq_2m_white_12Chairs.png','squire table with White map cover 2M with 12 chairs.obj','squire table with White map cover 2M with 12 chairs.mtl','squire table with White map cover 2M with 12 chairs.jpg','0.9,0.9',''),
(29,'white_simple_chair',1,'Chair with cover white map.png','Chir_whiteMap.obj','Chir_whiteMap.mtl','White_chir.jpeg','0.3,3','');

/*Table structure for table `tbl_project` */

DROP TABLE IF EXISTS `tbl_project`;

CREATE TABLE `tbl_project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` text NOT NULL,
  `descr` text NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=latin1;

/*Data for the table `tbl_project` */

insert  into `tbl_project`(`id`,`user_id`,`title`,`descr`,`data`) values 
(33,1,'New 1','asdf','[{\"obj\":\"TableSq_2_4m_black_noMap.obj\",\"type\":\"image\",\"x\":430.5,\"y\":125.21875,\"width\":120,\"height\":120,\"depth\":\"1.5\",\"color\":\"\",\"angle\":0,\"points\":null,\"url\":\"img/objs/2d_shape/2_4m_SQ_table_black_noMap.png\",\"canvScale\":1},{\"obj\":\"Table_01.obj\",\"type\":\"image\",\"x\":192.5,\"y\":194.21875,\"width\":120,\"height\":120,\"depth\":\"0.5\",\"color\":\"\",\"angle\":0,\"points\":null,\"url\":\"img/objs/2d_shape/table_01 top.png\",\"canvScale\":1}]'),
(34,1,'Testing 2','This is scaling Test','[{\"obj\":\"rounded table with white map cover 2M with 11 chairs.obj\",\"type\":\"image\",\"x\":247.89999847412108,\"y\":283.33125,\"width\":150,\"height\":150,\"depth\":\"0.5\",\"color\":\"\",\"angle\":0,\"points\":null,\"url\":\"img/objs/2d_shape/rounded table with white map cover 2M with 11 chairs.png\",\"canvScale\":1.6666666666666667},{\"obj\":\"TableSq_2_4m_black_noMap.obj\",\"type\":\"image\",\"x\":383.49999847412107,\"y\":139.93125,\"width\":120,\"height\":120,\"depth\":\"1.5\",\"color\":\"\",\"angle\":0,\"points\":null,\"url\":\"img/objs/2d_shape/2_4m_SQ_table_black_noMap.png\",\"canvScale\":1.6666666666666667},{\"obj\":\"Table_01.obj\",\"type\":\"image\",\"x\":136.29999847412108,\"y\":115.33125,\"width\":120,\"height\":120,\"depth\":\"0.5\",\"color\":\"\",\"angle\":0,\"points\":null,\"url\":\"img/objs/2d_shape/table_01 top.png\",\"canvScale\":1.6666666666666667}]'),
(35,1,'small Scaling','','[{\"obj\":\"Table_01.obj\",\"type\":\"image\",\"x\":450.24998474121094,\"y\":259.078125,\"width\":120,\"height\":120,\"depth\":\"0.5\",\"color\":\"\",\"angle\":0,\"points\":null,\"url\":\"img/objs/2d_shape/table_01 top.png\",\"canvScale\":0.6666666666666666},{\"obj\":\"TableSq_2_4m_black_noMap.obj\",\"type\":\"image\",\"x\":138.24998474121094,\"y\":275.578125,\"width\":120,\"height\":120,\"depth\":\"1.5\",\"color\":\"\",\"angle\":0,\"points\":null,\"url\":\"img/objs/2d_shape/2_4m_SQ_table_black_noMap.png\",\"canvScale\":0.6666666666666666},{\"obj\":\"Table_01.obj\",\"type\":\"image\",\"x\":313.74998474121094,\"y\":89.578125,\"width\":120,\"height\":120,\"depth\":\"0.5\",\"color\":\"\",\"angle\":0,\"points\":null,\"url\":\"img/objs/2d_shape/table_01 top.png\",\"canvScale\":0.6666666666666666}]'),
(36,1,'Scaling test 2','','[{\"obj\":\"rounded table with white map cover 2M with 11 chairs.obj\",\"type\":\"image\",\"x\":231.16665649414062,\"y\":116.71875,\"width\":100,\"height\":100,\"depth\":\"0.5\",\"color\":\"\",\"angle\":0,\"points\":null,\"url\":\"img/objs/2d_shape/rounded table with white map cover 2M with 11 chairs.png\",\"canvScale\":0.6666666666666666},{\"obj\":\"TableSq_2_4m_black_noMap.obj\",\"type\":\"image\",\"x\":24.166656494140625,\"y\":127.71875,\"width\":80,\"height\":80,\"depth\":\"1.5\",\"color\":\"\",\"angle\":0,\"points\":null,\"url\":\"img/objs/2d_shape/2_4m_SQ_table_black_noMap.png\",\"canvScale\":0.6666666666666666},{\"obj\":\"Table_01.obj\",\"type\":\"image\",\"x\":137.16665649414062,\"y\":128.71875,\"width\":80,\"height\":80,\"depth\":\"0.5\",\"color\":\"\",\"angle\":0,\"points\":null,\"url\":\"img/objs/2d_shape/table_01 top.png\",\"canvScale\":0.6666666666666666}]'),
(37,1,'scaling 3','','[{\"obj\":\"rounded table with white map cover 2M with 11 chairs.obj\",\"type\":\"image\",\"x\":300.1666564941406,\"y\":104.71875,\"width\":100,\"height\":100,\"depth\":\"0.5\",\"color\":\"\",\"angle\":0,\"points\":null,\"url\":\"img/objs/2d_shape/rounded table with white map cover 2M with 11 chairs.png\",\"canvScale\":0.6666666666666666},{\"obj\":\"TableSq_2_4m_black_noMap.obj\",\"type\":\"image\",\"x\":189.16665649414062,\"y\":116.71875,\"width\":80,\"height\":80,\"depth\":\"1.5\",\"color\":\"\",\"angle\":0,\"points\":null,\"url\":\"img/objs/2d_shape/2_4m_SQ_table_black_noMap.png\",\"canvScale\":0.6666666666666666},{\"obj\":\"Table_01.obj\",\"type\":\"image\",\"x\":65.16665649414062,\"y\":113.71875,\"width\":80,\"height\":80,\"depth\":\"0.5\",\"color\":\"\",\"angle\":0,\"points\":null,\"url\":\"img/objs/2d_shape/table_01 top.png\",\"canvScale\":0.6666666666666666}]'),
(38,1,'scaling 5','','[{\"obj\":\"TableSq_2_4m_black_noMap.obj\",\"type\":\"image\",\"x\":489.24998474121094,\"y\":154.078125,\"width\":120,\"height\":120,\"depth\":\"1.5\",\"color\":\"\",\"angle\":0,\"points\":null,\"url\":\"img/objs/2d_shape/2_4m_SQ_table_black_noMap.png\",\"canvScale\":0.6666666666666666},{\"obj\":\"Table_01.obj\",\"type\":\"image\",\"x\":291.24998474121094,\"y\":158.578125,\"width\":120,\"height\":120,\"depth\":\"0.5\",\"color\":\"\",\"angle\":0,\"points\":null,\"url\":\"img/objs/2d_shape/table_01 top.png\",\"canvScale\":0.6666666666666666},{\"obj\":\"rounded table with white map cover 2M with 11 chairs.obj\",\"type\":\"image\",\"x\":87.24998474121094,\"y\":146.578125,\"width\":150,\"height\":150,\"depth\":\"0.5\",\"color\":\"\",\"angle\":0,\"points\":null,\"url\":\"img/objs/2d_shape/rounded table with white map cover 2M with 11 chairs.png\",\"canvScale\":0.6666666666666666}]'),
(39,1,'scaling6','','[{\"obj\":\"rounded table with white map cover 2M with 11 chairs.obj\",\"type\":\"image\",\"x\":151.83331298828125,\"y\":155.15625,\"width\":150,\"height\":150,\"depth\":\"0.5\",\"color\":\"\",\"angle\":0,\"points\":null,\"url\":\"img/objs/2d_shape/rounded table with white map cover 2M with 11 chairs.png\",\"canvScale\":0.3333333333333333},{\"obj\":\"TableSq_2_4m_black_noMap.obj\",\"type\":\"image\",\"x\":94.83331298828125,\"y\":170.15625,\"width\":120,\"height\":120,\"depth\":\"1.5\",\"color\":\"\",\"angle\":0,\"points\":null,\"url\":\"img/objs/2d_shape/2_4m_SQ_table_black_noMap.png\",\"canvScale\":0.3333333333333333},{\"obj\":\"Table_01.obj\",\"type\":\"image\",\"x\":33.83331298828125,\"y\":182.15625,\"width\":120,\"height\":120,\"depth\":\"0.5\",\"color\":\"\",\"angle\":0,\"points\":null,\"url\":\"img/objs/2d_shape/table_01 top.png\",\"canvScale\":0.3333333333333333}]'),
(40,1,'scaling 7','','[{\"obj\":\"TableSq_2_4m_black_noMap.obj\",\"type\":\"image\",\"x\":351.1666564941406,\"y\":133.71875,\"width\":120,\"height\":120,\"depth\":\"1.5\",\"color\":\"\",\"angle\":0,\"points\":null,\"url\":\"img/objs/2d_shape/2_4m_SQ_table_black_noMap.png\",\"canvScale\":0.6666666666666666},{\"obj\":\"Table_01.obj\",\"type\":\"image\",\"x\":226.16665649414062,\"y\":129.71875,\"width\":120,\"height\":120,\"depth\":\"0.5\",\"color\":\"\",\"angle\":0,\"points\":null,\"url\":\"img/objs/2d_shape/table_01 top.png\",\"canvScale\":0.6666666666666666},{\"obj\":\"rounded table with white map cover 2M with 11 chairs.obj\",\"type\":\"image\",\"x\":85.16665649414062,\"y\":115.71875,\"width\":150,\"height\":150,\"depth\":\"0.5\",\"color\":\"\",\"angle\":0,\"points\":null,\"url\":\"img/objs/2d_shape/rounded table with white map cover 2M with 11 chairs.png\",\"canvScale\":0.6666666666666666}]'),
(41,1,'scaling 8','','[{\"obj\":\"TableSq_2_4m_black_noMap.obj\",\"type\":\"image\",\"x\":908.3809465680804,\"y\":645.9642857142857,\"width\":120.00000000000004,\"height\":120.00000000000004,\"depth\":\"1.5\",\"color\":\"\",\"angle\":0,\"points\":null,\"url\":\"img/objs/2d_shape/2_4m_SQ_table_black_noMap.png\",\"canvScale\":2.6666666666666665},{\"obj\":\"Table_01.obj\",\"type\":\"image\",\"x\":896.9523751395088,\"y\":267.6785714285714,\"width\":120.00000000000004,\"height\":120.00000000000004,\"depth\":\"0.5\",\"color\":\"\",\"angle\":0,\"points\":null,\"url\":\"img/objs/2d_shape/table_01 top.png\",\"canvScale\":2.6666666666666665}]'),
(42,1,'aaaaaaaaaaaa123','aaaaaaaaaaaaaaaaaaaaa','[{\"obj\":\"Chir_whiteMap.obj\",\"type\":\"image\",\"x\":190,\"y\":285.71875,\"width\":35,\"height\":35,\"depth\":\"0.5\",\"color\":\"#fff\",\"angle\":0,\"points\":null,\"url\":\"img/objs/2d_shape/Chair with cover white map.png\",\"canvScale\":1},{\"obj\":null,\"type\":\"rect\",\"x\":-134.75,\"y\":116.4453125,\"width\":150,\"height\":100,\"color\":\"\",\"angle\":0,\"canvScale\":1},{\"obj\":\"rounded table with white map cover 2M with 11 chairs.obj\",\"type\":\"image\",\"x\":-52.75,\"y\":-216.9453125,\"width\":150,\"height\":150,\"depth\":\"0.0002\",\"color\":\"#fff\",\"angle\":0,\"points\":null,\"url\":\"img/objs/2d_shape/rounded table with white map cover 2M with 11 chairs.png\",\"canvScale\":1},{\"obj\":\"Table_01.obj\",\"type\":\"image\",\"x\":14.25,\"y\":-50.9453125,\"width\":120,\"height\":120,\"depth\":\"0.000004\",\"color\":\"#fff\",\"angle\":0,\"points\":null,\"url\":\"img/objs/2d_shape/table_01 top.png\",\"canvScale\":1},{\"obj\":\"TableSq_2_4m_black_noMap.obj\",\"type\":\"image\",\"x\":527,\"y\":145.71875,\"width\":120,\"height\":120,\"depth\":\"0.000011999999999999999\",\"color\":\"#fff\",\"angle\":0,\"points\":null,\"url\":\"img/objs/2d_shape/2_4m_SQ_table_black_noMap.png\",\"canvScale\":1},{\"obj\":\"squire table with White map cover 2M with 12 chairs.obj\",\"type\":\"image\",\"x\":466,\"y\":333.71875,\"width\":150,\"height\":150,\"depth\":\"0.01\",\"color\":\"#fff\",\"angle\":0,\"points\":null,\"url\":\"img/objs/2d_shape/TableSq_2m_white_12Chairs.png\",\"canvScale\":1}]'),
(43,1,'ggggggggwerwer123','','[{\"obj\":null,\"type\":\"triangle\",\"x\":228,\"y\":310.109375,\"width\":100,\"height\":100,\"color\":\"\",\"angle\":0,\"canvScale\":1},{\"obj\":\"TableSq_2_4m_black_noMap.obj\",\"type\":\"image\",\"x\":205,\"y\":122.71875,\"width\":120,\"height\":120,\"depth\":\"0.03\",\"color\":\"#fff\",\"angle\":0,\"points\":null,\"url\":\"img/objs/2d_shape/2_4m_SQ_table_black_noMap.png\",\"canvScale\":1},{\"obj\":\"Table_01.obj\",\"type\":\"image\",\"x\":356,\"y\":113.71875,\"width\":120,\"height\":120,\"depth\":\"0.01\",\"color\":\"#fff\",\"angle\":0,\"points\":null,\"url\":\"img/objs/2d_shape/table_01 top.png\",\"canvScale\":1},{\"obj\":null,\"type\":\"rect\",\"x\":339,\"y\":352.109375,\"width\":150,\"height\":100,\"color\":\"\",\"angle\":0,\"canvScale\":1}]'),
(44,1,'Newmod123123','asdfasdf','[{\"obj\":null,\"type\":\"triangle\",\"x\":68.93405151367188,\"y\":89.06945037841797,\"width\":100,\"height\":100,\"depth\":null,\"color\":\"\",\"angle\":0,\"canvScale\":1},{\"obj\":null,\"type\":\"rect\",\"x\":297.49658203125,\"y\":231.04515838623047,\"width\":150,\"height\":100,\"depth\":null,\"color\":\"\",\"angle\":0,\"canvScale\":1},{\"obj\":\"Table_01.obj\",\"type\":\"image\",\"x\":97.482666015625,\"y\":250.10418701171875,\"width\":120,\"height\":120,\"depth\":25,\"color\":\"#fff\",\"angle\":0,\"points\":null,\"url\":\"img/objs/2d_shape/table_01 top.png\",\"canvScale\":1},{\"obj\":\"TableSq_2_4m_black_noMap.obj\",\"type\":\"image\",\"x\":505.48614501953125,\"y\":186.11111450195312,\"width\":120,\"height\":120,\"depth\":75,\"color\":\"#fff\",\"angle\":0,\"points\":null,\"url\":\"img/objs/2d_shape/2_4m_SQ_table_black_noMap.png\",\"canvScale\":1},{\"obj\":\"TableSq_2_4m_black_noMap.obj\",\"type\":\"image\",\"x\":205,\"y\":122.71875,\"width\":120,\"height\":120,\"depth\":0.03,\"color\":\"#fff\",\"angle\":0,\"points\":null,\"url\":\"img/objs/2d_shape/2_4m_SQ_table_black_noMap.png\",\"canvScale\":1},{\"obj\":\"Table_01.obj\",\"type\":\"image\",\"x\":356,\"y\":113.71875,\"width\":120,\"height\":120,\"depth\":0.01,\"color\":\"#fff\",\"angle\":0,\"points\":null,\"url\":\"img/objs/2d_shape/table_01 top.png\",\"canvScale\":1},{\"obj\":null,\"type\":\"triangle\",\"x\":228,\"y\":310.109375,\"width\":100,\"height\":100,\"depth\":null,\"color\":\"\",\"angle\":0,\"canvScale\":1},{\"obj\":null,\"type\":\"rect\",\"x\":339,\"y\":352.109375,\"width\":150,\"height\":100,\"depth\":null,\"color\":\"\",\"angle\":0,\"canvScale\":1}]'),
(45,1,'1111111','','[{\"obj\":\"TableSq_2_4m_black_noMap.obj\",\"type\":\"image\",\"x\":302,\"y\":131.22000000000003,\"width\":120,\"height\":120,\"depth\":75,\"color\":\"none\",\"angle\":0,\"points\":null,\"url\":\"img/objs/2d_shape/2_4m_SQ_table_black_noMap.png\",\"canvScale\":1,\"gid\":1},{\"obj\":\"Table_01.obj\",\"type\":\"image\",\"x\":64,\"y\":179.22000000000003,\"width\":120,\"height\":120,\"depth\":25,\"color\":\"none\",\"angle\":0,\"points\":null,\"url\":\"img/objs/2d_shape/table_01 top.png\",\"canvScale\":1,\"gid\":1}]'),
(46,1,'123123123','','[{\"obj\":\"TableSq_2_4m_black_noMap.obj\",\"type\":\"image\",\"x\":438,\"y\":162.21875,\"width\":120,\"height\":120,\"depth\":75,\"color\":\"none\",\"angle\":0,\"points\":null,\"url\":\"img/objs/2d_shape/2_4m_SQ_table_black_noMap.png\",\"canvScale\":1},{\"obj\":\"Table_01.obj\",\"type\":\"image\",\"x\":265,\"y\":269.21875,\"width\":120,\"height\":120,\"depth\":25,\"color\":\"none\",\"angle\":0,\"points\":null,\"url\":\"img/objs/2d_shape/table_01 top.png\",\"canvScale\":1}]');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `UserId` varchar(255) NOT NULL,
  `Username` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `ConnStr` varchar(255) NOT NULL,
  PRIMARY KEY (`UserId`(12))
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `users` */

insert  into `users`(`UserId`,`Username`,`Password`,`ConnStr`) values 
('1','admin','revenger','kcs');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
