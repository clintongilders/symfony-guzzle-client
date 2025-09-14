<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250912195346 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE character_id_seq CASCADE');
        $this->addSql('ALTER TABLE "character" DROP CONSTRAINT "character_pkey"');
        $this->addSql('ALTER TABLE "character" DROP id');
        $this->addSql('ALTER TABLE "character" ADD PRIMARY KEY (uuid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE character_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('DROP INDEX character_pkey');
        $this->addSql('ALTER TABLE character ADD id SERIAL NOT NULL');
        $this->addSql('ALTER TABLE character ADD PRIMARY KEY (id)');
    }
}
