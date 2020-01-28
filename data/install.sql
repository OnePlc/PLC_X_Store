--
-- Permissions
--
INSERT INTO `permission` (`permission_key`, `module`, `label`, `nav_label`, `nav_href`, `show_in_menu`) VALUES
('index', 'OnePlace\\Store\\Controller\\StoreController', 'Index', 'Store', '/store', 1),
('buy', 'OnePlace\\Store\\Controller\\StoreController', 'Buy', '', '', 0);

COMMIT;