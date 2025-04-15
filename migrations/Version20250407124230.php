<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250407124230 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE access_log ADD user_id INT DEFAULT NULL, ADD date DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', ADD name VARCHAR(255) NOT NULL, ADD notified TINYINT(1) NOT NULL, DROP access_time, DROP image_path, CHANGE identifier face_capture_path VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE access_log ADD CONSTRAINT FK_EF7F3510A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_EF7F3510A76ED395 ON access_log (user_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE access_log DROP FOREIGN KEY FK_EF7F3510A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_EF7F3510A76ED395 ON access_log
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE access_log ADD identifier VARCHAR(255) NOT NULL, ADD access_time DATETIME NOT NULL, ADD image_path VARCHAR(255) DEFAULT NULL, DROP user_id, DROP date, DROP face_capture_path, DROP name, DROP notified
        SQL);
    }
}
