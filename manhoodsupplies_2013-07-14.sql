# ************************************************************
# Sequel Pro SQL dump
# Version 4096
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 127.0.0.1 (MySQL 5.6.12)
# Database: manhoodsupplies
# Generation Time: 2013-07-14 08:51:30 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `data`;

CREATE TABLE `data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` text NOT NULL,
  `value` text,
  `thing_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `data` WRITE;
/*!40000 ALTER TABLE `data` DISABLE KEYS */;

INSERT INTO `data` (`id`, `key`, `value`, `thing_id`)
VALUES
	(559,'body_html','<p>The perfect way to disguise yourself without anyone knowing</p>',24),
	(560,'created_at','2013-07-14T00:38:18-06:00',24),
	(561,'handle','horse-mask',24),
	(562,'id','147293252',24),
	(563,'product_type','Horse Mask',24),
	(564,'published_at','2013-07-14T00:38:18-06:00',24),
	(565,'published_scope','global',24),
	(566,'template_suffix',NULL,24),
	(567,'title','Horse Mask',24),
	(568,'updated_at','2013-07-14T00:39:47-06:00',24),
	(569,'vendor','Black and Decker',24),
	(570,'tags','',24),
	(571,'barcode',NULL,25),
	(572,'compare_at_price',NULL,25),
	(573,'created_at','2013-07-14T00:38:18-06:00',25),
	(574,'fulfillment_service','manual',25),
	(575,'grams','0',25),
	(576,'id','336326064',25),
	(577,'inventory_management',NULL,25),
	(578,'inventory_policy','deny',25),
	(579,'option1','Default Horse Mask',25),
	(580,'option2','Small',25),
	(581,'option3','Brown',25),
	(582,'position','1',25),
	(583,'price','20.00',25),
	(584,'product_id','147293252',25),
	(585,'requires_shipping','1',25),
	(586,'sku','mask1',25),
	(587,'taxable','1',25),
	(588,'title','Default Horse Mask / Small / Brown',25),
	(589,'updated_at','2013-07-14T00:39:32-06:00',25),
	(590,'inventory_quantity','1',25),
	(591,'barcode',NULL,26),
	(592,'compare_at_price',NULL,26),
	(593,'created_at','2013-07-14T00:39:24-06:00',26),
	(594,'fulfillment_service','manual',26),
	(595,'grams','0',26),
	(596,'id','336326167',26),
	(597,'inventory_management','',26),
	(598,'inventory_policy','deny',26),
	(599,'option1','Unicorn Mask',26),
	(600,'option2','Medium',26),
	(601,'option3','White',26),
	(602,'position','2',26),
	(603,'price','20.00',26),
	(604,'product_id','147293252',26),
	(605,'requires_shipping','1',26),
	(606,'sku','mask2',26),
	(607,'taxable','1',26),
	(608,'title','Unicorn Mask / Medium / White',26),
	(609,'updated_at','2013-07-14T00:39:24-06:00',26),
	(610,'inventory_quantity','1',26),
	(611,'id','176637520',27),
	(612,'name','Title',27),
	(613,'position','1',27),
	(614,'product_id','147293252',27),
	(615,'id','176637574',28),
	(616,'name','Size',28),
	(617,'position','2',28),
	(618,'product_id','147293252',28),
	(619,'id','176637575',29),
	(620,'name','Color',29),
	(621,'position','3',29),
	(622,'product_id','147293252',29),
	(623,'body_html','<p>Nothing is more manly than the roar of a chainsaw</p>',30),
	(624,'created_at','2013-07-13T10:57:28-06:00',30),
	(625,'handle','manly-chainsaw',30),
	(626,'id','147240927',30),
	(627,'product_type','Yardwork',30),
	(628,'published_at','2013-07-13T10:57:28-06:00',30),
	(629,'published_scope','global',30),
	(630,'template_suffix',NULL,30),
	(631,'title','Manly Chainsaw',30),
	(632,'updated_at','2013-07-13T13:59:41-06:00',30),
	(633,'vendor','Black and Decker',30),
	(634,'tags','',30),
	(635,'barcode',NULL,31),
	(636,'compare_at_price',NULL,31),
	(637,'created_at','2013-07-13T10:57:29-06:00',31),
	(638,'fulfillment_service','manual',31),
	(639,'grams','0',31),
	(640,'id','336245107',31),
	(641,'inventory_management',NULL,31),
	(642,'inventory_policy','deny',31),
	(643,'option1','Chainsaw 1',31),
	(644,'option2','Black',31),
	(645,'option3','Badass',31),
	(646,'position','1',31),
	(647,'price','120.00',31),
	(648,'product_id','147240927',31),
	(649,'requires_shipping','1',31),
	(650,'sku','chain1',31),
	(651,'taxable','1',31),
	(652,'title','Chainsaw 1 / Black / Badass',31),
	(653,'updated_at','2013-07-13T13:56:44-06:00',31),
	(654,'inventory_quantity','1',31),
	(655,'barcode',NULL,32),
	(656,'compare_at_price',NULL,32),
	(657,'created_at','2013-07-13T13:56:31-06:00',32),
	(658,'fulfillment_service','manual',32),
	(659,'grams','0',32),
	(660,'id','336261410',32),
	(661,'inventory_management','',32),
	(662,'inventory_policy','deny',32),
	(663,'option1','Chainsaw 2',32),
	(664,'option2','Pink',32),
	(665,'option3','Low Profile',32),
	(666,'position','2',32),
	(667,'price','200.00',32),
	(668,'product_id','147240927',32),
	(669,'requires_shipping','1',32),
	(670,'sku','chain2',32),
	(671,'taxable','1',32),
	(672,'title','Chainsaw 2 / Pink / Low Profile',32),
	(673,'updated_at','2013-07-13T13:56:31-06:00',32),
	(674,'inventory_quantity','1',32),
	(675,'id','176579741',33),
	(676,'name','Title',33),
	(677,'position','1',33),
	(678,'product_id','147240927',33),
	(679,'id','176589961',34),
	(680,'name','Color',34),
	(681,'position','2',34),
	(682,'product_id','147240927',34),
	(683,'id','176589962',35),
	(684,'name','Style',35),
	(685,'position','3',35),
	(686,'product_id','147240927',35),
	(687,'body_html','<p>The number one craft beer of San Francisco</p>',36),
	(688,'created_at','2013-07-14T00:40:44-06:00',36),
	(689,'handle','pbr',36),
	(690,'id','147293428',36),
	(691,'product_type','liquid gold',36),
	(692,'published_at','2013-07-14T00:40:44-06:00',36),
	(693,'published_scope','global',36),
	(694,'template_suffix',NULL,36),
	(695,'title','PBR',36),
	(696,'updated_at','2013-07-14T00:43:13-06:00',36),
	(697,'vendor','Urinals Nationwide',36),
	(698,'tags','Beer, Cheap, Not Really Beer, PBR, Piss',36),
	(699,'barcode',NULL,37),
	(700,'compare_at_price',NULL,37),
	(701,'created_at','2013-07-14T00:40:44-06:00',37),
	(702,'fulfillment_service','manual',37),
	(703,'grams','0',37),
	(704,'id','336326333',37),
	(705,'inventory_management',NULL,37),
	(706,'inventory_policy','deny',37),
	(707,'option1','PBR',37),
	(708,'option2','10',37),
	(709,'option3','3.2',37),
	(710,'position','1',37),
	(711,'price','5.00',37),
	(712,'product_id','147293428',37),
	(713,'requires_shipping','1',37),
	(714,'sku','PBR1',37),
	(715,'taxable','1',37),
	(716,'title','PBR / 10 / 3.2',37),
	(717,'updated_at','2013-07-14T00:42:04-06:00',37),
	(718,'inventory_quantity','1',37),
	(719,'barcode',NULL,38),
	(720,'compare_at_price',NULL,38),
	(721,'created_at','2013-07-14T00:42:26-06:00',38),
	(722,'fulfillment_service','manual',38),
	(723,'grams','0',38),
	(724,'id','336326474',38),
	(725,'inventory_management','',38),
	(726,'inventory_policy','deny',38),
	(727,'option1','PBR Limited',38),
	(728,'option2','20',38),
	(729,'option3','5',38),
	(730,'position','2',38),
	(731,'price','15.00',38),
	(732,'product_id','147293428',38),
	(733,'requires_shipping','1',38),
	(734,'sku','PBR2',38),
	(735,'taxable','1',38),
	(736,'title','PBR Limited / 20 / 5',38),
	(737,'updated_at','2013-07-14T00:42:26-06:00',38),
	(738,'inventory_quantity','1',38),
	(739,'id','176637702',39),
	(740,'name','Title',39),
	(741,'position','1',39),
	(742,'product_id','147293428',39),
	(743,'id','176637770',40),
	(744,'name','Quantity',40),
	(745,'position','2',40),
	(746,'product_id','147293428',40),
	(747,'id','176637771',41),
	(748,'name','Alchohol',41),
	(749,'position','3',41),
	(750,'product_id','147293428',41);

/*!40000 ALTER TABLE `data` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table thing
# ------------------------------------------------------------

DROP TABLE IF EXISTS `thing`;

CREATE TABLE `thing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `channel` int(11) NOT NULL,
  `thing_id` int(11) DEFAULT NULL,
  `altid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `thing` WRITE;
/*!40000 ALTER TABLE `thing` DISABLE KEYS */;

INSERT INTO `thing` (`id`, `name`, `channel`, `thing_id`, `altid`)
VALUES
	(24,'product',1,NULL,147293252),
	(25,'variant',1,24,336326064),
	(26,'variant',1,24,336326167),
	(27,'option',1,24,176637520),
	(28,'option',1,24,176637574),
	(29,'option',1,24,176637575),
	(30,'product',1,NULL,147240927),
	(31,'variant',1,30,336245107),
	(32,'variant',1,30,336261410),
	(33,'option',1,30,176579741),
	(34,'option',1,30,176589961),
	(35,'option',1,30,176589962),
	(36,'product',1,NULL,147293428),
	(37,'variant',1,36,336326333),
	(38,'variant',1,36,336326474),
	(39,'option',1,36,176637702),
	(40,'option',1,36,176637770),
	(41,'option',1,36,176637771);

/*!40000 ALTER TABLE `thing` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
