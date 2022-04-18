<?php

namespace Composer\Support\Database;

use Exception;
use Illuminate\Support\Fluent;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Grammars\Grammar;
use Illuminate\Database\Migrations\Migration as AbstractMigration;

class Migration extends AbstractMigration
{
    public function __construct()
    {
        $this->addCommentTableMethod();
    }

    protected function addCommentTableMethod()
    {
        Blueprint::macro('comment', function ($comment) {
            if (!Grammar::hasMacro('compileCommentTable')) {
                Grammar::macro('compileCommentTable', function (Blueprint $blueprint, Fluent $command, Connection $connection) {
                    switch ($database_driver = $connection->getDriverName()) {
                        case 'mysql':
                            return 'alter table ' . $this->wrapTable($blueprint) . $this->modifyComment($blueprint, $command);
                        case 'pgsql':
                            return sprintf(
                                'comment on table %s is %s',
                                $this->wrapTable($blueprint),
                                "'" . str_replace("'", "''", $command->comment) . "'"
                            );
                        case 'sqlserver':
                        case 'sqlite':
                        default:
                            throw new Exception("The {$database_driver} not support table comment.");
                    }
                });
            }

            return $this->addCommand('commentTable', compact('comment'));
        });
    }
}
