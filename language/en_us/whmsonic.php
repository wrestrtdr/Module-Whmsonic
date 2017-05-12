<?php
/**
 * en_us language for the Whmsonic module.
 */
// Basics
$lang['Whmsonic.name'] = 'WHMSonic';
$lang['Whmsonic.module_row'] = 'Server';
$lang['Whmsonic.module_row_plural'] = 'Servers';
$lang['Whmsonic.module_group'] = 'Server Group';
$lang['Whmsonic.tab_stats'] = 'Statistics';
$lang['Whmsonic.tab_client_actions'] = 'Actions';
$lang['Whmsonic.submit'] = 'Submit';

$lang['Whmsonic.tab_stats.info.WEB_DOMAINS'] = 'Domains';
$lang['Whmsonic.tab_stats.info.WEB_ALIASES'] = 'Aliases';
$lang['Whmsonic.tab_stats.info.DNS_DOMAINS'] = 'DNS Domains';
$lang['Whmsonic.tab_stats.info.DNS_RECORDS'] = 'DNS Records';
$lang['Whmsonic.tab_stats.info.MAIL_DOMAINS'] = 'Mail Domains';
$lang['Whmsonic.tab_stats.info.MAIL_ACCOUNTS'] = 'Mail Accounts';
$lang['Whmsonic.tab_stats.info.DATABASES'] = 'Databases';
$lang['Whmsonic.tab_stats.info.CRON_JOBS'] = 'Cron Jobs';
$lang['Whmsonic.tab_stats.info.DISK_QUOTA'] = 'Disk Usage';
$lang['Whmsonic.tab_stats.info.BANDWIDTH'] = 'Bandwidth Usage';
$lang['Whmsonic.tab_stats.info.BACKUPS'] = 'Backups';

// Module management
$lang['Whmsonic.add_module_row'] = 'Add Server';
$lang['Whmsonic.add_module_group'] = 'Add Server Group';
$lang['Whmsonic.manage.module_rows_title'] = 'Servers';
$lang['Whmsonic.manage.module_groups_title'] = 'Server Groups';
$lang['Whmsonic.manage.module_rows_heading.name'] = 'Server Label';
$lang['Whmsonic.manage.module_rows_heading.ip_address'] = 'IP Address';
$lang['Whmsonic.manage.module_rows_heading.hostname'] = 'Hostname';
$lang['Whmsonic.manage.module_rows_heading.port'] = 'Port';
$lang['Whmsonic.manage.module_rows_heading.accounts'] = 'Accounts';
$lang['Whmsonic.manage.module_rows_heading.options'] = 'Options';
$lang['Whmsonic.manage.module_groups_heading.name'] = 'Group Name';
$lang['Whmsonic.manage.module_groups_heading.servers'] = 'Server Count';
$lang['Whmsonic.manage.module_groups_heading.options'] = 'Options';
$lang['Whmsonic.manage.module_rows.count'] = '%1$s / %2$s'; // %1$s is the current number of accounts, %2$s is the total number of accounts available
$lang['Whmsonic.manage.module_rows.edit'] = 'Edit';
$lang['Whmsonic.manage.module_groups.edit'] = 'Edit';
$lang['Whmsonic.manage.module_rows.delete'] = 'Delete';
$lang['Whmsonic.manage.module_groups.delete'] = 'Delete';
$lang['Whmsonic.manage.module_rows.confirm_delete'] = 'Are you sure you want to delete this server?';
$lang['Whmsonic.manage.module_groups.confirm_delete'] = 'Are you sure you want to delete this server group?';
$lang['Whmsonic.manage.module_rows_no_results'] = 'There are no servers.';
$lang['Whmsonic.manage.module_groups_no_results'] = 'There are no server groups.';

$lang['Whmsonic.order_options.first'] = 'First non-full server';

// Add row
$lang['Whmsonic.add_row.box_title'] = 'Add WHMSonic Server';
$lang['Whmsonic.add_row.basic_title'] = 'Basic Settings';
$lang['Whmsonic.add_row.name_servers_title'] = 'Name Servers';
$lang['Whmsonic.add_row.notes_title'] = 'Notes';
$lang['Whmsonic.add_row.name_server_btn'] = 'Add Additional Name Server';
$lang['Whmsonic.add_row.name_server_col'] = 'Name Server';
$lang['Whmsonic.add_row.name_server_host_col'] = 'Hostname';
$lang['Whmsonic.add_row.name_server'] = 'Name server %1$s'; // %1$s is the name server number (e.g. 3)
$lang['Whmsonic.add_row.remove_name_server'] = 'Remove';
$lang['Whmsonic.add_row.add_btn'] = 'Add Server';

