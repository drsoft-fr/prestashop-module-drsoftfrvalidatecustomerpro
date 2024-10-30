CREATE TABLE IF NOT EXISTS `_DB_PREFIX_drsoft_fr_validate_customer_pro_adapter_customer`
(
    `id`          INT(10) UNSIGNED                              NOT NULL AUTO_INCREMENT,
    `id_customer` INT(10) UNSIGNED,
    `active`      TINYINT(1) UNSIGNED DEFAULT 0                 NOT NULL,
    `date_add`    DATETIME                                      NOT NULL,
    `date_upd`    DATETIME            DEFAULT CURRENT_TIMESTAMP NOT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `ind_uni__DB_PREFIX_dfrvcpac_id_customer` UNIQUE (`id_customer`),
    CONSTRAINT `fk__DB_PREFIX_dfrvcpac_id_customer`
        FOREIGN KEY (`id_customer`) REFERENCES `_DB_PREFIX_customer` (`id_customer`)
            ON DELETE CASCADE
)
    ENGINE = _MYSQL_ENGINE_
    DEFAULT CHARSET = utf8mb4
    AUTO_INCREMENT = 1;
