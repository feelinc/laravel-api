--
-- Table structure for table `oauth_clients`
--

CREATE TABLE IF NOT EXISTS `oauth_clients` (
  `id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `secret` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `request_limit` int(10) NOT NULL DEFAULT '5000',
  `current_total_request` int(10) NOT NULL DEFAULT '0',
  `request_limit_until` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_request_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  UNIQUE KEY `oauth_clients_id_unique` (`id`),
  UNIQUE KEY `oauth_clients_id_secret_unique` (`id`,`secret`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_client_endpoints`
--

CREATE TABLE IF NOT EXISTS `oauth_client_endpoints` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `redirect_uri` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `oauth_client_endpoints_client_id_foreign` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_client_grants`
--

CREATE TABLE IF NOT EXISTS `oauth_client_grants` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `grant_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `oauth_client_grants_client_id_foreign` (`client_id`),
  KEY `oauth_client_grants_grant_id_foreign` (`grant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_client_metadata`
--

CREATE TABLE IF NOT EXISTS `oauth_client_metadata` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `key` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `oauth_client_metadata_cid_key_unique` (`client_id`,`key`),
  KEY `oauth_client_metadata_client_id_index` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_client_scopes`
--

CREATE TABLE IF NOT EXISTS `oauth_client_scopes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `scope_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `oauth_client_scopes_client_id_foreign` (`client_id`),
  KEY `oauth_client_scopes_scope_id_foreign` (`scope_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_grants`
--

CREATE TABLE IF NOT EXISTS `oauth_grants` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `grant` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `oauth_grants_grant_unique` (`grant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_grant_scopes`
--

CREATE TABLE IF NOT EXISTS `oauth_grant_scopes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `grant_id` int(10) unsigned NOT NULL,
  `scope_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `oauth_grant_scopes_grant_id_foreign` (`grant_id`),
  KEY `oauth_grant_scopes_scope_id_foreign` (`scope_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_scopes`
--

CREATE TABLE IF NOT EXISTS `oauth_scopes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `scope` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `oauth_scopes_scope_unique` (`scope`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_sessions`
--

CREATE TABLE IF NOT EXISTS `oauth_sessions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `owner_type` enum('client','user') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'user',
  `owner_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `oauth_sessions_client_id_owner_type_owner_id_index` (`client_id`,`owner_type`,`owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_session_access_tokens`
--

CREATE TABLE IF NOT EXISTS `oauth_session_access_tokens` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `session_id` int(10) unsigned NOT NULL,
  `access_token` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `access_token_expires` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `oauth_session_access_tokens_access_token_session_id_unique` (`access_token`,`session_id`),
  KEY `oauth_session_access_tokens_session_id_index` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_session_authcodes`
--

CREATE TABLE IF NOT EXISTS `oauth_session_authcodes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `session_id` int(10) unsigned NOT NULL,
  `auth_code` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `auth_code_expires` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `oauth_session_authcodes_session_id_index` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_session_authcode_scopes`
--

CREATE TABLE IF NOT EXISTS `oauth_session_authcode_scopes` (
  `oauth_session_authcode_id` int(10) unsigned NOT NULL,
  `scope_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `oauth_session_authcode_scopes_oauth_session_authcode_id_index` (`oauth_session_authcode_id`),
  KEY `oauth_session_authcode_scopes_scope_id_index` (`scope_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_session_redirects`
--

CREATE TABLE IF NOT EXISTS `oauth_session_redirects` (
  `session_id` int(10) unsigned NOT NULL,
  `redirect_uri` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_session_refresh_tokens`
--

CREATE TABLE IF NOT EXISTS `oauth_session_refresh_tokens` (
  `session_access_token_id` int(10) unsigned NOT NULL,
  `refresh_token` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `refresh_token_expires` int(11) NOT NULL,
  `client_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`session_access_token_id`),
  KEY `oauth_session_refresh_tokens_client_id_index` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_session_token_scopes`
--

CREATE TABLE IF NOT EXISTS `oauth_session_token_scopes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `session_access_token_id` int(10) unsigned NOT NULL,
  `scope_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `oauth_session_token_scopes_satid_sid_unique` (`session_access_token_id`,`scope_id`),
  KEY `oauth_session_token_scopes_scope_id_index` (`scope_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `oauth_client_endpoints`
--
ALTER TABLE `oauth_client_endpoints`
  ADD CONSTRAINT `oauth_client_endpoints_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `oauth_clients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `oauth_client_grants`
--
ALTER TABLE `oauth_client_grants`
  ADD CONSTRAINT `oauth_client_grants_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `oauth_clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `oauth_client_grants_grant_id_foreign` FOREIGN KEY (`grant_id`) REFERENCES `oauth_grants` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `oauth_client_metadata`
--
ALTER TABLE `oauth_client_metadata`
  ADD CONSTRAINT `oauth_client_metadata_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `oauth_clients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `oauth_client_scopes`
--
ALTER TABLE `oauth_client_scopes`
  ADD CONSTRAINT `oauth_client_scopes_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `oauth_clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `oauth_client_scopes_scope_id_foreign` FOREIGN KEY (`scope_id`) REFERENCES `oauth_scopes` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `oauth_grant_scopes`
--
ALTER TABLE `oauth_grant_scopes`
  ADD CONSTRAINT `oauth_grant_scopes_grant_id_foreign` FOREIGN KEY (`grant_id`) REFERENCES `oauth_grants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `oauth_grant_scopes_scope_id_foreign` FOREIGN KEY (`scope_id`) REFERENCES `oauth_scopes` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `oauth_sessions`
--
ALTER TABLE `oauth_sessions`
  ADD CONSTRAINT `oauth_sessions_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `oauth_clients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `oauth_session_access_tokens`
--
ALTER TABLE `oauth_session_access_tokens`
  ADD CONSTRAINT `oauth_session_access_tokens_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `oauth_sessions` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `oauth_session_authcodes`
--
ALTER TABLE `oauth_session_authcodes`
  ADD CONSTRAINT `oauth_session_authcodes_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `oauth_sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `oauth_session_authcode_scopes`
--
ALTER TABLE `oauth_session_authcode_scopes`
  ADD CONSTRAINT `oauth_session_authcode_scopes_oauth_session_authcode_id_foreign` FOREIGN KEY (`oauth_session_authcode_id`) REFERENCES `oauth_session_authcodes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `oauth_session_authcode_scopes_scope_id_foreign` FOREIGN KEY (`scope_id`) REFERENCES `oauth_scopes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `oauth_session_redirects`
--
ALTER TABLE `oauth_session_redirects`
  ADD CONSTRAINT `oauth_session_redirects_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `oauth_sessions` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `oauth_session_refresh_tokens`
--
ALTER TABLE `oauth_session_refresh_tokens`
  ADD CONSTRAINT `oauth_session_refresh_tokens_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `oauth_clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `oauth_session_refresh_tokens_session_access_token_id_foreign` FOREIGN KEY (`session_access_token_id`) REFERENCES `oauth_session_access_tokens` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `oauth_session_token_scopes`
--
ALTER TABLE `oauth_session_token_scopes`
  ADD CONSTRAINT `oauth_session_token_scopes_scope_id_foreign` FOREIGN KEY (`scope_id`) REFERENCES `oauth_scopes` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `oauth_session_token_scopes_session_access_token_id_foreign` FOREIGN KEY (`session_access_token_id`) REFERENCES `oauth_session_access_tokens` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;