-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: localhost    Database: lacompudelolo
-- ------------------------------------------------------
-- Server version	5.7.26

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `carrito`
--

DROP TABLE IF EXISTS `carrito`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carrito` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT '1',
  `fecha_agregado` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `producto_id` (`producto_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carrito`
--

LOCK TABLES `carrito` WRITE;
/*!40000 ALTER TABLE `carrito` DISABLE KEYS */;
INSERT INTO `carrito` VALUES (1,1,30,1,'2026-07-16 02:08:44'),(2,1,29,2,'2026-07-16 02:14:08'),(3,1,27,1,'2026-07-16 02:15:01'),(4,1,24,1,'2026-07-16 02:15:12');
/*!40000 ALTER TABLE `carrito` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `descripcion` text,
  `imagen` varchar(255) DEFAULT NULL,
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (1,'Laptops Gamer','laptops-gamer',NULL,NULL,'2026-07-12 21:05:57'),(2,'PC Armadas','pc-armadas',NULL,NULL,'2026-07-12 21:05:57'),(3,'Todo en Uno','todo-en-uno',NULL,NULL,'2026-07-12 21:05:57'),(4,'Teclados Mecánicos','teclados-mecanicos',NULL,NULL,'2026-07-12 21:05:57'),(5,'Mouses RGB','mouses-rgb',NULL,NULL,'2026-07-12 21:05:57'),(6,'Audífonos 7.1','audifonos-7-1',NULL,NULL,'2026-07-12 21:05:57'),(7,'Procesadores','procesadores',NULL,NULL,'2026-07-12 21:05:57'),(8,'Tarjetas Gráficas','tarjetas-graficas',NULL,NULL,'2026-07-12 21:05:57'),(9,'Memorias RAM','memorias-ram',NULL,NULL,'2026-07-12 21:05:57'),(10,'Tabletas Gráficas','tabletas-graficas',NULL,NULL,'2026-07-12 21:05:57'),(11,'Parlantes PC','parlantes-pc',NULL,NULL,'2026-07-12 21:05:57');
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pedido_detalles`
--

