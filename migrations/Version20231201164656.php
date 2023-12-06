<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231201164656 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE diagram ADD adjacency_matrix JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE edge DROP FOREIGN KEY FK_7506D36668C90015');
        $this->addSql('ALTER TABLE edge DROP FOREIGN KEY FK_7506D3668DB60186');
        $this->addSql('ALTER TABLE edge ADD CONSTRAINT FK_7506D36668C90015 FOREIGN KEY (predecessor_id) REFERENCES task (id)');
        $this->addSql('ALTER TABLE edge ADD CONSTRAINT FK_7506D3668DB60186 FOREIGN KEY (task_id) REFERENCES task (id)');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB252AAC800A');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB252AAC800A FOREIGN KEY (pert_chart_id) REFERENCES diagram (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE diagram DROP adjacency_matrix');
        $this->addSql('ALTER TABLE edge DROP FOREIGN KEY FK_7506D3668DB60186');
        $this->addSql('ALTER TABLE edge DROP FOREIGN KEY FK_7506D36668C90015');
        $this->addSql('ALTER TABLE edge ADD CONSTRAINT FK_7506D3668DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE edge ADD CONSTRAINT FK_7506D36668C90015 FOREIGN KEY (predecessor_id) REFERENCES task (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB252AAC800A');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB252AAC800A FOREIGN KEY (pert_chart_id) REFERENCES diagram (id) ON DELETE CASCADE');
    }
}
