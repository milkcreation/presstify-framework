<?php

declare(strict_types=1);

namespace tiFy\Console\Commands;

use Illuminate\Database\Schema\Blueprint;
use Pollen\Support\Proxy\DbProxy;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use tiFy\Console\Command;

/**
 * @see https://symfony.com/doc/current/console.html
 */
class UpdateCore20345 extends Command
{
    use DbProxy;

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->db()->addConnection(
            array_merge($this->db()->getConnection()->getConfig(), ['strict' => false]),
            'update.v20345.form.addon.record'
        );

        if (is_multisite()) {
            global $wpdb;

            $this->db()->getConnection('update.v20345.form.addon.record')->setTablePrefix($wpdb->prefix);
        }

        $schema = $this->schema('update.v20345.form.addon.record');

        if ($schema->hasTable('tify_forms_record')) {
            $schema->table('tify_forms_record', function (Blueprint $table) {
                $table->string('session', 255)->change();
            });
        }

        return 0;
    }
}