DROP TABLE IF EXISTS `pedido_detalles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pedido_detalles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pedido_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pedido_id` (`pedido_id`),
  KEY `producto_id` (`producto_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedido_detalles`
--

LOCK TABLES `pedido_detalles` WRITE;
/*!40000 ALTER TABLE `pedido_detalles` DISABLE KEYS */;
/*!40000 ALTER TABLE `pedido_detalles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pedidos`
--

DROP TABLE IF EXISTS `pedidos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `fecha_pedido` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `total` decimal(10,2) NOT NULL,
  `estado` enum('pendiente','pagado','enviado','entregado','cancelado') DEFAULT 'pendiente',
  `direccion_envio` text NOT NULL,
  `telefono` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedidos`
--

LOCK TABLES `pedidos` WRITE;
/*!40000 ALTER TABLE `pedidos` DISABLE KEYS */;
/*!40000 ALTER TABLE `pedidos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `productos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text,
  `precio` decimal(10,2) NOT NULL,
  `precio_oferta` decimal(10,2) DEFAULT NULL,
  `imagen` varchar(255) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT '0',
  `destacado` tinyint(1) DEFAULT '0',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `categoria_id` (`categoria_id`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES (1,'Ultra Slim T-889','Producto de alta gama con tecnologÃ­a de punta y rendimiento excepcional.',2087.99,NULL,'img1.jpg',4,40,0,'2026-07-14 18:04:19',1),(2,'Quantum W-356','Producto de alta gama con tecnologÃ­a de punta y rendimiento excepcional.',1003.99,NULL,'img2.jpg',6,20,0,'2026-07-14 18:04:19',1),(3,'Xtreme B-951','Producto de alta gama con tecnologÃ­a de punta y rendimiento excepcional.',2482.99,NULL,'img3.jpg',7,50,0,'2026-07-14 18:04:19',1),(4,'Acer Predator','Producto de alta gama con tecnologÃ­a de punta y rendimiento excepcional.',225.99,NULL,'img16.jpg',1,24,0,'2026-07-14 18:04:19',1),(5,'Eco O-645','Producto de alta gama con tecnologÃ­a de punta y rendimiento excepcional.',234.99,NULL,'img5.jpg',7,25,0,'2026-07-14 18:04:19',1),(6,'Xtreme G-415','Producto de alta gama con tecnologÃ­a de punta y rendimiento excepcional.',530.99,NULL,'img6.jpg',6,32,0,'2026-07-14 18:04:19',1),(7,'Acer Nitro','Producto de alta gama con tecnologÃ­a de punta y rendimiento excepcional.',1171.99,NULL,'img15.jpg',1,15,0,'2026-07-14 18:04:19',1),(8,'Dell Alienware','Producto de alta gama con tecnologÃ­a de punta y rendimiento excepcional.',466.99,NULL,'img14.jpg',1,20,0,'2026-07-14 18:04:19',1),(9,'Zen Y-763','Producto de alta gama con tecnologÃ­a de punta y rendimiento excepcional.',1469.99,NULL,'img9.jpg',9,17,0,'2026-07-14 18:04:19',1),(10,'Nova X-126','Producto de alta gama con tecnologÃ­a de punta y rendimiento excepcional.',2030.99,NULL,'img10.jpg',2,22,0,'2026-07-14 18:04:19',1),(11,'Ultra Slim O-385','Producto de alta gama con tecnologÃ­a de punta y rendimiento excepcional.',631.99,NULL,'img11.jpg',11,46,0,'2026-07-14 18:04:19',1),(12,'Thor U-573','Producto de alta gama con tecnologÃ­a de punta y rendimiento excepcional.',222.99,NULL,'img12.jpg',9,25,0,'2026-07-14 18:04:19',1),(13,'Atlas D-665','Producto de alta gama con tecnologÃ­a de punta y rendimiento excepcional.',1477.99,NULL,'img13.jpg',2,5,0,'2026-07-14 18:04:19',1),(14,'Laptop Gamer Lenovo LOQ','Producto de alta gama con tecnologÃ­a de punta y rendimiento excepcional.',4399.99,NULL,'img1.jpg',4,45,0,'2026-07-14 18:04:19',1),(15,'Laptop Gamer','Producto de alta gama con tecnologÃ­a de punta y rendimiento excepcional.',3399.99,NULL,'img2.jpg',8,13,0,'2026-07-14 18:04:19',1),(16,'Mouse Gamer Lighstin','Producto de alta gama con tecnologÃ­a de punta y rendimiento excepcional.',99.99,NULL,'img3.jpg',3,43,0,'2026-07-14 18:04:19',1),(17,'Audífono Gamer G332','Producto de alta gama con tecnologÃ­a de punta y rendimiento excepcional.',139.99,NULL,'img4.jpg',10,19,0,'2026-07-14 18:04:19',1),(18,'Teclado Gamer G915TKL','Producto de alta gama con tecnologÃ­a de punta y rendimiento excepcional.',599.99,NULL,'img5.jpg',3,42,0,'2026-07-14 18:04:19',1),(19,'Tarjeta de video RTX3090 Asus TUF','Producto de alta gama con tecnologÃ­a de punta y rendimiento excepcional.',6599.99,NULL,'img6.jpg',7,20,0,'2026-07-14 18:04:19',1),(20,'Tableta gráfica Wacom intuos','Producto de alta gama con tecnologÃ­a de punta y rendimiento excepcional.',249.99,NULL,'img7.jpg',11,44,0,'2026-07-14 18:04:19',1),(21,'Parlante Logitech Z213','Producto de alta gama con tecnologÃ­a de punta y rendimiento excepcional.',139.99,NULL,'img8.jpg',7,23,0,'2026-07-14 18:04:19',1),(22,'Asus Rog Strix G16','Producto de alta gama con tecnologÃ­a de punta y rendimiento excepcional.',6899.99,NULL,'img9.jpg',10,29,0,'2026-07-14 18:04:19',1),(23,'Fuente Poder Atom 550','Producto de alta gama con tecnologÃ­a de punta y rendimiento excepcional.',189.99,NULL,'img10.jpg',8,25,0,'2026-07-14 18:04:19',1),(24,'Web cam Logitech c922e','Producto de alta gama con tecnologÃ­a de punta y rendimiento excepcional.',249.99,NULL,'img11.jpg',6,14,0,'2026-07-14 18:04:19',1),(25,'Parlante BT Wonderwoom Lila','Producto de alta gama con tecnologÃ­a de punta y rendimiento excepcional.',139.99,NULL,'img12.jpg',10,29,0,'2026-07-14 18:04:19',1),(26,'Parlante BT Wonderwoom red','Producto de alta gama con tecnologÃ­a de punta y rendimiento excepcional.',139.99,NULL,'img13.jpg',8,31,0,'2026-07-14 18:04:19',1),(27,'Lenovo LOQ Gaming','Producto de alta gama con tecnologÃ­a de punta y rendimiento excepcional.',3099.99,NULL,'img1.jpg',10,12,0,'2026-07-14 18:04:19',1),(28,'Asus TUF Gaming F15','Producto de alta gama con tecnologÃ­a de punta y rendimiento excepcional.',3199.99,NULL,'img2.jpg',8,34,0,'2026-07-14 18:04:19',1),(29,'Mouse Logitech G203','Producto de alta gama con tecnologÃ­a de punta y rendimiento excepcional.',99.99,NULL,'img3.jpg',3,11,0,'2026-07-14 18:04:19',1),(30,'Audífono Logitech G332','Producto de alta gama con tecnologÃ­a de punta y rendimiento excepcional.',129.99,NULL,'img4.jpg',11,36,0,'2026-07-14 18:04:19',1);
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` text,
  `rol` enum('admin','cliente') DEFAULT 'cliente',
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'alex portillo','pierookstore@gmail.com','$2y$10$mYiIvvhzGwNZuijtyBFskOewbYH3sLe1QfyXxGmG15D3S2zsPYayO',NULL,NULL,'cliente','2026-07-16 00:01:56');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'lacompudelolo'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-17 12:51:35
