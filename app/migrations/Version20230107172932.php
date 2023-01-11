<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230107172932 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE vacancy_resume (vacancy_id INT NOT NULL, resume_id INT NOT NULL, PRIMARY KEY(vacancy_id, resume_id))');
        $this->addSql('CREATE INDEX IDX_C3A49EAB433B78C4 ON vacancy_resume (vacancy_id)');
        $this->addSql('CREATE INDEX IDX_C3A49EABD262AF09 ON vacancy_resume (resume_id)');
        $this->addSql('ALTER TABLE vacancy_resume ADD CONSTRAINT FK_C3A49EAB433B78C4 FOREIGN KEY (vacancy_id) REFERENCES vacancy (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE vacancy_resume ADD CONSTRAINT FK_C3A49EABD262AF09 FOREIGN KEY (resume_id) REFERENCES resume (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE vacancy_resume DROP CONSTRAINT FK_C3A49EAB433B78C4');
        $this->addSql('ALTER TABLE vacancy_resume DROP CONSTRAINT FK_C3A49EABD262AF09');
        $this->addSql('DROP TABLE vacancy_resume');
    }
}
