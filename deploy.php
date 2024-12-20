<?php
namespace Deployer;

require 'recipe/common.php';

// Config

set('repository', 'https://jpirnat@github.com/jpirnat/dex.git');

add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', ['config/cache', 'templates/cache']);

// Hosts

host('147.182.213.212')
	->set('remote_user', 'jpirnat')
	->set('labels', ['stage' => 'production'])
	->set('deploy_path', '/var/www/dex')
	->set('branch', 'main')
;
/*
Add the following to your computer's ~/.ssh/config file:
Host 147.182.213.212
	HostName 147.182.213.212
	User jpirnat
	IdentityFile ~/.ssh/PRIVATE_KEY_NAME

And on the server, add the public key in ~/.ssh/authorized_keys
*/

// Tasks

desc('Deploy your project');
task('deploy', [
	'deploy:prepare',
	'deploy:vendors',
	'deploy:clear_paths',
	'deploy:publish',
]);

task('reload:php-fpm', function () {
    run('sudo /etc/init.d/php8.4-fpm restart');
});
// NOTE TO SELF: Whenever I upgrade PHP and thus need to update the php-fpm
// restart command here, I also need to update `sudo visudo` on the server so
// the updated command can be run without a password:
// jpirnat ALL=NOPASSWD: /etc/init.d/php8.4-fpm restart

// Hooks

after('deploy', 'reload:php-fpm');
after('rollback', 'reload:php-fpm');

after('deploy:failed', 'deploy:unlock');
