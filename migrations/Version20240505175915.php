<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240505175915 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TYPE security_status AS ENUM(\'waiting_for_confirmation\', \'active\')');
        $this->addSql(<<<SQL
            CREATE FUNCTION GENERATE_ONE_TIME_PASSWORD() 
                RETURNS VARCHAR(6) 
                LANGUAGE plpgsql
                AS $$
            DECLARE
                new_one_time_password VARCHAR(6);
            BEGIN
                SELECT to_char(codes.new_code, 'FM000000')
                INTO new_one_time_password
                FROM (SELECT (trunc((random() * (999999 - 1)) + 1)) as new_code
                      FROM generate_series(1, 99999)) AS codes
                WHERE to_char(codes.new_code, 'FM000000') NOT IN (SELECT one_time_password FROM core_pending_one_time_password)
                LIMIT 1;
                RETURN new_one_time_password;
            END;
            $$;
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TYPE security_status');
        $this->addSql('DROP FUNCTION GENERATE_ONE_TIME_PASSWORD');
    }
}
