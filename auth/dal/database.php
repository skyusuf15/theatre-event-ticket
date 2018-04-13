<?php

// DATABASE connection script

// database Connection variables
define('HOST', 'localhost'); // Database host name ex. localhost
define('USER', 'root'); // Database user. ex. root ( if your on local server)
define('PASSWORD', ''); // Database user password  (if password is not set for user then keep it empty )
define('DATABASE', 'electronic_ticket_system'); // Database name (you can change DATABASE name to what you desire)
define('CHARSET', 'utf8');

function createDB(){
    try{

        //connect to mysql
        $link = mysql_connect(HOST,USER,PASSWORD);

        if(!$link){
            die('Could not connect: ' .mysql_error());
        }

        if(!mysql_select_db(DATABASE, $link)){
            $sql = "CREATE DATABASE IF NOT EXISTS ".DATABASE."";  

            if(mysql_query($sql, $link)){
                mysql_select_db(DATABASE, $link); //select db to use

                // -- Table structure for table `base_page_type` --------------
                $sql = "CREATE TABLE IF NOT EXISTS `base_page_type` 
                (
                `page_type_id` int(11) NOT NULL AUTO_INCREMENT,
                `page_type_name` varchar(50) NOT NULL,
                `custom_type` varchar(50) NOT NULL,
                `page_type_icon` varchar(50) NOT NULL,
                `page_type_order` int(11) NOT NULL,
                `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`page_type_id`),
                UNIQUE KEY `page_type_id` (`page_type_id`)
                )" ;
                mysql_query($sql, $link); //run query
                // -- Dumping data for table `base_page_type`
                $sql = "INSERT INTO `base_page_type` (`page_type_id`, `page_type_name`, `custom_type`, `page_type_icon`, `page_type_order`) VALUES
                (1, 'access_control', 'Access Control', 'widgets', 2),
                (2, 'ticket_setup', 'Ticket Setup', 'swap_calls', 3),
                (3, 'auth', 'Auth', '', 0),
                (4, 'other', 'Other', '', 1)";
                mysql_query($sql, $link); //run query                
                // -- ---------------------------------------------------------

                // -- Table structure for table `base_page` -------------------
                $sql = "CREATE TABLE IF NOT EXISTS `base_page` 
                (
                `page_id` int(11) NOT NULL AUTO_INCREMENT,
                `page_name` varchar(50) NOT NULL,
                `custom_name` varchar(50) NOT NULL,
                `page_type_id` int(11) NOT NULL,
                `page_icon` varchar(20) NOT NULL,
                `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`page_id`),
                KEY `page_icon` (`page_icon`),
                KEY `page_type_id` (`page_type_id`)
                )" ;
                mysql_query($sql, $link); //run query
                // -- Dumping data for table `base_page`
                $sql = "INSERT INTO `base_page` (`page_id`, `page_name`, `custom_name`, `page_type_id`, `page_icon`) VALUES
                (1, 'role', 'Role', 1, 'verified_user'),
                (2, 'user', 'User', 1, 'supervisor_account'),
                (3, 'category', 'Category', 2, 'redeem'),
                (4, 'hall', 'Hall', 2, 'location_city'),
                (5, 'event', 'Event', 2, 'event'),   
                (6, 'ticket', 'Ticket', 2, ''),
                (7, 'unit', 'Unit', 2, ''),
                (8, 'login', 'Login', 4, ''),
                (9, 'signup', 'Sign Up', 4, ''),
                (10, 'explore', 'Explore', 4, 'dashboard'),
                (11, 'cart', 'Cart', 4, 'add_shopping_cart'),
                (12, 'my_ticket', 'My Ticket', 4, 'explore')";
                mysql_query($sql, $link); //run query
                // -- --------------------------------------------------------

                // -- Table structure for table `base_role` -------------------
                $sql = "CREATE TABLE IF NOT EXISTS `base_role` 
                (
                `role_id` int(11) NOT NULL AUTO_INCREMENT,
                `role_name` varchar(100) NOT NULL,
                `role_active` tinyint(1) NOT NULL,
                `default_page_id` int(11) NOT NULL,
                `created_by_id` int(11) NOT NULL,
                `modified_by_id` int(11) NOT NULL,
                `page_id` int(11) NOT NULL,
                `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `date_modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
                PRIMARY KEY (`role_id`),
                KEY `page_id` (`page_id`)
                )";
                mysql_query($sql, $link); //run query
                // -- Dumping data for table `base_role`
                $sql = "INSERT INTO `base_role` (`role_id`, `role_name`, `role_active`, `default_page_id`, `created_by_id`, `modified_by_id`, `page_id`) VALUES
                (1, 'Super Administrator', 1, 1, 1, 0, 1),
                (2, 'Technical Assistant', 1, 3, 1, 0, 1),
                (3, 'Customer', 1, 10, 1, 0, 1)";
                mysql_query($sql, $link); //run query
                // -- --------------------------------------------------------

                // -- Table structure for table `base_user` ------------------
                $sql = "CREATE TABLE IF NOT EXISTS `base_user` 
                (
                `user_id` int(11) NOT NULL AUTO_INCREMENT,
                `role_id` int(11) NOT NULL,
                `user_name` varchar(100) NOT NULL,
                `user_first_name` varchar(100) NOT NULL,
                `user_last_name` varchar(100) NOT NULL,
                `user_gender` varchar(1) NOT NULL,
                `user_email` varchar(150) NOT NULL,
                `user_phone_number` varchar(20) NOT NULL,
                `user_password` varchar(40) NOT NULL,
                `user_active` tinyint(1) NOT NULL,
                `login_status` tinyint(1) NOT NULL,
                `created_by_id` int(11) NOT NULL,
                `modified_by_id` int(11) NOT NULL,
                `page_id` int(11) NOT NULL,
                `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `date_modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
                PRIMARY KEY (`user_id`),
                KEY `role_id` (`role_id`),
                KEY `page_id` (`page_id`)
                )";
                mysql_query($sql, $link); //run query
                // -- Dumping data for table `base_user`
                $sql = "INSERT INTO `base_user` (`user_id`, `role_id`, `user_name`, `user_first_name`, `user_last_name`, `user_gender`, `user_email`, `user_phone_number`, `user_password`, `user_active`, `login_status`, `created_by_id`, `modified_by_id`, `page_id`) VALUES
                (1, 1, 'ahmzyjazzy', 'Ahmed', 'Olanrewaju', '', 'olanrewajuahmed095@yahoo.com', '08093570289', 'tic9193ce3b31332b03f7d8af056c692b84ket', 1, 0, 0, 0, 2),
                (2, 2, 'capable', 'Sunday', 'Alabi', '', 'alabi@yahoo.com', '9048358455', 'tic787c74a2e618a696e34e025adda33ad3ket', 1, 0, 0, 0, 2),
                (3, 3, 'sammy', 'Sam', 'Samuel', 'M', 'sammy@y.com', '515132013', 'tic4385695633f8c6c8ab52592092cecf04ket', 1, 0, 0, 0, 9)";
                mysql_query($sql, $link); //run query
                // -- --------------------------------------------------------

                // -- Table structure for table `base_page_access` -----------
                $sql = "CREATE TABLE IF NOT EXISTS `base_page_access` 
                (
                `page_access_id` int(11) NOT NULL AUTO_INCREMENT,
                `page_id` int(11) NOT NULL,
                `role_id` int(11) NOT NULL,
                `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`page_access_id`),
                KEY `page_id` (`page_id`,`role_id`),
                KEY `role_id` (`role_id`)
                )";
                mysql_query($sql, $link); //run query
                // -- Dumping data for table `base_page_access`
                $sql = "INSERT INTO `base_page_access` (`page_access_id`, `page_id`, `role_id`) VALUES
                (1, 1, 1), (2, 2, 1), (3, 3, 1), (4, 4, 1), (5, 5, 1), (6, 6, 1), (7, 7, 1),
                (8, 3, 2), (9, 4, 2), (10, 5, 2), (11, 6, 2), (12, 7, 2),
                (13, 10, 3), (14, 11, 3), (15, 12, 3)";
                mysql_query($sql, $link); //run query
                // -- --------------------------------------------------------

                // -- Table structure for table `base_log` ------------------
                $sql = "CREATE TABLE IF NOT EXISTS `base_log` (
                `log_id` int(11) NOT NULL AUTO_INCREMENT,
                `user_id` int(11) NOT NULL,
                `user_name` varchar(100) NOT NULL,
                `user_role` varchar(100) NOT NULL,
                `user_action` varchar(100) NOT NULL,
                `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `page` varchar(50) NOT NULL,
                PRIMARY KEY (`log_id`),
                KEY `user_id` (`user_id`)
                )";
                mysql_query($sql, $link); //run query
                // -- --------------------------------------------------------

                // -- Table structure for table `base_category_type` -------------
                $sql = "CREATE TABLE IF NOT EXISTS `base_category_type` (
                `type_id` int(11) NOT NULL AUTO_INCREMENT,
                `type_name` varchar(100) NOT NULL,
                `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`type_id`)
                )";
                mysql_query($sql, $link); //run query
                // -- Dumping data for table `base_category_type`
                $sql = "INSERT INTO `base_category_type` (`type_id`, `type_name`) VALUES
                (1, 'Event'), (2, 'Hall'), (3, 'Ticket')";
                mysql_query($sql, $link); //run query
                // -- --------------------------------------------------------

                // -- Table structure for table `base_category` -----------------
                $sql = "CREATE TABLE IF NOT EXISTS `base_category` (
                `category_id` int(11) NOT NULL AUTO_INCREMENT,
                `category_code` varchar(20) NOT NULL,
                `category_name` varchar(100) NOT NULL,
                `type_id` int(11) NOT NULL,
                `category_active` tinyint(1) NOT NULL,
                `created_by_id` int(11) NOT NULL,
                `modified_by_id` int(11) NOT NULL,
                `page_id` int(11) NOT NULL,
                `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `date_modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
                PRIMARY KEY (`category_id`),
                KEY `page_id` (`page_id`)
                )";
                mysql_query($sql, $link); //run query
                // -- Dumping data for table `base_category`
                $sql = "INSERT INTO `base_category` (`category_id`, `category_code`, `category_name`, `type_id`, `category_active`, `created_by_id`, `modified_by_id`, `page_id`) VALUES
                (1, 'H001', 'Small', 2, 1, 1, 0, 3),
                (2, 'Music', 'Music', 1, 1, 1, 0, 3),
                (3, 'H002', 'Medium', 2, 1, 1, 0, 3),
                (4, 'Drama', 'Drama', 1, 1, 1, 0, 3),
                (5, 'EDU', 'Education', 1, 1, 1, 0, 3)";
                mysql_query($sql, $link); //run query
                // -- --------------------------------------------------------

                // -- Table structure for table `base_hall` -----------------
                $sql = "CREATE TABLE IF NOT EXISTS `base_hall` (
                `hall_id` int(11) NOT NULL AUTO_INCREMENT,
                `category_id` int(11) NOT NULL,
                `hall_code` varchar(100) NOT NULL,
                `hall_name` varchar(100) NOT NULL,
                `hall_capacity` int(11) NOT NULL,
                `hall_active` tinyint(1) NOT NULL,
                `created_by_id` int(11) NOT NULL,
                `modified_by_id` int(11) NOT NULL,
                `page_id` int(11) NOT NULL,
                `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `date_modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
                PRIMARY KEY (`hall_id`),
                KEY `page_id` (`page_id`)
                )";
                mysql_query($sql, $link); //run query
                // -- Dumping data for table `base_hall`
                $sql = "INSERT INTO `base_hall` (`hall_id`, `category_id`, `hall_code`, `hall_name`, `hall_capacity`, `hall_active`, `created_by_id`, `modified_by_id`, `page_id`) VALUES
                (1, 3, 'H1', 'Yankari', 20, 1, 1, 0, 4),
                (2, 1, 'H2', 'Westend', 10, 1, 1, 0, 4),
                (3, 3, 'H3', 'Intercontinental', 15, 1, 1, 0, 4)";
                mysql_query($sql, $link); //run query
                // -- --------------------------------------------------------

                // -- Table structure for table `base_event` -----------------
                $sql = "CREATE TABLE IF NOT EXISTS `base_event` (
                `event_id` int(11) NOT NULL AUTO_INCREMENT,
                `hall_id` int(11) NOT NULL,
                `event_code` varchar(100) NOT NULL,
                `event_name` varchar(100) NOT NULL,
                `event_description` text NOT NULL,
                `image_link` varchar(100) NOT NULL,
                `thumbnail_link` varchar(100) NOT NULL,
                `event_active` tinyint(1) NOT NULL,
                `event_date` date NOT NULL,
                `event_time` time NOT NULL,
                `use_hall_capacity` tinyint(1) NOT NULL,
                `created_by_id` int(11) NOT NULL,
                `modified_by_id` int(11) NOT NULL,
                `page_id` int(11) NOT NULL,
                `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `date_modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
                PRIMARY KEY (`event_id`),
                KEY `page_id` (`page_id`)
                )";
                mysql_query($sql, $link); //run query
                // -- Dumping data for table `base_event`
                $sql = "INSERT INTO `base_event` (`event_id`, `hall_id`, `event_code`, `event_name`, `event_description`, `image_link`, `thumbnail_link`, `event_active`, `event_date`, `event_time`, `use_hall_capacity`, `created_by_id`, `modified_by_id`, `page_id`) VALUES
                (1, 2, 'E1', 'Teju Babyface', 'This is a sample event, am just testing it.', '', '', 1, '2018-02-28', '09:30:00', 0, 1, 0, 5),
                (2, 3, 'E2', 'Industry Night', 'This is a sample event, am just testing it.', '', '', 1, '2018-03-02', '11:30:00', 0, 1, 0, 5),
                (3, 1, 'E3', 'Villa Day 2018', 'This is get together of all freshers in the campus', '', '', 1, '2018-03-18', '15:00:00', 0, 1, 0, 5)";
                mysql_query($sql, $link); //run query
                // -- -----------------------------------------------------------

                // -- Table structure for table `base_tag` ----------------------
                $sql = "CREATE TABLE IF NOT EXISTS `base_tag` (
                `tag_id` int(11) NOT NULL AUTO_INCREMENT,
                `event_id` int(11) NOT NULL,
                `category_id` int(11) NOT NULL,
                `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`tag_id`),
                KEY `event_id` (`event_id`),
                KEY `category_id` (`category_id`)
                )";
                mysql_query($sql, $link); //run query
                // -- Dumping data for table `base_tag`
                $sql = "INSERT INTO `base_tag` (`tag_id`, `event_id`, `category_id`) VALUES
                (1, 1, 2), (2, 1, 4), (3, 2, 5), (4, 3, 5), (5, 3, 2)";
                mysql_query($sql, $link); //run query
                // -- ----------------------------------------------------------
                
                // -- Table structure for table `base_unit` --------------------
                $sql = "CREATE TABLE IF NOT EXISTS `base_unit` (
                `unit_id` int(11) NOT NULL AUTO_INCREMENT,
                `unit_code` varchar(100) NOT NULL,
                `unit_name` varchar(100) NOT NULL,
                `quantity` int(11) NOT NULL,
                `unit_active` tinyint(1) NOT NULL,
                `created_by_id` int(11) NOT NULL,
                `modified_by_id` int(11) NOT NULL,
                `page_id` int(11) NOT NULL,
                `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `date_modified` datetime NOT NULL,
                PRIMARY KEY (`unit_id`)
                )";
                mysql_query($sql, $link); //run query
                // -- Dumping data for table `base_unit`
                $sql = "INSERT INTO `base_unit` (`unit_id`, `unit_code`, `unit_name`, `quantity`, `unit_active`, `created_by_id`, `modified_by_id`, `page_id`) VALUES
                (1, 'Table Of 5', 'Table Of 5', 5, 1, 1, 0, 7),
                (2, 'Student', 'Student', 1, 1, 1, 0, 7),
                (3, 'Couples', 'Couples', 2, 1, 1, 0, 7),
                (4, 'Dozen', 'Total of 12', 12, 1, 1, 0, 7),
                (5, 'Score', 'Total of 20', 20, 1, 1, 0, 7),
                (6, 'Adult', '18+', 1, 1, 1, 0, 7),
                (7, 'Children', 'Below 12', 1, 1, 1, 0, 7),
                (8, 'Teenagers', 'From Age 13 - 17', 1, 1, 1, 0, 7),
                (9, 'Table of 10', 'Table of 10', 10, 1, 1, 0, 7),
                (10, 'Table of 20', 'Table of 20', 20, 1, 1, 0, 7),
                (11, 'VIP', 'VIP', 1, 1, 1, 0, 7),
                (12, 'Regular', 'Regular', 1, 1, 1, 0, 7)";
                mysql_query($sql, $link); //run query
                // --------------------------------------------------------------

                // -- Table structure for table `base_ticket` -------------------
                $sql = "CREATE TABLE IF NOT EXISTS `base_ticket` (
                `ticket_id` int(11) NOT NULL AUTO_INCREMENT,
                `event_id` int(11) NOT NULL,
                `ticket_code` varchar(100) NOT NULL,
                `ticket_name` varchar(100) NOT NULL,
                `ticket_active` tinyint(1) NOT NULL,
                `created_by_id` int(11) NOT NULL,
                `modified_by_id` int(11) NOT NULL,
                `page_id` int(11) NOT NULL,
                `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `date_modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
                PRIMARY KEY (`ticket_id`),
                KEY `page_id` (`page_id`)
                )";
                mysql_query($sql, $link); //run query
                // -- Dumping data for table `base_ticket`
                $sql = "INSERT INTO `base_ticket` (`ticket_id`, `event_id`, `ticket_code`, `ticket_name`, `ticket_active`, `created_by_id`, `modified_by_id`, `page_id`) VALUES
                (1, 2, 'Ticket Sample', 'Ticket Sample', 1, 1, 0, 12),
                (2, 1, 'Awardee', 'Awardee', 1, 1, 0, 12),
                (3, 3, 'Villa Day 18', 'Villa Day 18', 1, 1, 0, 12)";
                mysql_query($sql, $link); //run query
                // -- ----------------------------------------------------------

                // -- Table structure for table `base_ticket_detail` -----------
                $sql = "CREATE TABLE IF NOT EXISTS `base_ticket_detail` (
                `ticket_detail_id` int(11) NOT NULL AUTO_INCREMENT,
                `ticket_id` int(11) NOT NULL,
                `unit_id` int(11) NOT NULL,
                `price` float NOT NULL,
                `discount` varchar(100) NOT NULL,
                `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`ticket_detail_id`),
                KEY `ticket_id` (`ticket_id`),
                KEY `unit_id` (`unit_id`)
                )";
                mysql_query($sql, $link); //run query
                // -- Dumping data for table `base_ticket_detail`
                $sql = "INSERT INTO `base_ticket_detail` (`ticket_detail_id`, `ticket_id`, `unit_id`, `price`, `discount`) VALUES
                (1, 1, 1, 100, ''),
                (2, 2, 6, 2000, ''),
                (3, 2, 8, 1500, ''),
                (4, 2, 7, 500, ''),
                (5, 3, 12, 2000, ''),
                (6, 3, 11, 5000, '')";
                mysql_query($sql, $link); //run query
                // -- --------------------------------------------------------

                // -- Table structure for table `base_txn` -------------------
                $sql = "CREATE TABLE IF NOT EXISTS `base_txn` (
                `txn_id` int(11) NOT NULL AUTO_INCREMENT,
                `user_id` int(11) NOT NULL,
                `ticket_qty` int(11) NOT NULL,
                `total_amount` float NOT NULL,
                `status` varchar(20) NOT NULL,
                `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`txn_id`),
                KEY `user_id` (`user_id`)
                )";
                mysql_query($sql, $link); //run query
                // -- Dumping data for table `base_txn`
                $sql = "INSERT INTO `base_txn` (`txn_id`, `user_id`, `ticket_qty`, `total_amount`, `status`) VALUES
                (1, 3, 3, 10100, 'paid'),
                (2, 3, 1, 500, 'paid'),
                (3, 3, 2, 4500, 'paid')";
                mysql_query($sql, $link); //run query
                // -- --------------------------------------------------------

                // -- Table structure for table `base_txn_detail`
                $sql = "CREATE TABLE IF NOT EXISTS `base_txn_detail` (
                `txn_detail_id` int(11) NOT NULL AUTO_INCREMENT,
                `txn_id` int(11) NOT NULL,
                `event_id` int(11) NOT NULL,
                `ticket_id` int(11) NOT NULL,
                `unit_id` int(11) NOT NULL,
                `quantity_order` int(11) NOT NULL,
                `unit_price` float NOT NULL,
                `total_amount` float NOT NULL,
                `is_used` tinyint(1) NOT NULL,
                `status` varchar(20) NOT NULL,
                `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`txn_detail_id`),
                KEY `txn_id` (`txn_id`)
                )";
                mysql_query($sql, $link); //run query
                // -- Dumping data for table `base_txn_detail`
                $sql = "INSERT INTO `base_txn_detail` (`txn_detail_id`, `txn_id`, `event_id`, `ticket_id`, `unit_id`, `quantity_order`, `unit_price`, `total_amount`, `is_used`, `status`) VALUES
                (1, 1, 3, 3, 12, 2, 2000, 4000, 0, 'pending'),
                (2, 1, 2, 2, 1, 1, 100, 100, 0, 'pending'),
                (3, 1, 1, 3, 6, 3, 2000, 6000, 0, 'pending'),
                (4, 2, 2, 2, 1, 5, 100, 500, 0, 'pending'),
                (5, 3, 3, 3, 12, 2, 2000, 4000, 0, 'pending'),
                (6, 3, 2, 2, 1, 5, 100, 500, 0, 'pending')";
                mysql_query($sql, $link); //run query
                // -- --------------------------------------------------------

            }else{
                
            } 

        }else{
            // echo "Database already exist \n";
        }

    }
    catch(Exception $e){
        echo json_encode($conn) . "<br>" . $e->getMessage();
    }
}

?>