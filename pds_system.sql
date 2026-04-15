-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 15, 2026 at 10:38 AM
-- Server version: 8.0.44
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pds_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `id` int NOT NULL,
  `person_id` int DEFAULT NULL,
  `type` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `house` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `street` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `subdivision` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `barangay` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `province` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `zip` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`id`, `person_id`, `type`, `house`, `street`, `subdivision`, `barangay`, `city`, `province`, `zip`) VALUES
(125, 75, 'residential', 'BLK 5 ROAD 11', 'PLANTERS BERM WEST BANK', 'N/A', 'SAN ANDRES', 'CAINTA', 'RIZAL', '1900'),
(126, 75, 'permanent', 'BLK 5 ROAD 11', 'PLANTERS BERM WEST BANK', 'N/A', 'SAN ANDRES', 'CAINTA', 'RIZAL', '1900');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int NOT NULL,
  `person_id` int DEFAULT NULL,
  `username` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `action` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `education`
--

CREATE TABLE `education` (
  `id` int NOT NULL,
  `person_id` int DEFAULT NULL,
  `education_level` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `school_name` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `course` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `edu_from` date DEFAULT NULL,
  `edu_to` date DEFAULT NULL,
  `units` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `year_graduated` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `honors` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `education`
--

INSERT INTO `education` (`id`, `person_id`, `education_level`, `school_name`, `course`, `edu_from`, `edu_to`, `units`, `year_graduated`, `honors`) VALUES
(67, 75, 'College', 'RIZAL TECHNOLOGICAL UNIVERSITY', 'BACHELOR OF SCIENCE IN INFORMATION TECHNOLOGY', '2026-04-13', '2026-04-17', '84', '2027', 'LATINA'),
(68, 75, 'Elementary', 'PLANTERS ELEMENTARY SCHOOL', 'BASIC EDUCATION', '2026-04-01', '2026-04-09', 'N/A', '2016', 'ACADEMIC ACHIEVER');

-- --------------------------------------------------------

--
-- Table structure for table `eligibility`
--

CREATE TABLE `eligibility` (
  `id` int NOT NULL,
  `person_id` int DEFAULT NULL,
  `career_service` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `rating` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `exam_date` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `exam_place` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `license` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `license_number` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `valid_until` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eligibility`
--

INSERT INTO `eligibility` (`id`, `person_id`, `career_service`, `rating`, `exam_date`, `exam_place`, `license`, `license_number`, `valid_until`) VALUES
(25, 75, 'CSC', '100', '2025-12-10', 'DYAN LANG', 'DRINKING LICENCE', '099999999', '2026-10-01');

-- --------------------------------------------------------

--
-- Table structure for table `personal_info`
--

CREATE TABLE `personal_info` (
  `id` int NOT NULL,
  `surname` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `firstname` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `middlename` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `extension` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `birth_place` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sex` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `civil_status` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `height` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `weight` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `blood_type` varchar(5) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `umid` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pagibig` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `philhealth` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `philsys` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tin` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `agency_employee` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `citizenship` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `dual_country` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telephone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mobile` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `photo` longblob,
  `photo_type` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `personal_info`
--

INSERT INTO `personal_info` (`id`, `surname`, `firstname`, `middlename`, `extension`, `dob`, `birth_place`, `sex`, `civil_status`, `height`, `weight`, `blood_type`, `umid`, `pagibig`, `philhealth`, `philsys`, `tin`, `agency_employee`, `citizenship`, `dual_country`, `telephone`, `mobile`, `email`, `photo`, `photo_type`) VALUES
(75, 'MAYANI', 'CHARLES', 'SEGISMUNDO', '', '2005-01-08', 'CAINTA RIZAL', 'Male', 'Single', '165', '82', 'O+', '111111111111', '1111111111111111', '1111111111111', '11111111111111111', '000-000-000-000', '11111111111111', 'Filipino', '', '02-8123-4567', '09297437470', 'mayanicharles10@gmail.com', 0x52494646201d000057454250565038580a00000018000000f90000f90000414c5048b007000001f0c7ffd7e234feff9d6b122cb8532fb2ee06757777f7f50ab2be9bbabbbbbefc552fb2eeee50bd15ba5a770382c4e6fc4148263327e77af92b222600fe6fefc8dddf7efbc72977861321cc96ee0d1bb26915d6bd76b4571c01918d9f7ebb4cad3c7b1f935ad83d203a6ece8e37963262d7ad8be8f14c048f76a397ea8d02abc53041e97f56b17e358f45269b37755735368872cfdfd1ebd3a11ceaebf005cfcdbbc70077f5ccbf81ded7b4e5d08ba8e1e505493abb6b5b25fa3e99437fd702f1e6dc441dddbddd865acee750a1368837e6378bf6c154afb0c4c4c4842af584f4da61436d7f53f8a39cd50ad1fe59162866b3d9dcc36ab55ae75cbce9f9d3529bcd76f99b4e83064544468f38ec46ad3964bea01d226efb19fd5a30f377f423877254bf189a435694b7b1645567c91b8ef8df1b26c89bf998bcc171791327e4ad8b9baea9dc119f22ddc5dcc9741356fb10739620e579bcb9ab96b48f783319493f9bc89a3fd0865d3873ef59e27673a63112ff96c217b18f3a771a5f602d7598ce97d1d5e4cde0cb40247f0b5f9ad2f755285794f3f43913b9220ec81b58ff91a53ec395a4b7e9c3c55c791effcbc36e895b227133e4cd7d3f57622be8537b73c574330074e18a729ebe13315c8159f41d07b65ae97b962fa3e91bc7173379971ee70b6ca3ae5030660871ea64606c5c356da7631863eaf6156db3044f84d275cc97a5487ced877d924398214c2d9f7fef372706c493179e79900f4a8675ef4d3b06d26b5ff76dc202cb98320cc43f7df0b80874a96b8f61a0aed99b1916c0825a6dacc2406ebb640d0f5069c38f62e0bff26ac3e080239aafbe812c546bf35b07059686dfdc463e3a0a5a8ac091b0a60679e9fc7b7b11182257d9919ff6a2a800103af407e4e9d559f1d4b52946b6aad7672894ddb5a91c39ab7ed02d882af3f2dbc8de4fefa06974998a0cbeb8b8293d197f5691c957b34db444e521a35d5f3614848c2d455e578c0fa3e2e1dd0ee4b6ebe366245876d720c76b4746192fbc18b99eff80d1465e53d986b79f0a3152d82a37b27ecfe3c649fd01b97f39d7649096d791ffeaea18239856dd441954cfb734eb2e68ab8ab2b8b8b1ce327e52511e4f7553f4647a13a5d2bda0a97ecc8750364b92f562de82f2599cac937628a32509bac8ba2d25783849072d6fa0a416b712fe0af915a5d539c14fc17b557941e704ff8c47a9754cf447f871b941fbf37e9880b26b6ba4596a8df4e00fc15a6d42f955676834c8294158fea0368b508abf0bd622f47b3942ab1639aa24552469f0324ab2fabe59dad0d943def033c5a7a1f274ab914fbde4099ff625f847892af6659aaa0bd76939b87d9f77d1b7d1ff35e7ba3f36400ef029efeeacf493fde7b5e31b264093ab92f08577afa01fd5e33b739b09a8db1d6564ab87aaeb3e394b8b06a70aa8ff6d59b03fe255d1ca5f106f3c70cf166fdc47760dca0001de6654cb82d33bf1e04d3cd8136033225ef9eb962d5be63ed15c80cf6350160f0baf20f1c3e58d019a5ec2cb5b1a81e639d2f01268d97f67ab64d05e39250d7334f173f86569b81aa2bb3c944675b2ee26f0a924e5d1b9171dfec032a13351ce27ec0da0b45d71cea55d4da6ce12ce306a30d40deadeed804b237c45675391d19f7b0000d1f6c55f7c73b9110be5a5a03e0048edf38937274f21fea9ed791caeb3919caa7ecc1b80e875459b571eaf7423be189fb4c1b619e267bda7b3324e614bef3c5ac2b3330b2300a0db2400b0e84b9ce594fa880686e6957b3a2db1273885f9b48cc67f92ec94b8ea0c527af30aef26a544e28ef2cad154de5c5679c39dff36b74ee24e46ca1b26fdabc70e6639e22919cdac65206f3bff8de3f11a79836289fb8bc40d67556d775ad26b59d5881651c1295b222db09153c7a999c9a9dd406c574ee553135cc127752b35b0804fae46e424dc62d3e55472601e9b0a81defbd8d48520e513267d1e44107474f3680e90fc098b6afbd2d4c5c5a1e3822658c4a18d4074f22dfed434a50a26b8d9b309e8dec29e54c23ad998b34d210c26f2a62416280f7ec9ce18772f207e1363be16d4c57cce165b5b203ff60baeac8000f8580d4b9cdb83c90b7e64971d39ea9a06d49b8616abc8527736506fdaae224f5dd380fae0dd2af2f4b79940bd699b8a4c6d05d48b1d2a72f5eb6e26e2b290b3f9a1b43dc71a3c2448fb9837eede94c5ffc21bac6e45582612e9aea60a3f89a42bf586a15e6b527fa3e1eb3c17908383c98a7efdaa91ae2681864ab8e727e6785c6db3d9ae971b6b3a51f17967d0d0a784163e0bb3d96cea919b3bbeec7cad517ea069f26f68f043a06745d965946b2914e53ad1e0ea33ba02d86614ec448fc875a1c1d5bdc1fa8ab419661d3922d78906b76d08067d077d679857c8099bf8cc7c431dded60a74bfdd309dc851962fd8b6fe72797979f937cb2b75a556aae757ee1f100506344e47723c86582c164b08e4ebc476fa8273e9b425310f258341b71ae5d7509aea5df3951b8b4b4b7fec37fc506969e9576edfcaf1f0e8d12f6699421a83a1ef3c6d906f8076a5e78048210480a8db63c080fee3d75edc37bf6e66545454485410909871c610d73389d3b601109cb256d59fbd0b48a2b2a25277eade10490071ffc6d3aa13f1ba7ef0cc44590080e096c933378ef2977afac6910ff6cdf69825111ec30a4e97bff0c6abcf7abdf4c4c58adba74a26646767674f8e0853405a8539167c4e8b7ea485c904ff1f3756503820881400009060009d012afa00fa003e7534964824a2a525a8114b50b00e894ddbabd0a4a8dbfcdfe697b7cda7fb2fe37feb7cfd35879d9737fe80f68de8a7f42ffd4f704fd6bfd7ceb95e603f6a7d677fe3faa8ff19ea0bfe13fd77a557b12fa047ee57a72fb27ff5fffa7e909ffffd803fffec517f47fc69fd73f2d3fd474ef7be903ac7972c5e4a6a10f63b43bbe9e74532cc803beefc1da80de2c7ff8798ffaafd83ff5efac7feebfb35fedb9be3d52ebb8f572968461e910f935f7871aaf82466dc1cb99312b07bfd71ce1b3712f776d5d75fb2bf92df7cf41f33acf72420c61b1faabe091a46266e0860cab08be9cfddcd4d9e0ba19844c95af0803e1d3537836d9a224671a9cc9f4cb0cf95e19764a10f42aab842d8786c94e8a6753b43707f554867197534d55678348d4e980e2218f8bfa1050c39b889948a514dff9ab8e573c076325c354360c6cb53b7ffb9a478127b0dfffe956b5433236a3a104d96f1eaa600aeeaedafb0553adc5369dd080ad426ef5f39bb1b00bbc0f1abe9dfdd637985956ff0a7f9ffed0a651d857473464f4c75c0c1790ce362aba4f733f7fa3e1986f54b8b0828b336a0efa37e141f3f837324f661fe2dfa5be5f51934a64119ecb605eaaeb5b0ff84d3bb3927cb166367e8c8d70d980db6609314ee71c65c8a4bfa1bd43c38f3f626b572aa7d351347b9b775b0b57bfbbd7b3e52cd831b497a76c4da3a61932acc2b96a2e3118b0e29ba9795e577ce0650711c6c5b491e7d4890fb71f7538d9bafe560ded65fbc087f4b49959cc59736a1dcffb7fb6c226ce291b0e95993d9fbdda16a8ffeb490dc7219adb69d109b8a53cd2a9ad7a4ecf501cdeba58d509b9f41b2e0436245d69dd0458cdf40ef527fb03deb6509c534d2bc9d38048a825ccae189938f3414367783adf5da9472fd1637292b6f7580473851eb9e4f0f3deaf97bd896d8bb78618d340a6b17631ee8c4a50c2f16dbd51d5f0a48ba7d502243647baa03c6c85ac73c2b4d35e47b5f12af583acd19a43c8f5e68b0c7f763c9f06f1c95f7ff2b119554f65ce3486993adf4c1122dcc7a18d79eadc8b3faabe09246ddbcca8d14b4480000fefcad104cad8c5fff73a2966ae855bda04b82ddbd9066228d1f5953d9ff25e05bef900a341b2e608bf17c0002ef49e5742a0a278db68143f4b554a86e20d341e5b2b9d6396dfa32fc5a938a2571cf8a4471c83421828f83e1f78f7034593ecd50307e62bd2b1ee3df12d6a0a85f53ba930f4dc947925d0c0fc50e9991f82bd0ff8201519b86b205f576c2a4ac4d52c707017045c8b8c37fdf64d9fd068165f7a2be0cdf9bcb409eb60ec07cb50bb196043856475213c0db56470a97b14329874c341693dfec77393c00cf6658d7c879f72a24ae52b9c17de1f620002b756be2c0cb9579e7674ee097bd8a8f0aeb32faf1ae4d5b27f79acb93abffe7c9f2d7f3bca48f0d1e44c64d837d3d0e0d3cff491206d285c182dab25e914a28805b1ebc7cbd67df5625ba1c927aa364bef423365952152cfedd221643fbd0bc9137298f13e0d1c5bbb2d7ff4c93bdaad0e0000d9f20b2517e07d4d96de36c3b043891dffb7f026ceb30decb693f683c0d98a9430de118a0e274ec0079d859fa6331e415fbd501ddba1ce01ae950f9635e4640f2ceaab9c8ad1fc0b7361de92bc0b112ccb528dc92ef80c5c6f89cd3926e274838d70b6b99f4735fa301d55df0d7f84f368a3698d6eb1b30efeb9942ffe0851a3580f4e8f2517c5ca56d43793f642b3e61d83b605a1a968255c3e3f896e06708bfc212275d59f1b06523acaa3a7f8845be28dae8175330125bf9427de19f7467015c785f015d723efe6100ef5e96a3f00ef73bfe4a0753ef3d9a5ca4d9bea55e06c7370217715e38b4e757f0daa9aca57fb29b7e6121d4affe00f7bd58d8c1b34f58897beab9a071654c13ec2260e5787b7e62ee596f0c63ae73c6d2ddce38e7070004da7d315535e7e63c88ce62cc5ab00cf07e92bdfbe29f5fffdcee163e04fc41e1999033ed92fdec393d17c952c25a0f131d16233f43a61ecdb83c10208733fe8c839c353a211df24c6d6cf56da3104a51f28bd43ac9594052bfed03375d58c687e540cbed7e858de95210d0aeb67005105443ab0ba921d75b3c47aa4b3cea41b7ee4567861a2f2d9312b644d7c6bc741905a22e2dd2bd2743ebe9e90868db6d362f0d453bfcbe5d0987049ba1194566af0f85c3a47cc5448e6064bcd3fd4f54d5478945373accddb6dd56c50cffe99b92f1621360d95915105926e81dafcd0b37daa8185d30d49d33e4fe8b4b1e68a418c6f70cac80c926e5340000641a3490d728b2a60d942086fd7539f70d4e87ea0321b9827a24621da2c1eacaf5adcc6e8fa01deafae68800f8c5e0cdbf51bf77a3b405cb0b1f0050be7804b05b07c9b9e522ff5f9a226817c7e2e0bbf3b22c657fa48326508fc6f05447f4c91c9e6fbde914fdd54f955c3511c1a97280ec668e6a2fca75f807528d64c65427e2119a39739c7b2326ac2d5356b656baee36bb977db37c9d8a1e4a22a1928611d76d8e48f2b588b69dae68738b67ce42a1cc463ebbf646a2bc5cc7fb1c28c6285f737b66ba6b195a6adf6e2fff000000befc3a8aba6cd99ade6a59c934083f32332efb4c1386b35ed1c944926fa90ef79007e05531cc89d5736b6c73ffd066db3c5b4d21505897ea503c790a62c8d4af00bd84bd89bf6cbb736e4ac9a16ce6393220974baa6a564f77ef28d8a83707444f66fd3e704374062644e8edcf7de283bc9939d3332828ac17985bb33dfe7719b9ceeaa8360b95c1db21fa5fc32348b0934132fe2783c13c90dd1d5b7d4ea3e9e5dfb6e935c3f0dc1f635ce73055c0d873cfcd0e155d663e940e9845144cb4f1d3165ddddc8ef61d050212114b6b4ccd3a9f51a12c971a8f58ca758e488603edb527ca2ba5a184e4db5435392966cc118288bd00b2bafef46b78c585d62026c8b1f78b7ce11e541b1adc2d97810bbe78e56ec3c487168400a8fd8c44f7081a2ba669448d30a52971c9e83e9f7cadd74cef59f9b3d7c07222912d15b140f2cd793f4d8422d3bbdd559b075c7aab32dccd9ecb5a1ea5c0e80e5e5685aad11d69ec87c1af1c96cc607f879d9ce2b1ef72dd27fc299b79f3c010aa0b64bc3acc5d21f12fa137e8eae5492f8b8675fce347ef5ba4b4cf4da46718ba0eff2bb55f5e8767d79539a41dfd26af1d0631ce5960f421b739a94403c96c1c61380727acb3cff9d9d7788e676bb507e00173109af807bb4e0f4e1ae166635a23b4d209ef0a2ac575e770e26debab96c527ef5f72b1ca26336b72f84627c4154832fed5cd5258f24f8eb32c641b0a45e1577c5f4c6d8a9d86dae5cc746e94d2a8d993e9ac2e316f50c8ede8af8306ebb01b27150bf986e8c07b537eefe3aa7b31a41f987529acc01c8a2f5c78f78d6690933e9c265a28feb34d2b03898005c0f797753de17f053320bec9f21134d503441cc423b9d2c89f0fd891dacf451ed5168c32cd10499e44068e050c881f81edcec8bb09240adcca8e493dd4c6424b8b51cf5228d9a1aedb50b8a03fd6473081e137e5566930489c153f62b5367bf70e841096781b403f5a2d53139812818acf4803d9ab282d5bf5b7389e259b1fca3a70aac241f4b8d947275df5020487d5a582d288044ed82609a66019437dcff367a4b5f1340101ff4b44ed97e2bebfcccce1859d8755bf11e69e0ceccade2e08158fdcc1f0c1deb8f7ed017edf5fb0ca44f187c6797deeb769797157d696a0fbb10d222b07f9ca99ea1f7abb8bb0a89cb74df107217858612b38d57dc493fe8bf8330429745888906954290d699d471f600b0e0ce56289872c4680aa124d538df7c15e4a05ae82023530ecfc701cc8a7220d69045bb6fbd707e32ef58a656bff20183e18cd8308304ee0119c0a96cd8c71c3c457c532f97bd74964dc4cad66703b18612049caf145a50ca1fb59a439664c96629569612f75b77d097c98a2ab4c3139aefdf078acc07329ee4b696a022e7fa8634af744306902d22bf68239ef3fa1f85d59197c2d9729e9dfccbc36cc5668ec24c2d6c7beb08c125cd09a5d083fad150502f11466598cc3e49241901f6a2f06780cf42c4d3a0e8cdd43a26164b1e3324f936a95bbb908502787f97a1a2d99c8d8c95b90cfe309c69645bf5dd49ea8bc3c1c913dd1eaa21f53d9317d17abd649170cccee3f7c6766ce057ae477f11d97a3a4df9cc2b8ab70b6986fc70e5e6e1e3691fc248e571f8edfe1b9bca0707f3d812ff9b4ac48050c02b60916e627f682c6429ca3809fa3b48529ba8fe810f9567620bd88e534c02018959bce618cfaf03aab4c0580a7d8106cbe324ead6ae064f94e37b3b1e036e51af2a0f02011fec15b3c3dcb4ffc8fbd5f89776c37028c0edbe6c8d64d1159c2c27486d03e61652ba45144375b8202f51526f8e28f7cf9600e5b7c0ba962929a50a7fa9852fec330f761f0b9680235e8bb25a5d2532c39f99b25c2c2d600ca82aba9ed7d1b7095b530f5ad2b7f03a6c6f54bcd16d48c65bcff13f01095f875c4bc61f92cff70eb27fcfa9aa89a024262b6e2726d0679d194a12936de28c00a966887d022e5f573ed982b5115df41b53d216c2ed1de8e637729cdbeb3b9e3d184ac68f6a8807f7d014e9370569ff24afd67b72f8c5d647e4295ad0b403da523b757e14d640e27fd2bf38300feb83df82eb22753583300fd8f1a78438490d4bc0e7d870aba043688a9cd44e603e15c8344fee0fb98e49a1b2fe5c5b9907f80b67c01e90020ded40bb0663b55f64b148a6978fc2968f19972d6af3e35a44498b3f274958436239531d98651cbe8fdef933352f85504eb1850d81c849cba2c1983c4078c4bceccdb4c26b762cd94cb200fb15879412ab3aeab0153ee96530392db7fbdfba1c82c683f60938e94f291b9e89e3bfe5060764aaa326b050ee6da1890d4c64c4f15fffb4350e90c92cac96a2fdaf453ae851e319f70ce53919e094f0fef82feddcbd2dcf0a7c0dbfc178e0fa32e7e03917f8ac01a58a188948901228dc5884f7d79fff722430844a7d6f6986f50aa26f7a850aee49c4607247d125eb316c7893b927dcc3bbbe2a3033023c97bf726ca31e16ebf20ee387802823ff8151f9deafed20d28ffca23e26f91ffc26ceaf389dc540d8f1e32f9a9f6f529a0ded1a2d4332370b39010a06bc04c209221242ce9be4a94f09e6cb302def23e3228c79a877604fc837de947f91414c85d34cd7da9818c0e57e195ba98143260927f72482ddb2c8146ac179f5a7fba5b40f4d16d96a7a1453d929dc08d89c269f87ecb4e1ab01a3ac5ae926e2a8c77129950cc0534ef6db3c8574811f510d7b2b2446b92eeea689c1e60029381b9020ce41c6c74b839ef5a62eb8cb9a65f765f022f63891e88efb5e4c8c2c2b93a8df78db33f1b2e7d802a1aca5d9893e0c388f22213096070912ac898b0b9dc8f4f4cd6dae93df63921fc4eba2bf6623cc7d9849e5f1d0ae64f1f4073b80d1eb7c5d7b9c724966a4c0d57b8ab87adf62c8b1936c37be4e93927bb1d982d277902f1f8fbe674ef523033f04da1137ef9235f806ec96120c90174bc7a524aa576bb2a6bcbff772fe600f5d871090814a572061b8b31e663391789ac0f414ed1bd5e378182c7b06ba2dbefe4c516a8805373394515b2063eb576765f77da6cfc72fc4b4e052df56dbf514ad33843a7bcc35f03ce6e9ba9c84ee025be00954027513b6acc735ecc041b10832c6f1dc4feda9db01ed4b04f5ca0b0867659b347ad631a45d290b3eecabcb0b9f4ac483ee7f7d6ca1464493b5d39ca5eb34d36c8f31e0cc5d25e8b05dcc6e833a964f289253b7b6e7f859d15d0ecb59b889037c2a0bded14d9757d91878a923f0846571b95b58a9474712a3b83da5b3cde12b4522d0ef16d051c59adf2e4ed0588c68d569c56478e130fcde3136b17ac7ced6fe6949e7d91e078a38dd5ada33c0a5ed5e25235a127cc45beba19a7101936a43e9c3915cc118fa679dade42549e171bd1dfa39728b02cdefbf165d0727c3ba85a3e10d149d184c4d88276da76f420fe3ecb28e9a3f160d1190fc5ffaee1cf0f22ef5d31e496cb52e9e09f43d84e959499df19d3b8fb02d18a166c8468193c5124f1f64d08497b73abd90127f8a373748037950a9b4eb7dec9e4687925c61c6fcb330a336873f72a7538ee4c250856408224ff6cd17649135f77f03d9b67f46103811df521ec6da210df752519dab4bdda70b568ad0570197f929a4c3a67145f993c2af3dffca4998b9c90d576e13354bf5042880216cfa246c6977cf9b6f2da30b4b7363ec43e1d217e93b940be11129255c409bc4837d41e023b8e53b80a48022105f021e545c04fa268a76ea7f9ae91e7ccd6912a91fadf37b6a16f4fea39c0fec3e0efdb77bc3300a8fa64ddfbfd60a514e404b6166321f9aa673161e3c9a79a53a3b033cdf8de78e4431f84323c7a522ceb1a050c307992aafa931e230e1c45c7d26ca57ebd2b137eb8eb94a72f864fae0da9124b12cdc64d2f3955f23167e4b23686d5f797d3b6f9c9b8a34f31cb8f639d96f76a2ab5b95a77cc52862f3cdb597fbfb7c67d1d2bd81e59f599c1c7e4fe67030b6955f68ce50ea0c914c0ddd0c55a28a6081ca7ecb4f8cf2e47b97640b26960c2d2cf7687312ff9c90ccce8bd6ae7a2f0a40a709861ffb1084aa103c3a811626324bd47555dc61c51ab8662ea42c3afa8607d696adb22b9c3e33cbab9f01e5b282e570e3e817d78899469ad85c28d25ba6119e7b81658f3c33716f79dbe1d4f1cb194f5b0b0e65f599658c280d881f95a4441a3447c8b76ee3a648274feb8556cc6bcc28a56c533cd35172a2097904f24f17f79064c47a173e299aa2a2a60724076e22298d9b414ca460715d9bf24a4e1d86532736f84f2544326f47ef2b009664c75e31b9996ada833b209cab1d2255289855d303570f74e70421669cf33f2659fdd2b62a7a1bf1d98434889354551542732897c6acb401d1c8247a18dae325eca844e26638a69c07eacf727143c9ce0325bb897a926db8a436d152a4f0c07cbc241df6b95f26621c93de318c21dd6a77fcb9e704514df53e28da858ff1058893a76e4385c1cb5c8381c04ae75e086f55afea2d665fb76bf4ff582fb7096c0dc9a5692619c2136657688023da2d6ef2659272e163d037f442f18e4e9d5e7144cbe6f356de38c8203fb0dfeb9e96b881c914e7d5c9ab7f6a190270320f066e08bc912ac9305e4607755f9c8b8ff4ec9b2d7962e95a7a0e3f6ee485de4890e7f9e65614fb697da17d7ecc5ff84c3c563948b2a35b834f2aebf92a051018e1fd3c27c77eb79ada5f5ff9a4378a120ebba1ba7be12408c7000000000000000000045584946ba00000045786966000049492a000800000006001201030001000000010000001a01050001000000560000001b010500010000005e0000002801030001000000020000001302030001000000010000006987040001000000660000000000000048000000010000004800000001000000060000900700040000003032313001910700040000000102030000a00700040000003031303001a0030001000000ffff000002a0040001000000fa00000003a0040001000000fa00000000000000, 'image/webp');

-- --------------------------------------------------------

--
-- Table structure for table `training`
--

CREATE TABLE `training` (
  `id` int NOT NULL,
  `person_id` int DEFAULT NULL,
  `title` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `training_from` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `training_to` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `hours` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `type` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sponsor` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `training`
--

INSERT INTO `training` (`id`, `person_id`, `title`, `training_from`, `training_to`, `hours`, `type`, `sponsor`) VALUES
(29, 75, 'OJT', '2026-04-06', '2026-04-15', '300', 'TEST', 'PCGG');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(120) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `reset_token` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `reset_expiry` datetime DEFAULT NULL,
  `role` varchar(20) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `reset_token`, `reset_expiry`, `role`) VALUES
(4, 'charles', 'mayanicharles10@gmail.com', '$2y$10$k3XNCeCwwSieWEdgrJ3PaOtwlzwUeqYuvHZBXp3/aL88UknLEge6S', NULL, NULL, 'admin'),
(8, 'leslie', 'mangobosleslie22@gmail.com', '$2y$10$ryaAYHjWpYFb2yCY87z5t.l7nJpm.BY46qkFmh8x2c1r3lJdjSDsG', '112427', '2026-03-23 03:24:02', 'user'),
(9, 'Yasmin', 'yasminpilapil1620@gmail.com', '$2y$10$oq5naMy1dx6DTuF2/1Ei7O0JAhbYLIcTEX21rGAoOfb4fkZdRq7hy', '198459', '2026-03-24 09:15:13', 'user'),
(10, 'admin', '1@gmail.com', '$2y$10$Ay3zMkz3LMXfAGA4Rag01.3UypECt0.ljVgHbiqzEf4eom3qMJh.u', NULL, NULL, 'admin'),
(11, 'User', '@gmail.com', '$2y$10$mUzt/LbQJFutIvJzp35nL.R2/PqItD7Q5SdXt.QTnwW71ehQNW5Am', NULL, NULL, 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `person_id` (`person_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `education`
--
ALTER TABLE `education`
  ADD PRIMARY KEY (`id`),
  ADD KEY `person_id` (`person_id`);

--
-- Indexes for table `eligibility`
--
ALTER TABLE `eligibility`
  ADD PRIMARY KEY (`id`),
  ADD KEY `person_id` (`person_id`);

--
-- Indexes for table `personal_info`
--
ALTER TABLE `personal_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `training`
--
ALTER TABLE `training`
  ADD PRIMARY KEY (`id`),
  ADD KEY `person_id` (`person_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email_2` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=135;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=571;

--
-- AUTO_INCREMENT for table `education`
--
ALTER TABLE `education`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `eligibility`
--
ALTER TABLE `eligibility`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `personal_info`
--
ALTER TABLE `personal_info`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `training`
--
ALTER TABLE `training`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `personal_info` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `education`
--
ALTER TABLE `education`
  ADD CONSTRAINT `education_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `personal_info` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `eligibility`
--
ALTER TABLE `eligibility`
  ADD CONSTRAINT `eligibility_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `personal_info` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `training`
--
ALTER TABLE `training`
  ADD CONSTRAINT `training_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `personal_info` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
