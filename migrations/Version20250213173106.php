<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250213173106 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event (event_id INT AUTO_INCREMENT NOT NULL, event_name VARCHAR(255) NOT NULL, event_description VARCHAR(1000) NOT NULL, event_date DATETIME NOT NULL, event_location VARCHAR(255) NOT NULL, event_statut VARCHAR(50) NOT NULL, organizer_id INT NOT NULL, PRIMARY KEY(event_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sponsor (sponsor_id INT AUTO_INCREMENT NOT NULL, event_id INT DEFAULT NULL, sponsor_name VARCHAR(255) NOT NULL, product_id INT NOT NULL, montant INT NOT NULL, INDEX IDX_818CC9D471F7E88B (event_id), PRIMARY KEY(sponsor_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sponsor ADD CONSTRAINT FK_818CC9D471F7E88B FOREIGN KEY (event_id) REFERENCES event (event_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sponsor DROP FOREIGN KEY FK_818CC9D471F7E88B');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE sponsor');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
