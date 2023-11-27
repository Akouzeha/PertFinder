<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231127145518 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE task_dependency (id INT AUTO_INCREMENT NOT NULL, task_id INT DEFAULT NULL, dependent_task_id INT DEFAULT NULL, INDEX IDX_334B1EAA8DB60186 (task_id), INDEX IDX_334B1EAA8447C86E (dependent_task_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE task_dependency ADD CONSTRAINT FK_334B1EAA8DB60186 FOREIGN KEY (task_id) REFERENCES task (id)');
        $this->addSql('ALTER TABLE task_dependency ADD CONSTRAINT FK_334B1EAA8447C86E FOREIGN KEY (dependent_task_id) REFERENCES task (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE task_dependency DROP FOREIGN KEY FK_334B1EAA8DB60186');
        $this->addSql('ALTER TABLE task_dependency DROP FOREIGN KEY FK_334B1EAA8447C86E');
        $this->addSql('DROP TABLE task_dependency');
    }
}
