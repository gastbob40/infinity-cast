<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201209055533 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE association_member (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, association_id INTEGER NOT NULL, user_id INTEGER NOT NULL)');
        $this->addSql('CREATE INDEX IDX_4390CF0FEFB9C8A5 ON association_member (association_id)');
        $this->addSql('CREATE INDEX IDX_4390CF0FA76ED395 ON association_member (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE association_member');
    }
}
