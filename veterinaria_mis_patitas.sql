CREATE DATABASE  IF NOT EXISTS `clinica_veterinaria` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `clinica_veterinaria`;
-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: clinica_veterinaria
-- ------------------------------------------------------
-- Server version	9.4.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `duenos`
--

DROP TABLE IF EXISTS `duenos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `duenos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `nombre_completo` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `direccion` text,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `duenos_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `duenos`
--

LOCK TABLES `duenos` WRITE;
/*!40000 ALTER TABLE `duenos` DISABLE KEYS */;
INSERT INTO `duenos` VALUES (1,2,'Juan Perez','987654321','juan@example.com','Av. Siempre Viva 123'),(2,NULL,'Maria Lopez','987654322','maria@example.com','Calle Los Pinos 456'),(3,NULL,'Carlos Ruiz','987654323','carlos@example.com','Jr. Las Flores 789'),(4,NULL,'Ilson','921777229','ilson@ilson.com',NULL),(5,NULL,'Yerson','921777228','yerson@yerson.com',NULL),(6,NULL,'Jorge','987456321','jorge@jorge.com',NULL),(7,7,'Alison','921777221','alison@alison.com','El Mirador'),(8,8,'Kike','921777224','kike@kike.com','Mirador'),(9,4,'Ilson','921777229','ilson@ilson.com',NULL),(10,9,'Yefer','987456328','yefer@yefer.com','Mirador');
/*!40000 ALTER TABLE `duenos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mascotas`
--

DROP TABLE IF EXISTS `mascotas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mascotas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `especie` varchar(30) NOT NULL,
  `raza` varchar(50) DEFAULT NULL,
  `edad` int DEFAULT NULL,
  `sexo` enum('Macho','Hembra') DEFAULT 'Macho',
  `peso` decimal(5,2) DEFAULT NULL,
  `dueno_id` int DEFAULT NULL,
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `dueno_id` (`dueno_id`),
  CONSTRAINT `mascotas_ibfk_1` FOREIGN KEY (`dueno_id`) REFERENCES `duenos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mascotas`
--

