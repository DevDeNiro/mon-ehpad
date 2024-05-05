<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240505195433 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE core_pending_one_time_password (id UUID NOT NULL, one_time_password VARCHAR(255) NOT NULL, expires_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL, target JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX one_time_password_idx ON core_pending_one_time_password (one_time_password)');
        $this->addSql('COMMENT ON COLUMN core_pending_one_time_password.id IS \'(DC2Type:id)\'');
        $this->addSql('COMMENT ON COLUMN core_pending_one_time_password.one_time_password IS \'(DC2Type:one_time_password)\'');
        $this->addSql('COMMENT ON COLUMN core_pending_one_time_password.expires_at IS \'(DC2Type:chronos)\'');
        $this->addSql('COMMENT ON COLUMN core_pending_one_time_password.target IS \'(DC2Type:target)\'');
        $this->addSql('CREATE TABLE security_user (id UUID NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(60) NOT NULL, status security_status NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX email ON security_user (email)');
        $this->addSql('COMMENT ON COLUMN security_user.id IS \'(DC2Type:id)\'');
        $this->addSql('COMMENT ON COLUMN security_user.email IS \'(DC2Type:email)\'');
        $this->addSql('COMMENT ON COLUMN security_user.password IS \'(DC2Type:password)\'');
        $this->addSql('COMMENT ON COLUMN security_user.status IS \'(DC2Type:status)\'');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE core_pending_one_time_password');
        $this->addSql('DROP TABLE security_user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
