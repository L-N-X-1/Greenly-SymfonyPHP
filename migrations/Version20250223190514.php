<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250223190514 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attendee ADD email VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1150D567E7927C74 ON attendee (email)');
        $this->addSql('ALTER TABLE sponsor ADD email VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_818CC9D4E7927C74 ON sponsor (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_1150D567E7927C74 ON attendee');
        $this->addSql('ALTER TABLE attendee DROP email');
        $this->addSql('DROP INDEX UNIQ_818CC9D4E7927C74 ON sponsor');
        $this->addSql('ALTER TABLE sponsor DROP email');
    }
}