LOCK TABLES `mascotas` WRITE;
/*!40000 ALTER TABLE `mascotas` DISABLE KEYS */;
INSERT INTO `mascotas` VALUES (1,'Luna','Perro','Golden Retriever',3,'Hembra',25.50,1,'2026-04-16 15:31:10'),(2,'Max','Perro','Pastor Alemán',2,'Macho',30.00,1,'2026-04-16 15:31:10'),(3,'Mishi','Gato','Persa',1,'Hembra',4.20,2,'2026-04-16 15:31:10'),(4,'Rocky','Perro','Bulldog',4,'Macho',22.00,3,'2026-04-16 15:31:10'),(5,'Lola','Perro','Labrador',1,'Hembra',18.50,2,'2026-04-16 15:31:10'),(6,'Elison','Perro','Persa',15,'Macho',30.00,4,'2026-04-23 14:29:23'),(7,'Edison','Gato','Tigrillo',3,'Macho',10.00,4,'2026-04-23 14:30:17'),(8,'Max','Perro','Rotwelier',3,'Macho',28.00,8,'2026-04-23 15:04:41'),(9,'Jorge','Perro','Rotwelier',2,'Macho',3.00,10,'2026-04-23 15:20:03'),(10,'Yersona','Perro','Rotwelier',23,'Macho',4.00,10,'2026-04-23 15:29:55');
/*!40000 ALTER TABLE `mascotas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `productos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text,
  `precio` decimal(10,2) NOT NULL,
  `cantidad` int NOT NULL DEFAULT '0',
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES (1,'Came Premium','Alimento balanceado para perros adultos - 15kg',89.90,50,'2026-04-16 15:31:10'),(2,'Arena para Gatos','Arena sanitaria aglomerante - 10kg',35.50,30,'2026-04-16 15:31:10'),(3,'Juguete Pelota','Pelota de goma resistente para perros',15.90,100,'2026-04-16 15:31:10'),(4,'Shampoo Antipulgas','Shampoo medicinal para perros y gatos',42.00,25,'2026-04-16 15:31:10'),(5,'Correa Extensible','Correa de 5 metros para paseo',55.00,40,'2026-04-16 15:31:10'),(6,'Cama Térmica','Cama acolchada para mascotas',89.00,15,'2026-04-16 15:31:10'),(7,'Comedero Elevado','Comedero ergonómico ajustable',65.00,20,'2026-04-16 15:31:10'),(8,'Cepillo Dental','Kit de limpieza dental para perros',35.00,35,'2026-04-16 15:31:10');
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservaciones`
--

DROP TABLE IF EXISTS `reservaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reservaciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre_cliente` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `nombre_mascota` varchar(50) NOT NULL,
  `especie` varchar(30) NOT NULL,
  `motivo_consulta` text,
  `fecha_solicitada` date NOT NULL,
  `hora_solicitada` time NOT NULL,
  `tipo_cita` enum('presencial','domicilio') DEFAULT 'presencial',
  `estado` enum('pendiente','confirmada','atendido','cancelada') DEFAULT 'pendiente',
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservaciones`
--

LOCK TABLES `reservaciones` WRITE;
/*!40000 ALTER TABLE `reservaciones` DISABLE KEYS */;
INSERT INTO `reservaciones` VALUES (1,'Juan Perez','987654321','juan@example.com','Luna','Perro','Vacunación anual','2026-04-16','10:00:00','presencial','confirmada','2026-04-16 15:31:10'),(2,'Maria Lopez','987654322','maria@example.com','Mishi','Gato','Consulta general y desparasitación','2026-04-17','11:30:00','presencial','pendiente','2026-04-16 15:31:10'),(3,'Carlos Ruiz','987654323','carlos@example.com','Rocky','Perro','Revisión por cojera','2026-04-18','09:00:00','domicilio','pendiente','2026-04-16 15:31:10'),(4,'Juan Perez','987654321','juan@example.com','Max','Perro','Chequeo anual','2026-04-17','15:00:00','presencial','confirmada','2026-04-16 15:31:10'),(5,'Maria Lopez','987654322','maria@example.com','Lola','Perro','Primera vacuna','2026-04-19','14:30:00','presencial','pendiente','2026-04-16 15:31:10'),(6,'Kike','921777224','kike@kike.com','Max','Perro','asd','2026-04-24','12:09:00','presencial','pendiente','2026-04-23 15:05:11');
/*!40000 ALTER TABLE `reservaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre_completo` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `rol` enum('admin','veterinario','cliente') DEFAULT 'cliente',
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Administrador','admin@veterinaria.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','987654321','admin','2026-04-16 15:31:10'),(2,'Juan Perez','juan@example.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','987654322','cliente','2026-04-16 15:31:10'),(3,'Maria Lopez','maria@example.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','987654323','cliente','2026-04-16 15:31:10'),(4,'Ilson','ilson@ilson.com','$2y$10$l9LN7kJfl/pTiS.X8N6UKeqpxJNrSC.kJW07Cg35hotivwMZKctxO','921777229','cliente','2026-04-16 15:53:27'),(5,'Yerson','yerson@yerson.com','$2y$10$db8AUAqkPFNHSuS7fLgtSO0hTsFipd9m/r8xGbHTF1uWSqz43yV1u','921777228','cliente','2026-04-16 16:00:39'),(6,'Max','jorge@jorge.com','$2y$10$.fjYbwtxvXOKAc1n7vIoI.rzjjFkQ8AzEI9pgwUTk5Q8CMwZV3l2i','987456321','admin','2026-04-16 16:05:09'),(7,'Alison','alison@alison.com','$2y$10$XEgFRWJ/6YxszQRJ/K4tXeeTVsuSFjM4UOtVlrNbpLpYkmW22e9du','921777221','cliente','2026-04-23 14:50:38'),(8,'Kike','kike@kike.com','$2y$10$jpixYHhPBLfIARz15WQkvORy1wqz/FCMOC3iWkZzOFuC9qhf/4HYO','921777224','cliente','2026-04-23 14:57:06'),(9,'Yefer','yefer@yefer.com','$2y$10$YA4s8xJ6zcbmiF4ql9OEpeZPUHrPWwZvA1SdSD0cpVu.TcwwCIBum','987456328','cliente','2026-04-23 15:18:44');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-23 10:56:27
