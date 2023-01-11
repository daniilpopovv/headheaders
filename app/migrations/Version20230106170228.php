<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230106170228 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE resume ADD seeker_id INT NULL');
        $this->addSql('ALTER TABLE resume ADD CONSTRAINT FK_60C1D0A057555B2 FOREIGN KEY (seeker_id) REFERENCES seeker (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_60C1D0A057555B2 ON resume (seeker_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE resume DROP CONSTRAINT FK_60C1D0A057555B2');
        $this->addSql('DROP INDEX IDX_60C1D0A057555B2');
        $this->addSql('ALTER TABLE resume DROP seeker_id');
    }
}
