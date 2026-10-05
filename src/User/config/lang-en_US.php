<?php declare(strict_types=1);
/**
 * DuckAdmin\User - English sentences (same key set as lang-zh_CN.php).
 *
 * Keys are flat: the dots are just part of the name, Lang does no path lookup.
 *
 * Reached with ?lang=en-US, the lang cookie or Accept-Language: en-US; when no hint is
 * given the root application's lang_default (zh_CN) applies. Missing keys fall
 * back to the framework's built-in English sentences.
 *
 * The webinstaller.* keys belong to the install wizard (view/install.php).
 *
 * Note: the main language file wins over any frag, so this file (not a
 * a frag) is the place to edit the installer wording.
 */
return [
    'webinstaller.h1'                     => 'DuckPhp Web Installer',
    'webinstaller.install_complete'       => 'Install Complete',
    'webinstaller.congratulations'        => 'Congratulations! The application has been installed successfully.',
    'webinstaller.install_redirect'       => 'Installation successful. Redirecting to the homepage in 5 seconds.',
    'webinstaller.manual_redirect'        => 'Go to homepage now',
    'webinstaller.env_check'              => 'Environment Check',
    'webinstaller.current_controller_prefix' => 'Current controller_resource_prefix',
    'webinstaller.item'                   => 'Item',
    'webinstaller.status'                 => 'Status',
    'webinstaller.redis_config'           => 'Redis Config',
    'webinstaller.follow_main_application' => 'Follow Main Application',
    'webinstaller.no_redis_in_root'       => 'Main application has no redis configured.',
    'webinstaller.no_database_in_root'    => 'Main application has no database configured.',
    'webinstaller.host'                   => 'Host',
    'webinstaller.port'                   => 'Port',
    'webinstaller.auth'                   => 'Auth',
    'webinstaller.select'                 => 'Select',
    'webinstaller.multi_redis_hint'       => 'Multiple redis configs are supported: add more entries to the config file manually after install.',
    'webinstaller.database_config'        => 'Database Config',
    'webinstaller.driver'                 => 'Driver',
    'webinstaller.file'                   => 'File',
    'webinstaller.dbname'                 => 'Database',
    'webinstaller.username'               => 'Username',
    'webinstaller.password'               => 'Password',
    'webinstaller.multi_db_hint'          => 'Multiple database configs (master/slave) are supported: add more entries to the config file manually after install.',
    'webinstaller.force_reinstall'        => 'Force reinstall (drop existing tables)',
    'webinstaller.customer_setting'       => 'Customer Setting',
    'webinstaller.install'                => 'Install',
];
