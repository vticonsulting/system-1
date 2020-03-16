@setup
$repo = 'https://github.com/tolbertdesign/system';
$branch = 'master';
$server = 'gentle-breeze';
$site = 'tolbert.design';
$release_dir = '/home/forge/releases/' . $site;
$app_dir = '/home/forge/' . $site;
$release = 'release_' . date('Y-md-Hi-s');
function logMessage($message) {
    return "echo '\033[32m" .$message. "\033[0m';\n";
}
@endsetup

@servers(['local' => 'localhost', 'remote' => $server])

@macro('deploy', ['on' => 'remote'])
fetch_repo
run_composer
update_permissions
update_symlinks
@endmacro

@task('fetch_repo')
{{ logMessage("🌀  Fetching repository…") }}
[ -d {{ $release_dir }} ] || mkdir -p {{ $release_dir }}
cd {{ $release_dir }}
git clone --branch {{ $branch }} {{ $repo }} {{ $release }}
@endtask

@task('run_composer')
{{ logMessage("🚚  Running Composer…") }}
cd {{ $release_dir }}/{{ $release }}
composer install --prefer-dist
@endtask

@task('update_permissions')
{{ logMessage("🔑  Updating permissions…") }}
cd {{ $release_dir }}
chgrp -R www-data {{ $release }}
chmod -R ug+rxw {{ $release }}
@endtask

@task('update_symlinks')
{{ logMessage("🔗  Updating symlinks…") }}
ln -nfs {{ $release_dir }}/{{ $release }} {{ $app_dir }}
chgrp -h www-data {{ $app_dir }}
@endtask
