--
-- Permissions
--
INSERT INTO `permission` (`permission_key`, `module`, `label`, `nav_label`, `nav_href`, `show_in_menu`) VALUES
('index', 'OnePlace\\Store\\Controller\\StoreController', 'Index', 'Store', '/store', 1),
('buy', 'OnePlace\\Store\\Controller\\StoreController', 'Buy', '', '', 0),
('list', 'OnePlace\\Store\\Controller\\StoreController', 'List', '', '', 0),
('index', 'OnePlace\\Store\\Controller\\ApiController', 'List', '', '', 0),
('list', 'OnePlace\\Store\\Controller\\ApiController', 'List', '', '', 0);

--
-- icon
--
INSERT INTO `settings` (`settings_key`, `settings_value`) VALUES ('store-icon', 'fas fa-store');

COMMIT;