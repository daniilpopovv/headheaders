<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230117145854 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE admin_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE company_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE recruiter_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE resume_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE seeker_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE skill_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE vacancy_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE admin (id INT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_880E0D76F85E0677 ON admin (username)');
        $this->addSql('CREATE TABLE company (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE recruiter (id INT NOT NULL, company_id INT DEFAULT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, full_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DE8633D8F85E0677 ON recruiter (username)');
        $this->addSql('CREATE INDEX IDX_DE8633D8979B1AD6 ON recruiter (company_id)');
        $this->addSql('CREATE TABLE resume (id INT NOT NULL, seeker_id INT NOT NULL, specialization VARCHAR(255) NOT NULL, description VARCHAR(2000) DEFAULT NULL, salary INT NOT NULL, photo_filename VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_60C1D0A057555B2 ON resume (seeker_id)');
        $this->addSql('CREATE TABLE resume_skill (resume_id INT NOT NULL, skill_id INT NOT NULL, PRIMARY KEY(resume_id, skill_id))');
        $this->addSql('CREATE INDEX IDX_C2CA241FD262AF09 ON resume_skill (resume_id)');
        $this->addSql('CREATE INDEX IDX_C2CA241F5585C142 ON resume_skill (skill_id)');
        $this->addSql('CREATE TABLE resume_vacancy (resume_id INT NOT NULL, vacancy_id INT NOT NULL, PRIMARY KEY(resume_id, vacancy_id))');
        $this->addSql('CREATE INDEX IDX_9F74D355D262AF09 ON resume_vacancy (resume_id)');
        $this->addSql('CREATE INDEX IDX_9F74D355433B78C4 ON resume_vacancy (vacancy_id)');
        $this->addSql('CREATE TABLE seeker (id INT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, full_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_835D8AF3F85E0677 ON seeker (username)');
        $this->addSql('CREATE TABLE skill (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE vacancy (id INT NOT NULL, recruiter_id INT NOT NULL, specialization VARCHAR(255) NOT NULL, description VARCHAR(2000) DEFAULT NULL, salary INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A9346CBD156BE243 ON vacancy (recruiter_id)');
        $this->addSql('CREATE TABLE vacancy_skill (vacancy_id INT NOT NULL, skill_id INT NOT NULL, PRIMARY KEY(vacancy_id, skill_id))');
        $this->addSql('CREATE INDEX IDX_87739B15433B78C4 ON vacancy_skill (vacancy_id)');
        $this->addSql('CREATE INDEX IDX_87739B155585C142 ON vacancy_skill (skill_id)');
        $this->addSql('CREATE TABLE vacancy_resume (vacancy_id INT NOT NULL, resume_id INT NOT NULL, PRIMARY KEY(vacancy_id, resume_id))');
        $this->addSql('CREATE INDEX IDX_C3A49EAB433B78C4 ON vacancy_resume (vacancy_id)');
        $this->addSql('CREATE INDEX IDX_C3A49EABD262AF09 ON vacancy_resume (resume_id)');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE recruiter ADD CONSTRAINT FK_DE8633D8979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE resume ADD CONSTRAINT FK_60C1D0A057555B2 FOREIGN KEY (seeker_id) REFERENCES seeker (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE resume_skill ADD CONSTRAINT FK_C2CA241FD262AF09 FOREIGN KEY (resume_id) REFERENCES resume (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE resume_skill ADD CONSTRAINT FK_C2CA241F5585C142 FOREIGN KEY (skill_id) REFERENCES skill (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE resume_vacancy ADD CONSTRAINT FK_9F74D355D262AF09 FOREIGN KEY (resume_id) REFERENCES resume (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE resume_vacancy ADD CONSTRAINT FK_9F74D355433B78C4 FOREIGN KEY (vacancy_id) REFERENCES vacancy (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE vacancy ADD CONSTRAINT FK_A9346CBD156BE243 FOREIGN KEY (recruiter_id) REFERENCES recruiter (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE vacancy_skill ADD CONSTRAINT FK_87739B15433B78C4 FOREIGN KEY (vacancy_id) REFERENCES vacancy (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE vacancy_skill ADD CONSTRAINT FK_87739B155585C142 FOREIGN KEY (skill_id) REFERENCES skill (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE vacancy_resume ADD CONSTRAINT FK_C3A49EAB433B78C4 FOREIGN KEY (vacancy_id) REFERENCES vacancy (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE vacancy_resume ADD CONSTRAINT FK_C3A49EABD262AF09 FOREIGN KEY (resume_id) REFERENCES resume (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE TABLE sessions (sess_id VARCHAR(128) NOT NULL PRIMARY KEY, sess_data BYTEA NOT NULL, sess_lifetime INTEGER NOT NULL, sess_time INTEGER NOT NULL)');
        $this->addSql('CREATE INDEX expiry ON sessions (sess_lifetime)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE admin_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE company_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE recruiter_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE resume_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE seeker_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE skill_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE vacancy_id_seq CASCADE');
        $this->addSql('ALTER TABLE recruiter DROP CONSTRAINT FK_DE8633D8979B1AD6');
        $this->addSql('ALTER TABLE resume DROP CONSTRAINT FK_60C1D0A057555B2');
        $this->addSql('ALTER TABLE resume_skill DROP CONSTRAINT FK_C2CA241FD262AF09');
        $this->addSql('ALTER TABLE resume_skill DROP CONSTRAINT FK_C2CA241F5585C142');
        $this->addSql('ALTER TABLE resume_vacancy DROP CONSTRAINT FK_9F74D355D262AF09');
        $this->addSql('ALTER TABLE resume_vacancy DROP CONSTRAINT FK_9F74D355433B78C4');
        $this->addSql('ALTER TABLE vacancy DROP CONSTRAINT FK_A9346CBD156BE243');
        $this->addSql('ALTER TABLE vacancy_skill DROP CONSTRAINT FK_87739B15433B78C4');
        $this->addSql('ALTER TABLE vacancy_skill DROP CONSTRAINT FK_87739B155585C142');
        $this->addSql('ALTER TABLE vacancy_resume DROP CONSTRAINT FK_C3A49EAB433B78C4');
        $this->addSql('ALTER TABLE vacancy_resume DROP CONSTRAINT FK_C3A49EABD262AF09');
        $this->addSql('DROP TABLE admin');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE recruiter');
        $this->addSql('DROP TABLE resume');
        $this->addSql('DROP TABLE resume_skill');
        $this->addSql('DROP TABLE resume_vacancy');
        $this->addSql('DROP TABLE seeker');
        $this->addSql('DROP TABLE skill');
        $this->addSql('DROP TABLE vacancy');
        $this->addSql('DROP TABLE vacancy_skill');
        $this->addSql('DROP TABLE vacancy_resume');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
