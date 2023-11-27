<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231127151318 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE task_task (task_source INT NOT NULL, task_target INT NOT NULL, INDEX IDX_21CD4F5E6423FBA0 (task_source), INDEX IDX_21CD4F5E7DC6AB2F (task_target), PRIMARY KEY(task_source, task_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE task_task ADD CONSTRAINT FK_21CD4F5E6423FBA0 FOREIGN KEY (task_source) REFERENCES task (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE task_task ADD CONSTRAINT FK_21CD4F5E7DC6AB2F FOREIGN KEY (task_target) REFERENCES task (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE task_task DROP FOREIGN KEY FK_21CD4F5E6423FBA0');
        $this->addSql('ALTER TABLE task_task DROP FOREIGN KEY FK_21CD4F5E7DC6AB2F');
        $this->addSql('DROP TABLE task_task');
    }
}
