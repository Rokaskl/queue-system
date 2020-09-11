<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200907095209 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_437EE93965A020A6 ON visit');
        $this->addSql('ALTER TABLE visit CHANGE registration_date start_date DATETIME NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_437EE93995275AB8 ON visit (start_date)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_437EE93995275AB8 ON visit');
        $this->addSql('ALTER TABLE visit CHANGE start_date registration_date DATETIME NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_437EE93965A020A6 ON visit (registration_date)');
    }
}
