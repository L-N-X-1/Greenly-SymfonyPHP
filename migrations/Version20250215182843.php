<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250215182843 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE app_user DROP FOREIGN KEY FK_88BDF3E94584665A');
        $this->addSql('CREATE TABLE sponsor_event (sponsor_id INT NOT NULL, event_id INT NOT NULL, INDEX IDX_9B444A9C12F7FB51 (sponsor_id), INDEX IDX_9B444A9C71F7E88B (event_id), PRIMARY KEY(sponsor_id, event_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sponsor_event ADD CONSTRAINT FK_9B444A9C12F7FB51 FOREIGN KEY (sponsor_id) REFERENCES sponsor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sponsor_event ADD CONSTRAINT FK_9B444A9C71F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_sponsor DROP FOREIGN KEY FK_4DB607B71F7E88B');
        $this->addSql('ALTER TABLE event_sponsor DROP FOREIGN KEY FK_4DB607B12F7FB51');
        $this->addSql('ALTER TABLE sponsor_product DROP FOREIGN KEY FK_99240AEF12F7FB51');
        $this->addSql('ALTER TABLE sponsor_product DROP FOREIGN KEY FK_99240AEF4584665A');
        $this->addSql('DROP TABLE event_sponsor');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE sponsor_product');
        $this->addSql('DROP INDEX IDX_88BDF3E94584665A ON app_user');
        $this->addSql('ALTER TABLE app_user DROP product_id');
        $this->addSql('ALTER TABLE sponsor CHANGE product_id sponsor_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event_sponsor (event_id INT NOT NULL, sponsor_id INT NOT NULL, INDEX IDX_4DB607B71F7E88B (event_id), INDEX IDX_4DB607B12F7FB51 (sponsor_id), PRIMARY KEY(event_id, sponsor_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, product_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE sponsor_product (product_id INT NOT NULL, sponsor_id INT NOT NULL, INDEX IDX_99240AEF4584665A (product_id), INDEX IDX_99240AEF12F7FB51 (sponsor_id), PRIMARY KEY(product_id, sponsor_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE event_sponsor ADD CONSTRAINT FK_4DB607B71F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_sponsor ADD CONSTRAINT FK_4DB607B12F7FB51 FOREIGN KEY (sponsor_id) REFERENCES sponsor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sponsor_product ADD CONSTRAINT FK_99240AEF12F7FB51 FOREIGN KEY (sponsor_id) REFERENCES sponsor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sponsor_product ADD CONSTRAINT FK_99240AEF4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sponsor_event DROP FOREIGN KEY FK_9B444A9C12F7FB51');
        $this->addSql('ALTER TABLE sponsor_event DROP FOREIGN KEY FK_9B444A9C71F7E88B');
        $this->addSql('DROP TABLE sponsor_event');
        $this->addSql('ALTER TABLE app_user ADD product_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE app_user ADD CONSTRAINT FK_88BDF3E94584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_88BDF3E94584665A ON app_user (product_id)');
        $this->addSql('ALTER TABLE sponsor CHANGE sponsor_id product_id INT NOT NULL');
    }
}
