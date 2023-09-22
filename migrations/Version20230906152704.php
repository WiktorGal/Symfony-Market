<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230906152704 extends AbstractMigration
{
    public function getDescription(): string
    {
        // Add a description for your migration if needed
        return 'Add created_at column and update existing data';
    }

    public function up(Schema $schema): void
    {
        // Add the created_at column with a default value of the current timestamp
        $this->addSql('ALTER TABLE item ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');

        // Update existing rows' created_at values to the current timestamp if they are NULL
        $this->addSql('UPDATE item SET created_at = CURRENT_TIMESTAMP WHERE created_at IS NULL');
    }

    public function down(Schema $schema): void
    {
        // Remove the created_at column if needed (reverse the changes)
        $this->addSql('ALTER TABLE item DROP created_at');
    }
}
