<?php

declare(strict_types=1);

namespace App\Core;

/**
 * The Migration Runner.
 *
 * This class orchestrates the migration process. It takes a list of root entity classes,
 * uses the MigrationGenerator to produce the full SQL schema, and then executes it.
 */
class Migration
{
    private Database $db;
    private MigrationGenerator $generator;

    public function __construct(Database $database)
    {
        $this->db = $database;
        $this->generator = new MigrationGenerator();
    }

    /**
     * Generates and runs the SQL for all specified root entities.
     *
     * @param array<string> $rootEntities An array of fully qualified class names for the root entities.
     * @return void
     */
    public function run(array $rootEntities): void
    {
        echo "Starting migration process...\n";

        $fullSqlScript = "";
        foreach ($rootEntities as $entityClass) {
            echo "Generating schema for {$entityClass}...\n";
            $fullSqlScript .= "-- Schema for root entity: {$entityClass}\n";
            $fullSqlScript .= $this->generator->generate($entityClass);
            $fullSqlScript .= "\n";
        }

        if (empty(trim($fullSqlScript))) {
            echo "No migrations to run. Schema appears up to date.\n";
            return;
        }

        try {
            echo "Executing SQL script against the database...\n";
            $this->db->getConnection()->exec($fullSqlScript);
            echo "✅ Migration completed successfully!\n";
        } catch (\PDOException $e) {
            echo "❌ Migration failed: " . $e->getMessage() . "\n";
            // For debugging, you could save the failed script to a file
            // file_put_contents('migration_error.sql', $fullSqlScript);
        }
    }
}
