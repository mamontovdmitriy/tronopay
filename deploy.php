<?php

namespace Deployer;

require_once 'recipe/common.php';


set('env', [
    'APP_ENV' => 'prod',
    'APP_DEBUG' => 0,
]);

// Project repository
set('repository', 'https://github.com/mamontovdmitriy/tronopay.git');

set('allow_anonymous_stats', false);
set('git_tty', false); // [Optional] Allocate tty for git clone. Default value is false. - for Windows
set('writable_mode', 'chown');
set('composer_options', 'install --verbose --prefer-dist --no-progress --no-interaction --no-dev --optimize-autoloader --no-suggest --ignore-platform-reqs');

set('shared_files', [
    '.env.local',
]);
set('shared_dirs', [
    'var/log',
]);
set('writable_dirs', [
    'var',
    'var/log',
    'var/cache',
    'var/cache/prod',
]);

set('bin/console', function () {
    return parse('{{bin/php}} {{release_path}}/bin/console --no-interaction');
});

task('deploy:assets', function () {
    run('{{bin/console}} assets:install');

})->desc('Installs bundles web assets under a public directory');

task('deploy:webpack', function () {
    run('cd {{release_path}} && yarn install && yarn encore production');

})->desc('Installs bundles web assets under a public directory');

task('deploy:cache:clear', function () {
    run('{{bin/console}} cache:clear --no-warmup');
})->desc('Clear cache');

task('deploy:cache:warmup', function () {
    run('{{bin/console}} cache:warmup');
})->desc('Warm up cache');

task('deploy:executable', function () {
    run('chmod +x {{release_path}}/bin/*');
})->desc('Set executable ');

task('deploy:php_fpm:restart', function () {
    host('tronopay.com')->become('root');
    run('service php8.1-fpm restart');
    host('tronopay.com')->become('www-data');
})->desc('Restart PHP-FPM');

task('deploy:queue:stop', function () {
    if (has('previous_release')) {
        run('rm -f {{previous_release}}/var/cache/prod/queue*.pid');
    }
})->desc('Stop cron jobs');

task('deploy:cron:stop', function () {
    run('echo "" | crontab');
})->desc('Stop cron jobs');

task('deploy:cron:install', function () {
    run('crontab {{release_path}}/crontab.conf');
})->desc('Install cron jobs');


task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:cron:stop',
    'deploy:release',
    'deploy:queue:stop',
    'deploy:update_code',
    'deploy:shared',
    'deploy:vendors',
    'deploy:executable',
	'deploy:writable',
    'deploy:assets',
    'deploy:webpack',
    'deploy:cache:clear',
    'deploy:cache:warmup',
    'deploy:symlink',
    'deploy:unlock',
    'deploy:php_fpm:restart',
    'deploy:cron:install',
    'cleanup',
])->desc('Deploy project');

after('deploy', 'success');
// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Hosts
host('tronopay.com')
    ->user('root')
    ->become('www-data')
    ->port(22)
    ->identityFile('~/.ssh/id_rsa')
    ->forwardAgent(true)
    ->multiplexing(false) // true
    ->addSshOption('UserKnownHostsFile', '/dev/null')
    ->addSshOption('StrictHostKeyChecking', 'no')
    ->set('deploy_path', '/var/www/tronopay.com');