$lang['Whmsonic.edit_row.box_title'] = 'Edit WHMSonic Server';
$lang['Whmsonic.edit_row.basic_title'] = 'Basic Settings';
$lang['Whmsonic.edit_row.name_servers_title'] = 'Name Servers';
$lang['Whmsonic.edit_row.notes_title'] = 'Notes';
$lang['Whmsonic.edit_row.name_server_btn'] = 'Add Additional Name Server';
$lang['Whmsonic.edit_row.name_server_col'] = 'Name Server';
$lang['Whmsonic.edit_row.name_server_host_col'] = 'Hostname';
$lang['Whmsonic.edit_row.name_server'] = 'Name server %1$s'; // %1$s is the name server number (e.g. 3)
$lang['Whmsonic.edit_row.remove_name_server'] = 'Remove';
$lang['Whmsonic.edit_row.add_btn'] = 'Edit Server';

$lang['Whmsonic.row_meta.server_name'] = 'Server Label';
$lang['Whmsonic.row_meta.ip_address'] = 'IP Address';
$lang['Whmsonic.row_meta.password'] = 'Password';
$lang['Whmsonic.row_meta.use_ssl'] = 'Use SSL when connecting to the API (recommended)';

// Package fields
$lang['Whmsonic.package_fields.client_type'] = 'Client Type';
$lang['Whmsonic.package_fields.client_type.external'] = 'External';
$lang['Whmsonic.package_fields.client_type.internal'] = 'Internal';
$lang['Whmsonic.package_fields.bitrate'] = 'BitRate Limit';
$lang['Whmsonic.package_fields.hspace'] = 'Hosting Space (In MB)';
$lang['Whmsonic.package_fields.bandwidth'] = 'Bandwidth Limit (In MB)';
$lang['Whmsonic.package_fields.listeners'] = 'Listeners Limit';
$lang['Whmsonic.package_fields.autodj'] = 'AutoDJ Access';
$lang['Whmsonic.package_fields.autodj.yes'] = 'Yes';
$lang['Whmsonic.package_fields.autodj.no'] = 'No';

// Service fields
$lang['Whmsonic.service_field.cpanel_username'] = 'cPanel Username';
$lang['Whmsonic.service_field.username'] = 'Radio Username';
$lang['Whmsonic.service_field.password'] = 'Radio Password';
$lang['Whmsonic.service_field.username.tooltip'] = 'The username will only be updated locally within Blesta';
$lang['Whmsonic.service_field.password.tooltip'] = 'The password will only be updated locally within Blesta';

// Service info
$lang['Whmsonic.stored_locally_only'] = 'This field will be updated locally only';
$lang['Whmsonic.service_info.ip_address'] = 'IP Address';
$lang['Whmsonic.service_info.username'] = 'Username';
$lang['Whmsonic.service_info.password'] = 'Password';
$lang['Whmsonic.service_info.server'] = 'Server';
$lang['Whmsonic.service_info.options'] = 'Options';
$lang['Whmsonic.service_info.option_login'] = 'Log in';
$lang['Whmsonic.tab_client_actions.change_password'] = 'Change Password';
$lang['Whmsonic.tab_client_actions.password'] = 'Password';
$lang['Whmsonic.tab_client_actions.confirm_password'] = 'Password';
$lang['Whmsonic.tab_stats.info_heading.field'] = 'Field';
$lang['Whmsonic.tab_stats.info_heading.value'] = 'Value';

// Errors
$lang['Whmsonic.!error.server_name_valid'] = 'You must enter a Server Label.';
$lang['Whmsonic.!error.ip_address_valid'] = 'The IP Address appears to be invalid.';
$lang['Whmsonic.!error.password_valid'] = 'The Password appears to be invalid.';
$lang['Whmsonic.!error.api.internal'] = 'An internal error occurred, or the server did not respond to the request.';
$lang['Whmsonic.!error.module_row.missing'] = 'An internal error occurred. The module row is unavailable.';

$lang['Whmsonic.!error.meta[client_type].empty'] = 'Please select the client type.';
$lang['Whmsonic.!error.meta[bitrate].empty'] = 'Please enter the BitRate Limit.';
$lang['Whmsonic.!error.meta[hspace].empty'] = 'Please enter the Hosting Space (In MB), e.g: 1000.';
$lang['Whmsonic.!error.meta[bandwidth].empty'] = 'Please enter the Bandwidth Limit (In MB), e.g: 1000.';
$lang['Whmsonic.!error.meta[listeners].empty'] = 'Please select the AutoDJ Access.';

$lang['Whmsonic.!error.user_name.empty'] = "Username can't be empty.";
$lang['Whmsonic.!error.user_password.valid'] = 'Password must be at least 8 characters in length.';

$lang['Whmsonic.!error.actions_password.valid'] = "Password & Confirm Password fields can't be empty.";
