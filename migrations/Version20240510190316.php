<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240510190316 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE security_company (id UUID NOT NULL, owner_id UUID NOT NULL, company_name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_225402527E3C61F9 ON security_company (owner_id)');
        $this->addSql("COMMENT ON COLUMN security_company.id IS '(DC2Type:ulid)'");
        $this->addSql("COMMENT ON COLUMN security_company.owner_id IS '(DC2Type:ulid)'");
        $this->addSql('CREATE TABLE security_user (id UUID NOT NULL, verification_code_id UUID DEFAULT NULL, company_id UUID DEFAULT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(60) NOT NULL, status VARCHAR(10) NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_52825A883CB0AEC6 ON security_user (verification_code_id)');
        $this->addSql('CREATE INDEX IDX_52825A88979B1AD6 ON security_user (company_id)');
        $this->addSql('CREATE UNIQUE INDEX email ON security_user (email)');
        $this->addSql("COMMENT ON COLUMN security_user.id IS '(DC2Type:ulid)'");
        $this->addSql("COMMENT ON COLUMN security_user.verification_code_id IS '(DC2Type:ulid)'");
        $this->addSql("COMMENT ON COLUMN security_user.company_id IS '(DC2Type:ulid)'");
        $this->addSql("COMMENT ON COLUMN security_user.status IS '(DC2Type:status)'");
        $this->addSql('CREATE TABLE security_verification_code (id UUID NOT NULL, code VARCHAR(6) NOT NULL, expires_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX code ON security_verification_code (code)');
        $this->addSql("COMMENT ON COLUMN security_verification_code.id IS '(DC2Type:ulid)'");
        $this->addSql("COMMENT ON COLUMN security_verification_code.expires_at IS '(DC2Type:chronos)'");
        $this->addSql('ALTER TABLE security_company ADD CONSTRAINT FK_225402527E3C61F9 FOREIGN KEY (owner_id) REFERENCES security_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE security_user ADD CONSTRAINT FK_52825A883CB0AEC6 FOREIGN KEY (verification_code_id) REFERENCES security_verification_code (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE security_user ADD CONSTRAINT FK_52825A88979B1AD6 FOREIGN KEY (company_id) REFERENCES security_company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE security_company DROP CONSTRAINT FK_225402527E3C61F9');
        $this->addSql('ALTER TABLE security_user DROP CONSTRAINT FK_52825A883CB0AEC6');
        $this->addSql('ALTER TABLE security_user DROP CONSTRAINT FK_52825A88979B1AD6');
        $this->addSql('DROP TABLE security_company');
        $this->addSql('DROP TABLE security_user');
        $this->addSql('DROP TABLE security_verification_code');
    }
}
