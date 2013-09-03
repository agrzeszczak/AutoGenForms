CREATE USER 'jeffrey'@'localhost' IDENTIFIED BY 'letmein';
GRANT USAGE ON *.* TO 'mydbtest'@'localhost' IDENTIFIED BY PASSWORD '*D37C49F9CBEFBF8B6F4B165AC703AA271E079004';
GRANT SELECT ON `mydbtest`.* TO 'mydbtest'@'localhost';


SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `mydbtest`
--
CREATE DATABASE IF NOT EXISTS `mydbtest` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `mydbtest`;

-- --------------------------------------------------------

--
-- Table structure for table `table1`
--

CREATE TABLE IF NOT EXISTS `table1` (
  `id_table1` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `foregin_key_return` varchar(20) NOT NULL,
  `column1` varchar(20) NOT NULL,
  `column2` varchar(20) NOT NULL,
  `hidden_column` varchar(20) NOT NULL,
  PRIMARY KEY (`id_table1`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `table1`
--

INSERT INTO `table1` (`id_table1`, `foregin_key_return`, `column1`, `column2`, `hidden_column`) VALUES
(1, 'Table 1 Row 1', 'See Me', 'See Me2', 'Hidden'),
(2, 'Table 1 Row 2', 'See Me', 'See Me2', 'Hidden');

-- --------------------------------------------------------

--
-- Table structure for table `table2`
--

CREATE TABLE IF NOT EXISTS `table2` (
  `id_table2` int(11) NOT NULL AUTO_INCREMENT,
  `table1_id` int(11) unsigned NOT NULL,
  `data` varchar(20) NOT NULL,
  PRIMARY KEY (`id_table2`),
  KEY `fk_table2_table1` (`table1_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `table2`
--

INSERT INTO `table2` (`id_table2`, `table1_id`, `data`) VALUES
(1, 0, 'Row1'),
(2, 0, 'Row2'),
(3, 0, 'Row3');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `table2`
--
ALTER TABLE `table2`
  ADD CONSTRAINT `fk_table2_table1` FOREIGN KEY (`table1_id`) REFERENCES `table1` (`id_table1`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;