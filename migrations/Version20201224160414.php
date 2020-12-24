<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201224160414 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "cast" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, author_id INTEGER NOT NULL, association_id INTEGER DEFAULT NULL, description CLOB NOT NULL, image VARCHAR(255) DEFAULT NULL, date DATE NOT NULL)');
        $this->addSql('CREATE INDEX IDX_12B8B9F6F675F31B ON "cast" (author_id)');
        $this->addSql('CREATE INDEX IDX_12B8B9F6EFB9C8A5 ON "cast" (association_id)');
        $this->addSql('DROP INDEX IDX_4390CF0FA76ED395');
        $this->addSql('DROP INDEX IDX_4390CF0FEFB9C8A5');
        $this->addSql('CREATE TEMPORARY TABLE __temp__association_member AS SELECT id, association_id, user_id FROM association_member');
        $this->addSql('DROP TABLE association_member');
        $this->addSql('CREATE TABLE association_member (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, association_id INTEGER NOT NULL, user_id INTEGER NOT NULL, CONSTRAINT FK_4390CF0FEFB9C8A5 FOREIGN KEY (association_id) REFERENCES association (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_4390CF0FA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO association_member (id, association_id, user_id) SELECT id, association_id, user_id FROM __temp__association_member');
        $this->addSql('DROP TABLE __temp__association_member');
        $this->addSql('CREATE INDEX IDX_4390CF0FA76ED395 ON association_member (user_id)');
        $this->addSql('CREATE INDEX IDX_4390CF0FEFB9C8A5 ON association_member (association_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE "cast"');
        $this->addSql('DROP INDEX IDX_4390CF0FEFB9C8A5');
        $this->addSql('DROP INDEX IDX_4390CF0FA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__association_member AS SELECT id, association_id, user_id FROM association_member');
        $this->addSql('DROP TABLE association_member');
        $this->addSql('CREATE TABLE association_member (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, association_id INTEGER NOT NULL, user_id INTEGER NOT NULL)');
        $this->addSql('INSERT INTO association_member (id, association_id, user_id) SELECT id, association_id, user_id FROM __temp__association_member');
        $this->addSql('DROP TABLE __temp__association_member');
        $this->addSql('CREATE INDEX IDX_4390CF0FEFB9C8A5 ON association_member (association_id)');
        $this->addSql('CREATE INDEX IDX_4390CF0FA76ED395 ON association_member (user_id)');
    }
}
