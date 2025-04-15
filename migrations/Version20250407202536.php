<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250407202536 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE access_log (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, date DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', status VARCHAR(255) NOT NULL, face_capture_path VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, notified TINYINT(1) DEFAULT 0 NOT NULL, INDEX IDX_EF7F3510A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE access_log ADD CONSTRAINT FK_EF7F3510A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE authorization DROP FOREIGN KEY FK_7A6D8BEFA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_7A6D8BEFA76ED395 ON authorization
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE authorization DROP user_id, DROP status
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD authorization_status VARCHAR(20) NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE access_log DROP FOREIGN KEY FK_EF7F3510A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE access_log
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE authorization ADD user_id INT NOT NULL, ADD status VARCHAR(50) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE authorization ADD CONSTRAINT FK_7A6D8BEFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_7A6D8BEFA76ED395 ON authorization (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user DROP authorization_status
        SQL);
    }
}
