<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250215110647 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, organizer_id_id INT DEFAULT NULL, event_id INT NOT NULL, event_name VARCHAR(255) NOT NULL, event_description VARCHAR(255) NOT NULL, event_date DATETIME NOT NULL, event_location VARCHAR(255) NOT NULL, event_status VARCHAR(255) NOT NULL, INDEX IDX_3BAE0AA7E78C696A (organizer_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sponsor (id INT AUTO_INCREMENT NOT NULL, sponsor_id INT NOT NULL, sponsor_name VARCHAR(255) NOT NULL, montant INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sponsor_event (sponsor_id INT NOT NULL, event_id INT NOT NULL, INDEX IDX_9B444A9C12F7FB51 (sponsor_id), INDEX IDX_9B444A9C71F7E88B (event_id), PRIMARY KEY(sponsor_id, event_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7E78C696A FOREIGN KEY (organizer_id_id) REFERENCES app_user (id)');
        $this->addSql('ALTER TABLE sponsor_event ADD CONSTRAINT FK_9B444A9C12F7FB51 FOREIGN KEY (sponsor_id) REFERENCES sponsor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sponsor_event ADD CONSTRAINT FK_9B444A9C71F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7E78C696A');
        $this->addSql('ALTER TABLE sponsor_event DROP FOREIGN KEY FK_9B444A9C12F7FB51');
        $this->addSql('ALTER TABLE sponsor_event DROP FOREIGN KEY FK_9B444A9C71F7E88B');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE sponsor');
        $this->addSql('DROP TABLE sponsor_event');
    }
}
