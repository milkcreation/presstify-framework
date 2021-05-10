<?php declare(strict_types=1);

namespace tiFy\Console\Commands;

use Illuminate\Database\Schema\Blueprint;
use Pollen\Proxy\Proxies\Database;
use Pollen\Proxy\Proxies\Schema;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use tiFy\Console\Command;

/**
 * @see https://symfony.com/doc/current/console.html
 */
class UpdateCore20345 extends Command
{
    public function execute(InputInterface $input, OutputInterface $output)
    {
        Database::addConnection(
            array_merge(Database::getConnection()->getConfig(), ['strict' => false]),
            'update.v20345.form.addon.record'
        );

        if (is_multisite()) {
            global $wpdb;

            Database::getConnection('update.v20345.form.addon.record')->setTablePrefix($wpdb->prefix);
        }

        $schema = Schema::connexion('update.v20345.form.addon.record');

        if ($schema->hasTable('tify_forms_record')) {
            $schema->table('tify_forms_record', function (Blueprint $table) {
                $table->string('session', 255)->change();
            });
        }

        return 0;
    }
}