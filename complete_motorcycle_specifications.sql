-- Complete Honda Motorcycle Specifications and Descriptions
-- Star Honda Calamba Motorcycle Management System
-- Updated with all Honda motorcycle models and detailed specifications

-- First, let's create a table for motorcycle specifications if it doesn't exist
CREATE TABLE IF NOT EXISTS `motorcycle_specifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(30) NOT NULL,
  `make` varchar(100) NOT NULL,
  `model` varchar(100) NOT NULL,
  `transmission` varchar(50) DEFAULT NULL,
  `engine_type` text DEFAULT NULL,
  `displacement` varchar(20) DEFAULT NULL,
  `seat_height` varchar(20) DEFAULT NULL,
  `brake_system_front` varchar(100) DEFAULT NULL,
  `brake_system_rear` varchar(100) DEFAULT NULL,
  `fuel_capacity` varchar(20) DEFAULT NULL,
  `front_tire` varchar(50) DEFAULT NULL,
  `rear_tire` varchar(50) DEFAULT NULL,
  `wheels_type` varchar(50) DEFAULT NULL,
  `starting_system` varchar(50) DEFAULT NULL,
  `overall_dimensions` varchar(100) DEFAULT NULL,
  `ground_clearance` varchar(20) DEFAULT NULL,
  `fuel_system` varchar(50) DEFAULT NULL,
  `headlight` varchar(50) DEFAULT NULL,
  `taillight` varchar(50) DEFAULT NULL,
  `maximum_power` varchar(50) DEFAULT NULL,
  `maximum_torque` varchar(50) DEFAULT NULL,
  `fuel_consumption` varchar(50) DEFAULT NULL,
  `compression_ratio` varchar(20) DEFAULT NULL,
  `ignition_type` varchar(50) DEFAULT NULL,
  `bore_stroke` varchar(50) DEFAULT NULL,
  `engine_oil_capacity` varchar(20) DEFAULT NULL,
  `battery_type` varchar(50) DEFAULT NULL,
  `gear_shift_pattern` varchar(50) DEFAULT NULL,
  `suspension_front` varchar(100) DEFAULT NULL,
  `suspension_rear` varchar(100) DEFAULT NULL,
  `wheelbase` varchar(20) DEFAULT NULL,
  `curb_weight` varchar(20) DEFAULT NULL,
  `frame_type` varchar(50) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Update existing Honda motorcycles with proper names and add new ones
-- Honda brand_id = 9, Motorcycles category_id = 10

-- Update existing Honda Click 125i
UPDATE `product_list` SET 
  `name` = 'Honda Click 125i',
  `models` = 'CLICK 125i',
  `description` = '<p><strong>Honda Click 125i</strong></p><p>The New CLICK125 SE is powered by a 125cc Liquid-cooled, PGM-FI engine with Enhanced Smart Power and an ACG starter, making the model fuel efficient at 53 km/L. The model comes with the Combi Brake System and Park Brake Lock for added safety features.</p>',
  `price` = 85000,
  `abc_category` = 'B',
  `available_colors` = 'Red, White, Black, Blue',
  `date_updated` = NOW()
WHERE `name` LIKE '%Click%' AND `brand_id` = 9 AND `category_id` = 10 LIMIT 1;

-- Insert Honda Click 160
INSERT INTO `product_list` (`brand_id`, `category_id`, `name`, `models`, `available_colors`, `description`, `price`, `abc_category`, `reorder_point`, `max_stock`, `min_stock`, `unit_cost`, `supplier_id`, `lead_time_days`, `status`, `image_path`, `delete_flag`, `date_created`) VALUES
(9, 10, 'Honda Click 160', 'CLICK 160', 'Red, White, Black, Blue', '<p><strong>Honda Click 160</strong></p><p>The New CLICK160, now featuring a bold, dynamic, and aggressive stripe design that demands attention on the road and ensures you stand out with its innovative aesthetics.</p>', 95000, 'B', 5, 20, 2, 0.00, NULL, 7, 1, 'uploads/products/honda_click_160.png', 0, NOW());

-- Insert Honda CRF150L
INSERT INTO `product_list` (`brand_id`, `category_id`, `name`, `models`, `available_colors`, `description`, `price`, `abc_category`, `reorder_point`, `max_stock`, `min_stock`, `unit_cost`, `supplier_id`, `lead_time_days`, `status`, `image_path`, `delete_flag`, `date_created`) VALUES
(9, 10, 'Honda CRF150L', 'CRF150L', 'Red, White', '<p><strong>Honda CRF150L</strong></p><p>Break your limitations and explore the world through The New CRF150L. Combined with powerful 149cc 4-Stroke, 2 Valves, SOHC, Air-cooled, PGM-Fi engine, advanced features such as digital meter panel, plus Showa brand Inverted Front Fork and Pro-Link Rear Suspension, and a lower Seat Height (863 mm) suitable for Filipino market. This motorcycle gives an excellent fuel efficiency up to 45.5 km/L, so you\'re sure to go further than your daily ride.</p>', 120000, 'A', 3, 15, 1, 0.00, NULL, 7, 1, 'uploads/products/honda_crf150l.png', 0, NOW());

-- Update existing Honda Dio
UPDATE `product_list` SET 
  `name` = 'Honda Dio',
  `models` = 'DIO',
  `description` = '<p><strong>Honda Dio</strong></p><p>This scooter delivers exceptional power and performance perfectly fits for your commuting with Stylish looks, aesthetic design, and functional features compact in one scooter.</p>',
  `price` = 66500,
  `abc_category` = 'C',
  `available_colors` = 'Red, White, Black, Blue',
  `date_updated` = NOW()
WHERE `name` = 'Honda Dio' AND `brand_id` = 9 AND `category_id` = 10;

-- Insert Honda PCX 150
INSERT INTO `product_list` (`brand_id`, `category_id`, `name`, `models`, `available_colors`, `description`, `price`, `abc_category`, `reorder_point`, `max_stock`, `min_stock`, `unit_cost`, `supplier_id`, `lead_time_days`, `status`, `image_path`, `delete_flag`, `date_created`) VALUES
(9, 10, 'Honda PCX 150', 'PCX 150', 'Red, White, Black, Blue', '<p><strong>Honda PCX 150</strong></p><p>The Honda PCX 150 is a premium, stylish, and fuel-efficient scooter designed for both city commuting and longer rides. Known for its sleek and modern design, it features an aerodynamic body, LED lighting, and a comfortable step-through frame that gives it a sophisticated yet sporty look.</p><p>Powered by a 149cc liquid-cooled, fuel-injected engine, the PCX 150 delivers smooth acceleration and reliable performance while maintaining excellent fuel economy. Its smart key system, digital LCD display, and ample under-seat storage make it both convenient and practical for daily use.</p>', 135000, 'A', 3, 15, 1, 0.00, NULL, 7, 1, 'uploads/products/honda_pcx150.png', 0, NOW());

-- Insert Honda ADV 160 CBS
INSERT INTO `product_list` (`brand_id`, `category_id`, `name`, `models`, `available_colors`, `description`, `price`, `abc_category`, `reorder_point`, `max_stock`, `min_stock`, `unit_cost`, `supplier_id`, `lead_time_days`, `status`, `image_path`, `delete_flag`, `date_created`) VALUES
(9, 10, 'Honda ADV 160 CBS', 'ADV 160 CBS', 'Red, White, Black', '<p><strong>Honda ADV 160 CBS</strong></p><p>Bringing elegance and superiority to the next level, PCX160 lets Filipino riders to stand out on the road and ride with pride with its all-new premium and elegant design, improved driving performance with comfortable and spacious riding, and the latest technology and security features.</p>', 155000, 'A', 3, 15, 1, 0.00, NULL, 7, 1, 'uploads/products/honda_adv160_cbs.png', 0, NOW());

-- Insert Honda ADV 160 ABS
INSERT INTO `product_list` (`brand_id`, `category_id`, `name`, `models`, `available_colors`, `description`, `price`, `abc_category`, `reorder_point`, `max_stock`, `min_stock`, `unit_cost`, `supplier_id`, `lead_time_days`, `status`, `image_path`, `delete_flag`, `date_created`) VALUES
(9, 10, 'Honda ADV 160 ABS', 'ADV 160 ABS', 'Red, White, Black', '<p><strong>Honda ADV 160 ABS</strong></p><p>The ADV160 is now equipped with a new generation 157cc, 4-Valve, Liquid-Cooled, eSP+ Engine, offering advanced technology with 4-valve mechanism and low friction technologies to provide excellent power output and environmental performance (Fuel Efficient). It delivers a maximum power of 11.8 kW @ 8,500 rpm and a top torque of 14.7 Nm @ 6,500 rpm, which proves more than enough for a reliable ride that takes you from daily commuting to leisure trips.</p>', 165000, 'A', 3, 15, 1, 0.00, NULL, 7, 1, 'uploads/products/honda_adv160_abs.png', 0, NOW());

-- Update existing Honda RS125
UPDATE `product_list` SET 
  `name` = 'Honda RS125',
  `models` = 'RS 125',
  `description` = '<p><strong>Honda RS125</strong></p><p>The New RS125, designed too dominate the road with its bold, fresh look and enhanced racing image. This powerful ride combines performance and style, making it a true street leader and standout choice. With two aggressive color variants available - Matte Axis Gray Metallic and Victory Red</p>',
  `price` = 75000,
  `abc_category` = 'B',
  `available_colors` = 'Matte Axis Gray Metallic, Victory Red',
  `date_updated` = NOW()
WHERE `name` LIKE '%RS125%' AND `brand_id` = 9 AND `category_id` = 10;

-- Insert Honda Supra GTR 150
INSERT INTO `product_list` (`brand_id`, `category_id`, `name`, `models`, `available_colors`, `description`, `price`, `abc_category`, `reorder_point`, `max_stock`, `min_stock`, `unit_cost`, `supplier_id`, `lead_time_days`, `status`, `image_path`, `delete_flag`, `date_created`) VALUES
(9, 10, 'Honda Supra GTR 150', 'SUPRA GTR 150', 'Red, White, Black', '<p><strong>Honda Supra GTR 150</strong></p><p>Honda Supra GTR150 is equipped with a 6-Speed DOHC 4-Valve Liquid-Cooled Engine for maximum performance, great handling, and better fuel efficiency of 42 km/liter when riding in highways. It also has a LED Headlight that ensures safety and clear sight on the road, as well as a Full Digital Meter Panel for ease of information in determining speed and distance.</p>', 110000, 'A', 3, 15, 1, 0.00, NULL, 7, 1, 'uploads/products/honda_supra_gtr150.png', 0, NOW());

-- Insert Honda Giorno
INSERT INTO `product_list` (`brand_id`, `category_id`, `name`, `models`, `available_colors`, `description`, `price`, `abc_category`, `reorder_point`, `max_stock`, `min_stock`, `unit_cost`, `supplier_id`, `lead_time_days`, `status`, `image_path`, `delete_flag`, `date_created`) VALUES
(9, 10, 'Honda Giorno', 'GIORNO', 'Red, White, Black, Blue', '<p><strong>Honda Giorno</strong></p><p>The All-New Giorno+ is designed adapted to fashion-forward Filipino customers that perfectly blends modern classic design with exceptional performance and innovative features with its 125cc, 4-Valve, Liquid-Cooled, eSP+ Engine, making it perfect fit for those who value both style and substance. Setting a new standard for high-performance scooters with its impressive curves and advanced technology making every ride into #ClassThatLast.</p>', 90000, 'B', 5, 20, 2, 0.00, NULL, 7, 1, 'uploads/products/honda_giorno.png', 0, NOW());

-- Update existing Honda PCX160
UPDATE `product_list` SET 
  `name` = 'Honda PCX 160 ABS',
  `models` = 'PCX 160 ABS',
  `description` = '<p><strong>Honda PCX 160 ABS</strong></p><p>The Honda PCX 160 ABS is a premium maxi-scooter that blends elegant design, advanced technology, and powerful performance—perfect for both daily city rides and longer journeys. Equipped with a 157cc liquid-cooled, fuel-injected engine with eSP+ technology, it delivers smooth acceleration, impressive fuel efficiency, and a refined riding experience.</p><p>This model features Anti-Lock Braking System (ABS) and Honda Selectable Torque Control (HSTC) for enhanced safety and stability, especially on slippery roads. Its sleek LED headlight and taillight, fully digital instrument panel, and modern, aerodynamic body give it a premium and stylish appeal.</p>',
  `price` = 140000,
  `abc_category` = 'A',
  `available_colors` = 'Red, White, Black, Blue',
  `date_updated` = NOW()
WHERE `name` = 'Honda PCX160' AND `brand_id` = 9 AND `category_id` = 10;

-- Insert Honda PCX 160 CBS
INSERT INTO `product_list` (`brand_id`, `category_id`, `name`, `models`, `available_colors`, `description`, `price`, `abc_category`, `reorder_point`, `max_stock`, `min_stock`, `unit_cost`, `supplier_id`, `lead_time_days`, `status`, `image_path`, `delete_flag`, `date_created`) VALUES
(9, 10, 'Honda PCX 160 CBS', 'PCX 160 CBS', 'Red, White, Black, Blue', '<p><strong>Honda PCX 160 CBS</strong></p><p>The Honda PCX 160 CBS is a stylish and practical maxi-scooter designed for smooth and comfortable urban commuting. Powered by a 157cc liquid-cooled, fuel-injected engine with Honda\'s eSP+ technology, it delivers efficient performance and a refined riding experience.</p><p>Equipped with Combi Brake System (CBS), it automatically distributes braking force between the front and rear wheels for balanced stopping power and added safety. Its LED headlight and taillight, digital instrument panel, and elegant aerodynamic design give it a premium and modern look.</p>', 130000, 'A', 3, 15, 1, 0.00, NULL, 7, 1, 'uploads/products/honda_pcx160_cbs.png', 0, NOW());

-- Insert Honda Click 125 SE
INSERT INTO `product_list` (`brand_id`, `category_id`, `name`, `models`, `available_colors`, `description`, `price`, `abc_category`, `reorder_point`, `max_stock`, `min_stock`, `unit_cost`, `supplier_id`, `lead_time_days`, `status`, `image_path`, `delete_flag`, `date_created`) VALUES
(9, 10, 'Honda Click 125 SE', 'CLICK 125 SE', 'Red, White, Black, Blue', '<p><strong>Honda Click 125 SE</strong></p><p>The New Click125 showcasing a fresh design featuring striking new two-tone colors and dynamic stripes for the Click125 Standard Variant, while complemented by a sophisticated 3D Emblem exclusive to Special Edition Variant.</p>', 87000, 'B', 5, 20, 2, 0.00, NULL, 7, 1, 'uploads/products/honda_click125_se.png', 0, NOW());

-- Insert Honda TMX Alpha
INSERT INTO `product_list` (`brand_id`, `category_id`, `name`, `models`, `available_colors`, `description`, `price`, `abc_category`, `reorder_point`, `max_stock`, `min_stock`, `unit_cost`, `supplier_id`, `lead_time_days`, `status`, `image_path`, `delete_flag`, `date_created`) VALUES
(9, 10, 'Honda TMX Alpha', 'TMX ALPHA', 'Red, White, Black', '<p><strong>Honda TMX Alpha</strong></p><p>TMX125 Alpha is powered by the legendary Overhead Valve (OHV) engine, making it unique from other motorbikes. This OHV engine uses a push rod to balance acceleration and control for hours of easy and hassle-free operations while being fuel-efficient at 62.5km/L at 45Km/H constant speed. And to meet the customers\' requirement for best balance of engine power and acceleration, the rear sprocket is improved from 44T to 38T, making it perfect bike for daily commuting usage.</p>', 78000, 'B', 5, 20, 2, 0.00, NULL, 7, 1, 'uploads/products/honda_tmx_alpha.png', 0, NOW());

-- Insert Honda TMX Supremo
INSERT INTO `product_list` (`brand_id`, `category_id`, `name`, `models`, `available_colors`, `description`, `price`, `abc_category`, `reorder_point`, `max_stock`, `min_stock`, `unit_cost`, `supplier_id`, `lead_time_days`, `status`, `image_path`, `delete_flag`, `date_created`) VALUES
(9, 10, 'Honda TMX Supremo', 'TMX SUPREMO', 'Red, White, Black', '<p><strong>Honda TMX Supremo</strong></p><p>The 3rd Generation TMX Supremo now boasts of enhanced features, such as its new and improved engine that maintains its fuel efficiency at 62km/L. It also comes with 18-inch tires, as well as a high ground clearance and a seat height that ensures the riders\' comfort despite the impact of rough roads. This makes the 3rd Generation TMX Supremo better suited for heavy-duty rides and climbs on demanding roads, even when carrying loads.</p>', 95000, 'B', 5, 20, 2, 0.00, NULL, 7, 1, 'uploads/products/honda_tmx_supremo.png', 0, NOW());

-- Update existing Honda Wave 110
UPDATE `product_list` SET 
  `name` = 'Honda Wave RSX (DISC)',
  `models` = 'WAVE RSX (DISC)',
  `description` = '<p><strong>Honda Wave RSX (DISC)</strong></p><p>The Wave RSX turns your riding experience into something remarkable. With its newest sporty dynamic design bringing out impressive stickers, functional features providing convenience, plus fuel efficiency upto 69.5 km/l powered by PGM-FI, this underbone lets you stands out wherever you go.</p>',
  `price` = 62500,
  `abc_category` = 'C',
  `available_colors` = 'Red, White, Black',
  `date_updated` = NOW()
WHERE `name` = 'Honda Wave 110' AND `brand_id` = 9 AND `category_id` = 10;

-- Insert Honda Wave RSX Drum
INSERT INTO `product_list` (`brand_id`, `category_id`, `name`, `models`, `available_colors`, `description`, `price`, `abc_category`, `reorder_point`, `max_stock`, `min_stock`, `unit_cost`, `supplier_id`, `lead_time_days`, `status`, `image_path`, `delete_flag`, `date_created`) VALUES
(9, 10, 'Honda Wave RSX Drum', 'WAVE RSX DRUM', 'Red, White, Black', '<p><strong>Honda Wave RSX Drum</strong></p><p>The Wave RSX turns your riding experience into something remarkable. With its newest sporty dynamic design bringing out impressive stickers, functional features providing convenience, plus fuel efficiency upto 69.5 km/l powered by PGM-FI, this underbone lets you stands out wherever you go.</p>', 60000, 'C', 5, 20, 2, 0.00, NULL, 7, 1, 'uploads/products/honda_wave_rsx_drum.png', 0, NOW());

-- Insert Honda Winner X Premium
INSERT INTO `product_list` (`brand_id`, `category_id`, `name`, `models`, `available_colors`, `description`, `price`, `abc_category`, `reorder_point`, `max_stock`, `min_stock`, `unit_cost`, `supplier_id`, `lead_time_days`, `status`, `image_path`, `delete_flag`, `date_created`) VALUES
(9, 10, 'Honda Winner X Premium', 'WINNER X PREMIUM', 'Red, White, Black', '<p><strong>Honda Winner X Premium</strong></p><p>It boasts outstanding performance through its 150cc, DOHC, 6-Speed, Liquid-Cooled Engine along with worthwhile features: USB Charging Port, Smart Key System, All LED Lighting System, Digital Meter Panel, Bank Angle Sensor, Assist & Slipper Clutch, Colored Cast Wheel, and Anti-Lock Braking System available in ABS Racing and ABS Premium variants only.</p>', 180000, 'A', 2, 10, 1, 0.00, NULL, 7, 1, 'uploads/products/honda_winner_x_premium.png', 0, NOW());

-- Insert Honda Winner X Standard
INSERT INTO `product_list` (`brand_id`, `category_id`, `name`, `models`, `available_colors`, `description`, `price`, `abc_category`, `reorder_point`, `max_stock`, `min_stock`, `unit_cost`, `supplier_id`, `lead_time_days`, `status`, `image_path`, `delete_flag`, `date_created`) VALUES
(9, 10, 'Honda Winner X Standard', 'WINNER X STANDARD', 'Red, White, Black', '<p><strong>Honda Winner X Standard</strong></p><p>The All-New Winner X that is designed to let you #RideLikeAChampion is now here! This sports cub is sure to become one of another favorite among Filipino riders with its aggressive sports styling, powerful engine and advanced features.</p>', 160000, 'A', 2, 10, 1, 0.00, NULL, 7, 1, 'uploads/products/honda_winner_x_standard.png', 0, NOW());

-- Now let's add detailed specifications for each motorcycle
-- Get the product IDs for the motorcycles we just added/updated
SET @click125i_id = (SELECT id FROM product_list WHERE name = 'Honda Click 125i' AND brand_id = 9 LIMIT 1);
SET @click160_id = (SELECT id FROM product_list WHERE name = 'Honda Click 160' AND brand_id = 9 LIMIT 1);
SET @crf150l_id = (SELECT id FROM product_list WHERE name = 'Honda CRF150L' AND brand_id = 9 LIMIT 1);
SET @dio_id = (SELECT id FROM product_list WHERE name = 'Honda Dio' AND brand_id = 9 LIMIT 1);
SET @pcx150_id = (SELECT id FROM product_list WHERE name = 'Honda PCX 150' AND brand_id = 9 LIMIT 1);
SET @adv160_cbs_id = (SELECT id FROM product_list WHERE name = 'Honda ADV 160 CBS' AND brand_id = 9 LIMIT 1);
SET @adv160_abs_id = (SELECT id FROM product_list WHERE name = 'Honda ADV 160 ABS' AND brand_id = 9 LIMIT 1);
SET @rs125_id = (SELECT id FROM product_list WHERE name = 'Honda RS125' AND brand_id = 9 LIMIT 1);
SET @supra_gtr150_id = (SELECT id FROM product_list WHERE name = 'Honda Supra GTR 150' AND brand_id = 9 LIMIT 1);
SET @giorno_id = (SELECT id FROM product_list WHERE name = 'Honda Giorno' AND brand_id = 9 LIMIT 1);
SET @pcx160_abs_id = (SELECT id FROM product_list WHERE name = 'Honda PCX 160 ABS' AND brand_id = 9 LIMIT 1);
SET @pcx160_cbs_id = (SELECT id FROM product_list WHERE name = 'Honda PCX 160 CBS' AND brand_id = 9 LIMIT 1);
SET @click125_se_id = (SELECT id FROM product_list WHERE name = 'Honda Click 125 SE' AND brand_id = 9 LIMIT 1);
SET @tmx_alpha_id = (SELECT id FROM product_list WHERE name = 'Honda TMX Alpha' AND brand_id = 9 LIMIT 1);
SET @tmx_supremo_id = (SELECT id FROM product_list WHERE name = 'Honda TMX Supremo' AND brand_id = 9 LIMIT 1);
SET @wave_rsx_disc_id = (SELECT id FROM product_list WHERE name = 'Honda Wave RSX (DISC)' AND brand_id = 9 LIMIT 1);
SET @wave_rsx_drum_id = (SELECT id FROM product_list WHERE name = 'Honda Wave RSX Drum' AND brand_id = 9 LIMIT 1);
SET @winner_x_premium_id = (SELECT id FROM product_list WHERE name = 'Honda Winner X Premium' AND brand_id = 9 LIMIT 1);
SET @winner_x_standard_id = (SELECT id FROM product_list WHERE name = 'Honda Winner X Standard' AND brand_id = 9 LIMIT 1);

-- Insert specifications for Honda Click 125i
INSERT INTO `motorcycle_specifications` (`product_id`, `make`, `model`, `transmission`, `engine_type`, `displacement`, `seat_height`, `brake_system_front`, `brake_system_rear`, `fuel_capacity`, `front_tire`, `rear_tire`, `wheels_type`, `starting_system`, `overall_dimensions`, `ground_clearance`, `fuel_system`, `headlight`, `taillight`, `maximum_power`, `maximum_torque`, `fuel_consumption`, `compression_ratio`, `ignition_type`, `bore_stroke`, `engine_oil_capacity`, `battery_type`, `gear_shift_pattern`, `suspension_front`, `suspension_rear`, `wheelbase`, `curb_weight`, `frame_type`, `category`) VALUES
(@click125i_id, 'HONDA - The Power of Dreams', 'HONDA Click 125i', 'Automatic', '4-Stroke, 2-Valve, SOHC, Liquid-Cooled, eSP', '125 cc', '769 mm', 'Hydraulic Disc Brake', 'Mechanical Leading Trailing', '5.5 L', '80/90 - 14 M/C 40P', '90/90 - 14 M/C 46P', 'Cast Wheel', 'Electric / DECOMP', '1,919 x 679 x 1,062 (mm)', '132 mm', 'PGM-Fi', 'LED', 'LED', '8.2 kW @ 8,500 rpm', '10.8 Nm @ 5,000 rpm', '50.3 km/L (WMTC Test Method)', '11.0 : 1', 'Full Transisterized', '52.4 x 57.9 (mm)', '0.9L', '12V - 5Ah (MF-WET)', 'Automatic (V-Matic)', 'Telescopic', 'Unit Swing', '1,280 mm', '112 Kg', 'Scooter', 'Pang Araw-Araw');

-- Insert specifications for Honda Click 160
INSERT INTO `motorcycle_specifications` (`product_id`, `make`, `model`, `transmission`, `engine_type`, `displacement`, `seat_height`, `brake_system_front`, `brake_system_rear`, `fuel_capacity`, `front_tire`, `rear_tire`, `wheels_type`, `starting_system`, `overall_dimensions`, `ground_clearance`, `fuel_system`, `ignition_type`, `maximum_power`, `maximum_torque`, `engine_oil_capacity`, `category`) VALUES
(@click160_id, 'HONDA - The Power of Dreams', 'HONDA CLICK 160', 'Automatic', '4-Stroke, 4-Valve, SOHC, Liquid Cooled, eSP+', '157cc', '778 mm', 'Hydraulic Disc', 'Mechanical Leading Trailing', '5.5 L', '100/80-14 M/C 48P (Tubeless)', '120/70-14 M/C 61P (Tubeless)', 'Cast Wheel', 'Electric (ACG Starter)', '1,929 x 678 x 1,062 (mm)', '138mm', 'PGM-Fi', 'Full Transisterized', '11.3kW @ 8,500rpm', '13.8Nm @ 7,000rpm', '0.9L', 'Pang Araw-Araw');

-- Insert specifications for Honda CRF150L
INSERT INTO `motorcycle_specifications` (`product_id`, `make`, `model`, `gear_shift_pattern`, `engine_type`, `seat_height`, `brake_system_front`, `brake_system_rear`, `fuel_capacity`, `displacement`, `front_tire`, `rear_tire`, `wheels_type`, `starting_system`, `overall_dimensions`, `ground_clearance`, `fuel_system`, `fuel_consumption`, `category`) VALUES
(@crf150l_id, 'HONDA - The Power of Dreams', 'HONDA CRF150L', 'Manual (1-N-2-3-4-5)', '4-Stroke, 2 Valves, SOHC, Air-Cooled', '863 mm', 'Hydraulic Disc Brake', 'Hydraulic Disc Brake', '7.2 L', '149 cc', '70/100 - 21', '90/100 - 18', 'Spoke', 'Electric & Kick', '2,119 x 793 x 1,153 (mm)', '285 mm', 'PGM-Fi', '45.5 km/L', 'Adventure');

-- Insert specifications for Honda Dio
INSERT INTO `motorcycle_specifications` (`product_id`, `make`, `model`, `transmission`, `engine_type`, `seat_height`, `brake_system_front`, `brake_system_rear`, `fuel_capacity`, `displacement`, `front_tire`, `rear_tire`, `wheels_type`, `overall_dimensions`, `ground_clearance`, `fuel_system`, `engine_oil_capacity`, `category`) VALUES
(@dio_id, 'HONDA - The Power of Dreams', 'HONDA DIO', 'Automatic', '4-Stroke, SOHC, Air-Cooled', '765 mm', 'Mechanical Drum Brake', 'Mechanical Drum Brake', '5.3 Liters', '109 cc', '90 / 100 – 10 53J (Tubeless)', '90 / 100 – 10 53J (Tubeless)', 'Steel Rims', '1,781 x 710 x 1,133 (mm)', '158 mm', 'Carburetor', '0.8 Liter', 'Pang Araw-Araw');

-- Insert specifications for Honda PCX 150
INSERT INTO `motorcycle_specifications` (`product_id`, `make`, `model`, `transmission`, `frame`, `transmission_type`, `ignition_type`, `bore_stroke`, `engine_type`, `category`, `seat_height`, `brake_system_front`, `brake_system_rear`, `fuel_capacity`, `front_tire`, `rear_tire`, `wheelbase`) VALUES
(@pcx150_id, 'HONDA - The Power of Dreams', 'HONDA PCX 150', 'Automatic', 'Scooter', 'Honda V-Matic belt-converter automatic transmission', 'Full transistorized ignition', '58.0mm x 57.9mm', '153cc liquid-cooled single-cylinder four-stroke', 'Automatic', '29.9 inches', 'Single 220mm disc with three-piston caliper and CBS', 'Drum with CBS', '8 Liters', '90/90-14', '100/90-14', '51.8 inches');

-- Insert specifications for Honda ADV 160 CBS
INSERT INTO `motorcycle_specifications` (`product_id`, `make`, `model`, `transmission`, `fuel_capacity`, `ignition_type`, `engine_type`, `seat_height`, `brake_system_front`, `brake_system_rear`, `displacement`, `front_tire`, `rear_tire`, `wheels_type`, `overall_dimensions`, `ground_clearance`, `fuel_system`, `engine_oil_capacity`, `category`) VALUES
(@adv160_cbs_id, 'HONDA - The Power of Dreams', 'HONDA PCX160 - ABS (2021)', 'Automatic', '8.0 L', 'Full Transistorized', '4-Stroke, Liquid-Cooled, 4-Valve, Single Overhead Cam (SOHC), eSP+ Engine', '764 mm', 'Disc Brake with Anti-Lock Braking System (ABS)', 'Disc Brake with Anti-Lock Braking System (ABS)', '157 cc', '110/70 – 14 MC (Tubeless)', '130/70 – 13 MC (Tubeless)', 'Cast Wheel', '1,936 x 742 x 1,108 (mm)', '134 mm', 'PGM-Fi', '1.0 L', 'Adventure');

-- Insert specifications for Honda ADV 160 ABS
INSERT INTO `motorcycle_specifications` (`product_id`, `make`, `model`, `engine_type`, `displacement`, `starting_system`, `ignition_system`, `brake_type_front`, `brake_type_rear`, `tire_size_front`, `tire_size_rear`, `wheel_type`, `overall_dimensions`, `seat_height`, `ground_clearance`, `fuel_tank_capacity`, `fuel_system`, `battery_type`, `fuel_consumption`, `maximum_power`, `category`) VALUES
(@adv160_abs_id, 'HONDA - The Power of Dreams', 'HONDA ADV 160 ABS', '4-Stroke, 4-Valve, SOHC, Liquid-Cooled, eSP+', '157 cc', 'Electric (ACG Starter)', 'Full Transisterized', 'Hydraulic Disc with ABS', 'Hydraulic Disc', '110/80-14M/C 53P (Tubeless)', '130/70-13M/C 57P (Tubeless)', 'Cast Wheel', '1,950 x 763 x 1,196 (mm)', '780 mm', '165 mm', '8.1 L', 'PGM-FI', '12V - 5Ah (MF-WET)', '45.0 km/L (WMTC Test Method)', '11.8 kW @ 8,500 rpm', 'Adventure');

-- Insert specifications for Honda RS125
INSERT INTO `motorcycle_specifications` (`product_id`, `make`, `model`, `engine_type`, `displacement`, `bore_stroke`, `compression_ratio`, `maximum_power`, `maximum_torque`, `starting_system`, `engine_oil_capacity`, `fuel_system`, `fuel_consumption`, `transmission_type`, `gear_shift_pattern`, `suspension_front`, `suspension_rear`, `brake_type_front`, `brake_type_rear`, `tire_size_front`, `tire_size_rear`, `wheel_type`, `overall_dimensions`, `curb_weight`, `seat_height`, `wheelbase`, `ground_clearance`, `fuel_tank_capacity`, `ignition_system`, `battery_type`, `category`) VALUES
(@rs125_id, 'HONDA - The Power of Dreams', 'HONDA RS125', '4-Stroke, SOHC, Air-Cooled', '125 cc', '52.4 x 57.9 (mm)', '9.3 : 1', '7.12 kW @ 7,500 rpm', '9.55 N.m @ 6,500 rpm', 'Electric & Kick', '0.9L', 'PGM-Fi', '67.5 Km/L', 'Manual, 4-Speed, Constant Mesh', 'Rotary (N-1-2-3-4)', 'Telescopic', 'Twin', 'Hydraulic Disc', 'Drum', '70/90 - 17 M/C 38P', '80/90 - 17 M/C 50P', 'Spoke', '1,909 x 685 x987 (mm)', '104 Kg', '767 mm', '1,228 mm', '135 mm', '3.9 L', 'Full Transisterized', '12V - 3 Ah MF-Wet', 'Sport');

-- Insert specifications for Honda Supra GTR 150
INSERT INTO `motorcycle_specifications` (`product_id`, `make`, `model`, `transmission`, `fuel_capacity`, `transmission_type`, `ignition_type`, `bore_stroke`, `engine_type`, `seat_height`, `brake_system_front`, `brake_system_rear`, `displacement`, `front_suspension`, `engine_oil_capacity`, `front_tire`, `wheels_type`, `starting_system`, `maximum_power`, `maximum_torque`, `overall_dimensions`, `fuel_system`, `headlight`, `category`) VALUES
(@supra_gtr150_id, 'HONDA - The Power of Dreams', 'HONDA Supra GTR 150', 'Manual', '4.5 L', 'Manual 6-Speed Constant Mesh', 'Full Transistorized', '57.3 x 57.8 (mm)', '4-Stroke, 4 Valves, DOHC, Liquid-Cooled w/ Auto Fan', '786 mm', 'Hydraulic Disc', 'Hydraulic Disc', '150', 'Telescopic', '1.1 L', '90/80 - 17MC 46P', 'Cast Wheel', 'Electric/Kick', '11.5 kW @ 9,000 rpm', '13.6 N.m @ 6,700 rpm', '2,021 x 725 x 1,105 (mm)', 'PGM-FI', 'Dual Layer Led', 'Sport');

-- Insert specifications for Honda Giorno
INSERT INTO `motorcycle_specifications` (`product_id`, `make`, `model`, `transmission`, `fuel_capacity`, `transmission_type`, `bore_stroke`, `engine_type`, `seat_height`, `brake_system_front`, `brake_system_rear`, `displacement`, `max_output`, `front_suspension`, `engine_oil_capacity`, `front_tire`, `wheels_type`, `starting_system`, `maximum_torque`, `compression_ratio`, `overall_dimensions`, `wheelbase`, `fuel_system`, `category`) VALUES
(@giorno_id, 'HONDA - The Power of Dreams', 'HONDA THE ALL-NEW GIORNO', 'Automatic', '47.0 km/L based on WMTC mode', 'Automatic (V-Matic)', '53.5 x 55.5 mm', '4-Stroke, 4-Valve, SOHC, Liquid-Cooled, eSP+', '780 mm', 'Hydraulic Disc', 'Mechanical Leading Trailing', '125 cc', '8.49 kW @ 8,750 rpm', 'Telescopic', '0.9 L', '100/90-12 59J (Tubeless)', 'Cast Wheel', 'Electric (ACG Starter)', '11.6 Nm @ 5,250 rpm', '11.5 : 1', '1,902 x 681 x 1,144 mm', '1,312 mm', 'PGM-FI', 'Pang Araw-Araw');

-- Insert specifications for Honda PCX 160 ABS
INSERT INTO `motorcycle_specifications` (`product_id`, `make`, `model`, `transmission`, `transmission_type`, `ignition_type`, `bore_stroke`, `engine_type`, `brake_system_front`, `brake_system_rear`, `fuel_capacity`, `displacement`, `engine_oil_capacity`, `wheels_type`, `fuel`, `maximum_power`, `maximum_torque`, `compression_ratio`, `overall_dimensions`, `wheelbase`, `minimum_ground_clearance`, `fuel_system`, `category`) VALUES
(@pcx160_abs_id, 'HONDA - The Power of Dreams', 'HONDA The ALL-New PCX160 ABS', 'Automatic', 'Automatic', 'Full Transisterized', '60.0 x 55.5 mm', '4-Stroke, 4-Valve, SOHC, Liquid-Cooled, eSP+', 'Hydraulic Disk', 'Hydraulic Disk', '8.1 L', '157 cc', '0.9 L', 'Cast Wheel', 'PGM-FI', '11.8 kW @ 8,500 rpm', '14.7 Nm @ 6,500 rpm', '12.0 : 1', '1,936 x 742 x 1,123 (mm)', '1,313 mm', '134 mm', 'PGM-FI', 'Premium');

-- Insert specifications for Honda PCX 160 CBS
INSERT INTO `motorcycle_specifications` (`product_id`, `make`, `model`, `transmission`, `transmission_type`, `ignition_type`, `bore_stroke`, `engine_type`, `seat_height`, `brake_system_front`, `brake_system_rear`, `fuel_capacity`, `displacement`, `front_suspension`, `engine_oil_capacity`, `wheels_type`, `starting_system`, `fuel`, `maximum_power`, `maximum_torque`, `compression_ratio`, `rear_suspension`, `rear_tire`, `overall_dimensions`, `wheelbase`, `minimum_ground_clearance`, `fuel_system`, `category`) VALUES
(@pcx160_cbs_id, 'HONDA - The Power of Dreams', 'HONDA The ALL-New PCX160 CBS', 'Automatic', 'Automatic', 'Full Transisterized', '60.0 x 55.5 mm', '4-Stroke, 4-Valve, SOHC, Liquid-Cooled, eSP+', '764 mm', 'Hydraulic Disk', 'Hydraulic Disk', '8.1 L', '157 cc', 'Telescopic', '0.9 L', 'Cast Wheel', 'Electric', 'PGM-FI', '11.8 kW @ 8,500 rpm', '14.7 Nm @ 6,500 rpm', '12.0 : 1', 'Twin Shock', '130/70 – 13MC 63P (Tubeless)', '1,936 x 742 x 1,123 (mm)', '1,313 mm', '134 mm', 'PGM-FI', 'Premium');

-- Insert specifications for Honda Click 125 SE
INSERT INTO `motorcycle_specifications` (`product_id`, `make`, `model`, `engine_type`, `displacement`, `starting_system`, `ignition_type`, `brake_type_front`, `brake_type_rear`, `tire_size_front`, `tire_size_rear`, `wheel_type`, `overall_dimensions`, `curb_weight`, `seat_height`, `ground_clearance`, `fuel_tank_capacity`, `fuel_system`, `battery_type`, `maximum_power`, `maximum_torque`, `bore_stroke`, `fuel_consumption`, `compression_ratio`, `engine_oil_capacity`, `transmission_type`, `suspension_type_front`, `suspension_type_rear`, `wheelbase`, `category`) VALUES
(@click125_se_id, 'HONDA - The Power of Dreams', 'HONDA Click 125 SE', '4-Stroke, 2-Valve, SOHC, Liquid-Cooled, eSP', '125 cc', 'Electric', 'Full Transisterized', 'Hydraulic Disc', 'Mechanical Leading Trailing Drum', '90/80 - 14 M/C 43P (Tubeless)', '100/80 - 14 M/C 48P (Tubeless)', 'Cast Wheel', '1,918 x 679 x 1,066 (mm)', '112 Kg', '769 mm', '131 mm', '5.5 L', 'PGM-FI', '12V - 5Ah (MF-WET)', '8.2 kW @ 8,500 rpm', '10.8 Nm @ 5,000 rpm', '52.4 x 57.9 (mm)', '50.3 km/L (WMTC Test Method)', '11.0 : 1', '0.9L', 'Automatic (V-Matic)', 'Telescopic', 'Unit Swing', '1,280 mm', 'Pang Araw-Araw');

-- Insert specifications for Honda TMX Alpha
INSERT INTO `motorcycle_specifications` (`product_id`, `make`, `model`, `engine_type`, `displacement`, `starting_system`, `ignition_system`, `brake_type_front`, `brake_type_rear`, `tire_size_front`, `tire_size_rear`, `wheel_type`, `overall_dimensions`, `curb_weight`, `seat_height`, `ground_clearance`, `fuel_tank_capacity`, `fuel_system`, `battery_type`, `engine_oil_capacity`, `gear_shift_pattern`, `fuel_consumption`, `category`) VALUES
(@tmx_alpha_id, 'HONDA - The Power of Dreams', 'HONDA TMX Alpha', '4-Stroke, Over Head Valve (OHV)', '125 cc', 'Electric & Kick', 'AC - CDI Magnetic', 'Mechanical Leading Trailing (Drum Brake)', 'Mechanical Leading Trailing (Drum Brake)', '2.50 x 18 40L', '2.75 x 18 48L', 'Spoke', '1,904 x 754 x 1,026 (mm)', '113 Kg', '759 mm', '156 mm', '8.6 L', 'Carburetor', '12 V - 5 Ah - MF - WET', '1.1 L', '5-Speed, Constant Mesh (N-1-2-3-4-5)', '62.5km/L at 45Km/H constant speed', 'Pang Negosyo');

-- Insert specifications for Honda TMX Supremo
INSERT INTO `motorcycle_specifications` (`product_id`, `make`, `model`, `frame`, `gear_shift_pattern`, `transmission_type`, `ignition_type`, `bore_stroke`, `engine_type`, `category`, `dry_weight`, `seat_height`, `brake_system_front`, `brake_system_rear`, `fuel_capacity`, `displacement`, `front_suspension`, `engine_oil_capacity`, `front_tire`, `wheels_type`, `starting_system`, `fuel`, `maximum_power`, `maximum_torque`, `rear_suspension`, `rear_tire`, `overall_dimensions`, `wheelbase`, `minimum_ground_clearance`, `fuel_system`, `fuel_consumption`) VALUES
(@tmx_supremo_id, 'HONDA - The Power of Dreams', 'HONDA TMX Supremo (150 - 3rd Gen)', 'Backbone', 'Manual / 1-2-3-4-5', 'Manual, 5-Speed Constant Mesh', 'DC-CDI', '57.3mm x 57.8mm', '4 Stroke, OHC, Air Cooled', 'Pang Negosyo', '120 Kg', '771 mm', 'Mechanical Leading Trailing', 'Mechanical Leading Trailing', '14.3 Liters (Reserve 2.0 L)', '149.2cc', 'Telescopic Fork', '1.0 Liter', '80/100 - 18M/C 47P', 'Spoke', 'Kick / Electric Starter', 'Unleaded Gasoline (93+ or above octane rating)', '7.85kW (10.7Ps) @ 7,000rpm', '11.58Nm (1.18kgfm) @5,000rpm', 'Twin', '90/90 - 18M/C 51P', '2,037 mm x 778 mm x 1,068 mm', '1,306 mm', '163 mm', 'Carburator', '62km/L');

-- Insert specifications for Honda Wave RSX (DISC)
INSERT INTO `motorcycle_specifications` (`product_id`, `make`, `model`, `frame`, `fuel_capacity`, `gear_shift_pattern`, `transmission_type`, `ignition_type`, `bore_stroke`, `engine_type`, `category`, `dry_weight`, `seat_height`, `brake_system_front`, `brake_system_rear`, `displacement`, `front_suspension`, `engine_oil_capacity`, `front_tire`, `wheels_type`, `starting_system`, `maximum_power`, `maximum_torque`, `compression_ratio`, `rear_suspension`, `rear_tire`, `overall_dimensions`, `wheelbase`, `minimum_ground_clearance`, `fuel_system`, `fuel_consumption`) VALUES
(@wave_rsx_disc_id, 'HONDA - The Power of Dreams', 'HONDA Wave 110R Disk', 'Underbone', '4.0L', 'N-1-2-3-4-N (Rotary)', 'Manual, 4-Speed Constant Mesh', 'Full Transisterized', '50.0 x 55.6 (mm)', '4-Stroke, SOHC, Air-Cooled', 'Pang Araw-Araw', '99kg', '760mm', 'Hydraulic Disc', 'Mechanical Leading Trailing', '109cc', 'Telescopic', '1.0L', '70/90-17M/C 38P (Tube Type)', 'Spoke', 'Electric / Kick', '6.46 kW @ 7,500 rpm', '8.70 N.m @ 6,000 rpm', '9.3:1', 'Twin Shock', '80/90-17M/C 50P (Tube Type)', '1,921 x 709 x 1,081 mm', '1,227mm', '135mm', 'PGM-Fi', '69.5 km/l');

-- Insert specifications for Honda Wave RSX Drum
INSERT INTO `motorcycle_specifications` (`product_id`, `make`, `model`, `frame`, `fuel_capacity`, `gear_shift_pattern`, `transmission_type`, `ignition_type`, `bore_stroke`, `engine_type`, `category`, `dry_weight`, `seat_height`, `brake_system_front`, `brake_system_rear`, `displacement`, `front_suspension`, `engine_oil_capacity`, `front_tire`, `wheels_type`, `starting_system`, `maximum_power`, `maximum_torque`, `compression_ratio`, `rear_suspension`, `rear_tire`, `overall_dimensions`, `wheelbase`, `minimum_ground_clearance`, `fuel_system`, `fuel_consumption`) VALUES
(@wave_rsx_drum_id, 'HONDA - The Power of Dreams', 'HONDA Wave 110 Drum', 'Underbone', '4.0L', 'N-1-2-3-4-N (Rotary)', 'Manual, 4-Speed Constant Mesh', 'Full Transisterized', '50.0 x 55.6 (mm)', '4-Stroke, SOHC, Air-Cooled', 'Pang Araw-Araw', '94 kg', '760mm', 'Mechanical Leading Trailing', 'Mechanical Leading Trailing', '109cc', 'Telescopic', '1.0L', '70/90-17M/C 38P (Tube Type)', 'Spoke', 'Electric / Kick', '6.46 kW @ 7,500 rpm', '8.70 N.m @ 6,000 rpm', '9.3:1', 'Twin Shock', '80/90-17M/C 50P (Tube Type)', '1,921 x 709 x 1,081 mm', '1,227mm', '135mm', 'PGM-Fi', '69.5 km/l');

-- Insert specifications for Honda Winner X Premium
INSERT INTO `motorcycle_specifications` (`product_id`, `make`, `model`, `engine_type`, `displacement`, `bore_stroke`, `starting_system`, `ignition_system`, `transmission_type`, `suspension_front`, `suspension_rear`, `brake_type_front`, `brake_type_rear`, `tire_size_front`, `tire_size_rear`, `wheel_type`, `wheel_base`, `overall_dimensions`, `curb_weight`, `seat_height`, `ground_clearance`, `fuel_tank_capacity`, `fuel_system`, `battery_type`, `engine_oil_capacity`, `maximum_power`, `maximum_torque`, `gear_shift_pattern`, `compression_ratio`, `fuel_consumption`, `category`) VALUES
(@winner_x_premium_id, 'HONDA - The Power of Dreams', 'HONDA Winner X Premium', '4-Stroke, 4-Valve, DOHC, Liquid-Cooled', '149cc', '57.3 x 57.8 mm', 'Electric', 'Full Transisterized', 'Manual, 6-speed constant mesh', 'Telescopic', 'Single Cylindrical Suspension', 'Hydraulic Disc with ABS', 'Hydraulic Disc', '90/80-17M/C 46P (Tubeless)', '120/70-17M/C 58P (Tubeless)', 'Cast Wheel', '1,278 mm', '2,019 x 725 x 1,104 mm', '122 kg', '795 mm', '151 mm', '4.5 L', 'PGM-FI', '12V 5Ah MF-WET', '1.3 L', '11.5kW @ 9,000rpm', '13.5Nm @ 7,000rpm', '1-N-2-3-4-5-6 (Down Up)', '11.3 : 1', '52.3 km/L (WMTC Test Method)', 'Sport');

-- Insert specifications for Honda Winner X Standard
INSERT INTO `motorcycle_specifications` (`product_id`, `make`, `model`, `transmission`, `transmission_type`, `ignition_type`, `bore_stroke`, `engine_type`, `seat_height`, `displacement`, `front_suspension`, `engine_oil_capacity`, `front_tire`, `wheels_type`, `maximum_power`, `maximum_torque`, `rear_suspension`, `rear_tire`, `overall_dimensions`, `wheelbase`, `category`) VALUES
(@winner_x_standard_id, 'HONDA - The Power of Dreams', 'HONDA WINNER X', 'Manual', 'Manual, 6-speed constant mesh', 'Full Transisterized', '57.3 x 57.8 mm', '4-Stroke, 4-Valve, DOHC, Liquid-Cooled', '795 mm', '149cc', 'Telescopic', '1.3 L', '90/80-17M/C 46P (Tubeless)', 'Cast Wheel', '11.5kW @ 9,000rpm', '13.5Nm @ 7,000rpm', 'Single Cylindrical Suspension', '120/70-17M/C 58P (Tubeless)', '2,019 x 725 x 1,104 mm', '1,278 mm', 'Sport');

-- Add stock entries for all new motorcycles
INSERT INTO `stock_list` (`product_id`, `quantity`, `type`, `date_created`) 
SELECT id, 5, 1, NOW() 
FROM product_list 
WHERE brand_id = 9 AND category_id = 10 AND name IN (
    'Honda Click 160', 'Honda CRF150L', 'Honda PCX 150', 'Honda ADV 160 CBS', 
    'Honda ADV 160 ABS', 'Honda Supra GTR 150', 'Honda Giorno', 'Honda PCX 160 CBS', 
    'Honda Click 125 SE', 'Honda TMX Alpha', 'Honda TMX Supremo', 'Honda Wave RSX Drum', 
    'Honda Winner X Premium', 'Honda Winner X Standard'
);

-- Update existing stock for updated motorcycles
UPDATE stock_list 
SET quantity = 5, date_created = NOW() 
WHERE product_id IN (
    SELECT id FROM product_list 
    WHERE brand_id = 9 AND category_id = 10 AND name IN (
        'Honda Click 125i', 'Honda Dio', 'Honda RS125', 'Honda PCX 160 ABS', 'Honda Wave RSX (DISC)'
    )
);

COMMIT;

