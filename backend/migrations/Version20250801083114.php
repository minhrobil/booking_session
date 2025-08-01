<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250801083114 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE booking (id SERIAL NOT NULL, full_name VARCHAR(100) NOT NULL, email VARCHAR(100) NOT NULL, phone_number VARCHAR(20) NOT NULL, created_at TIME(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE booking_item (id SERIAL NOT NULL, booking_id INT NOT NULL, session_time_slot_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_78A07503301C60 ON booking_item (booking_id)');
        $this->addSql('CREATE INDEX IDX_78A0750C3FF0D7B ON booking_item (session_time_slot_id)');
        $this->addSql('CREATE TABLE session (id SERIAL NOT NULL, session_type_id INT NOT NULL, date DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D044D5D4D7940EC9 ON session (session_type_id)');
        $this->addSql('CREATE TABLE session_time_slot (id SERIAL NOT NULL, session_id INT NOT NULL, trainer_id INT DEFAULT NULL, start_time TIME(0) WITHOUT TIME ZONE NOT NULL, end_time TIME(0) WITHOUT TIME ZONE NOT NULL, is_booked BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_42516C40613FECDF ON session_time_slot (session_id)');
        $this->addSql('CREATE INDEX IDX_42516C40FB08EDF6 ON session_time_slot (trainer_id)');
        $this->addSql('CREATE TABLE session_type (id SERIAL NOT NULL, name VARCHAR(20) NOT NULL, duration INT NOT NULL, price NUMERIC(5, 2) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE trainer (id SERIAL NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE booking_item ADD CONSTRAINT FK_78A07503301C60 FOREIGN KEY (booking_id) REFERENCES booking (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE booking_item ADD CONSTRAINT FK_78A0750C3FF0D7B FOREIGN KEY (session_time_slot_id) REFERENCES session_time_slot (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D4D7940EC9 FOREIGN KEY (session_type_id) REFERENCES session_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE session_time_slot ADD CONSTRAINT FK_42516C40613FECDF FOREIGN KEY (session_id) REFERENCES session (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE session_time_slot ADD CONSTRAINT FK_42516C40FB08EDF6 FOREIGN KEY (trainer_id) REFERENCES trainer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE booking_item DROP CONSTRAINT FK_78A07503301C60');
        $this->addSql('ALTER TABLE booking_item DROP CONSTRAINT FK_78A0750C3FF0D7B');
        $this->addSql('ALTER TABLE session DROP CONSTRAINT FK_D044D5D4D7940EC9');
        $this->addSql('ALTER TABLE session_time_slot DROP CONSTRAINT FK_42516C40613FECDF');
        $this->addSql('ALTER TABLE session_time_slot DROP CONSTRAINT FK_42516C40FB08EDF6');
        $this->addSql('DROP TABLE booking');
        $this->addSql('DROP TABLE booking_item');
        $this->addSql('DROP TABLE session');
        $this->addSql('DROP TABLE session_time_slot');
        $this->addSql('DROP TABLE session_type');
        $this->addSql('DROP TABLE trainer');
    }
}
