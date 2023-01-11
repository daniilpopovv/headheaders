<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230108112816 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE resume_vacancy (resume_id INT NOT NULL, vacancy_id INT NOT NULL, PRIMARY KEY(resume_id, vacancy_id))');
        $this->addSql('CREATE INDEX IDX_9F74D355D262AF09 ON resume_vacancy (resume_id)');
        $this->addSql('CREATE INDEX IDX_9F74D355433B78C4 ON resume_vacancy (vacancy_id)');
        $this->addSql('ALTER TABLE resume_vacancy ADD CONSTRAINT FK_9F74D355D262AF09 FOREIGN KEY (resume_id) REFERENCES resume (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE resume_vacancy ADD CONSTRAINT FK_9F74D355433B78C4 FOREIGN KEY (vacancy_id) REFERENCES vacancy (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE resume_vacancy DROP CONSTRAINT FK_9F74D355D262AF09');
        $this->addSql('ALTER TABLE resume_vacancy DROP CONSTRAINT FK_9F74D355433B78C4');
        $this->addSql('DROP TABLE resume_vacancy');
    }
}
