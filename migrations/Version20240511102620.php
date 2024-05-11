<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240511102620 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE security_forgotten_password_request (id UUID NOT NULL, user_id UUID NOT NULL, expires_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL, hashed_token TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_55795B34A76ED395 ON security_forgotten_password_request (user_id)');
        $this->addSql("COMMENT ON COLUMN security_forgotten_password_request.id IS '(DC2Type:ulid)'");
        $this->addSql("COMMENT ON COLUMN security_forgotten_password_request.user_id IS '(DC2Type:ulid)'");
        $this->addSql("COMMENT ON COLUMN security_forgotten_password_request.expires_at IS '(DC2Type:chronos)'");
        $this->addSql('ALTER TABLE security_forgotten_password_request ADD CONSTRAINT FK_55795B34A76ED395 FOREIGN KEY (user_id) REFERENCES security_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE security_forgotten_password_request DROP CONSTRAINT FK_55795B34A76ED395');
        $this->addSql('DROP TABLE security_forgotten_password_request');
    }
}
