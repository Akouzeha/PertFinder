<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231127153102 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB252AAC800A');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB252AAC800A FOREIGN KEY (pert_chart_id) REFERENCES diagram (id)');
        $this->addSql('ALTER TABLE task_dependency DROP FOREIGN KEY FK_334B1EAA8447C86E');
        $this->addSql('ALTER TABLE task_dependency DROP FOREIGN KEY FK_334B1EAA8DB60186');
        $this->addSql('ALTER TABLE task_dependency ADD CONSTRAINT FK_334B1EAA8447C86E FOREIGN KEY (dependent_task_id) REFERENCES task (id)');
        $this->addSql('ALTER TABLE task_dependency ADD CONSTRAINT FK_334B1EAA8DB60186 FOREIGN KEY (task_id) REFERENCES task (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB252AAC800A');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB252AAC800A FOREIGN KEY (pert_chart_id) REFERENCES diagram (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE task_dependency DROP FOREIGN KEY FK_334B1EAA8DB60186');
        $this->addSql('ALTER TABLE task_dependency DROP FOREIGN KEY FK_334B1EAA8447C86E');
        $this->addSql('ALTER TABLE task_dependency ADD CONSTRAINT FK_334B1EAA8DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE task_dependency ADD CONSTRAINT FK_334B1EAA8447C86E FOREIGN KEY (dependent_task_id) REFERENCES task (id) ON DELETE CASCADE');
    }
}
