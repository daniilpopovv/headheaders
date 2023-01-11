<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230105233220 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE vacancy_skill (vacancy_id INT NOT NULL, skill_id INT NOT NULL, PRIMARY KEY(vacancy_id, skill_id))');
        $this->addSql('CREATE INDEX IDX_87739B15433B78C4 ON vacancy_skill (vacancy_id)');
        $this->addSql('CREATE INDEX IDX_87739B155585C142 ON vacancy_skill (skill_id)');
        $this->addSql('ALTER TABLE vacancy_skill ADD CONSTRAINT FK_87739B15433B78C4 FOREIGN KEY (vacancy_id) REFERENCES vacancy (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE vacancy_skill ADD CONSTRAINT FK_87739B155585C142 FOREIGN KEY (skill_id) REFERENCES skill (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE vacancy_skill DROP CONSTRAINT FK_87739B15433B78C4');
        $this->addSql('ALTER TABLE vacancy_skill DROP CONSTRAINT FK_87739B155585C142');
        $this->addSql('DROP TABLE vacancy_skill');
    }
}
