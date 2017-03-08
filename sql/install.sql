CREATE TABLE IF NOT EXISTS `{_DB_PREFIX_}mp_carrier_import` ( 
    `id_carrier_import` INT NOT NULL AUTO_INCREMENT , 
    `carrier_name` VARCHAR(100) NOT NULL , 
    `carrier_import_type_id` INT NOT NULL , 
    `column_separator` VARCHAR(100) NOT NULL , 
    `web_link` VARCHAR(255) NOT NULL , 
    `col_order_reference` VARCHAR(255) NOT NULL , 
    `col_tracking_id` VARCHAR(255) NOT NULL , 
    `col_delivered_date` VARCHAR(255) NOT NULL , 
    `order_state_id` INT NOT NULL , 
    PRIMARY KEY (`id_carrier_import`), 
    INDEX `idx_carrier_name` (`carrier_name`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `{_DB_PREFIX_}mp_carrier_import_rows` ( 
    `id_import_row` INT NOT NULL AUTO_INCREMENT , 
    `id_carrier_import` INT NOT NULL , 
    `carrier_name` VARCHAR(255) NOT NULL,
    `carrier_import_type_id` INT NOT NULL,
    `carrier_import_type_name` VARCHAR(255) NOT NULL,
    `import_file_name` VARCHAR(255) NOT NULL,
    `order_reference` VARCHAR(255) NOT NULL,
    `tracking_code` VARCHAR(255) NOT NULL,
    `delivered_date` DATE NULL,
    `import_date` DATE NULL,
    `id_order_state` INT NOT NULL,
    `order_state_name` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id_import_row`)
) ENGINE = InnoDB;