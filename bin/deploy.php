<?php
namespace Deployer;

require 'recipe/common.php';

// Project name
set('application', 'dex');

// Project repository
set('repository', 'https://jpirnat@github.com/jpirnat/dex.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

// Shared files/dirs between deploys
set('shared_files', []);
set('shared_dirs', []);

// Writable dirs by web server
set('writable_dirs', ['config/cache', 'templates/cache']);
set('allow_anonymous_stats', false);

// Hosts

host('159.89.92.65')
	->user('jpirnat')
	->stage('production')
	->set('deploy_path', '/var/www/dex')
	->set('branch', 'master')
;


// Tasks

desc('Deploy your project');
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:writable',
    'deploy:vendors',
    'deploy:clear_paths',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'success'
]);

task('reload:php-fpm', function () {
    run('sudo /etc/init.d/php8.0-fpm restart');
});
// NOTE TO SELF: Whenever I upgrade PHP and thus need to update the php-fpm
// restart command here, I also need to update `sudo visudo` on the server so
// the updated command can be run without a password:
// jpirnat ALL=NOPASSWD: /etc/init.d/php8.0-fpm restart

after('deploy', 'reload:php-fpm');
after('rollback', 'reload:php-fpm');

// [Optional] If deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
