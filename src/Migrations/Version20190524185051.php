<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190524185051 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE user (username VARCHAR(100) NOT NULL, settings TEXT NOT NULL, PRIMARY KEY(username))');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE user');
    }
}
